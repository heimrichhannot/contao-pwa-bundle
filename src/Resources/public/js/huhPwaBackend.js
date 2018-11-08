let HuhPwaBackend = {
    unsentCountRoute: './pwa/pushnotification/unsent',
    sendNotificationRoute: './pwa/pushnotification/send',
    unsentNotificationRequest: (url) => {
        return new Request.JSON({
            url: url,
            method: 'get',
            onSuccess: (response) => {
                HuhPwaBackend.addLogEntry('Found ' + response.count);
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
                HuhPwaBackend.addLogEntry('Found ' + response.count);
                // return response;
            },
            onFailure: (xhr) => {
                console.log("Error", xhr);
                HuhPwaBackend.addLogEntry('Error sending notification: ' + xhr);
            },
            onCancel: () => {
                HuhPwaBackend.addLogEntry('Sending Notification canceled');
            },
            onException: (headerName, value) => {
                HuhPwaBackend.addLogEntry('Exception while sending notification: ' + headerName);
            },
        });
    },
    sendPushNotifications: function (button) {
        this.button = button;
        // button.disabled = true;
        if (null != (logger = document.querySelector('#huhPwaSendPushNotificationStatus')))
        {
           let request = this.unsentNotificationRequest(this.unsentCountRoute);
           request.send(this.unsentCountRoute).then((response) => {
               console.log(response);
               console.log("Response succeeded");
               if (response.json.count > 0)
               {
                   response.json.notifications.forEach((notification) => {
                       let sendRequest = this.sendNotificationRequest(this.sendNotificationRoute);
                       sendRequest.post("notificationId=" + notification).then((sendResponse) => {
                           let failCount = 0;
                           sendResponse.json.result.forEach((element) => {
                               if (element.success === false)
                               {
                                   failCount++;
                               }
                           });
                           this.addLogEntry("Sent notification with id " + notification + ": Sent " + sendResponse.json.sentCount + " messages, got " + failCount + " errors.");
                       });
                   });
               }
           });
           this.addLogEntry("Finished");

            // var node = document.createElement('span');
            //
            // logger.appendChild().
        }
    },
    addLogEntry: function(text) {
        if (null == this.logger)
        {
            if (null != (logger = document.querySelector('#huhPwaSendPushNotificationStatus')))
            {
                this.logger = logger;
            }
        }
        let node = document.createElement('span');
        let textNode = document.createTextNode(text);
        node.appendChild(textNode);
        this.logger.appendChild(node);
        console.log('[PWA BACKEND] addLogEntry: ' + text);
    }
};

// window.addEvent('domready', function() {
//     if (null != (sendButton = document.querySelector('#huhPwaSendPushNotificationButton')))
//     {
//         console.log("Found button");
//         sendButton.addEventListener('click', function(event) {
//             let button = event.srcElement;
//             HuhPwaBackend.sendPushNotifications(button);
//         })
//     }
// });