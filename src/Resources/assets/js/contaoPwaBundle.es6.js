HuhContaoPwaButtons = require('./HuhContaoPwaButtons.es6');
HuhPwaSubscription = require('./PushNotificationSubscription.es6');

PwaButtons = new HuhContaoPwaButtons();

document.addEventListener('DOMContentLoaded', function() {
    PwaButtons.onReady();
});