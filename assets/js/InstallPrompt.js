class InstallPrompt
{
    constructor(debug = false) {
        this.debug = debug;
        this.supportInstall = false;
    }

    registerListener()
    {
        if (('BeforeInstallPromptEvent' in window)) {
            this.supportInstall = true;
            this.#registerBeforeInstallPromptListener();
        } else {
            if (this.getNotSupportedMessage().length > 0) {
                this.getInstallButtons().forEach((element) => {
                    element.classList.remove('disabled');
                });
            }
        }

        this.getInstallButtons().forEach((element) => {
            element.addEventListener('click', (e) => {
                this.fireInstallPrompt();
            });
        });
    }

    #registerBeforeInstallPromptListener()
    {
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.deferredPrompt = e;
            if (this.debug) console.log('[PWA Install] beforeinstallprompt event fired');
            this.getInstallButtons().forEach((element) => {
                element.classList.remove('disabled');
            });
        });
    }

    getInstallButtons()
    {
        return document.querySelectorAll('.huh-pwa-install-button');
    }

    getNotSupportedMessage()
    {
        return document.querySelectorAll('.huh-pwa-install-message');
    }

    fireInstallPrompt()
    {
        if (this.deferredPrompt !== undefined)
        {
            this.deferredPrompt.prompt();
            if (debug) console.log('[PWA Install] dispatch beforeinstallprompt event');
            this.getInstallButtons().forEach((element) => {
                element.classList.add('disabled');
            });
        } else if (false === this.supportInstall) {
            this.getNotSupportedMessage().forEach((element) => {
                element.classList.remove('hidden');
            });
        }
    }
}

export default InstallPrompt;