export default class PushNotificationSubscription {
    #connected = false;

    /**
     *
     * @param {HuhPwa} pwa
     */
    constructor(pwa) {
        this.pwa = pwa;
        this.subscribePath = pwa.config.pushNotifications.subscribePath;
        this.unsubscribePath = pwa.config.pushNotifications.unsubscribePath;
    }

    _connected() {
        if (!this.#connected) {
            document.addEventListener('huh_pwa_push_changeSubscriptionState', this.changeSubscriptionStatus.bind(this));
            this.#connected = true;
        }
    }

    subscribe() {
        this.pwa.debugLog('[Push Notification Subscription] Trying to Subscribe');

        navigator.serviceWorker.ready.then((registration) => {
            fetch('/_huh_pwa/vapid.pub')
                .then((response) => {
                    return response.text();
                })
                .then((publicKey) => {
                    return registration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: PushNotificationSubscription.urlBase64ToUint8Array(publicKey),
                    });
                })
                .then((subscription) => {
                    this.pwa.debugLog('[Push Notification Subscription] Successful Subscribed', subscription.endpoint);

                    return fetch(this.subscribePath, {
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
                    document.dispatchEvent(new CustomEvent('huh_pwa_push_subscription_failed', {
                        detail: { reason: reason }
                    }));
                });
        });
    }

    unsubscribe() {
        this.pwa.debugLog('[Push Notification Subscription] Trying to unsubscribe');

        navigator.serviceWorker.ready.then((registration) => {
            return registration.pushManager.getSubscription();
        }).then((subscription) => {
            return subscription.unsubscribe().then(() => {
                this.pwa.debugLog('[Push Notification Subscription] Successful Unsubscribed', subscription.endpoint);

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
            document.dispatchEvent(new CustomEvent('huh_pwa_push_unsubscription_failed', {
                detail: {'reason': reason}
            }));
        });
    }
    setIsUnsubscribed(context = null) {
        if (!this.checkPermission()) return;

        document.dispatchEvent(new CustomEvent('huh_pwa_push_isUnsubscribed',{
            detail: { context: context }
        }));

        this.pwa.debugLog('[Push Notification Subscription] Fired huh_pwa_push_isUnsubscribed');
    }

    setIsSubscribed() {
        if (!this.checkPermission()) return;
        document.dispatchEvent(new Event('huh_pwa_push_isSubscribed'));
        this.pwa.debugLog('[Push Notification Subscription] Fired huh_pwa_push_isSubscribed"');
    }

    checkPermission() {
        if (Notification.permission === 'denied') {
            document.dispatchEvent(new Event('huh_pwa_push_permission_denied'));
            this.pwa.debugLog('[Push Notification Subscription] Fired huh_pwa_push_permission_denied');
            return false;
        }
        return true;
    }

    changeSubscriptionStatus(event) {
        this.pwa.debugLog("[Push Notification Subscription] CHANGE Subscription state");

        if (!this.checkPermission()) return;

        if (event.detail === 'subscribe') {
            this.subscribe();
        } else if (event.detail === 'unsubscribe') {
            this.unsubscribe();
        }
    }

    static urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        let outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }

        return outputArray;
    }
}