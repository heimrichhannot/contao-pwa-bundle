class HuhContaoPwaButtons {

    constructor() {
        this.buttons = [];
        this.debug = false;
        this.isInit = false;
        this.subscriptionAction = '';

        document.addEventListener('huh_pwa_push_isSubscribed', this.setUnsubscribe.bind(this));
        document.addEventListener('huh_pwa_push_isUnsubscribed', this.setSubscribe.bind(this));
        document.addEventListener('huh_pwa_push_permission_denied', this.setBlocked.bind(this));
    }

    onReady () {
        if (!this.isInit)
        {
            this.init();
        }
    }

    init ()  {
        this.collectElementsToUpdate();
        this.isInit = true;
    }

    collectElementsToUpdate() {
        this.buttons = document.querySelectorAll('.huhPwaWebSubscription');
        this.buttons.forEach((button) => {
            button.addEventListener('click', () => { this.changeSubscriptionStatus(button); });
        });
    }
    beforeEvent (debugMessage) {
        if (this.debug) {
            console.log('[Push Notification Buttons] ' + debugMessage);
        }
        if (!this.isInit)
        {
            this.init();
        }
    }

    setSubscribe (event) {
        this.beforeEvent('Update Buttons to "Subscribe"');
        this.subscriptionAction = 'subscribe';
        this.buttons.forEach((button) => {
            button.disabled = false;
            button.querySelector('.label').textContent = HuhPwaTranslator.pushnotifications.subscribe;
            button.classList.add('unsubscribed');
            button.classList.remove('subscribed');
            button.classList.remove('blocked');
        });
    }

    setUnsubscribe (event) {
        this.beforeEvent('Update Buttons to "Unsubscribe"');
        this.subscriptionAction = 'unsubscribe';
        this.buttons.forEach((button) => {
            button.disabled = false;
            button.querySelector('.label').textContent = HuhPwaTranslator.pushnotifications.unsubscribe;
            button.classList.add('subscribed');
            button.classList.remove('unsubscribed');
            button.classList.remove('blocked');
        });
    }

    setBlocked (event) {
        this.beforeEvent('Update Buttons to blocked');
        this.buttons.forEach((button) => {
            button.querySelector('.label').textContent = HuhPwaTranslator.pushnotifications.blocked;
            button.classList.add('blocked');
            button.classList.remove('unsubscribed');
            button.classList.remove('subscribed');
            button.disabled = true;
        });
    }
    changeSubscriptionStatus (button) {

        console.log("Fire huh_pwa_push_changeSubscriptionState event");
        button.disabled = true;
        document.dispatchEvent(new CustomEvent('huh_pwa_push_changeSubscriptionState', { detail: this.subscriptionAction }));
    }
}
exports = HuhContaoPwaButtons;