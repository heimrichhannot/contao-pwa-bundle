<?php

return [
    'tl_pwa_pushnotifications' => [
        'title' => ['Title', 'The title of the notification.'],
        'body' => ['Content', 'A short content text for the notification.'],
        'icon' => ['Icon', 'The image should be shown next to the notification.'],
        'iconSize' => ['Icon size', 'Size of the image.'],
        'sent' => ['Sent', 'Select if message is already sent.'],
        'receiverCount' => ['Recipient count', 'The number of recipients the message was sent.'],
        'clickEvent' => [
            'Notification click behavior',
            'Select what to do if the notification is clicked.',
            'openPage' => 'Open page',
            'openUrl' => 'Open url/news',
        ],
        'clickJumpTo' => ['Jump to page', 'Select the page that should be opened when the notification is clicked.'],
        'clickUrl' => ['Target url', 'The url that should be opened when notification is clicked.'],
        'published' => ['Publish notification', 'Activate the delivery of the push notification.'],
        'start' => ['Send date', 'If set, the push notification will not be sent before this date.'],
        'dateSent' => ['Sent date', 'The date, when the notification was sent.'],
        'message_legend' => 'Notification content',
        'behavior_legend' => 'Notification behavior',
        'publish_legend' => 'Publish',
        'new' => ['New Notification', 'Create a new push notification'],
        'edit' => ['Edit notification', 'Edit push notification with ID %s'],
        'copy' => ['Duplicate notification', 'Duplication push notification with ID %s'],
        'delete' => ['Delete notification', 'Delete push notification with ID %s'],
        'show' => ['Notification details', 'Show details of push notification with ID %s'],
    ],
];
