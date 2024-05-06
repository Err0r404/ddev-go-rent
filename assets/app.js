// Import libraries
import * as bootstrap from "bootstrap";
import $ from 'jquery';
import htmx from 'htmx.org';

// Import custom scripts
import './_toasts.js';

// Import styles
import 'bootstrap/dist/css/bootstrap.min.css';
import 'ninjabootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.min.css';

// Import custom styles
import './styles/app.css';

// Access HTMX in the global scope
window.htmx = htmx;

// Enable tooltips and popovers
let tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
let tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

let popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
let popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))

htmx.on('htmx:afterSwap', function (event) {
    // Hide all tooltips and popovers
    tooltipList.forEach(tooltip => tooltip.hide());
    popoverList.forEach(popover => popover.hide());

    // Re-enable tooltips and popovers
    tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

    popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
    popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))
});
