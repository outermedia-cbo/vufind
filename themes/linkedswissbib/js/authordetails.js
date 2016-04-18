$(document).ready(function() {

    var uri2name = getURIs();
    replaceInAuthorUris(',',';');
    getAndSetNames();

    function getURIs() {

        var uri2name = {};

        $( ".authoruris" ).each(function() {

            var authoruris = this.innerHTML;

            authoruris = authoruris.replace(/\s+/g, ''); // remove spaces: http://stackoverflow.com/a/5963202

            authoruris = authoruris.split(','); // split entries when meeting a ',' and convert to array of strings: http://stackoverflow.com/a/5269873

            authoruris.forEach( function(authoruri) {

                uri2name[authoruri] = '';
            });
        });

        return uri2name;
    }


    function getAndSetNames() {

        for (var uri in uri2name) { // http://stackoverflow.com/a/921808

            // skip loop if the property is from prototype
            if (!uri2name.hasOwnProperty(uri)) continue;

            var uri = JSON.parse(JSON.stringify(uri)); // deep-copy uri

            var value = uri2name[uri];

            $.ajax({
                method: "GET",
                url: "http://" + window.location.hostname + "/sbrd/Ajax/Json?lookfor=" + uri + "&method=getAuthor&searcher=Elasticsearch"
            })
                .done(function (msg) {

                    var uri = msg.person[0]._source["@id"]; // can't use variable 'uri' from outside directly

                    uri2name[uri] = msg.person[0]._source["foaf:lastName"] + ", " + msg.person[0]._source["foaf:firstName"];

                    replaceInAuthorUris(uri, uri2name[uri]);
                });
        }
    }

    function replaceInAuthorUris(theOld, theNew) {

        $( ".authoruris" ).each(function() {

            this.innerHTML = this.innerHTML.replace(new RegExp($.ui.autocomplete.escapeRegex(theOld), 'g'), theNew); // http://stackoverflow.com/a/13157996

        });
    }

});
