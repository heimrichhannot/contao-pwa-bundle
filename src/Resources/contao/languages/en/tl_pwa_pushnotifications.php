<?php

$lang = &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications'];

/**
 * Fields
 */
$lang['title'] = ["Title","The title of the notification."];
$lang['body'] = ["Content","A short content text for the notification."];
$lang['icon'] = ["Icon","The image should be shown next to the notification."];
$lang['iconSize'] = ["Icon size","Size of the image."];
$lang['sendDate'] = ["Send date","The the, when the notification should be sent."];
$lang['sent'] = ["Sent","Select if message is already sent."];
$lang['receiverCount'] = ["Recipient count","The number of recipients the message was sent."];
$lang['clickEvent'] = ["Notification click behavior","Select what to do if the notification is clicked."];
$lang['clickEvent'][\HeimrichHannot\ContaoPwaBundle\DataContainer\PwaPushNotificationContainer::CLICKEVENT_OPEN_PAGE] = "Open page";
$lang['clickJumpTo'] = ["Jump to page","Select the page that should be opened when the notification is clicked."];


/**
 * Legends
 */

$lang['message_legend'] = 'Notification content';
$lang['behavior_legend'] = 'Notification behavior';
$lang['send_legend'] = 'Send notification';

/**
 * Buttons
 */

$lang['new']    = ['New Notification', 'Create a new push notification'];
$lang['edit']   = ['Edit notification', 'Edit push notification with ID %s'];
$lang['copy']   = ['Duplicate notification', 'Duplication push notification with ID %s'];
$lang['delete'] = ['Delete notification', 'Delete push notification with ID %s'];
$lang['show']   = ['Notification details', 'Show details of push notification with ID %s'];