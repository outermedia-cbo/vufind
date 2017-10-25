// File contains javascript code for various files in themes/linkedswissbib

//go back function, returns to previous page
function goBack() {
    window.history.back();
}

//function that writes person's and subject's name in to html code --> authordetails, subjectdetails
function writeLabelIntoHtml (labelAsString) {
    $('.labelAsString').html(labelAsString);
}

//extract Ids form type BibliographicResources --> used as search (lookfor) parameters in ajax call
function getIdsFromPropertyInBibliographicResourcesAsString (data, property) {
    var array = data.bibliographicResource;
    if (typeof array !== 'undefined') {
        if ($.isArray(array)) {
            var result = "";
            for (var key in array) {
                result += array[key]._source[property] + ",";
                result = result.replace(/undefined,/g, "");
            }
            var result = result.substring(0, result.length - 1);
        }
    }
    if (!result) {
        result = "Nicht bekannt";
    }
    return result;
}

//Helper function for carousel
function getCoverLink (array, key) {
    var fallback = "../themes/linkedswissbib/images/icon_no_image_available.gif";
    //get isbn
    if ("bibo:isbn10" in array[key]._source) {
        var isbn10 = array[key]._source['bibo:isbn10'];
        if (typeof isbn10 !== 'undefined') {
            var url_start = 'https://swissbib.ch/Cover/Show?isn=';
            var url_end = '&size=large';
            var link_cover = url_start + isbn10 + url_end;
        }
    } else {
        var link_cover = fallback;
    }
    return link_cover;
}

//Helper function for carousel
// get link to full record of bibliographic resource
function getBibResLink (array, key) {
    //get ID
    var bibliographicResource_id = array[key]._source['@id'];
    //we have something like this
    //data.swissbib.ch/Record/[recordId]
    //and we are looking for the recordId
    var url = /[^\/]+$/;
    var result = bibliographicResource_id.match(url);
    return VuFind.path + '/Record/' + result;

}

//Helper function for carousel
//TODO: Find better fallback solution
//splits bibRes four carousel into four covers per carousel slider
function getItemForCarousel (array, keyStart, keyEnd){
    var result = "";
    var fallbackItem = "";
    if (array.length > keyEnd) {
        for (var key = keyStart; key <= keyEnd; key++) {
            //get cover
            var link_cover = getCoverLink(array, key);
            //get link to holding
            var link_bibRes = getBibResLink (array, key);
            //get title
            var bibResTitle = array[key]._source['dct:title'];

            result += '<div class="col-sm-3"><a href="' + link_bibRes + '" class="hover-overlay" style="max-height: 200px;"><img title="'+ bibResTitle +'" src="' + link_cover + '" style="max-height: 200px;"><div class="content"><b>' + bibResTitle +'</b></div></a></div>';
        }
    } else {
        fallbackItem = '<span class="hover-overlay" style="min-height: 200px;">Nicht bekannt</span>';
        var result= fallbackItem;
    }
    return result;
}

//Helper function for carousel
//gets bibRes for items for slider in carousel
function getBibResForCarousel (data) {
    var array = data.bibliographicResource;
    if (typeof array !== 'undefined') {
        if ($.isArray(array)) {
            var item0 = getItemForCarousel(array,1, 4);
            var item1 = getItemForCarousel(array,5, 8);
            var item2 = getItemForCarousel(array,9, 12);

        }
        var result = [item0, item1, item2];
    }
    if (!result) {
        result = 'Nicht bekannt';
    }
    return result;
}

//Helper function for tag cloud
//Count occurrences (of subjects)
function getOccurrences(string, substring){
    var n=0;
    var pos=0;
    while(true){
        pos=string.indexOf(substring,pos);
        if(pos!=-1){ n++; pos+=substring.length;}
        else{break;}
    }
    return(n);
}

//Helper function for tag cloud
// create array to create tag cloud (see also jquery.hotag.js) for subjects
function getTagCloudContentAsArray (data, gndIdsAsString) {
    var array = data.DEFAULT;
    if (typeof array !== 'undefined') {
        if ($.isArray(array)) {
            var result = [];
            for (var key in array) {
                //var link = '<span class="fa fa-info-circle fa-lg kcopener"></span>';
                var tag = getSubjectPreferredName(array, key);
                var id = array[key]._source['@id'];
                var count = getOccurrences(gndIdsAsString, id);
                var link = VuFind.path + '/Exploration/SubjectDetails?lookfor=' + id + '&type=SubjectById';
                result.push({counts: count, tag: tag, id: id, href: link})
            }
        }
    }
    if (!result) {
        result = "Nicht bekannt";
    }
    return result;
}

//Helper function knowledge Card for Solr result list
//generates list of authors including name and icon (knowledge card)
//persons only! no organisations!
function getPersonAuthorsNameIconAsString(data) {
    var array = data.person;
    if (typeof array !== 'undefined') {
        if ($.isArray(array)) {
            var result = "";
            for (var key in array) {
                //get id
                var person_id = array[key]._source['@id'];
                //get name
                var person = array[key]._source['rdf:type'];
                // if type is person and person is not subject of the details page
                if ((person == 'http://xmlns.com/foaf/0.1/Person')) {
                    //get author's name as label
                    var name = getPersonNameAsString(data, key);
                    //get person's id
                    var person_id = array[key]._source['@id'];
                    // create icon and name as link to author's details page
                    result += '<a style="display:inline;" href="' + VuFind.path +
                    '/Exploration/AuthorDetails?lookfor=' + person_id + '&type=AuthorForId">' + name + '</a>';
                    result += '<span class="fa fa-info-circle fa-lg kcopenerAuthor" style="display:inline;" authorId="' + person_id +'"></span>';
                    result +=  '; ';
                }
            }
            result = result.substring(0, result.length - 2);

        }
    }
    return result;
}

//Helper function thumbnail gallery
//generates gallery for authors including thumbnail, name and icon (knowledge card)
//persons only! no organisations! --> similar to authordetails.js
function getPersonAuthorsNameThumbnailIconAsString(data, person_uniqueId) {
    var array = data.person;
    if (typeof array !== 'undefined') {
        if ($.isArray(array)) {
            var result = "";
            for (var key in array) {
                //get id
                var person_id = array[key]._source['@id'];
                var person_uniqueId = person_uniqueId;
                //get name
                var person = array[key]._source['rdf:type'];
                // if type is person and person is not subject of the details page
                if ((person == 'http://xmlns.com/foaf/0.1/Person') && (person_id != person_uniqueId) ) {
                    //get author's name as label
                    var name = getPersonNameAsString(data, key);
                    //get thumbnail or dummy image
                    var thumbnail = getPersonThumbnail(data, key);
                    //get person's id
                    var person_id = array[key]._source['@id'];
                    // create <li> including ican and name as link to author's details page
                    result += '<li><a href="' + VuFind.path +
                    '/Exploration/AuthorDetails?lookfor=' + person_id + '&type=AuthorForId"><figure><img class="recordcover" src=" ' + thumbnail + ' " alt=" ' + name + ' "><figcaption>' + name + ' ' + '</a>';
                    result += '<span class="fa fa-info-circle fa-lg kcopenerAuthor" authorId="' + person_id +'"></span></figcaption></figure></li>';
                }
            }
        }
    }
    if (!result) {
        result = "Nicht bekannt";
    }
    return result;
}

//Duplicated code: same function in authordetails.js
function checkForArrays (property) {
    if ($.isArray(property)) {
        return property[0];
    } else {
        return property;
    }
}

//Similar in extractName in authordetails.js!!! --> knowledgeCard
function getPersonNameAsString(array, key) {
    var  array = array.person[key]._source;
    if (('foaf:lastName' in array) && ('foaf:firstName') in array) {
        //not ideal solution since it matches first and last name that do not actually belong together
        var firstName = checkForArrays(array['foaf:firstName']);
        var lastName = checkForArrays(array['foaf:lastName']);
        return firstName + ' ' + lastName;
    } else if ('foaf:lastName' in array) {
        return checkForArrays(array['foaf:lastName']);
    } else if ('foaf:name' in array) {
        return checkForArrays(array['foaf:name']);
    } else {
        return 'Nicht bekannt';
    }
}

// --> knowledgeCard
function getPersonThumbnail (data, key) {
    var result = data.person[key]._source['dbp:thumbnail'];
    var fallback = "../themes/linkedswissbib/images/personAvatar.png";
    if (typeof result !== 'undefined') {
        if ($.isArray(result)) {
            return result[0];
        } else {
            return result;
        }
    } else {
        return fallback;
    }
}

function getSubjectLiteral (array, key) {
    if (typeof array[key] != 'undefined') {
        var result = array[key]['@value'];
    } else {
        var result = '';
    }
    return result;
}

// Mainly used to fill fields in "More Details" accordion on detial pages subject
function writeSubjectLiteralsAsStringIntoHtmlId (data, gndUri, htmlId) {
    var array = data.DEFAULT[0]._source[gndUri];
    if (typeof array !== 'undefined') {
        if ($.isArray(array)) {
            var result = "";
            for (var key in array) {
                var literal = getSubjectLiteral(array, key);
                    result += literal + ', ';
            }
            var result = result.substring(0, result.length - 2);
        }
    }
    if (!result) {
        result = "Nicht bekannt";
    }
    $(htmlId).text(result);
}

//get literal from object in array (single value)
function getSubjectPreferredName (array, key) {
    if (typeof array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredName'] != 'undefined') {
        var result = array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredName'][0]['@value'];
    } else if (typeof array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForTheConferenceOrEvent'] != 'undefined') {
        var result = array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForTheConferenceOrEvent'][0]['@value'];
    } else if (typeof array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForTheCorporateBody'] != "undefined") {
        var result = array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForTheCorporateBody'][0]['@value'];
    } else if (typeof array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForTheFamily'] != "undefined") {
        var result = array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForTheFamily'][0]['@value'];
    } else if (typeof array[key]._source['http://d-nb.info/standards/elementset/gnd#preferredNameEntityForThePerson'] != "undefined") {
        var result = array[key]._source['http://d-nb.info/standards/elementset/gnd#preferredNameEntityForThePerson'][0]['@value'];
    } else if (typeof array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForThePerson'] != "undefined") {
        var result = array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForThePerson'][0]['@value'];
    } else if (typeof array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForThePlaceOrGeographicName'] != "undefined") {
        var result = array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForThePlaceOrGeographicName'][0]['@value'];
    } else if (typeof array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForTheSubjectHeading'] != "undefined") {
        var result = array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForTheSubjectHeading'][0]['@value'];
    } else if (typeof array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForTheWork'] != "undefined") {
        var result = array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForTheWork'][0]['@value'];
    } else {
        var result = '';
    }
    return result;
}

//get literals for gnd ids as a string
function getSubjectPreferredNamesAsString (data, knowledgeCardStatement) {
    var array = data.DEFAULT;
    if (typeof array !== 'undefined') {
        if ($.isArray(array)) {
            var result = "";
            for (var key in array) {
                var id = array[key]._source['@id'];
                var literal = getSubjectPreferredName(array, key);
                if (knowledgeCardStatement == 'withKnowledgeCard') {
                    result += '<a href="' + VuFind.path +
                    '/Exploration/SubjectDetails?lookfor=' + id + '&type=SubjectById">' + literal + '</a> <span class="fa fa-info-circle fa-lg kcopenerSubject" subjectId="' + id +'"></span>, ';
                    result = result.replace('undefined, ', ' ');
                } else {
                    result += '<a href="' + VuFind.path +
                    '/Exploration/SubjectDetails?lookfor=' + id + '&type=SubjectById">' + literal + ', ';
                    result = result.replace('undefined, ', ' ');
                }


            }
            var result = result.substring(0, result.length - 2);
        }
    }
    if (!result) {
        result = "Nicht bekannt";
    }
    return result;
}

//Writes Covers and links into carousel --> authordetails
function writeBibliographicResourcesIntoCarouselHtmlClasses (data, htmlId0, htmlId1, htmlId2) {
    var bibRes = getBibResForCarousel(data);
    var item0 = bibRes[0];
    var item1 = bibRes[1];
    var item2 = bibRes[2];
    $(htmlId0).append(item0);
    $(htmlId1).append(item1);
    $(htmlId2).append(item2);
}

//List of Titles of bibliographic Resources and links to instance --> authordetails
function writeBibliographicResourceIntoHtmlClass(data, htmlClass) {
    var array = data.bibliographicResource;
    if (typeof array !== 'undefined') {
        if ($.isArray(array)) {
           //show only 10 or less results
            if (array.length <11 ) {
                var maxLength = array.length;
            } else {
                var maxLength = 10;
            }
            var result = "";
            for (var key=0; key < maxLength; key++) {
                //get title
                var title = array[key]._source['dct:title'];
                var link_bibRes = getBibResLink(array, key);
                // create <li> that links to the Solr record
                result += '<li style="margin-bottom: 5px;"><i class="fa-li fa fa-long-arrow-right"></i><a title="' + title +'" href="' + link_bibRes + '"> ' + title +'</a></li>';
            }
        }
    }
    if (!result) {
        result = 'Nicht bekannt';
    }
    $(htmlClass).html(result);
}

// Write ((add) literals and other elements into html classes --> authordetails
function writePersonAuthorsNameThumbnailIconIntoHtmlClass (personIdsAsString, htmlClass, person_uniqueId) {
    $.ajax({
        url: VuFind.path + "/Ajax/Json?method=getAuthorMulti&searcher=Elasticsearch",
        type: "POST",
        data: {"lookfor": personIdsAsString},
        success: function (data) {
            var result = getPersonAuthorsNameThumbnailIconAsString(data, person_uniqueId);
            $(htmlClass).after(result);
        },
        error: function (e) {
            console.log(e);
        }
    })
}

// Write literals of gnd ids into html Id --> knowledgeCard, subjectdetails
function writeSubjectNamesIntoHtmlId (gndIdsAsString, htmlId, knowledgeCardStatement) {
    $.ajax({
        url: VuFind.path + "/Ajax/Json?&method=getSubjectMulti&searcher=Elasticsearch",
        type: "POST",
        data: {"lookfor": gndIdsAsString},
        success: function (result) {
            var preferredNamesAsString = getSubjectPreferredNamesAsString(result, knowledgeCardStatement);
            $(htmlId).html(preferredNamesAsString);
        },
        error: function (e) {
            console.log(e);
        }
    })
}

// Write literals of gnd ids into tag cloud --> authordetails
function writeSubjectNamesIntoTagCloud (gndIdsAsString, htmlId) {
    $.ajax({
        url: VuFind.path + "/Ajax/Json?&method=getSubjectMulti&searcher=Elasticsearch",
        type: "POST",
        data: {"lookfor": gndIdsAsString},
        success: function (data) {
            var tags = getTagCloudContentAsArray(data, gndIdsAsString);
            if (tags == "Nicht bekannt") {
                $(htmlId).text(tags);
            } else {
                $(htmlId).hotag({
                    tags: tags,
                    containerClass: 'hotag'
                });
            }
        },
        error: function (e) {
            console.log(e);
        }
    });
}

// Write contents of person with unique Id into file --> authordetails
function writeAuthordetailsModuleContentIntoHtml (person_uniqueId, person_nameAsString, person_genreAsUri, person_movementAsUri) {
    //get IDs from type bibliographicResources: ids of resources, ids contributors of resources, ids subjects of resources
    $.ajax({
        url: VuFind.path + "/Ajax/Json?method=getAuthorMulti&searcher=Elasticsearch",
        type: "POST",
        data: {"lookfor": person_uniqueId},
        success: function (data) {
            var idBibRes = getIdsFromPropertyInBibliographicResourcesAsString(data, '@id');
            var idContributorFromBibRes = getIdsFromPropertyInBibliographicResourcesAsString(data, 'dct:contributor');
            var idSubject = getIdsFromPropertyInBibliographicResourcesAsString(data, 'dct:subject');
            writePersonAuthorsNameThumbnailIconIntoHtmlClass (idContributorFromBibRes, ".ad_authorsOfCommonWorks", person_uniqueId);
            writeSubjectNamesIntoTagCloud(idSubject, "#ad_tagCloudSubjectsOfWorks");
            writeLabelIntoHtml (person_nameAsString);
            //works of authors with same genres
            writePersonAuthorsNameThumbnailIconIntoHtmlClass (person_genreAsUri, ".ad_authorsWithSameGenres", person_uniqueId);
            //works of authors with same movements
            writePersonAuthorsNameThumbnailIconIntoHtmlClass (person_movementAsUri, ".ad_authorsWithSameMovement", person_uniqueId);



            $.ajax({
                url: VuFind.path + "/Ajax/Json?method=getAuthorMulti&searcher=Elasticsearch",
                type: "POST",
                data: {"lookfor": idSubject},
                success: function (data) {
                    var idsContributorFromBibRes = getIdsFromPropertyInBibliographicResourcesAsString(data, 'dct:contributor');
                    writeBibliographicResourceIntoHtmlClass(data, ".ad_worksWithSimilarSubjects");
                },
                error: function (e) {
                    console.log(e);
                }
            });

            $.ajax({
                url: VuFind.path + "/Ajax/Json?method=getAuthorMulti&searcher=Elasticsearch",
                type: "POST",
                data: {"lookfor": idContributorFromBibRes},
                success: function (data) {
                    //works of authors of common works
                    writeBibliographicResourcesIntoCarouselHtmlClasses (data, ".item0", ".item1", ".item2");
                },
                error: function (e) {
                    console.log(e);
                }
            });
        },
        error: function (e) {
            console.log(e);
        }
    });
}

// Write contents of gnd subject with unique Id into file --> subjectdetails
function writeSubjectdetailsModuleContentIntoHtml (subject_uniqueId, subject_preferredNameAsString, gndIds_subject_broaderTerms, gndIds_subject_narrowerTerms) {
    //get IDs from type bibliographicResources: ids of resources, ids contributors of resources, ids subjects of resources
    $.ajax({
        url: VuFind.path + "/Ajax/Json?method=getAuthorMulti&searcher=Elasticsearch",
        type: "POST",
        data: {"lookfor": subject_uniqueId},
        success: function (data) {
            var idBibRes = getIdsFromPropertyInBibliographicResourcesAsString(data, '@id');
            var idContributorFromBibRes = getIdsFromPropertyInBibliographicResourcesAsString(data, 'dct:contributor');
            var idSubject = getIdsFromPropertyInBibliographicResourcesAsString(data, 'dct:subject');
            writePersonAuthorsNameThumbnailIconIntoHtmlClass (idContributorFromBibRes, ".sd_authorsOfWorksWithSubject", subject_uniqueId);
            writeLabelIntoHtml (subject_preferredNameAsString);

            $.ajax({
                url: VuFind.path + "/Ajax/Json?method=getAuthorMulti&searcher=Elasticsearch",
                type: "POST",
                data: {"lookfor": idSubject},
                success: function (data) {
                    writeBibliographicResourcesIntoCarouselHtmlClasses (data, ".item0", ".item1", ".item2");
                },
                error: function (e) {
                    console.log(e);
                }
            });

            $.ajax({
                url: VuFind.path + "/Ajax/Json?method=getAuthorMulti&searcher=Elasticsearch",
                type: "POST",
                data: {"lookfor": gndIds_subject_broaderTerms},
                success: function (data) {
                    //console.log(data);
                    //works with broader subjects
                    writeBibliographicResourceIntoHtmlClass(data, ".sd_worksWithBroaderSubjects");
                },
                error: function (e) {
                    console.log(e);
                }
            });

            $.ajax({
                url: VuFind.path + "/Ajax/Json?method=getAuthorMulti&searcher=Elasticsearch",
                type: "POST",
                data: {"lookfor": gndIds_subject_narrowerTerms},
                success: function (data) {
                    //console.log(data);
                    //works with narrower subjects
                    writeBibliographicResourceIntoHtmlClass(data, ".sd_worksWithNarrowerSubjects");
                },
                error: function (e) {
                    console.log(e);
                }
            });
        },
        error: function (e) {
            console.log(e);
        }
    });
}

// Write contents of person into file via unique Id from Solr --> result-list (Solr)
function writeContentOfPersonsIntoHtml (htmlId, uniqueId) {
    $.ajax({
        url: VuFind.path + "/Ajax/Json?method=getAuthorMulti&searcher=Elasticsearch",
        type: "POST",
        data: {"lookfor": uniqueId},
        success: function (data) {
            var idContributorFromBibRes = getIdsFromPropertyInBibliographicResourcesAsString(data, 'dct:contributor');
            $.ajax({
                url: VuFind.path + "/Ajax/Json?method=getAuthorMulti&searcher=Elasticsearch",
                type: "POST",
                data: {"lookfor": idContributorFromBibRes},
                success: function (data) {
                    var result = getPersonAuthorsNameIconAsString(data);
                    //id must consist of Solr unique Id
                    if (typeof result !== 'undefined') {
                        $(htmlId).html('<br /><strong>Mehr Details zu: </strong>' + result);
                    }
                },
                error: function (e) {
                    console.log(e);
                }
            });
        },
        error: function (e) {
            console.log(e);
        }
    });
}
