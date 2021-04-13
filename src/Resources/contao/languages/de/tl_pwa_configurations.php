<?php

$lang = &$GLOBALS['TL_LANG']['tl_pwa_configurations'];

/**
 * Fields
 */
$lang['title'] = ["Titel","Name der Konfiguration"];
$lang['supportPush'] = ["Push-Benachrichtigungen unterstützen","Aktivieren Sie die Unterstützung für Push-Benachrichtigungen für Ihre Webanwendung."];
$lang['addDebugLog'] = ["Debug-Ausgabe aktivieren","Wenn aktiviert, werden umfangreiche Debug-Ausgaben in der Browserkonsole ausgegeben. Achtung: Der Serviceworker muss dazu neu erzeugt werden!"];
$lang['sendWithCron'] = ["Benachrichtigungen automatisch per Cron senden","Wenn aktiviert, werden Push-Benachrichtigungen automatisch per Cronjob im festgelegten Interval sendet"];
$lang['cronIntervall'] = ["Sende-Intervall","Intervall in welchem nach ungesendeten Push-Benachrichtigungen gesucht und diese gesendet werden sollen."];

$lang['serviceWorkerTemplate'] = ["Service Worker Template","Wählen Sie ein Service Worker Template aus. Nicht alle Templates unterstützen alle Funktionen."];
$lang['offlinePage'] = ["Offline Seite","Wählen Sie eine Seite aus, welche angezeigt werden soll, wenn der Nutzer offline ist und eine nicht-gecachte Seite aufruft. Nicht alle Service Worker Templates unterstützen diese Funktion."];

$lang['pwaName'] = ["Name der Webanwendung","Wählen Sie hier aus, wie der Name der Webanwendung erzeugt werden soll. "];
$lang['pwaName'][\HeimrichHannot\ContaoPwaBundle\Model\PwaConfigurationsModel::PWA_NAME_PAGETITLE] = "Seitenname";
$lang['pwaName'][\HeimrichHannot\ContaoPwaBundle\Model\PwaConfigurationsModel::PWA_NAME_META_PAGETITLE] = "Seitentitel (Metadaten)";
$lang['pwaName'][\HeimrichHannot\ContaoPwaBundle\Model\PwaConfigurationsModel::PWA_NAME_CUSTOM] = "Individuell";
$lang['pwaCustomName'] = ["Individueller Name","Geben Sie hier einen individuellen Namen für Ihre Webanwendung an."];
$lang['pwaShortName'] = ["Kurz-Name","Geben Sie hier einen kurzem Namen für Ihre Webanwendung ein, welcher bspw. unter dem Icon auf dem Homescreen eines Smartphones angezeigt wird."];
$lang['pwaBackgroundColor'] = ["Hintergrundfarbe","Geben Sie hier die Hintergrund-Farbe Ihrer Website an, damit diese bspw. bereits beim Laden angezeigt werden kann."];
$lang['pwaThemeColor'] = ["Theme-Farbe","Geben Sie hier die Theme-Farbe Ihrer Website an, damit diese bspw. bereits beim Laden angezeigt werden kann."];
$lang['pwaDescription'] = ["Beschreibung","Geben Sie hier eine Beschreibung Ihrer Webanwendung an."];
$lang['pwaDirection'] = ["Textrichtung","Geben Sie hier die primäre Textrichtung Ihrer Webanwendung an."];
$lang['pwaDisplay'] = ["Anzeigemodus","Geben Sie hier bevorzugten Anzeigemodus für die Webanwendung an."];
$lang['pwaIcons'] = ["Icon","Geben Sie hier Icon für die Webanwendung an, welches in verschiedenen Kontexten genutzt wird (bspw. bei Hinzufügen Ihrer Webanwendung auf den Homescreen eines Smartphones)."];
$lang['pwaOrientation'] = ["Standartausrichtung","Geben Sie hier Icon für die Ausrichtung an, welches Ihre Webanwendung in verschiedenen Kontexten standardmäßig nutzen soll."];
$lang['pwaStartUrl'] = ["Start-URL","Geben Sie die URL an, die geladen wird, wenn ein Benutzer die Anwendung von einem Gerät startet."];
$lang['pwaScope'] = ["Navigationsbereich","Geben Sie den Navigationsbereich des Anwendungskontextes dieser Webanwendung. Dies beschränkt grundsätzlich, welche Webseiten angezeigt werden können, während das Manifest angewendet wird. Wenn der Benutzer die Anwendung außerhalb des Gültigkeitsbereichs navigiert, kehrt er zu einer normalen Webseite zurück."];
$lang['pwaPreferRelatedApplication'] = ["Verwandte Anwendungen bevorzugen","Geben Sie hier an, dass der Benutzeragent dem Benutzer mitteilen soll, dass die angegebenen verwandten Anwendungen verfügbar sind und über die Webanwendung empfohlen werden. Dies sollte nur verwendet werden, wenn die verwandten nativen Apps wirklich etwas bieten, das die Webanwendung nicht machen kann."];
$lang['pwaRelatedApplications'] = ["Verwandte Anwendungen","Geben Sie hier verwandte Anwendungen an, welche bspw. im AppStore oder Play Store zu finden sind."];
$lang['pwaRelatedApplications_plattform'] = ["Plattform","Die Plattform, auf der die Anwendung gefunden werden kann."];
$lang['pwaRelatedApplications_url'] = ["URL","Die URL, bei der die Anwendung gefunden werden kann."];
$lang['pwaRelatedApplications_id'] = ["ID","Die ID, die verwendet wird, um die Anwendung auf der angegebenen Plattform darzustellen."];


/**
 * Legends
 */

$lang['general_legend'] = 'Allgemein';
$lang['manifest_legend'] = 'Manifest';
$lang['serviceworker_legend'] = 'Service-Worker';

/**
 * Buttons
 */
$lang['new']    = ['Neue PWA Konfiguration', 'PWA Konfiguration erstellen'];
$lang['edit']   = ['PWA Konfiguration bearbeiten', 'PWA Konfiguration ID %s bearbeiten'];
$lang['copy']   = ['PWA Konfiguration duplizieren', 'PWA Konfiguration ID %s duplizieren'];
$lang['delete'] = ['PWA Konfiguration löschen', 'PWA Konfiguration ID %s löschen'];
$lang['toggle'] = ['PWA Konfiguration veröffentlichen', 'PWA Konfiguration ID %s veröffentlichen/verstecken'];
$lang['show']   = ['PWA Konfiguration Details', 'PWA Konfiguration-Details ID %s anzeigen'];
$lang['control']   = ['Verwaltung', 'Verwaltungswerkzeuge für Ihre Progressive Web App(s)'];
$lang['subscriber']   = ['Abonnenten', 'Abonnenten der Push Notifications für die Konfiguration'];
$lang['pushNotifications']   = ['Nachrichten', 'Push-Nachrichten der Konfiguration'];