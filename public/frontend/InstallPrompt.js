export default class InstallPrompt {
    /**
     * @param {HuhPwa} pwa
     */
    constructor(pwa) {
        this.pwa = pwa;
        this.supportInstall = ('BeforeInstallPromptEvent' in window);
    }

    registerListener() {
        const installButtons = this.getInstallButtons();
        const messageElements = this.getNotSupportedMessage();

        if (this.supportInstall) {
            this.#registerBeforeInstallPromptListener();
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

    fireInstallPrompt() {
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
            this.pwa.debugLog('[PWA Install] dispatch beforeinstallprompt event');
            this.getInstallButtons().forEach((element) => {
                element.classList.add('disabled');
            });
        } else {
            this.pwa.debugLog('[PWA Install] Deferred prompt is not available, showing not supported message');
            this.getNotSupportedMessage().forEach((element) => {
                element.classList.remove('hidden');
            });
        }
    }
}
