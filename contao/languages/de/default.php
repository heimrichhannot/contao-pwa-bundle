<?php

use HeimrichHannot\PwaBundle\Controller\ContentElement\PushSubscriptionElement;
use HeimrichHannot\PwaBundle\Controller\ContentElement\InstallPwaButtonElementController;

$lang = &$GLOBALS['TL_LANG'];

/**
 * Content Elements
 */
$lang['CTE']['pwa'] = ['Progressive Web App (PWA)'];
$lang['CTE'][InstallPwaButtonElementController::TYPE] = ['PWA installieren Button'];
$lang['CTE'][PushSubscriptionElement::TYPE] = ['Push Notification Abonnieren Button'];

/**
 * Erros
 */
$lang['ERR']['huhPwaGenerateManifest'] = "Es gab einen Fehler beim generieren der Manifest-Datei für die aktuelle Seite: %error%";
