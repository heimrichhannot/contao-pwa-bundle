<?php

return [
    'tl_pwa_pushnotifications' => [
        'title' => ['Titel', 'Titel der Banachrichtigung'],
        'body' => ['Inhalt', 'Kurzer Inhaltstext der Banchrichtigung'],
        'icon' => ['Icon', 'Das Bild das neben der Banchrichtigung angezeigt werden soll.'],
        'iconSize' => ['Icon-Bildgröße', 'Geben Sie hier die Bildgröße an, welche das Icon haben soll.'],
        'sent' => ['Gesendet', 'Geben Sie hier an, ob die Nachricht bereits gesendet wurde.'],
        'receiverCount' => ['Anzahl Empfänger', 'Geben Sie hier die Anzahl der Empfänger an, an welche die Nachricht gesendet wurde.'],
        'clickEvent' => [
            'Verhalten beim Klick auf die Benachrichtigung',
            'Geben Sie hier an, was bei einem Klick auf die Benachrichtigung passieren soll.',
            'openPage' => 'Seite öffnen',
            'openUrl' => 'URL/News öffnen',
        ],
        'clickJumpTo' => ['Ziel-Seite', 'Geben Sie hier an, welche Seite beim Klick auf die Benachrichtigung geöffnet werden soll.'],
        'clickUrl' => ['Ziel-URL', 'Geben Sie hier an, welche URL beim Klick auf die Benachrichtigung geöffnet werden soll.'],
        'published' => ['Benachrichtigung veröffentlichen', 'Die Push-Benachrichtigung wird zum Versand freigegeben.'],
        'start' => ['Sendezeitpunkt', 'Wenn gesetzt, wird die Benachrichtigung erst ab diesem Zeitpunkt versendet.'],
        'dateSent' => ['Sendedatum', 'Das Datum, an dem die Benachrichtigung gesendet wurde.'],
        'message_legend' => 'Inhalt der Banachrichtigung',
        'behavior_legend' => 'Verhalten der Banachrichtigung',
        'publish_legend' => 'Veröffentlichung',
        'new' => ['Neue Benachrichtigung', 'Push-Benachrichtigung erstellen'],
        'edit' => ['Benachrichtigung bearbeiten', 'Push-Benachrichtigung ID %s bearbeiten'],
        'copy' => ['Benachrichtigung duplizieren', 'Push-Benachrichtigung ID %s duplizieren'],
        'delete' => ['Benachrichtigung löschen', 'Push-Benachrichtigung ID %s löschen'],
        'show' => ['Benachrichtigung Details', 'Push-Benachrichtigung-Details ID %s anzeigen'],
    ],
];
