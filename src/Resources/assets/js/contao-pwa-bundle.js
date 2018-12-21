import HuhContaoPwaButtons from './huh-contao-pwa-buttons';
import PushNotificationSubscription from './push-notification-subscription';

let PwaButtons = new HuhContaoPwaButtons();

document.addEventListener('DOMContentLoaded', function() {
    PwaButtons.onReady();
});