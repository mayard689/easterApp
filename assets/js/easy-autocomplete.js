const options = {
    url: phrase => `/feature/search/ ${phrase}`,
    getValue: 'name',
};

// eslint-disable-next-line no-undef
$('#feature_name')
    .easyAutocomplete(options);

// eslint-disable-next-line no-undef
$('.easy-autocomplete')
    .removeAttr('style');
