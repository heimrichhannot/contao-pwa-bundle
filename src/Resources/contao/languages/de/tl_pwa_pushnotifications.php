<?php

$lang = &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications'];

/**
 * Fields
 */
$lang['title'] = ["Titel","Titel der Banachrichtigung"];
$lang['body'] = ["Inhalt","Kurzer Inhaltstext der Banchrichtigung"];
$lang['icon'] = ["Icon","Das Bild das neben der Banchrichtigung angezeigt werden soll."];
$lang['iconSize'] = ["Icon-Bildgröße","Geben Sie hier die Bildgröße an, welche das Icon haben soll."];
$lang['sent'] = ["Gesendet","Geben Sie hier an, ob die Nachricht bereits gesendet wurde."];
$lang['receiverCount'] = ["Anzahl Empfänger","Geben Sie hier die Anzahl der Empfänger an, an welche die Nachricht gesendet wurde."];
$lang['clickEvent'] = ["Verhalten beim Klick auf die Benachrichtigung","Geben Sie hier an, was bei einem Klick auf die Benachrichtigung passieren soll."];
$lang['clickEvent'][\HeimrichHannot\ContaoPwaBundle\DataContainer\PwaPushNotificationContainer::CLICKEVENT_OPEN_PAGE] = "Seite öffnen";
$lang['clickJumpTo'] = ["Ziel-Seite","Geben Sie hier an, welche Seite beim Klick auf die Benachrichtigung geöffnet werden soll."];
$lang['published'] = ["Benachrichtigung veröffentlichen","Die Push-Benachrichtigung wird zum Versand freigegeben."];
$lang['start'] = ["Versand ab","Die Push-Benachrichtigung erst ab diesem Zeitpunkt senden."];
$lang['dateSent'] = ["Sendedatum","Das Datum, an dem die Benachrichtigung gesendet wurde."];


/**
 * Legends
 */

$lang['message_legend'] = 'Inhalt der Banachrichtigung';
$lang['behavior_legend'] = 'Verhalten der Banachrichtigung';
$lang['publish_legend'] = 'Veröffentlichung';

/**
 * Buttons
 */

$lang['new']    = ['Neue Benachrichtigung', 'Push-Benachrichtigung erstellen'];
$lang['edit']   = ['Benachrichtigung bearbeiten', 'Push-Benachrichtigung ID %s bearbeiten'];
$lang['copy']   = ['Benachrichtigung duplizieren', 'Push-Benachrichtigung ID %s duplizieren'];
$lang['delete'] = ['Benachrichtigung löschen', 'Push-Benachrichtigung ID %s löschen'];
$lang['show']   = ['Benachrichtigung Details', 'Push-Benachrichtigung-Details ID %s anzeigen'];