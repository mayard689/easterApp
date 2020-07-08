const options = {
    url: (phrase) => {
        return `/feature/search/ ${phrase}`;
    },
    getValue: "name"
};

$('#feature_name')
    .easyAutocomplete(options);

$('.easy-autocomplete')
    .removeAttr('style');
