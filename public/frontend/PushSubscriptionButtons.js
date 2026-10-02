const BUTTON_SELECTOR = '.huhPwaWebSubscription';

export default class PushSubscriptionButtons {
    #connected = false;
    #state = null;
    #helpId = 0;

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
        document.addEventListener('huh_pwa_push_install_required', () => this.#setState('install_required'));
        document.addEventListener('huh_pwa_push_initialization_failed', () => this.#setState('error'));
        document.addEventListener('huh_pwa_push_checking', () => this.#setState('checking'));
        document.addEventListener('huh_pwa_push_busy', this.bindElements.bind(this));
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
        if (this.#state === 'error') {
            this.pwa.refreshStatus('retry');
            return;
        }
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
        let help = button.nextElementSibling;
        if (!help?.matches('[data-huh-pwa-push-help]')) {
            help = null;
        }
        const message = this.#state === 'install_required' ? translations.install_required_help
            : this.#state === 'error' ? translations.initialization_failed : null;
        // Also support custom button templates which predate the help element.
        if (message && !help) {
            help = document.createElement('p');
            help.setAttribute('data-huh-pwa-push-help', '');
            help.setAttribute('role', 'status');
            button.after(help);
        }
        if (help) {
            help.hidden = !message;
            if (message) {
                if (!help.id || document.getElementById(help.id) !== help) {
                    help.id = 'huh-pwa-push-help-' + ++this.#helpId;
                }
                if (help.textContent !== message) help.textContent = message;
                const describedBy = new Set((button.getAttribute('aria-describedby') || '').split(/\s+/).filter(Boolean));
                describedBy.add(help.id);
                button.setAttribute('aria-describedby', [...describedBy].join(' '));
            } else if (help.id) {
                const describedBy = (button.getAttribute('aria-describedby') || '').split(/\s+/).filter((id) => id && id !== help.id);
                if (describedBy.length) button.setAttribute('aria-describedby', describedBy.join(' '));
                else button.removeAttribute('aria-describedby');
            }
        }
        button.classList.remove('checking', 'error', 'install-required');

        switch (this.#state) {
            case 'subscribe':
            case 'unsubscribe': {
                const subscribed = this.#state === 'unsubscribe';
                button.disabled = this.pwa.pushSubscription.busy;
                if (label) label.textContent = subscribed ? translations.unsubscribe : translations.subscribe;
                button.classList.toggle('subscribed', subscribed);
                button.classList.toggle('unsubscribed', !subscribed);
                button.classList.remove('blocked');
                break;
            }
            case 'checking':
            case 'install_required':
            case 'error':
                button.disabled = this.#state !== 'error';
                if (label) label.textContent = this.#state === 'checking' ? translations.wait
                    : this.#state === 'install_required' ? translations.install_required : translations.retry;
                button.classList.remove('unsubscribed', 'subscribed', 'blocked');
                button.classList.add(this.#state === 'install_required' ? 'install-required' : this.#state);
                break;
            case 'blocked':
            case 'not_supported':
                button.disabled = true;
                if (label) label.textContent = this.#state === 'blocked' ? translations.blocked : translations.not_supported;
                button.classList.add('blocked');
                button.classList.remove('unsubscribed', 'subscribed');
                break;
        }
    }
}
