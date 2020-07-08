let options = {
    url: function (phrase) {
        return '/feature/search/' + phrase;
    },
    getValue: 'name'
};

$('#feature_name')
    .easyAutocomplete(options);

$('.easy-autocomplete')
    .removeAttr('style');
