class InstallPrompt
{
    registerListener(debug = false)
    {
        this.debug = debug;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.deferredPrompt = e;
            if (debug) console.log('[PWA Install] beforeinstallprompt event fired');
            this.getInstallButtons().forEach((element) => {
                element.classList.remove('disabled');
            });
        });

        this.getInstallButtons().forEach((element) => {
            element.addEventListener('click', (e) => {
                this.fireInstallPrompt();
            });
        });
    }

    getInstallButtons()
    {
        return document.querySelectorAll('.huh-pwa-install-button');
    }

    fireInstallPrompt()
    {
        if (this.deferredPrompt !== undefined)
        {
            this.deferredPrompt.prompt();
            if (debug) console.log('[PWA Install] dispatch beforeinstallprompt event');
        }
    }
}

export default InstallPrompt;