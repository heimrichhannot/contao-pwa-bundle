class PushSubscriptionButtons
{
    constructor()
    {
        this.buttons = [];
        this.debug = false;
        this.isInit = false;
        this.subscriptionAction = '';

        document.addEventListener('huh_pwa_push_isSubscribed', this.setUnsubscribe.bind(this));
        document.addEventListener('huh_pwa_push_isUnsubscribed', this.setSubscribe.bind(this));
        document.addEventListener('huh_pwa_push_permission_denied', this.setBlocked.bind(this));
        document.addEventListener('huh_pwa_sw_not_supported', this.setNotSupported.bind(this));
    }

    onLoaded ()
    {
        if (!this.isInit)
        {
            this.init();
        }
    }

    init ()
    {
        this.collectElementsToUpdate();
        this.isInit = true;
    }

    collectElementsToUpdate()
    {
        this.buttons = document.querySelectorAll('.huhPwaWebSubscription');
        this.buttons.forEach((button) => {
            button.addEventListener('click', () => { this.changeSubscriptionStatus(button); });
        });
    }

    beforeEvent (debugMessage)
    {
        if (this.debug) {
            console.log('[Push Notification Buttons] ' + debugMessage);
        }
        if (!this.isInit)
        {
            this.init();
        }
    }

    setSubscribe (event)
    {
        this.beforeEvent('Update Buttons to "Subscribe"');
        this.subscriptionAction = 'subscribe';
        this.buttons.forEach((button) => {
            button.disabled = false;
            button.querySelector('.label').innerHTML = HuhContaoPwaBundle.translations.pushnotifications.subscribe;
            button.classList.add('unsubscribed');
            button.classList.remove('subscribed');
            button.classList.remove('blocked');
        });
    }

    setUnsubscribe (event)
    {
        this.beforeEvent('Update Buttons to "Unsubscribe"');
        this.subscriptionAction = 'unsubscribe';
        this.buttons.forEach((button) => {
            button.disabled = false;
            button.querySelector('.label').innerHTML = HuhContaoPwaBundle.translations.pushnotifications.unsubscribe;
            button.classList.add('subscribed');
            button.classList.remove('unsubscribed');
            button.classList.remove('blocked');
        });
    }

    setBlocked (event)
    {
        this.beforeEvent('Update Buttons to blocked');
        this.buttons.forEach(function(button) {
            button.querySelector('.label').innerHTML = HuhContaoPwaBundle.translations.pushnotifications.blocked;
            button.classList.add('blocked');
            button.classList.remove('unsubscribed');
            button.classList.remove('subscribed');
            button.disabled = true;
        });
    }

    setNotSupported (event)
    {
        this.beforeEvent('Serviceworker not supported');
        this.buttons.forEach(function(button) {
            button.querySelector('.label').innerHTML = HuhContaoPwaBundle.translations.pushnotifications.not_supported;
            button.classList.add('blocked');
            button.classList.remove('unsubscribed');
            button.classList.remove('subscribed');
            button.disabled = true;
        });
    }

    changeSubscriptionStatus (button)
    {
        if (this.debug) console.log("Fire huh_pwa_push_changeSubscriptionState event");
        button.disabled = true;
        document.dispatchEvent(new CustomEvent('huh_pwa_push_changeSubscriptionState', { detail: this.subscriptionAction }));
    }
}

export default PushSubscriptionButtons