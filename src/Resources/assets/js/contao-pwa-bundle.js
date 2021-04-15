import PushSubscriptionButtons from './PushSubscriptionButtons';
import PushNotificationSubscription from './PushNotificationSubscription';

let PwaButtons = new PushSubscriptionButtons();
let PushSubscription = new PushNotificationSubscription(
    HuhContaoPwaBundle.pushNotifications.subscribePath,
    HuhContaoPwaBundle.pushNotifications.unsubscribePath
);
let debug = HuhContaoPwaBundle.debug;

window.addEventListener('load', function()
{
    if (!('serviceWorker' in navigator)) {
        if (debug) console.log('[SW Registration] Service Worker not supported');
        document.dispatchEvent(new CustomEvent('huh_pwa_sw_not_supported'));
    }
    else {
        if (debug) console.log("[SW Registration] Register service worker");

        navigator.serviceWorker.register(HuhContaoPwaBundle.serviceWorker.path, {
            scope: HuhContaoPwaBundle.serviceWorker.scope
        }).then(function(registration) {
            registration.addEventListener('updatefound', function() {
                if (debug) console.log("[SW Registration] New service worker found for scope " + registration.scope);
            });
            initializePush();
        });
    }
});

function initializePush()
{
    if (debug) console.log("[SW Registration] Initialize Push");
    if (!HuhContaoPwaBundle.pushNotifications.support)
    {
        if (debug) console.log("[SW Registration] Push notifications not activated");
        return;
    }

    PushSubscription.checkPermission();

    if (!('PushManager' in window))
    {
        document.dispatchEvent(new Event('huh_pwa_push_not_supported'));
        if (debug) console.log('[SW Registration] Browser don\'t support push. Hide subscription button.');
    }

    navigator.serviceWorker.ready.then(function(serviceWorkerRegistration)
    {
        if (debug) console.log("[SW Registration] Got service worker registration");
        serviceWorkerRegistration.pushManager.getSubscription()
        .then(function(subscription) {
            if (subscription)
            {
                if (debug)console.log('[SW Registration] Already subscribed');
                PushSubscription.setIsSubscribed();
            }
            else
            {
                if (debug)console.log('[SW Registration] Not subscribed');
                PushSubscription.setIsUnsubscribed('init');
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    PushSubscription.onLoaded();
    PwaButtons.onLoaded();
});