const options = {
    url: function (phrase) {
        return '/feature/search/' + phrase;
    },
    getValue: 'name'
};
$(document)
    .ready(() => {
        $('#feature_name')
            .easyAutocomplete(options);

        $('.easy-autocomplete')
            .removeAttr('style');
    });
