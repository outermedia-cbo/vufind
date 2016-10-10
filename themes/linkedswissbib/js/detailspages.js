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
        result = '';
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
            var url_start = 'https://resources.swissbib.ch/Cover/Show?isn=';
            var url_end = '&size=large';
            var link_cover = url_start + isbn10 + url_end;
        }
    } else {
        var link_cover = fallback;
    }
    return link_cover;
}

//Helper function for carousel
function getBibResLink (array, key) {
    //get ID
    var bibliographicResource_id = array[key]._source['@id'];
    //extract ID to link to the Solr record
    var id_classic = bibliographicResource_id.slice(33);
    var url_start = 'http://' + window.location.hostname +
        '/sbrd/Record/';
    var link_bibRes = url_start + id_classic;
    return link_bibRes;
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
        fallbackItem = '<div class="col-sm-3"><a href="/Record/335357466" class="hover-overlay" style="max-height: 200px;"> <img title="Selfish : poems"src="https://resources.swissbib.ch/Cover/Show?isn=1555977081&size=large" style="max-height: 200px;"> <div class="content"></div></a>';
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
        result = 'no content provided';
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
                var link = 'http://' + window.location.hostname +
                    '/sbrd/Exploration/SubjectDetails?lookfor=' + id + '&type=SubjectById';
                result.push({counts: count, tag: tag, href: link})
            }
        }
    }
    if (!result) {
        result = "no content provided";
    }
    return result;
}

//Helper function thumbnail gallery
//generates gallery for authors including thumbnail, name and icon (knowledge card)
//persons only! no organisations!
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
                    result += '<li><a href="http://' + window.location.hostname +
                    '/sbrd/Exploration/AuthorDetails?lookfor=' + person_id + '&type=AuthorForId"><figure><img class="thumbnail" src=" ' + thumbnail + ' " alt=" ' + name + ' "><figcaption>' + name + ' ' + '</a>';
                    result += '<span class="fa fa-info-circle fa-lg kcopenerAuthor" authorId="' + person_id +'"></span></figcaption></figure></li>';
                }
            }
        }
    }
    if (!result) {
        result = "no content provided";
    }
    return result;
}

//Similar in extractName in authordetails.js!!! --> knowledgeCardAuthor
function getPersonNameAsString(array, key) {
    var  array = array.person[key]._source;
    if (('foaf:lastName' in array) && ('foaf:firstName') in array) {
        return array['foaf:firstName'] + ' ' + array['foaf:lastName'];
    } else if ('foaf:lastName' in array) {
        return array['foaf:lastName'];
    } else if ('foaf:name' in array) {
        return array['foaf:name'];
    } else {
        return 'no content available';
    }
}

// --> knowledgeCardAuthor
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
                    result += '<a href="http://' + window.location.hostname +
                    '/sbrd/Exploration/SubjectDetails?lookfor=' + id + '&type=SubjectById">' + literal + '</a> <span class="fa fa-info-circle fa-lg kcopenerSubject" subjectId="' + id +'"></span>, ';
                    result = result.replace('undefined, ', ' ');
                } else {
                    result += '<a href="http://' + window.location.hostname +
                    '/sbrd/Exploration/SubjectDetails?lookfor=' + id + '&type=SubjectById">' + literal + ', ';
                    result = result.replace('undefined, ', ' ');
                }


            }
            var result = result.substring(0, result.length - 2);
        }
    }
    if (!result) {
        result = "no content provided";
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
                result += '<a href="' + link_bibRes + '"><i class="fa fa-arrow-right"></i> ' + title +'</br>';
            }
        }
    }
    if (!result) {
        result = 'no content provided';
    }
    $(htmlClass).html(result);
}

// Write ((add) literals and other elements into html classes --> authordetails
function writePersonAuthorsNameThumbnailIconIntoHtmlClass (personIdsAsString, htmlClass, person_uniqueId) {
    $.ajax({
        url: "http://" + window.location.hostname +
        "/sbrd/Ajax/Json?method=getAuthorMulti&searcher=Elasticsearch",
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

// Write literals of gnd ids into html Id --> knowledgeCardAuthor, subjectdetails
function writeSubjectNamesIntoHtmlId (gndIdsAsString, htmlId, knowledgeCardStatement) {
    $.ajax({
        url: "http://" + window.location.hostname +
        "/sbrd/Ajax/Json?&method=getSubjectMulti&searcher=Elasticsearch",
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
        url: "http://" + window.location.hostname +
        "/sbrd/Ajax/Json?&method=getSubjectMulti&searcher=Elasticsearch",
        type: "POST",
        data: {"lookfor": gndIdsAsString},
        success: function (data) {
            var tags = getTagCloudContentAsArray(data, gndIdsAsString);
            if (tags == "no content provided") {
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
function writeAuthordetailsModuleContentIntoHtml (person_uniqueId) {
    //get IDs from type bibliographicResources: ids of resources, ids contributors of resources, ids subjects of resources
    $.ajax({
        url: "http://" + window.location.hostname +
        "/sbrd/Ajax/Json?method=getAuthorMulti&searcher=Elasticsearch",
        type: "POST",
        data: {"lookfor": person_uniqueId},
        success: function (data) {
            var idBibRes = getIdsFromPropertyInBibliographicResourcesAsString(data, '@id');
            var idContributorFromBibRes = getIdsFromPropertyInBibliographicResourcesAsString(data, 'dct:contributor');
            var idSubject = getIdsFromPropertyInBibliographicResourcesAsString(data, 'dct:subject');
            writePersonAuthorsNameThumbnailIconIntoHtmlClass (idContributorFromBibRes, ".ad_authorsOfCommonWorks", person_uniqueId);
            writeSubjectNamesIntoTagCloud(idSubject, "#ad_tagCloudSubjectsOfWorks");

            $.ajax({
                url: "http://" + window.location.hostname +
                "/sbrd/Ajax/Json?method=getAuthorMulti&searcher=Elasticsearch",
                type: "POST",
                data: {"lookfor": idSubject},
                success: function (data) {
                    var idsContributorFromBibRes = getIdsFromPropertyInBibliographicResourcesAsString(data, 'dct:contributor');
                    writePersonAuthorsNameThumbnailIconIntoHtmlClass (idsContributorFromBibRes, ".ad_authorsOfWorksWithSimilarSubjects", person_uniqueId);
                    writeBibliographicResourceIntoHtmlClass(data, ".ad_worksWithSimilarSubjects");
                },
                error: function (e) {
                    console.log(e);
                }
            });

            $.ajax({
                url: "http://" + window.location.hostname +
                "/sbrd/Ajax/Json?method=getAuthorMulti&searcher=Elasticsearch",
                type: "POST",
                data: {"lookfor": idContributorFromBibRes},
                success: function (data) {
                    //works of authors of common works
                    writeBibliographicResourcesIntoCarouselHtmlClasses (data, ".ad_item0", ".ad_item1", ".ad_item2");
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
function writeSubjectdetailsModuleContentIntoHtml (subject_uniqueId) {
    //get IDs from type bibliographicResources: ids of resources, ids contributors of resources, ids subjects of resources
    $.ajax({
        url: "http://" + window.location.hostname +
        "/sbrd/Ajax/Json?method=getAuthorMulti&searcher=Elasticsearch",
        type: "POST",
        data: {"lookfor": subject_uniqueId},
        success: function (data) {
            var idBibRes = getIdsFromPropertyInBibliographicResourcesAsString(data, '@id');
            var idContributorFromBibRes = getIdsFromPropertyInBibliographicResourcesAsString(data, 'dct:contributor');
            var idSubject = getIdsFromPropertyInBibliographicResourcesAsString(data, 'dct:subject');
            writePersonAuthorsNameThumbnailIconIntoHtmlClass (idContributorFromBibRes, ".sd_authorsOfWorksWithSubject", subject_uniqueId);

            $.ajax({
                url: "http://" + window.location.hostname +
                "/sbrd/Ajax/Json?method=getAuthorMulti&searcher=Elasticsearch",
                type: "POST",
                data: {"lookfor": idSubject},
                success: function (data) {
                    writeBibliographicResourcesIntoCarouselHtmlClasses (data, ".sd_item0", ".sd_item1", ".sd_item2");
                },
                error: function (e) {
                    console.log(e);
                }
            });

            $.ajax({
                url: "http://" + window.location.hostname +
                "/sbrd/Ajax/Json?method=getAuthorMulti&searcher=Elasticsearch",
                type: "POST",
                data: {"lookfor": idContributorFromBibRes},
                success: function (data) {
                    console.log(data);
                    //works of co-authors of work with main subject
                    writeBibliographicResourceIntoHtmlClass(data, ".sd_worksOfAuthorsWithWorksWithSubject");
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