const BUTTON_SELECTOR = '.huhPwaWebSubscription';

export default class PushSubscriptionButtons {
    #connected = false;
    #state = null;

    /**
     * @param {HuhPwa} pwa - The PWA instance to which the buttons belong.
     */
    constructor(pwa) {
        this.pwa = pwa;
        this.subscriptionAction = '';

        document.addEventListener('huh_pwa_push_isSubscribed', this.setUnsubscribe.bind(this));
        document.addEventListener('huh_pwa_push_isUnsubscribed', this.setSubscribe.bind(this));
        document.addEventListener('huh_pwa_push_permission_denied', this.setBlocked.bind(this));
        document.addEventListener('huh_pwa_sw_not_supported', this.setNotSupported.bind(this));
        document.addEventListener('huh_pwa_push_not_supported', this.setNotSupported.bind(this));
        // A failed (un)subscription leaves the clicked button disabled; restore the current state.
        document.addEventListener('huh_pwa_push_subscription_failed', this.bindElements.bind(this));
        document.addEventListener('huh_pwa_push_unsubscription_failed', this.bindElements.bind(this));
    }

    /**
     * The buttons currently in the document. Queried on each access, because pages
     * that replace their content (Turbo, AJAX) add buttons after initialization.
     */
    get buttons() {
        return document.querySelectorAll(BUTTON_SELECTOR);
    }

    _connected() {
        if (this.#connected) {
            return;
        }

        this.#connected = true;

        document.addEventListener('click', (event) => {
            const button = event.target.closest?.(BUTTON_SELECTOR);
            if (button && !button.disabled) {
                this.changeSubscriptionStatus(button);
            }
        });

        new MutationObserver((mutations) => {
            if (!this.#state) {
                return;
            }
            for (const { addedNodes } of mutations) {
                for (const node of addedNodes) {
                    if (!(node instanceof Element)) {
                        continue;
                    }
                    if (node.matches(BUTTON_SELECTOR)) {
                        this.#render(node);
                    }
                    node.querySelectorAll(BUTTON_SELECTOR).forEach((button) => this.#render(button));
                }
            }
        }).observe(document.documentElement, { childList: true, subtree: true });

        this.bindElements();
    }

    /**
     * Applies the current state to all buttons in the document.
     */
    bindElements() {
        this.buttons.forEach((button) => this.#render(button));
    }

    beforeEvent(debugMessage) {
        this.pwa.debugLog('[Push Notification Buttons] ' + debugMessage);
    }

    setSubscribe(event) {
        this.beforeEvent('Update Buttons to "Subscribe"');
        this.subscriptionAction = 'subscribe';
        this.#setState('subscribe');
    }

    setUnsubscribe(event) {
        this.beforeEvent('Update Buttons to "Unsubscribe"');
        this.subscriptionAction = 'unsubscribe';
        this.#setState('unsubscribe');
    }

    setBlocked(event) {
        this.beforeEvent('Update Buttons to blocked');
        this.#setState('blocked');
    }

    setNotSupported(event) {
        this.beforeEvent('Serviceworker not supported');
        this.#setState('not_supported');
    }

    changeSubscriptionStatus(button) {
        this.pwa.debugLog("Fire huh_pwa_push_changeSubscriptionState event");
        button.disabled = true;
        document.dispatchEvent(new CustomEvent('huh_pwa_push_changeSubscriptionState', { detail: this.subscriptionAction }));
    }

    #setState(state) {
        this.#state = state;
        this.bindElements();
    }

    #render(button) {
        const translations = this.pwa.config.translations.pushnotifications;
        const label = button.querySelector('.label');

        switch (this.#state) {
            case 'subscribe':
            case 'unsubscribe': {
                const subscribed = this.#state === 'unsubscribe';
                button.disabled = false;
                if (label) label.innerHTML = subscribed ? translations.unsubscribe : translations.subscribe;
                button.classList.toggle('subscribed', subscribed);
                button.classList.toggle('unsubscribed', !subscribed);
                button.classList.remove('blocked');
                break;
            }
            case 'blocked':
            case 'not_supported':
                button.disabled = true;
                if (label) label.innerHTML = this.#state === 'blocked' ? translations.blocked : translations.not_supported;
                button.classList.add('blocked');
                button.classList.remove('unsubscribed', 'subscribed');
                break;
        }
    }
}
