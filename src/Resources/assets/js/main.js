if (!('serviceWorker' in navigator)) {
    // Service Worker isn't supported on this browser, disable or hide UI.
}
else if (!('PushManager' in window)) {
    // Push isn't supported on this browser, disable or hide UI.
} else {
    console.log("[Subscription] Register service worker");
    navigator.serviceWorker.register('sw_push.js', {
        scope: '/'
    });

    navigator.serviceWorker.ready
    .then(function(registration) {
        console.log("[Subscription] Service worker registered");
        return registration.pushManager.getSubscription();
    }).then(function(subscription) {
        if (subscription)
        {
            console.log('[Subscription] Already subscribed');
            PushNotificationSubscription.setUnsubscribe();
        }
        else
        {
            console.log('[Subscription] Not subscribed');
            PushNotificationSubscription.setSubscribe();
        }
    });
}

function urlBase64ToUint8Array(base64String) {
    var padding = '='.repeat((4 - base64String.length % 4) % 4);
    var base64 = (base64String + padding)
    .replace(/\-/g, '+')
    .replace(/_/g, '/');

    var rawData = window.atob(base64);
    var outputArray = new Uint8Array(rawData.length);

    for (var i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}


document.addEventListener('DOMContentLoaded', function() {
    PushNotificationSubscription.collectElementsToUpdate();
});

