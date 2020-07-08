require('easy-autocomplete');

const options = {
    url: phrase => `/feature/search/${phrase}`,
    getValue: 'name',
};


// eslint-disable-next-line no-undef
$('#feature_name')
    .easyAutocomplete(options);

// eslint-disable-next-line no-undef
$('.easy-autocomplete')
    .removeAttr('style');

// eslint-disable-next-line no-undef
$(document, '#feature_name').on('input', () => {
    // eslint-disable-next-line no-undef
    const input = $('#feature_name').val();
});

// eslint-disable-next-line no-undef
$(document, 'li .eac-item').on('click', () => {
    // eslint-disable-next-line no-undef
    const input = $('#feature_name').val();
});
