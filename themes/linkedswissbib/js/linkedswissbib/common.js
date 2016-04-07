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
                                datums.push({val:json.data[i]});
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

            baseurl = 'http://localhost/sbrd/';
            authorDetail = 'Exploration/AuthorDetails?lookfor=' + datum['val'][0] + '&type=AuthorForId';

            window.location.href = baseurl + authorDetail;
        });
});