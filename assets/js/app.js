/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../scss/app.scss');
// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
const $ = require('jquery');
// load the JS bootstrap part - note that bootstrap doesn't export anything
require('bootstrap');

const bootstrapToolTip = () => {
    $('[data-toggle="tooltip"]')
        .tooltip();
    $('[data-toggle="popover"]')
        .popover();
};

$(bootstrapToolTip());

// import fontawesome
require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.js');
