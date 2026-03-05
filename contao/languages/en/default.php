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
$lang['CTE'][InstallPwaButtonElementController::TYPE] = ['PWA install button'];
$lang['CTE'][PushSubscriptionElement::TYPE] = ['Push Notification Subscribe Button (PWA)'];
$lang['CTE'][PushSubscriptionPopupElementController::TYPE] = ['Push Notification Subscribe Popup (PWA)'];
$lang['CTE'][OfflinePagesElementController::TYPE] = ['Offline pages list (PWA)'];

/**
 * Errors
 */
$lang['ERR']['huhPwaGenerateManifest'] = "There was an error while generating the manifest file for the current page: %error%";
