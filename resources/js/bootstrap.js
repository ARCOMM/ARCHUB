/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

// window._ = require('lodash');
window.$ = window.jQuery = require('jquery');
window.select2 = require('select2');
// window.Tether = require('tether');
// window.Vue = require('vue');
// window.axios = require('axios');

// require('jquery-ui-bundle');
import Popper from 'popper.js';
window.Popper = Popper;

require('bootstrap-material-design');
require('jquery.waitforimages');
require('dropzone/dist/dropzone-amd-module');
require('magnific-popup');
window.showdown = require('showdown');

window.collect = require('collect.js');
window.Tribute = require('tributejs');

// window.axios.defaults.headers.common = {
//     'X-CSRF-TOKEN': window.Laravel.csrfToken,
//     'X-Requested-With': 'XMLHttpRequest'
// };
