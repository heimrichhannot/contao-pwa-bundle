function PushNotificationSubscription ()
{
    var _this = this;
    var debug = false;

    this.collectElementsToUpdate = function() {
        this.buttons = document.querySelectorAll('.huhPwaWebSubscription');
    };
    this.subscribe = function () {
        navigator.serviceWorker.ready
        .then(async function(registration) {
            let responce = await fetch('./api/notifications/publickey');
            const publicKey = await responce.text();

            return registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: _this.urlBase64ToUint8Array(publicKey)
            }).then(function(subscription) {
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
            }).then(_this.setUnsubscribe);
        });
    };
    this.unsubscribe = function() {
        navigator.serviceWorker.ready
        .then(function(registration) {
            return registration.pushManager.getSubscription();
        }).then(function(subscription) {
            return subscription.unsubscribe()
            .then(function() {
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
        }).then(_this.setSubscribe);
    };
    this.setSubscribe = function() {
        if (!_this.checkPermission())
        {
            return;
        }
        console.log('[Push Notification Subscription] Update Button to "Subscribe"');
        _this.buttons.forEach(function(button) {
            button.removeAttribute('disabled');
            button.textContent = "Subscribe";
            button.addEventListener('click', _this.subscribe);
        });
    };
    this.setUnsubscribe = function() {
        if (!_this.checkPermission())
        {
            return;
        }
        console.log('[Push Notification Subscription] Update Button to "Unsubscribe"');
        _this.buttons.forEach(function(button) {
            button.removeAttribute('disabled');
            button.textContent = "Unsubscribe";
            button.addEventListener('click', _this.unsubscribe);
        })
    };
    this.checkPermission = function() {
        if (Notification.permission === 'denied')
        {
            console.log('[Push Notification Subscription] Permission denied');
            _this.buttons.forEach(function(button) {
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
        _this.buttons.forEach(function(button) {
            button.classList.add('hidden');
        })
    };
    this.show = function() {
        if (true === _this.debug)
        {
            console.log('[Push Notification Subscription] Show Subscription Elements');
        }
        _this.buttons.forEach(function(button) {
            if (botton.classList.contains('hidden'))
            {
                button.classList.remove('hidden');
            }
        })
    };
    this.urlBase64ToUint8Array = function(base64String) {
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