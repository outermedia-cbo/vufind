$(document).ready(function() {

    // Search autocomplete
    $('.twitterautocomplete').typeahead(
        {
            highlight: true,
            minLength: 3
        }, {
            //Display bibliographicResources in autocomplete
            templates: {
                header: '<h4 class="autocomplete-header">' + VuFind.translate('tab.swissbib') + '</h4>'
            },
            displayKey: function(data) {
                return data['val'][2];
            },
            source: function(query, cb) {
                var searcher = extractClassParams('.twitterautocomplete');
                //todo we have to throw away this stupid /sbrd and the global path variable in
                //layout.phtml line 25 (theme linked swissbib
                $.ajax({

                    url: VuFind.path + '/AJAX/JSON',
                    data: {
                        q:query,
                        method:'getACSuggestions',
                        searcher:'Solr',
                        type:'Title'
                    },
                    dataType:'json',
                    success: function(json) {
                        if (json.status == 'OK' && json.data.length > 0) {
                            var datums = [];
                            for (var i=0;i< 5;i++) {
                                if (json.data[i][1] == 'BibRes') {
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
        } , {
            templates: {
                //Display persons in autocomplete
                header: '<h4 class="autocomplete-header">' + VuFind.translate('Author') + '</h4>'
            },
            displayKey: function(data){
                    return data['val'][2];
            },
            source: function(query, cb) {
                var searcher = extractClassParams('.twitterautocomplete');
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
                            for (var i=0;i < json.data.length;i++) {
                                if (json.data[i][1] == 'person') {
                                    if (datums.length < 5 ) {
                                        datums.push({val: json.data[i]});
                                    }
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
            //Display subjects (GND) in autocomplete
            templates: {
            header: '<h4 class="autocomplete-header">' + VuFind.translate('Topics') + '</h4>'
            },
            displayKey: function(data) {
                return data['val'][2];
            },
            source: function(query, cb) {
                var searcher = extractClassParams('.twitterautocomplete');
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
                            for (var i=0;i< json.data.length ;i++) {
                                if (json.data[i][1] == 'DEFAULT') {
                                    if (datums.length < 5 ) {
                                        datums.push({val: json.data[i]});
                                    }
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
            // Build link to landing page: details pages author and subject and full record of bibliographic resources
            var postfix;

            if (datum['val'][1] == 'person') {
                postfix = 'Exploration/AuthorDetails?lookfor=' + datum['val'][0] + '&type=AuthorForId';
            } else if (datum['val'][1] == 'DEFAULT') {
                postfix = 'Exploration/SubjectDetails?lookfor=' + datum['val'][0] + '&type=SubjectById';
            } else if (datum['val'][1] == 'BibRes') {
                postfix = 'Record/' + datum['val'][0];
            }

            window.location.href = VuFind.path + "/" + postfix;
        });
});
