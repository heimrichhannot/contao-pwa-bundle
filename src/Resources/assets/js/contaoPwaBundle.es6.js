HuhContaoPwaButtons = require('./HuhContaoPwaButtons.es6');

PwaButtons = new HuhContaoPwaButtons();

document.addEventListener('DOMContentLoaded', function() {
    PwaButtons.onReady();
});