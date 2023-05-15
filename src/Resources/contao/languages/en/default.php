<?php

use HeimrichHannot\ContaoPwaBundle\ContentElement\PushSubscriptionElement;
use HeimrichHannot\ContaoPwaBundle\Controller\ContentElement\InstallPwaButtonElementController;

$lang = &$GLOBALS['TL_LANG'];

/**
 * Content Elements
 */
$lang['CTE'][InstallPwaButtonElementController::TYPE][0] = 'PWA install button';
$lang['CTE'][PushSubscriptionElement::TYPE]              = ['Push Notification Subscribe Button'];
$lang['CTE']['pwa']           = ['Progressive Web App (PWA)'];

/**
 * Erros
 */
$lang['ERR']['huhPwaGenerateManifest'] = "There was an error while generating the manifest file for the current page: %error%";