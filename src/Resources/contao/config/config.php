<?php

use HeimrichHannot\ContaoPwaBundle\ContentElement\PushSubscriptionElement;
use HeimrichHannot\ContaoPwaBundle\FrontendModule\PushSubscriptionPopupFrontendModule;
use HeimrichHannot\ContaoPwaBundle\Model\PwaConfigurationsModel;
use HeimrichHannot\ContaoPwaBundle\Model\PwaPushNotificationsModel;
use HeimrichHannot\ContaoPwaBundle\Model\PwaPushSubscriberModel;

/**
 * Backend modules
 */
$GLOBALS['BE_MOD']['system']['huh_pwa_configurations'] = [
	'tables' => ['tl_pwa_configurations', 'tl_pwa_pushsubscriber', 'tl_pwa_pushnotifications']
];

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_pwa_configurations'] = PwaConfigurationsModel::class;
$GLOBALS['TL_MODELS']['tl_pwa_pushsubscriber'] = PwaPushSubscriberModel::class;
$GLOBALS['TL_MODELS']['tl_pwa_pushnotifications'] = PwaPushNotificationsModel::class;

/**
 * Content Elements
 */
$GLOBALS['TL_CTE']['pwa'][PushSubscriptionElement::TYPE] = PushSubscriptionElement::class;

/**
 * Frontend Modules
 */

$GLOBALS['FE_MOD']['miscellaneous'][PushSubscriptionPopupFrontendModule::TYPE] = PushSubscriptionPopupFrontendModule::class;


/**
 * Assets
 */
if (TL_MODE == 'BE') {
	$GLOBALS['TL_JAVASCRIPT']['huh.pwa.backend'] = 'bundles/heimrichhannotcontaopwa/js/contao-pwa-backend.js';
	$GLOBALS['TL_CSS']['huh.pwa.backend'] = 'bundles/heimrichhannotcontaopwa/css/contao-pwa-backend.css';
}
else {
    $GLOBALS['TL_JAVASCRIPT']['huh.pwa.bundle'] = 'bundles/heimrichhannotcontaopwa/js/contao-pwa-bundle.js';
}
