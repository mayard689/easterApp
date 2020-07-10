require('easy-autocomplete');

const options = {
    url: phrase => `/feature/search/${phrase}`,
    getValue: 'name',
};


// eslint-disable-next-line no-undef
$('#specific_feature_name')
    .easyAutocomplete(options);

// eslint-disable-next-line no-undef
$('.easy-autocomplete')
    .removeAttr('style');

// eslint-disable-next-line no-undef
$(document, '#specific_feature_name')
    .on('input', () => {
        // eslint-disable-next-line no-undef
        const input = $('#specific_feature_name')
            .val();
        // eslint-disable-next-line no-restricted-globals
        if (input.length < 3) {
            // eslint-disable-next-line no-use-before-define
            clearInput();
        }
        // eslint-disable-next-line no-use-before-define
        fetchFeature(input);
    });

function fetchFeature(input) {
    // eslint-disable-next-line no-console
    fetch(`/feature/search/${input}`)
        .then(response => response.json())
        // eslint-disable-next-line no-console
        .then((features) => {
            // eslint-disable-next-line no-undef
            if (!jQuery.isEmptyObject(features)) {
                // eslint-disable-next-line no-undef
                $('#specific_feature_day')
                    .val(features[0].day);
                // eslint-disable-next-line no-undef
                $('#specific_feature_description')
                    .val(features[0].description);
                // eslint-disable-next-line no-undef
                $('#specific_feature_category')
                    .val(features[0].id);
            }
        });
}

function clearInput() {
    // eslint-disable-next-line no-undef
    $('#specific_feature_day')
        .val('');
    // eslint-disable-next-line no-undef
    $('#specific_feature_description')
        .val('');
    // eslint-disable-next-line no-undef
    $('#specific_feature_category')
        .val('');
}
