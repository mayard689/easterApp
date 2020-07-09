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
$(document, '#feature_name')
    .on('input', () => {
        // eslint-disable-next-line no-undef
        const input = $('#feature_name')
            .val();
        if (input.length > 2) {
            // eslint-disable-next-line no-use-before-define
            fetchFeature(input);
        } else {
            // eslint-disable-next-line no-use-before-define
            clearInput();
        }
    });

// eslint-disable-next-line no-undef
$(document, 'li .eac-item')
    .on('click', () => {
        // eslint-disable-next-line no-undef
        const input = $('#feature_name')
            .val();
        if (input.length > 2) {
            // eslint-disable-next-line no-use-before-define
            fetchFeature(input);
        } else {
            // eslint-disable-next-line no-use-before-define
            clearInput();
        }
    });

function fetchFeature(input) {
    // eslint-disable-next-line no-console
    fetch(`/feature/fetch/${input}`)
        .then(response => response.json())
        // eslint-disable-next-line no-console
        .then((features) => {
            // eslint-disable-next-line no-undef
            if (!jQuery.isEmptyObject(features)) {
                // eslint-disable-next-line no-undef
                $('#feature_day')
                    .val(features[0].day);
                // eslint-disable-next-line no-undef
                $('#feature_description')
                    .val(features[0].description);
                // eslint-disable-next-line no-undef
                $('#feature_category')
                    .val(features[0].id);
            } else {
                // eslint-disable-next-line no-use-before-define
                clearInput();
            }
        });
}

function clearInput() {
    // eslint-disable-next-line no-undef
    $('#feature_day')
        .val('');
    // eslint-disable-next-line no-undef
    $('#feature_description')
        .val('');
    // eslint-disable-next-line no-undef
    $('#feature_category')
        .val('');
}
