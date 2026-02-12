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
            const startUrl = this.toCachePath(this.startUrl || '/');
            if (startUrl) {
                const startPage = await cache.match(startUrl);
                if (startPage) {
                    return startPage;
                }
            }
            const offline = await this.offlineFallback(cache);
            if (offline) {
                return offline;
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
