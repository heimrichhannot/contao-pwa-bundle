function PushNotificationSubscription(subscribePath, unsubscribePath)
{
    this.debug = false;
    this.subscribePath = subscribePath;
    this.unsubscribePath = unsubscribePath;

    this.init = function() {
        document.addEventListener('huh_pwa_push_changeSubscriptionState', this.changeSubscriptionStatus.bind(this));
    };

    this.subscribe = function(){
        if (this.debug) {
            console.log('[Push Notification Subscription] Subscribe');
        }
        navigator.serviceWorker.ready.then(async (registration) => {
            let responce = await fetch('./api/notifications/publickey');
            const publicKey = await responce.text();

            return registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array(publicKey),
            }).then((subscription) => {
                if (this.debug) {
                    console.log('[Push Notification Subscription] Subscribed');
                }
                fetch(this.subscribePath, {
                    method: 'post',
                    headers: {
                        'Content-type': 'application/json',
                    },
                    body: JSON.stringify({
                        subscription: subscription,
                    }),
                });
            }).then(() => {
                this.setUnsubscribe();
            }).catch((reason) => {
                document.dispatchEvent(new CustomEvent('huh_pwa_push_subscription_failed', {detail: {
                    reason: reason
                }}));
            });
        });
    };
    this.unsubscribe = function() {
        if (this.debug) {
            console.log('[Push Notification Subscription] Unsubscribe');
        }
        navigator.serviceWorker.ready.then((registration) => {
            return registration.pushManager.getSubscription();
        }).then((subscription) => {
            return subscription.unsubscribe().then(() => {
                if (this.debug) {
                    console.log('[Push Notification Subscription] Unsubscribed', subscription.endpoint);
                }
                return fetch(this.unsubscribePath, {
                    method: 'post',
                    headers: {
                        'Content-type': 'application/json',
                    },
                    body: JSON.stringify({
                        subscription: subscription,
                    }),
                });
            });
        }).then(() => {
            this.setSubscribe();
        }).catch((reason) => {
            document.dispatchEvent(new CustomEvent('huh_pwa_push_unsubscription_failed', {detail: {'reason': reason}}));
        });
    };
    this.setSubscribe = function() {
        if (!this.checkPermission()) return;
        document.dispatchEvent(new Event('huh_pwa_push_isUnsubscribed'));
        if (this.debug) {
            console.log('[Push Notification Subscription] Fired huh_pwa_push_isUnsubscribed');
        }
    };
    this.setUnsubscribe = function() {
        if (!this.checkPermission()) return;
        document.dispatchEvent(new Event('huh_pwa_push_isSubscribed'));
        if (this.debug) {
            console.log('[Push Notification Subscription] Fired huh_pwa_push_isSubscribed"');
        }
    };
    this.checkPermission = function() {
        if (Notification.permission === 'denied') {
            document.dispatchEvent(new Event('huh_pwa_push_permission_denied'));
            if (this.debug) {
                console.log('[Push Notification Subscription] Fired huh_pwa_push_permission_denied');
            }
            return false;
        }
        return true;
    };
    this.changeSubscriptionStatus = function (event)
    {
        if (!this.checkPermission()) return;
        if (event.detail === 'subscribe')
        {
            this.subscribe();
        }
        else if (event.detail === 'unsubscribe') {
            this.unsubscribe();
        }
    };
    this.urlBase64ToUint8Array = (base64String) => {
        var padding = '='.repeat((4 - base64String.length % 4) % 4);
        var base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');

        var rawData = window.atob(base64);
        var outputArray = new Uint8Array(rawData.length);

        for (var i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    };
}