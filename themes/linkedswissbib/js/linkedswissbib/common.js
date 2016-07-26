/**
 * Created by thomas on 04.04.16.
 */


$(document).ready(function() {

    // Search autocomplete
    $('.autocomplete').typeahead(
        {
            highlight: true,
            minLength: 3
        }, {
            templates: {
                header: "Autoren"
            },
            displayKey: function(data){
                    return data['val'][2];
            },
            source: function(query, cb) {
                var searcher = extractClassParams('.autocomplete');
                $.ajax({
                    url: path + '/AJAX/JSON',
                    data: {
                        q:query,
                        method:'getACSuggestions',
                        searcher:searcher['searcher'],
                        type:$('#searchForm_type').val()
                    },
                    dataType:'json',
                    success: function(json) {
                        if (json.status == 'OK' && json.data.length > 0) {
                            var datums = [];
                            for (var i=0;i<json.data.length;i++) {
                                if (json.data[i][1] == 'person') {
                                    datums.push({val:json.data[i]});
                                }
                            }
                            cb(datums);
                        } else {
                            cb([]);
                        }
                    }
                });
            }
        }, {
            templates: {
                header: "Themen"
            },
            displayKey: function(data) {
                return data['val'][2];
            },
            source: function(query, cb) {
                var searcher = extractClassParams('.autocomplete');
                $.ajax({
                    url: path + '/AJAX/JSON',
                    data: {
                        q:query,
                        method:'getACSuggestions',
                        searcher:searcher['searcher'],
                        type:$('#searchForm_type').val()
                    },
                    dataType:'json',
                    success: function(json) {
                        if (json.status == 'OK' && json.data.length > 0) {
                            var datums = [];
                            for (var i=0;i<json.data.length;i++) {
                                if (json.data[i][1] == 'DEFAULT') {
                                    datums.push({val: json.data[i]});
                                }
                            }
                            cb(datums);
                        } else {
                            cb([]);
                        }
                    }
                });
            }
        }
    ).bind('typeahead:selected', function(obj, datum, name) {

            var baseurl = 'http://' + window.location.hostname + '/sbrd/';

            var postfix;

            if (datum['val'][1] == 'person') {
                postfix = 'Exploration/AuthorDetails?lookfor=' + datum['val'][0] + '&type=AuthorForId';
            } else {
                postfix = 'Exploration/SubjectDetails?lookfor=' + datum['val'][0] + '&type=SubjectById';
            }

            window.location.href = baseurl + postfix;
        });
});