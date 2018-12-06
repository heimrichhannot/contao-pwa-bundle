function PushNotificationSubscription(subscribePath, unsubscribePath) {
    this.debug = false;
    this.subscribePath = subscribePath;
    this.unsubscribePath = unsubscribePath;
    this.buttons = [];

    this.collectElementsToUpdate = () => {
        this.buttons = document.querySelectorAll('.huhPwaWebSubscription');
    };
    this.subscribe = () => {
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
    this.unsubscribe = () => {
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
    this.setSubscribe = () => {
        if (!this.checkPermission()) {
            return;
        }
        document.dispatchEvent(new Event('huh_pwa_push_isUnsubscribed'));
        if (this.debug) {
            console.log('[Push Notification Subscription] Update Button to "Subscribe"');
        }
        this.buttons.forEach((button) => {
            button.removeAttribute('disabled');
            button.querySelector('.label').textContent = HuhPwaTranslator.pushnotifications.subscribe;
            button.classList.add('unsubscribed');
            button.classList.remove('subscribed');
            button.classList.remove('blocked');
            button.addEventListener('click', this.subscribe);
        });
    };
    this.setUnsubscribe = () => {
        if (!this.checkPermission()) {
            return;
        }
        document.dispatchEvent(new Event('huh_pwa_push_isSubscribed'));
        if (this.debug) {
            console.log('[Push Notification Subscription] Update Button to "Unsubscribe"');
        }
        this.buttons.forEach((button) => {
            button.removeAttribute('disabled');
            button.querySelector('.label').textContent = HuhPwaTranslator.pushnotifications.unsubscribe;
            button.classList.add('subscribed');
            button.classList.remove('unsubscribed');
            button.classList.remove('blocked');
            button.addEventListener('click', this.unsubscribe);
        });
    };
    this.checkPermission = () => {
        if (Notification.permission === 'denied') {
            document.dispatchEvent(new Event('huh_pwa_push_permission_denied'));
            if (this.debug) {
                console.log('[Push Notification Subscription] Permission denied');
            }
            this.buttons.forEach((button) => {
                button.querySelector('.label').textContent = HuhPwaTranslator.pushnotifications.blocked;
                button.classList.add('blocked');
                button.classList.remove('unsubscribed');
                button.classList.remove('subscribed');
                button.disabled = true;
            });
            return false;
        }
        return true;
    };
    this.hide = () => {
        if (this.debug) {
            console.log('[Push Notification Subscription] Hide Subscription Elements');
        }
        this.buttons.forEach((button) => {
            button.classList.add('hidden');
            button.setAttribute('aria-hidden','true');
        });
    };
    this.show = () => {
        if (this.debug) {
            console.log('[Push Notification Subscription] Show Subscription Elements');
        }
        this.buttons.forEach((button) => {
            button.removeAttribute('aria-hidden');
            button.classList.remove('hidden');
        });
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