let PwaButtons = {

    buttons: [],
    debug: false,

    init : () => {
        this.collectElementsToUpdate();
        document.addEventListener('huh_pwa_push_isSubscribed', this.setUnsubscribe);
        document.addEventListener('huh_pwa_push_isUnsubscribed', this.setSubscribe);
    },
    collectElementsToUpdate: () => {
        this.buttons = document.querySelectorAll('.huhPwaWebSubscriptions');
    },

    setSubscribe: (event) => {
        if (this.debug) {
            console.log('[Push Notification Buttons] Update Buttons to "Subscribe"');
        }
        this.buttons.forEach((button) => {
            button.removeAttribute('disabled');
            button.querySelector('.label').textContent = HuhPwaTranslator.pushnotifications.subscribe;
            button.classList.add('unsubscribed');
            button.classList.remove('subscribed');
            button.classList.remove('blocked');
            button.addEventListener('click', this.subscribe);
        });
    },
    setUnsubscribe: (event) => {
        if (this.debug) {
            console.log('[Push Notification Buttons] Update Buttons to "Unsubscribe"');
        }
        this.buttons.forEach((button) => {
            button.removeAttribute('disabled');
            button.querySelector('.label').textContent = HuhPwaTranslator.pushnotifications.unsubscribe;
            button.classList.add('subscribed');
            button.classList.remove('unsubscribed');
            button.classList.remove('blocked');
            button.addEventListener('click', this.unsubscribe);
        });
    },
};