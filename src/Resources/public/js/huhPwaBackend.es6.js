var HuhPwaBackend = {
    unsentCountRoute: '/pwa/pushnotification/unsent',
    backendRoute: '/pwa/pushnotification/send',
    sendPushNotifications: function(button) {
        button.disabled = true;
        if (null != (logger = document.querySelector('#huhPwaSendPushNotificationStatus')))
        {

            // var node = document.createElement('span');
            //
            // logger.appendChild().
        }
    },
};

window.addEvent('domready', function() {
    if (null != (sendButton = document.querySelector('#huhPwaSendPushNotificationButton')))
    {
        console.log("Found button");
        sendButton.addEventListener('click', function(event) {
            button = event.srcElement;
            HuhPwaBackend.sendPushNotifications(button);
        })
    }
});