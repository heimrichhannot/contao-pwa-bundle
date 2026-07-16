<?php

return [
    'tl_pwa_pushsubscriber' => [
        'lastSuccessfulSend' => ['Last successful send', 'Date and time when a push notification was last sent successfully to this subscriber.'],
        'member' => ['Member', 'The frontend member associated with the push subscription.'],
        'endpoint' => ['Endpoint', 'Push subscription endpoint.'],
        'publicKey' => ['Public key', 'Push subscription public key.'],
        'authToken' => ['Authentication token', 'Push subscription authentication token.'],
        'delete' => ['Delete subscription', 'Delete push notification subscription with ID %s'],
        'show' => ['Subscription details', 'Show push notification subscription with ID %s'],
    ],
];
