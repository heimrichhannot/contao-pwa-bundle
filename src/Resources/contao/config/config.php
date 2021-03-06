<?php

use HeimrichHannot\ContaoPwaBundle\EventListener\Contao\LoadDataContainerListener;
use HeimrichHannot\ContaoPwaBundle\EventListener\InitializeSystemListener;
use HeimrichHannot\ContaoPwaBundle\FrontendModule\PushSubscriptionPopupFrontendModule;

/**
 * Backend modules
 */
$GLOBALS['BE_MOD']['system']['huh_pwa_configurations'] = [
	'tables' => ['tl_pwa_configurations', 'tl_pwa_pushsubscriber', 'tl_pwa_pushnotifications']
];



/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['initializeSystem']['huh_pwa'] = [InitializeSystemListener::class, '__invoke'];
$GLOBALS['TL_HOOKS']['loadDataContainer']['huh_pwa'] = [LoadDataContainerListener::class, '__invoke'];

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_pwa_configurations'] = \HeimrichHannot\ContaoPwaBundle\Model\PwaConfigurationsModel::class;
$GLOBALS['TL_MODELS']['tl_pwa_pushsubscriber'] = \HeimrichHannot\ContaoPwaBundle\Model\PwaPushSubscriberModel::class;
$GLOBALS['TL_MODELS']['tl_pwa_pushnotifications'] = \HeimrichHannot\ContaoPwaBundle\Model\PwaPushNotificationsModel::class;

/**
 * Content Elements
 */
$GLOBALS['TL_CTE']['links'][\HeimrichHannot\ContaoPwaBundle\ContentElement\PushSubscriptionElement::TYPE] = \HeimrichHannot\ContaoPwaBundle\ContentElement\PushSubscriptionElement::class;

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


/**
 * Cronjobs
 */

$GLOBALS['TL_CRON']['monthly'][]    = ['huh.pwa.listener.commandscheduler', 'monthly'];
$GLOBALS['TL_CRON']['weekly'][]    = ['huh.pwa.listener.commandscheduler', 'weekly'];
$GLOBALS['TL_CRON']['daily'][]    = ['huh.pwa.listener.commandscheduler', 'daily'];
$GLOBALS['TL_CRON']['hourly'][]    = ['huh.pwa.listener.commandscheduler', 'hourly'];
$GLOBALS['TL_CRON']['minutely'][]    = ['huh.pwa.listener.commandscheduler', 'minutely'];
