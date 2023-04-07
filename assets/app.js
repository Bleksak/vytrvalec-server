/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

import { registerReactControllerComponents } from '@symfony/ux-react';

// any CSS you import will output into a single css file (app.css in this case)
const $ = require('jquery');

import './styles/global.scss';
const bootstrap = require('bootstrap');

import './styles/app.css';

let gdpr = document.getElementById("gdpr");
if(gdpr != undefined) {
    let tooltip = new bootstrap.Tooltip(gdpr, {animation: true})
}


// start the Stimulus application
registerReactControllerComponents(require.context('./react/controllers', true, /\.(j|t)sx?$/))
import './bootstrap';
