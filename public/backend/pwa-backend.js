let HuhPwaBackend = {
    unsentCountRoute: './contao/pwa/pushnotification/unsent',
    sendNotificationsRoute: './contao/pwa/pushnotification/send',
    findPagesRoute: './contao/pwa/pages',
    updatePageRoute: '',
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
    rebuildFiles: function() {
        this.logger = document.querySelector('#huhPwaRebuildFilesStatus');
        this.logger.innerHTML = '';
        url = this.findPagesRoute;
        let request = new Request.JSON({
            url: url,
            method: 'get',
            onFailure: () => {
                HuhPwaBackend.addLogEntry('findPages failure');
            },
            onCancel: () => {
                HuhPwaBackend.addLogEntry('findPages cancel');
            },
            onException: (headerName, value) => {
                HuhPwaBackend.addLogEntry('findPages Exception: ' + headerName);
            },
        });
        request.send(this.findPagesRoute).then((response) => {

            if (response.json.length < 1)
            {
                HuhPwaBackend.addLogEntry('No pages with PWA configuration found.');
                return;
            }
            HuhPwaBackend.addLogEntry('Found ' + response.json.length + ' page(s)').then(() => {
                let updateRequest = new Request.JSON({
                    url: this.updatePageRoute,
                    method: 'post',
                    onFailure: (xhr) => {
                        console.log('Error: ', xhr);
                        HuhPwaBackend.addLogEntry('Error update page files: ' + xhr.responseText);
                    },
                    onCancel: () => {
                        HuhPwaBackend.addLogEntry('Update page files canceled');
                    },
                    onException: (headerName, value) => {
                        HuhPwaBackend.addLogEntry('Exception while update page files: ' + headerName);
                    },
                });

                let promises = [];
                response.json.forEach((page) => {
                    promises.push(updateRequest.post('pageId=' + page.id).then(() => {
                        return HuhPwaBackend.addLogEntry("Updated manifest and serviceworker for page '" + page.name + "' (ID: " + page.id + ")");
                    }));
                });
                Promise.all(promises).then(() =>{
                    return HuhPwaBackend.addLogEntry("Finished generating page files.");
                });
            });
        });
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