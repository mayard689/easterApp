require('easy-autocomplete');
const $ = require('jquery');

const options = {
    url: phrase => `/feature/search/${phrase}`,
    getValue: 'name',
};

$('#specific_feature_name')
    .easyAutocomplete(options);

function clearInput() {
    $('#specific_feature_day')
        .val('');
    $('#specific_feature_description')
        .val('');
    $('#specific_feature_category')
        .val('');
}

$('#specific_feature_name')
    .on('change', () => {
        const input = $('#specific_feature_name')
            .val();
        fetch(`/feature/search/${input}`)
            .then(response => response.json())
            .then((features) => {
                // eslint-disable-next-line no-undef
                if (!jQuery.isEmptyObject(features)) {
                    $('#specific_feature_day')
                        .val(features[0].day);
                    $('#specific_feature_description')
                        .val(features[0].description);
                    $('#specific_feature_category')
                        .val(features[0].id);
                } else {
                    clearInput();
                }
            });
    });

$('.easy-autocomplete')
    .removeAttr('style');
