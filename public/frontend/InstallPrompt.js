export default class InstallPrompt {
    /**
     * @param {HuhPwa} pwa
     */
    constructor(pwa) {
        this.pwa = pwa;
        this.supportInstall = ('BeforeInstallPromptEvent' in window);
    }

    isIos() {
        return /iphone|ipad|ipod/i.test(navigator.userAgent)
            || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
    }

    isStandalone() {
        return window.matchMedia('(display-mode: standalone)').matches
            || navigator.standalone === true;
    }

    registerListener() {
        const installButtons = this.getInstallButtons();
        const messageElements = this.getNotSupportedMessage();

        window.addEventListener('appinstalled', () => {
            this.hideInstallButtons();
        });

        if (this.isStandalone()) {
            this.hideInstallButtons();
            messageElements.forEach((element) => {
                element.classList.add('hidden');
            });
            return;
        }

        if (this.supportInstall) {
            this.#registerBeforeInstallPromptListener();
        } else if (this.isIos()) {
            this.showIosInstructions();
        } else if (messageElements.length) {
            this.pwa.debugLog('[PWA Install] BeforeInstallPromptEvent not supported, showing not supported message');
            messageElements.forEach((element) => {
                element.classList.remove('hidden');
                element.innerHTML = this.pwa.config.translations?.install?.notSupported || 'Install prompt is not supported by your browser.';
            });
        }

        installButtons.forEach((element) => {
            element.addEventListener('click', (e) => {
                this.fireInstallPrompt();
            });
        });
    }

    #registerBeforeInstallPromptListener() {
        this.pwa.debugLog('[PWA Install] Registering beforeinstallprompt event listener');
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.deferredPrompt = e;
            this.pwa.debugLog('[PWA Install] beforeinstallprompt event fired');
            this.getInstallButtons().forEach((element) => {
                element.classList.remove('disabled');
            });
        });
    }

    getInstallButtons() {
        const $elms = document.querySelectorAll('[data-huh-pwa="action:install"]');
        this.pwa.debugLog('[PWA Install] Found install buttons: ' + $elms.length, $elms);
        return Array.from($elms);
    }

    getNotSupportedMessage() {
        return document.querySelectorAll('.huh-pwa-install-message');
    }

    hideInstallButtons() {
        this.getInstallButtons().forEach((element) => {
            element.classList.add('hidden');
        });
    }

    showIosInstructions() {
        this.pwa.debugLog('[PWA Install] Showing iOS Add to Home Screen instructions');
        this.getNotSupportedMessage().forEach((element) => {
            element.classList.remove('hidden');
            element.innerHTML = this.pwa.config.translations?.install?.iosInstructions
                || "To install this app: tap the Share button in Safari, then choose 'Add to Home Screen'.";
        });
    }

    async fireInstallPrompt() {
        this.pwa.debugLog('[PWA Install] Fire install prompt');
        if (this.supportInstall) {
            if (!this.deferredPrompt) {
                this.pwa.debugLog('[PWA Install] No deferred prompt available, cannot show install prompt');
                this.getNotSupportedMessage().forEach((element) => {
                    element.classList.remove('hidden');
                    element.innerHTML = this.pwa.config.translations?.install?.notAvailable || 'Install prompt is not available at this time.';
                });
                return;
            }

            this.pwa.debugLog('[PWA Install] Deferred prompt is available, showing install prompt');
            this.deferredPrompt.prompt();
            const choiceResult = await this.deferredPrompt.userChoice;
            this.pwa.debugLog('[PWA Install] User choice outcome: ' + choiceResult.outcome);
            this.deferredPrompt = null;
            this.getInstallButtons().forEach((element) => {
                element.classList.add('disabled');
            });
        } else if (this.isIos()) {
            this.showIosInstructions();
        } else {
            this.pwa.debugLog('[PWA Install] Deferred prompt is not available, showing not supported message');
            this.getNotSupportedMessage().forEach((element) => {
                element.classList.remove('hidden');
            });
        }
    }
}
