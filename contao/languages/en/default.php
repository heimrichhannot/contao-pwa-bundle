<?php

use HeimrichHannot\PwaBundle\Controller\ContentElement\PushSubscriptionElement;
use HeimrichHannot\PwaBundle\Controller\ContentElement\InstallPwaButtonElementController;

$lang = &$GLOBALS['TL_LANG'];

/**
 * Content Elements
 */
$lang['CTE']['pwa'] = ['Progressive Web App (PWA)'];
$lang['CTE'][InstallPwaButtonElementController::TYPE] = ['PWA install button'];
$lang['CTE'][PushSubscriptionElement::TYPE] = ['Push Notification Subscribe Button'];

/**
 * Erros
 */
$lang['ERR']['huhPwaGenerateManifest'] = "There was an error while generating the manifest file for the current page: %error%";
