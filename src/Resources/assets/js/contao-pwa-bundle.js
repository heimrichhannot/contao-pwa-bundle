require ('@babel/polyfill');
import PushSubscriptionButtons from './PushSubscriptionButtons';
import PushNotificationSubscription from './PushNotificationSubscription';

let PwaButtons = new PushSubscriptionButtons();
let PushSubscription = new PushNotificationSubscription(
    HuhContaoPwaBundle.pushNotifications.subscribePath,
    HuhContaoPwaBundle.pushNotifications.unsubscribePath
);
let debug = HuhContaoPwaBundle.debug;

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
        })
    });

    if (HuhContaoPwaBundle.pushNotifications.support && ('PushManager' in window)) {
        if (debug) console.log("[SW Registration] Client supports push notifications");
        navigator.serviceWorker.ready
        .then(function(registration) {
            if (debug)console.log("[SW Registration] Got service worker registration for push subscription");
            return registration.pushManager.getSubscription();
        }).then(function(subscription) {
            if (subscription)
            {
                if (debug)console.log('[SW Registration] Already subscribed');
                PushSubscription.setIsSubscribed();
            }
            else
            {
                if (debug)console.log('[SW Registration] Not subscribed');
                PushSubscription.setIsUnsubscribed();
            }
        });
    }
    else {
        document.dispatchEvent(new Event('huh_pwa_push_not_supported'));
        if (debug)console.log('[SW Registration] Browser don\'t support push. Hide subscription button.');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    PushSubscription.onLoaded();
    PwaButtons.onLoaded();
});