let HuhContaoPwaButtons = {
    buttons: [],
    debug: true,
    isInit: false,

    preInit: function() {
        document.addEventListener('huh_pwa_push_isSubscribed', this.setUnsubscribe.bind(this));
        document.addEventListener('huh_pwa_push_isUnsubscribed', this.setSubscribe.bind(this));
        document.addEventListener('huh_pwa_push_permission_denied', this.setBlocked.bind(this));
    },
    onReady: function() {
        if (!this.isInit)
        {
            this.init();
        }
    },
    init: function() {
        this.collectElementsToUpdate();
        this.isInit = true;
    },
    collectElementsToUpdate: function() {
        this.buttons = document.querySelectorAll('.huhPwaWebSubscription');
    },

    beforeEvent: function(debugMessage) {
        if (this.debug) {
            console.log('[Push Notification Buttons] ' + debugMessage);
        }
        if (!this.isInit)
        {
            this.init();
        }
    },
    setSubscribe: function (event) {
        this.beforeEvent('Update Buttons to "Subscribe"');
        this.buttons.forEach((button) => {
            button.disabled = false;
            button.querySelector('.label').textContent = HuhPwaTranslator.pushnotifications.subscribe;
            button.classList.add('unsubscribed');
            button.classList.remove('subscribed');
            button.classList.remove('blocked');
            button.addEventListener('click', () => { this.changeSubscriptionStatus('subscribe', button); });
        });
    },
    setUnsubscribe: function (event) {
        this.beforeEvent('Update Buttons to "Unsubscribe"');
        this.buttons.forEach((button) => {
            button.disabled = false;
            button.querySelector('.label').textContent = HuhPwaTranslator.pushnotifications.unsubscribe;
            button.classList.add('subscribed');
            button.classList.remove('unsubscribed');
            button.classList.remove('blocked');
            button.addEventListener('click', () => { this.changeSubscriptionStatus('unsubscribe', button); });
        });
    },
    setBlocked: function(event) {
        this.beforeEvent('Update Buttons to blocked');
        this.buttons.forEach((button) => {
            button.querySelector('.label').textContent = HuhPwaTranslator.pushnotifications.blocked;
            button.classList.add('blocked');
            button.classList.remove('unsubscribed');
            button.classList.remove('subscribed');
            button.disabled = true;
        });
    },
    changeSubscriptionStatus: function(action, button) {
        button.disabled = true;
        document.dispatchEvent(new CustomEvent('huh_pwa_push_changeSubscriptionState', { detail: action }));
    }
};

HuhContaoPwaButtons.preInit();
document.addEventListener('DOMContentLoaded', function() {
    HuhContaoPwaButtons.onReady();
});
