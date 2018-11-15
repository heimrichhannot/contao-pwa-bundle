<?php

$lang = &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications'];

/**
 * Fields
 */
$lang['title'] = ["Titel","Titel der Banachrichtigung"];
$lang['body'] = ["Inhalt","Kurzer Inhaltstext der Banchrichtigung"];
$lang['icon'] = ["Icon","Das Bild das neben der Banchrichtigung angezeigt wrden soll."];
$lang['iconSize'] = ["Icon-Bildgröße","Geben Sie hier die Bildgröße an, welche das Icon haben soll."];
$lang['sendDate'] = ["Sende-Datum","Geben Sie hier das Sendedatum an, an welchem die Benachrichtigung gesendet werden soll."];
$lang['sent'] = ["Gesendet","Geben Sie hier an, ob die Nachricht bereits gesendet wurde."];
$lang['receiverCount'] = ["Anzahl Empfänger","Geben Sie hier die Anzahl der Empfänger an, an welche die Nachricht gesendet wurde."];
$lang['clickEvent'] = ["Verhalten beim Klick auf die Benachrichtigung","Geben Sie hier an, was bei einem Klick auf die Benachrichtigung passieren soll."];
$lang['clickEvent'][\HeimrichHannot\ContaoPwaBundle\DataContainer\PwaPushNotificationContainer::CLICKEVENT_OPEN_PAGE] = "Seite öffnen";
$lang['clickJumpTo'] = ["Ziel-Seite","Geben Sie hier an, welche Seite beim Klick auf die Benachrichtigung geöffnet werden soll."];


/**
 * Legends
 */

$lang['message_legend'] = 'Inhalt der Banachrichtigung';
$lang['behavior_legend'] = 'Verhalten der Banachrichtigung';
$lang['send_legend'] = 'Senden der Banachrichtigung';

/**
 * Buttons
 */

$lang['new']    = ['Neue Benachrichtigung', 'Push-Benachrichtigung erstellen'];
$lang['edit']   = ['Benachrichtigung bearbeiten', 'Push-Benachrichtigung ID %s bearbeiten'];
$lang['copy']   = ['Benachrichtigung duplizieren', 'Push-Benachrichtigung ID %s duplizieren'];
$lang['delete'] = ['Benachrichtigung löschen', 'Push-Benachrichtigung ID %s löschen'];
$lang['show']   = ['Benachrichtigung Details', 'Push-Benachrichtigung-Details ID %s anzeigen'];