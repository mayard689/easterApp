let inputSearch = $('#feature_name');
let options = {
    url: function (phrase) {
        return '/feature/search/' + phrase;
    },

    getValue: 'name'
};

inputSearch
    .easyAutocomplete(options);

$('.easy-autocomplete')
    .removeAttr('style');
