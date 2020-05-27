<?php

$lang = &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications'];

/**
 * Fields
 */
$lang['title'] = ["Title","The title of the notification."];
$lang['body'] = ["Content","A short content text for the notification."];
$lang['icon'] = ["Icon","The image should be shown next to the notification."];
$lang['iconSize'] = ["Icon size","Size of the image."];
$lang['sent'] = ["Sent","Select if message is already sent."];
$lang['receiverCount'] = ["Recipient count","The number of recipients the message was sent."];
$lang['clickEvent'] = ["Notification click behavior","Select what to do if the notification is clicked."];
$lang['clickEvent'][\HeimrichHannot\ContaoPwaBundle\DataContainer\PwaPushNotificationContainer::CLICKEVENT_OPEN_PAGE] = "Open page";
$lang['clickEvent'][\HeimrichHannot\ContaoPwaBundle\DataContainer\PwaPushNotificationContainer::CLICKEVENT_OPEN_URL] = "Open url/news";
$lang['clickJumpTo'] = ["Jump to page","Select the page that should be opened when the notification is clicked."];
$lang['clickUrl'] = ["Target url","The url that should be opened when notification is clicked."];
$lang['published'] = ["Publish notification","Activate the delivery of the push notification."];
$lang['start'] = ["Send date","If set, the push notification will not be sent before this date."];
$lang['dateSent'] = ["Sent date","The date, when the notification was sent."];


/**
 * Legends
 */

$lang['message_legend'] = 'Notification content';
$lang['behavior_legend'] = 'Notification behavior';
$lang['publish_legend'] = 'Publish';

/**
 * Buttons
 */

$lang['new']    = ['New Notification', 'Create a new push notification'];
$lang['edit']   = ['Edit notification', 'Edit push notification with ID %s'];
$lang['copy']   = ['Duplicate notification', 'Duplication push notification with ID %s'];
$lang['delete'] = ['Delete notification', 'Delete push notification with ID %s'];
$lang['show']   = ['Notification details', 'Show details of push notification with ID %s'];