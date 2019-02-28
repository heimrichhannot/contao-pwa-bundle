class HuhPwaServiceWorker
{
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
    installEvent(event) {
        return new Promise((resolve, reject) => {
            if (this.supportOfflineCaching) {
                this.log(event, "Offline caching is supported");
                return caches.open(this.cache)
                .then((cache) => {
                    return new Promise((resolve) => {
                        let files = [this.startUrl];
                        if (typeof this.offlinePage === 'string' && this.offlinePage !== '') {
                            files.push(this.offlinePage);
                        }
                        resolve(files);
                    })
                    .then((files) => {
                        return new Promise((resolve) => {
                            if (this.supportEncorePrecache) {
                                this.log(event, "Encore files caching is suported. Precache files from webpack manigest.");
                                fetch('/build/manifest.json')
                                .then((responce) => {
                                    return responce.json();
                                }).then((json) => {
                                    return new Promise((resolve) => {
                                        let encoreFiles = Object.keys(json).map(function(key) {
                                            return json[key];
                                        });
                                        resolve(files.concat(encoreFiles));
                                    });
                                }).then((files) => {
                                    resolve(files);
                                });
                            } else {
                                this.log(event, "Encore files caching not supported");
                                resolve(files);
                            }
                        });
                    })
                    .then((files) => {
                        this.log(event, 'Precache file list: ' + files.toString());
                        return cache.addAll(files);
                    });
                })
                .then(() => {
                    resolve();
                });
            } else {
                this.log(event, "Offline caching not supported.");
                resolve();
            }
        });
    }

    /**
     *
     * @param {PushSubscriptionChangeEvent} event
     * @param {ServiceWorkerGlobalScope} serviceWorker
     * @returns {Promise<Response | never>}
     */
    pushSubscriptionChangeEvent(event, serviceWorker)
    {
        if (event.oldSubscription === undefined || event.oldSubscription === null)
        {
             return Promise.resolve();
        }
        else {
            return serviceWorker.registration.pushManager
            .subscribe(event.oldSubscription.options)
            .then(function(newSubscription) {
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

    returnFromCache(request)
    {
        return caches.open(this.cache).then((cache) => {
            return cache.match(request).then((matching) => {
                if (!matching || matching.status == 404)
                {
                    return this.offlineFallback(cache);
                }
                return matching;
            });
        });
    }

    offlineFallback(cache) {
        if (this.offlinePage !== '')
        {
            return cache.match(this.offlinePage);
        }
        return Promise.reject('no-match');
    }

    notificationTitle(payload)
    {
        let title = this.pageTitle;
        if (typeof payload.title === 'string')
        {
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