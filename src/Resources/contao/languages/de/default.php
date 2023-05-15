<?php

use HeimrichHannot\ContaoPwaBundle\ContentElement\PushSubscriptionElement;
use HeimrichHannot\ContaoPwaBundle\Controller\ContentElement\InstallPwaButtonElementController;

$lang = &$GLOBALS['TL_LANG'];

/**
 * Content Elements
 */
$lang['CTE'][InstallPwaButtonElementController::TYPE][0] = 'PWA installieren Button';
$lang['CTE'][PushSubscriptionElement::TYPE]           = ['Push Notification Abonnieren Button'];
$lang['CTE']['pwa']           = ['Progressive Web App (PWA)'];

/**
 * Erros
 */
$lang['ERR']['huhPwaGenerateManifest'] = "Es gab einen Fehler beim generieren der Manifest-Datei für die aktuelle Seite: %error%";

