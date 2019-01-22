class PushNotificationSubscription
{
    constructor(subscribePath = '', unsubscribePath = '') {
        this.isInit = false;
        this.debug = false;
        this.subscribePath = subscribePath;
        this.unsubscribePath = unsubscribePath;
    }

    onLoaded() {
        if (!this.isInit) {
            document.addEventListener('huh_pwa_push_changeSubscriptionState', this.changeSubscriptionStatus.bind(this));
            this.isInit = true;
        }
    }

    subscribe() {
        if (this.debug) console.log('[Push Notification Subscription] Trying to Subscribe');
        navigator.serviceWorker.ready.then(async (registration) => {
            let responce = await fetch('./api/notifications/publickey');
            const publicKey = await responce.text();

            return registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: PushNotificationSubscription.urlBase64ToUint8Array(publicKey),
            }).then((subscription) => {
                if (this.debug) console.log('[Push Notification Subscription] Successful Subscribed', subscription.endpoint);
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
                this.setIsSubscribed();
            }).catch((reason) => {
                document.dispatchEvent(new CustomEvent('huh_pwa_push_subscription_failed', {detail: {
                        reason: reason
                    }}));
            });
        });
    };

    unsubscribe() {
        if (this.debug) console.log('[Push Notification Subscription] Trying to unsubscribe');
        navigator.serviceWorker.ready.then((registration) => {
            return registration.pushManager.getSubscription();
        }).then((subscription) => {
            return subscription.unsubscribe().then(() => {
                if (this.debug) console.log('[Push Notification Subscription] Successful Unsubscribed', subscription.endpoint);
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
            this.setIsUnsubscribed();
        }).catch((reason) => {
            document.dispatchEvent(new CustomEvent('huh_pwa_push_unsubscription_failed', {detail: {'reason': reason}}));
        });
    };
    setIsUnsubscribed() {
        if (!this.checkPermission()) return;
        document.dispatchEvent(new Event('huh_pwa_push_isUnsubscribed'));
        if (this.debug) console.log('[Push Notification Subscription] Fired huh_pwa_push_isUnsubscribed');
    };
    setIsSubscribed() {
        if (!this.checkPermission()) return;
        document.dispatchEvent(new Event('huh_pwa_push_isSubscribed'));
        if (this.debug) console.log('[Push Notification Subscription] Fired huh_pwa_push_isSubscribed"');
    };
    checkPermission() {
        if (Notification.permission === 'denied') {
            document.dispatchEvent(new Event('huh_pwa_push_permission_denied'));
            if (this.debug) console.log('[Push Notification Subscription] Fired huh_pwa_push_permission_denied');
            return false;
        }
        return true;
    };

    changeSubscriptionStatus (event)
    {
        if (this.debug) console.log("[Push Notification Subscription] CHANGE Subscription state");
        if (!this.checkPermission()) return;
        if (event.detail === 'subscribe')
        {
            this.subscribe();
        }
        else if (event.detail === 'unsubscribe') {
            this.unsubscribe();
        }
    };

    static urlBase64ToUint8Array(base64String)
    {
        let padding = '='.repeat((4 - base64String.length % 4) % 4);
        let base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');

        let rawData = window.atob(base64);
        let outputArray = new Uint8Array(rawData.length);

        for (var i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    };
}

export default PushNotificationSubscription