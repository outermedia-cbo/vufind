function getSubjectsPreferredName (data) {
    var array = data.DEFAULT;
    if (typeof array !== 'undefined') {
        if ($.isArray(array)) {
            var result = "";
            for (var key in array) {
                if (typeof array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredName'] != 'undefined') {
                    var literal = array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredName'][0]['@value'];
                } else if (typeof array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForTheConferenceOrEvent'] != 'undefined') {
                    var literal = array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForTheConferenceOrEvent'][0]['@value'];
                } else if (typeof array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForTheCorporateBody'] != "undefined") {
                    var literal = array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForTheCorporateBody'][0]['@value'];
                } else if (typeof array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForTheFamily'] != "undefined") {
                    var literal = array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForTheFamily'][0]['@value'];
                } else if (typeof array[key]._source['http://d-nb.info/standards/elementset/gnd#preferredNameEntityForThePerson'] != "undefined") {
                    var literal = array[key]._source['http://d-nb.info/standards/elementset/gnd#preferredNameEntityForThePerson'][0]['@value'];
                } else if (typeof array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForThePerson'] != "undefined") {
                    var literal = array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForThePerson'][0]['@value'];
                } else if (typeof array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForThePlaceOrGeographicName'] != "undefined") {
                    var literal = array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForThePlaceOrGeographicName'][0]['@value'];
                } else if (typeof array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForTheSubjectHeading'] != "undefined") {
                    var literal = array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForTheSubjectHeading'][0]['@value'];
                } else if (typeof array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForTheWork'] != "undefined") {
                    var literal = array[key]._source['http://d-nb_info/standards/elementset/gnd#preferredNameForTheWork'][0]['@value'];
                } else {
                    var literal = '';
                }
                result += literal + ", ";
                result = result.replace('undefined, ', ' ');
            }
            var result = result.substring(0, result.length - 2);

        }
    } else {
        var result = "no content provided";
    }

    if (!result) {
        result = "no content provided";
    } else {
        result;
    }
    return result;
}

function writeSubjectNamesIntoHtmlId (gndIdsAsString, htmlId) {
    $.ajax({
        url: "http://" + window.location.hostname +
        "/sbrd/Ajax/Json?&method=getSubjectMulti&searcher=Elasticsearch",
        type: "POST",
        data: {"lookfor": gndIdsAsString},
        success: function (result) {
            var preferredNamesAsString = getSubjectsPreferredName(result);
            //console.log(kc_subject);
            //$('#subject_relatedTerms').text(subject_relatedTerms);
            $(htmlId).text(preferredNamesAsString);
        },
        error: function (e) {
            console.log(e);
        }
    })
}