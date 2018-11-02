// function getEndpoint() {
//     return self.registration.pushManager.getSubscription()
//     .then(function(subscription) {
//         if (subscription) {
//             return subscription.endpoint;
//         }
//
//         throw new Error('User not subscribed');
//     });
// }

self.addEventListener('push', function(event) {
    let payload = event.data ? event.data.text() : "No payload";
    console.log(payload);
    event.waitUntil(
        self.registration.showNotification("Example Page", JSON.parse(payload))
    );

    // const promiseChain = self.registration.showNotification('Hello, World.');
    // event.waitUntil(promiseChain);


    // event.waitUntil(
    //     getEndpoint()
    //     .then(function(endpoint) {
    //         return fetch('./getPayload?endpoint=' + endpoint);
    //     })
    //     .then(function(response) {
    //         return response.text();
    //     })
    //     .then(function(payload) {
    //         self.registration.showNotification('ServiceWorker Cookbook', {
    //             body: payload,
    //         });
    //     })
    // );
});