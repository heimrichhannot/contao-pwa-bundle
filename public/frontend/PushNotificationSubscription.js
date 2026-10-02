export default class PushNotificationSubscription {
    #connected = false;

    /**
     *
     * @param {HuhPwa} pwa
     */
    constructor(pwa) {
        this.pwa = pwa;
        this.subscribePath = pwa.config.pushNotifications.subscribePath;
        this.unsubscribePath = pwa.config.pushNotifications.unsubscribePath;
    }

    _connected() {
        if (!this.#connected) {
            document.addEventListener('huh_pwa_push_changeSubscriptionState', this.changeSubscriptionStatus.bind(this));
            this.#connected = true;
        }
    }

    busy = false;

    async subscribe() {
        const config = this.pwa.config;
        const subscribePath = this.subscribePath;
        try {
            // Request permission during the click, before worker/network awaits.
            if (Notification.permission !== 'granted' && await Notification.requestPermission() !== 'granted') {
                throw new Error('Notification permission was not granted');
            }
            const registration = await this.pwa.getRegistration();
            const keyResponse = await fetch('/_huh_pwa/vapid.pub', { signal: AbortSignal.timeout(15000) });
            if (!keyResponse.ok) throw new Error('Failed to fetch the push public key');
            const subscription = await this.pwa.withTimeout(registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: PushNotificationSubscription.urlBase64ToUint8Array(await keyResponse.text()),
            }), 'Push subscription timed out');
            const response = await fetch(subscribePath, {
                method: 'post',
                headers: { 'Content-type': 'application/json' },
                body: JSON.stringify({ subscription }),
                signal: AbortSignal.timeout(15000),
            });
            if (!response.ok) throw new Error('Failed to save the push subscription');
            if (config === this.pwa.config) this.setIsSubscribed();
        } catch (reason) {
            this.checkPermission();
            document.dispatchEvent(new CustomEvent('huh_pwa_push_subscription_failed', { detail: { reason } }));
        }
    }

    async unsubscribe() {
        const config = this.pwa.config;
        const unsubscribePath = this.unsubscribePath;
        try {
            const registration = await this.pwa.getRegistration();
            const subscription = await this.pwa.withTimeout(registration.pushManager.getSubscription(), 'Push subscription check timed out');
            if (subscription) {
                const removed = await this.pwa.withTimeout(subscription.unsubscribe(), 'Push unsubscription timed out');
                if (!removed) throw new Error('Failed to remove the push subscription');
                const response = await fetch(unsubscribePath, {
                    method: 'post',
                    headers: { 'Content-type': 'application/json' },
                    body: JSON.stringify({ subscription }),
                    signal: AbortSignal.timeout(15000),
                });
                if (!response.ok) throw new Error('Failed to save the push unsubscription');
            }
            if (config === this.pwa.config) this.setIsUnsubscribed();
        } catch (reason) {
            document.dispatchEvent(new CustomEvent('huh_pwa_push_unsubscription_failed', { detail: { reason } }));
        }
    }
    setIsUnsubscribed(context = null) {
        if (!this.checkPermission()) return;

        document.dispatchEvent(new CustomEvent('huh_pwa_push_isUnsubscribed',{
            detail: { context: context }
        }));

        this.pwa.debugLog('[Push Notification Subscription] Fired huh_pwa_push_isUnsubscribed');
    }

    setIsSubscribed() {
        if (!this.checkPermission()) return;
        document.dispatchEvent(new Event('huh_pwa_push_isSubscribed'));
        this.pwa.debugLog('[Push Notification Subscription] Fired huh_pwa_push_isSubscribed"');
    }

    checkPermission() {
        if ('Notification' in window && Notification.permission === 'denied') {
            document.dispatchEvent(new Event('huh_pwa_push_permission_denied'));
            this.pwa.debugLog('[Push Notification Subscription] Fired huh_pwa_push_permission_denied');
            return false;
        }
        return true;
    }

    async changeSubscriptionStatus(event) {
        if (this.busy || !this.pwa.checkPushSupport()) return;
        if (!['subscribe', 'unsubscribe'].includes(event.detail)) return;

        this.pwa.invalidateStatus();
        this.busy = true;
        document.dispatchEvent(new Event('huh_pwa_push_busy'));
        try {
            if (event.detail === 'subscribe') await this.subscribe();
            else await this.unsubscribe();
        } finally {
            this.busy = false;
            // Read the actual browser state, including partially failed operations.
            await this.pwa.refreshStatus();
        }
    }

    static urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        let outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }

        return outputArray;
    }
}
