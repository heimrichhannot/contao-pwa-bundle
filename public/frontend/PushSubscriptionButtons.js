export default class PushSubscriptionButtons {
    #connected = false;

    /**
     * @param {HuhPwa} pwa - The PWA instance to which the buttons belong.
     */
    constructor(pwa) {
        this.pwa = pwa;
        this.buttons = [];
        this.subscriptionAction = '';

        document.addEventListener('huh_pwa_push_isSubscribed', this.setUnsubscribe.bind(this));
        document.addEventListener('huh_pwa_push_isUnsubscribed', this.setSubscribe.bind(this));
        document.addEventListener('huh_pwa_push_permission_denied', this.setBlocked.bind(this));
        document.addEventListener('huh_pwa_sw_not_supported', this.setNotSupported.bind(this));
    }

    _connected() {
        if (!this.#connected) {
            this.#connected = true;
            this.bindElements();
        }
    }

    bindElements() {
        if (this.buttons.length) {
            for (const button of this.buttons) {
                button.removeEventListener('click', button.huhPwaWebSubscriptionHandler);
                delete button.huhPwaWebSubscriptionHandler; // Remove the stored handler
            }
        }

        this.buttons = document.querySelectorAll('.huhPwaWebSubscription');
        for (const button of this.buttons) {
            const handler = this.changeSubscriptionStatus.bind(this, button);
            button.huhPwaWebSubscriptionHandler = handler; // Store the handler for later removal
            button.addEventListener('click', handler);
        }
    }

    beforeEvent(debugMessage) {
        this.pwa.debugLog('[Push Notification Buttons] ' + debugMessage);
        if (!this.#connected) {
            this.bindElements();
        }
    }

    setSubscribe(event) {
        this.beforeEvent('Update Buttons to "Subscribe"');
        this.subscriptionAction = 'subscribe';
        this.buttons.forEach((button) => {
            button.disabled = false;
            button.querySelector('.label').innerHTML = this.pwa.config.translations.pushnotifications.subscribe;
            button.classList.add('unsubscribed');
            button.classList.remove('subscribed');
            button.classList.remove('blocked');
        });
    }

    setUnsubscribe(event) {
        this.beforeEvent('Update Buttons to "Unsubscribe"');
        this.subscriptionAction = 'unsubscribe';
        this.buttons.forEach((button) => {
            button.disabled = false;
            button.querySelector('.label').innerHTML = this.pwa.config.translations.pushnotifications.unsubscribe;
            button.classList.add('subscribed');
            button.classList.remove('unsubscribed');
            button.classList.remove('blocked');
        });
    }

    setBlocked(event) {
        this.beforeEvent('Update Buttons to blocked');
        this.buttons.forEach(function(button) {
            button.querySelector('.label').innerHTML = this.pwa.config.translations.pushnotifications.blocked;
            button.classList.add('blocked');
            button.classList.remove('unsubscribed');
            button.classList.remove('subscribed');
            button.disabled = true;
        });
    }

    setNotSupported(event) {
        this.beforeEvent('Serviceworker not supported');
        this.buttons.forEach(function(button) {
            button.querySelector('.label').innerHTML = this.pwa.config.translations.pushnotifications.not_supported;
            button.classList.add('blocked');
            button.classList.remove('unsubscribed');
            button.classList.remove('subscribed');
            button.disabled = true;
        });
    }

    changeSubscriptionStatus(button) {
        this.pwa.debugLog("Fire huh_pwa_push_changeSubscriptionState event");
        button.disabled = true;
        document.dispatchEvent(new CustomEvent('huh_pwa_push_changeSubscriptionState', { detail: this.subscriptionAction }));
    }
}