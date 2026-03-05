<?php

use HeimrichHannot\PwaBundle\Controller\ContentElement\PushSubscriptionElement;
use HeimrichHannot\PwaBundle\Controller\ContentElement\InstallPwaButtonElementController;
use HeimrichHannot\PwaBundle\Controller\ContentElement\OfflinePagesElementController;
use HeimrichHannot\PwaBundle\Controller\ContentElement\PushSubscriptionPopupElementController;

$lang = &$GLOBALS['TL_LANG'];

/**
 * Content Elements
 */
$lang['CTE']['pwa'] = ['Progressive Web App (PWA)'];
$lang['CTE'][InstallPwaButtonElementController::TYPE] = ['PWA installieren Button'];
$lang['CTE'][PushSubscriptionElement::TYPE] = ['Push Benachrichtigung abonnieren Button (PWA)'];
$lang['CTE'][PushSubscriptionPopupElementController::TYPE] = ['Push Benachrichtigung abonnieren Popup (PWA)'];
$lang['CTE'][OfflinePagesElementController::TYPE] = ['Offline-Seitenliste (PWA)'];

/**
 * Errors
 */
$lang['ERR']['huhPwaGenerateManifest'] = "Es gab einen Fehler beim generieren der Manifest-Datei für die aktuelle Seite: %error%";
