let HuhPwaBackend = {
    unsentCountRoute: './contao/pwa/pushnotification/unsent',
    sendNotificationsRoute: './contao/pwa/pushnotification/send',
    findPagesRoute: './contao/pwa/pages',
    updatePageRoute: '',
    requestToken: '',
    unsentNotificationRequest: (url) => {
        return new Request.JSON({
            url: url,
            method: 'get',
            onSuccess: (response) => {
                HuhPwaBackend.addLogEntry('Found ' + response.count + ' unsent notifications');
                // return response;
            },
            onFailure: () => {
                HuhPwaBackend.addLogEntry('failure');
            },
            onCancel: () => {
                HuhPwaBackend.addLogEntry('cancel');
            },
            onException: (headerName, value) => {
                HuhPwaBackend.addLogEntry('Exception: ' + headerName);
            },
        });
    },
    sendNotificationRequest: (url) => {
        return new Request.JSON({
            url: url,
            method: 'post',
            onSuccess: (response) => {
                // HuhPwaBackend.addLogEntry('Found ' + response.count);
                // return response;
            },
            onFailure: (xhr) => {
                console.log('Error: ', xhr);
                HuhPwaBackend.addLogEntry('Error sending notification: ' + xhr.statusText);
            },
            onCancel: () => {
                HuhPwaBackend.addLogEntry('Sending Notification canceled');
            },
            onException: (headerName, value) => {
                HuhPwaBackend.addLogEntry('Exception while sending notification: ' + headerName);
            },
        });
    },
    sendPushNotifications: function(button) {
        this.button = button;
        this.button.disabled = true;
        this.logger = document.querySelector('#huhPwaSendPushNotificationStatus');
        this.logger.innerHTML = '';

        let request = this.unsentNotificationRequest(this.unsentCountRoute);
        request.send(this.unsentCountRoute).then((response) => {
            if (response.json.count > 0) {
                let promises = [];
                response.json.notifications.forEach((notification) => {
                    let sendRequest = this.sendNotificationRequest(this.sendNotificationsRoute);
                    promises.push(sendRequest.post('notificationId=' + notification).then((sendResponse) => {
                        if (sendResponse.json.success === false) {
                            return this.addLogEntry('Error sending notification with id ' + notification + ': ' + sendResponse.json.message);
                        } else {
                            let failCount = 0;
                            if (Array.isArray(sendResponse.json.result) === true) {
                                sendResponse.json.result.forEach((element) => {
                                    if (element.success === false) {
                                        failCount++;
                                    }
                                });
                            } else {
                                failCount = (sendResponse.json.sentCount - sendResponse.json.successCount);
                            }

                            return this.addLogEntry('Sent notification with id ' + notification + ': Sent ' + sendResponse.json.sentCount + ' messages, got ' + failCount + ' errors.');
                        }
                    }));
                });
                Promise.all(promises).then(() => {
                    this.addLogEntry('Done').then(() => {
                        this.button.disabled = false;
                    });
                });

            }
            else {
                this.addLogEntry('Done').then(() => {
                    this.button.disabled = false;
                });
            }
        }).catch(()=> {
            this.button.disabled = false;
        });
    },
    rebuildFiles: async function(button) {
        this.logger = document.querySelector('#huhPwaRebuildFilesStatus');
        this.logger.innerHTML = '';
        button.disabled = true;

        try {
            const findPagesResponse = await fetch(this.findPagesRoute, {
                headers: {
                    'Accept': 'application/json',
                },
            });

            if (!findPagesResponse.ok) {
                throw new Error('Finding pages failed: ' + findPagesResponse.statusText);
            }

            const pages = await findPagesResponse.json();

            if (pages.length < 1) {
                await this.addLogEntry('No pages with PWA configuration found.');
                return;
            }

            await this.addLogEntry('Found ' + pages.length + ' page(s)');

            for (const page of pages) {
                const updatePageResponse = await fetch(this.updatePageRoute, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: new URLSearchParams({
                        pageId: page.id,
                        REQUEST_TOKEN: this.requestToken,
                    }),
                });

                if (!updatePageResponse.ok) {
                    const message = await updatePageResponse.text();
                    throw new Error('Updating page files failed: ' + (message || updatePageResponse.statusText));
                }

                await this.addLogEntry("Updated manifest and serviceworker for page '" + page.name + "' (ID: " + page.id + ")");
            }

            await this.addLogEntry('Finished generating page files.');
        } catch (error) {
            await this.addLogEntry(error.message);
        } finally {
            button.disabled = false;
        }
    },
    addLogEntry: function(text) {
        return new Promise((resolve, reject) => {
            if (null == this.logger) {
                console.log("No logger defined.");
                console.log('[PWA BACKEND] addLogEntry: ' + text);

            }
            else {
                let node = document.createElement('div');
                let textNode = document.createTextNode(text);
                node.appendChild(textNode);
                this.logger.appendChild(node);
            }
            resolve();
        });
    },
};

document.addEventListener('click', (event) => {
    const button = event.target.closest('button');

    if (!button) {
        return;
    }

    if (button.id === 'huhPwaSendPushNotificationButton') {
        HuhPwaBackend.unsentCountRoute = button.dataset.unsentCountRoute;
        HuhPwaBackend.sendNotificationsRoute = button.dataset.sendNotificationsRoute;
        HuhPwaBackend.sendPushNotifications(button);
    }

    if (button.id === 'huhPwaRebuildFilesButton') {
        HuhPwaBackend.findPagesRoute = button.dataset.findPagesRoute;
        HuhPwaBackend.updatePageRoute = button.dataset.updatePageRoute;
        HuhPwaBackend.requestToken = button.dataset.requestToken;
        HuhPwaBackend.rebuildFiles(button);
    }
});

// Reset transient state before Turbo caches the page (Contao 5.7+ backend),
// so the back button never restores a disabled button or stale log output.
document.addEventListener('turbo:before-cache', () => {
    document.querySelectorAll('#huhPwaSendPushNotificationButton, #huhPwaRebuildFilesButton')
        .forEach((button) => { button.disabled = false; });
    document.querySelectorAll('#huhPwaSendPushNotificationStatus, #huhPwaRebuildFilesStatus')
        .forEach((status) => { status.innerHTML = ''; });
});
