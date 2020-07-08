require('easy-autocomplete');

let options = {
    url: phrase => '/feature/search/' + phrase,
    getValue: 'name',
};


// eslint-disable-next-line no-undef
$('#feature_name')
    .easyAutocomplete(options);

// eslint-disable-next-line no-undef
$('.easy-autocomplete')
    .removeAttr('style');

$(document, '#feature_name').on('input', function () {
    let input = $('#feature_name').val();
})

$(document, 'li .eac-item').on('click', function () {
    let input = $('#feature_name').val();
})
