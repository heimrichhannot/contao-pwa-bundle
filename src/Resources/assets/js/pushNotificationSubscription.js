function PushNotificationSubscription ()
{
    this.debug = false;

    this.collectElementsToUpdate = function() {
        this.buttons = document.querySelectorAll('.huhPwaWebSubscription');
    };
    this.subscribe = function () {
        navigator.serviceWorker.ready
        .then(async (registration) => {
            let responce = await fetch('./api/notifications/publickey');
            const publicKey = await responce.text();

            return registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(publicKey)
            }).then((subscription) => {
                console.log("[Push Notification Subscription] Subscribed");
                fetch('/api/notifications/subscribe', {
                    method: 'post',
                    headers: {
                        'Content-type': 'application/json'
                    },
                    body: JSON.stringify({
                        subscription: subscription
                    }),
                });
            }).then(this.setUnsubscribe);
        });
    };
    this.unsubscribe = function() {
        navigator.serviceWorker.ready
        .then((registration) => {
            return registration.pushManager.getSubscription();
        }).then((subscription) => {
            return subscription.unsubscribe()
            .then(() => {
                console.log('[Push Notification Subscription] Unsubscribed', subscription.endpoint);
                return fetch('/api/notifications/unsubscribe', {
                    method: 'post',
                    headers: {
                        'Content-type': 'application/json'
                    },
                    body: JSON.stringify({
                        subscription: subscription
                    })
                });
            });
        }).then(this.setSubscribe);
    };
    this.setSubscribe = function() {
        if (!this.checkPermission())
        {
            return;
        }
        console.log("[Push Notification Subscription] Set Subscribe");
        this.buttons.forEach((button) => {
            button.removeAttribute('disabled');
            button.textContent = "Subscribe";
            button.addEventListener('click', this.subscribe);
        });
    };
    this.setUnsubscribe = function() {
        if (!this.checkPermission())
        {
            return;
        }
        console.log("[Push Notification Subscription] Set Unsubscribe");
        this.buttons.forEach((button) => {
            button.removeAttribute('disabled');
            button.textContent = "Unsubscribe";
            button.addEventListener('click', this.unsubscribe);
        })
    };
    this.checkPermission = function() {
        if (Notification.permission === 'denied')
        {
            console.log('[Push Notification Subscription] Permission denied');
            this.buttons.forEach((button) => {
                button.textContent = "Blocked";
                button.disabled = true;
            });
            return false;
        }
        return true;
    };
    this.hide = function() {
        if (true === this.debug)
        {
            console.log('[Push Notification Subscription] Hide Subscription Elements');
        }
        this.buttons.forEach((button) => {
            button.classList.add('hidden');
        })
    };
    this.show = function() {
        if (true === this.debug)
        {
            console.log('[Push Notification Subscription] Show Subscription Elements');
        }
        this.buttons.forEach((button) => {
            if (button.classList.contains('hidden'))
            {
                button.classList.remove('hidden');
            }
        })
    };
}