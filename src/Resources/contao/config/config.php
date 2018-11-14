<?php

/**
 * Backend modules
 */
$GLOBALS['BE_MOD']['system']['huh_pwa'] = [
	'tables' => ['tl_pwa_pushsubscriber']
];
$GLOBALS['BE_MOD']['system']['huh_pwa_configurations'] = [
	'tables' => ['tl_pwa_configurations']
];



/**
 * Hooks
 */
\HeimrichHannot\UtilsBundle\Arrays\ArrayUtil::insertBeforeKey(
	$GLOBALS['TL_HOOKS']['generatePage'],
	'huh.head-bundle',
	'huh.pwa',
	['huh.pwa.listener.hook', 'onGeneratePage']
);
$GLOBALS['TL_HOOKS']['getUserNavigation'][]     = ['huh.pwa.listener.usernavigation', 'onGetUserNavigation'];

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_pwa_configurations'] = \HeimrichHannot\ContaoPwaBundle\Model\PwaConfigurationsModel::class;
$GLOBALS['TL_MODELS']['tl_pwa_pushsubscriber'] = \HeimrichHannot\ContaoPwaBundle\Model\PwaPushSubscriberModel::class;
$GLOBALS['TL_MODELS']['tl_pwa_pushnotifications'] = \HeimrichHannot\ContaoPwaBundle\Model\PwaPushNotificationsModel::class;

/**
 * Content Elements
 */
$GLOBALS['TL_CTE']['links'][\HeimrichHannot\ContaoPwaBundle\ContentElement\SubscribeButtonElement::TYPE] = \HeimrichHannot\ContaoPwaBundle\ContentElement\SubscribeButtonElement::class;

/**
 * Assets
 */
if (TL_MODE == 'BE') {
	$GLOBALS['TL_JAVASCRIPT']['filecredits-be'] = 'bundles/heimrichhannotcontaopwa/js/huhPwaBackend.js';
}

/**
 * Cronjobs
 */

//$GLOBALS['TL_CRON']['monthly'][]    = ['HeimrichHannot\ContaoNewsAlertBundle\Components\Cronjob', 'monthly'];
//$GLOBALS['TL_CRON']['weekly'][]    = ['HeimrichHannot\ContaoNewsAlertBundle\Components\Cronjob', 'weekly'];
//$GLOBALS['TL_CRON']['daily'][]    = ['HeimrichHannot\ContaoNewsAlertBundle\Components\Cronjob', 'daily'];
//$GLOBALS['TL_CRON']['hourly'][]    = ['HeimrichHannot\ContaoNewsAlertBundle\Components\Cronjob', 'hourly'];
//$GLOBALS['TL_CRON']['minutely'][]    = ['HeimrichHannot\ContaoNewsAlertBundle\Components\Cronjob', 'minutely'];
