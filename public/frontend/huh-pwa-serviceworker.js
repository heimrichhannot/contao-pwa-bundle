class HuhPwaServiceWorker {
    constructor() {
        this.offlinePage = '';
        this.debug = false;
        this.pageTitle = '';
        this.supportOfflineCaching = false;
        this.supportEncorePrecache = false;
        this.startUrl = '';
        this.cache = 'huh_pwa_sw_cache';
        this.updateSubscriptionPath = '';
    }

    /**
     *
     * @param {ExtendableEvent} event
     */
    async installEvent(event) {
        if (!this.supportOfflineCaching) {
            this.log(event, "Offline caching not supported.");
            return;
        }

        this.log(event, "Offline caching is supported");
        const cache = await caches.open(this.cache);
        const files = await this.getPrecacheFileList(event);

        this.log(event, 'Precache file list: ' + files.toString());
        await Promise.allSettled(
            files.map((file) => cache.add(new Request(file, { cache: 'reload' })))
        );
    }

    /**
     *
     * @param {PushSubscriptionChangeEvent} event
     * @param {ServiceWorkerGlobalScope} serviceWorker
     * @returns {Promise<Response | never>}
     */
    pushSubscriptionChangeEvent(event, serviceWorker) {
        if (event.oldSubscription === undefined || event.oldSubscription === null) {
             return Promise.resolve();
        } else {
            return serviceWorker.registration.pushManager
                .subscribe(event.oldSubscription.options)
                .then((newSubscription) => {
                    return fetch(this.updateSubscriptionPath, {
                        method: 'post',
                        headers: {
                            'Content-type': 'application/json',
                        },
                        body: JSON.stringify({
                            newSubscription: newSubscription,
                            oldSubscription: event.oldSubscription
                        }),
                    });
                });
        }
    }

    returnFromCache(request) {
        return caches.open(this.cache).then((cache) => {
            return cache.match(request).then((matching) => {
                if (!matching || matching.status == 404)
                {
                    return this.offlineFallback(cache).then((offlineResponse) => {
                        if (offlineResponse) {
                            return offlineResponse;
                        }
                        return Response.error();
                    });
                }
                return matching;
            });
        });
    }

    offlineFallback(cache) {
        const offlinePath = this.toCachePath(this.offlinePage);
        if (offlinePath !== '') {
            return cache.match(offlinePath);
        }
        return Promise.resolve(undefined);
    }

    shouldHandleRequest(request) {
        if (request.method !== 'GET') {
            return false;
        }

        const url = new URL(request.url);
        if (url.protocol !== 'http:' && url.protocol !== 'https:') {
            return false;
        }

        // Do not interfere with backend/dev/API tools.
        if (
            url.pathname.startsWith('/contao')
            || url.pathname.startsWith('/_contao')
            || url.pathname.startsWith('/_wdt')
            || url.pathname.startsWith('/_profiler')
            || url.pathname.startsWith('/api')
            || url.pathname.startsWith('/app_dev.php')
        ) {
            return false;
        }

        return true;
    }

    async fetchEvent(event) {
        const request = event.request;

        if (!this.supportOfflineCaching) {
            return fetch(request);
        }

        if (request.headers.has('range')) {
            return fetch(request);
        }

        const url = new URL(request.url);
        const isNavigation = request.mode === 'navigate';
        const isSameOrigin = url.origin === self.location.origin;
        const staticAsset = request.destination === 'script'
            || request.destination === 'style'
            || request.destination === 'image'
            || request.destination === 'font';

        if (isNavigation) {
            return this.networkNavigateWithFallback(request);
        }

        if (isSameOrigin && staticAsset) {
            return this.staleWhileRevalidate(request);
        }

        return this.networkWithFallback(request);
    }

    async getPrecacheFileList(event) {
        const files = [];
        const startUrl = this.toCachePath(this.startUrl || '/');
        if (startUrl) {
            files.push(startUrl);
        }

        const offlinePage = this.toCachePath(this.offlinePage);
        if (offlinePage) {
            files.push(offlinePage);
        }

        if (this.supportEncorePrecache) {
            this.log(event, "Encore files caching is supported. Precache files from webpack manifest.");
            try {
                const response = await fetch('/build/manifest.json', { cache: 'no-store' });
                if (response.ok) {
                    const json = await response.json();
                    Object.keys(json).forEach((key) => {
                        const path = this.toCachePath(json[key]);
                        if (path) {
                            files.push(path);
                        }
                    });
                } else {
                    this.log(event, 'Skipping Encore precache: manifest request failed with status ' + response.status);
                }
            } catch (error) {
                this.log(event, 'Skipping Encore precache: failed to fetch manifest (' + error.message + ')');
            }
        } else {
            this.log(event, "Encore files caching not supported");
        }

        return Array.from(new Set(files));
    }

    toCachePath(value) {
        if (typeof value !== 'string' || value.trim() === '') {
            return '';
        }

        try {
            const url = new URL(value, self.location.origin);
            if (url.origin !== self.location.origin) {
                return '';
            }
            return url.pathname + url.search;
        } catch (error) {
            this.log({ type: 'cache' }, 'Invalid cache path skipped: ' + value);
            return '';
        }
    }

    async networkNavigateWithFallback(request) {
        try {
            const response = await fetch(request);
            if (response && response.ok) {
                const cache = await caches.open(this.cache);
                await cache.put(request, response.clone());
            }
            return response;
        } catch (error) {
            const cache = await caches.open(this.cache);
            const cached = await cache.match(request);
            if (cached) {
                return cached;
            }
            const offline = await this.offlineFallback(cache);
            if (offline) {
                return offline;
            }
            const startUrl = this.toCachePath(this.startUrl || '/');
            if (startUrl) {
                const startPage = await cache.match(startUrl);
                if (startPage) {
                    return startPage;
                }
            }
            return Response.error();
        }
    }

    async staleWhileRevalidate(request) {
        const cache = await caches.open(this.cache);
        const cached = await cache.match(request);
        const networkFetch = fetch(request)
            .then(async (response) => {
                if (response && response.ok) {
                    await cache.put(request, response.clone());
                }
                return response;
            })
            .catch(() => null);

        if (cached) {
            return cached;
        }

        const networkResponse = await networkFetch;
        if (networkResponse) {
            return networkResponse;
        }

        return this.returnFromCache(request);
    }

    async networkWithFallback(request) {
        try {
            const response = await fetch(request);
            if (response && response.ok) {
                const cache = await caches.open(this.cache);
                await cache.put(request, response.clone());
            }
            return response;
        } catch (error) {
            return this.returnFromCache(request);
        }
    }

    /**
     * Opens what a clicked notification points at. An installed app or an already
     * open tab of the same origin is navigated and focused; a new window is opened
     * only when there is none, because openWindow() always creates a new browsing
     * context and would hand the link to the browser while the installed app stays
     * in the background. Without a target the start URL opens, so that a click
     * never does nothing.
     *
     * @param {NotificationEvent} event
     * @param {Clients} clients
     * @returns {Promise<?WindowClient>}
     */
    async notificationClickEvent(event, clients) {
        const data = event.notification ? event.notification.data : null;
        const jumpTo = data ? data.clickJumpTo : undefined;
        const target = jumpTo === undefined || jumpTo === null || jumpTo === '' ? this.startUrl : jumpTo;
        if (!target) {
            this.log(event, 'Notification has no target and no start URL.');
            return null;
        }

        let url;
        try {
            url = new URL(target, self.location.origin);
        } catch (e) {
            this.log(event, 'Notification target is not a valid URL: ' + target);
            return null;
        }

        let windows = [];
        try {
            windows = await clients.matchAll({type: 'window', includeUncontrolled: true});
        } catch (e) {
            this.log(event, 'Could not list open windows: ' + e.message);
        }

        for (const client of windows) {
            let sameOrigin = false;
            try {
                sameOrigin = new URL(client.url).origin === url.origin;
            } catch (e) {
            }
            if (!sameOrigin) {
                continue;
            }
            try {
                // An uncontrolled client rejects navigate(); fall through to a new window.
                const navigated = client.url !== url.href && typeof client.navigate === 'function'
                    ? await client.navigate(url.href)
                    : client;
                return await (navigated || client).focus();
            } catch (e) {
                this.log(event, 'Could not reuse an open window: ' + e.message);
                break;
            }
        }

        return clients.openWindow(url.href);
    }

    notificationTitle(payload) {
        let title = this.pageTitle;
        if (typeof payload.title === 'string') {
            title = payload.title;
        }
        return title;
    }

    /**
     * @param event
     * @param {string} message
     * @param {?string} caller Calling method
     */
    log(event, message, caller = null) {
        if(!this.debug) return;
        let log = '[Serviceworker ' + event.type + ' event] ';
        log += message;
        if (caller !== null) log += ' ' + caller;
        console.log(log);
    }
}
