<?php

use HeimrichHannot\PwaBundle\Contao\FrontendModule\PushSubscriptionPopupModule;
use HeimrichHannot\PwaBundle\Model\PwaConfigurationsModel;
use HeimrichHannot\PwaBundle\Model\PwaPushNotificationsModel;
use HeimrichHannot\PwaBundle\Model\PwaPushSubscriberModel;

/**
 * Backend modules
 */
$GLOBALS['BE_MOD']['system']['huh_pwa_configurations'] = [
	'tables' => [
        PwaConfigurationsModel::getTable(),
        PwaPushSubscriberModel::getTable(),
        PwaPushNotificationsModel::getTable(),
    ],
];

/**
 * Models
 */
$GLOBALS['TL_MODELS'][PwaConfigurationsModel::getTable()] = PwaConfigurationsModel::class;
$GLOBALS['TL_MODELS'][PwaPushSubscriberModel::getTable()] = PwaPushSubscriberModel::class;
$GLOBALS['TL_MODELS'][PwaPushNotificationsModel::getTable()] = PwaPushNotificationsModel::class;

/**
 * Frontend Modules
 */
$GLOBALS['FE_MOD']['miscellaneous'][PushSubscriptionPopupModule::TYPE] = PushSubscriptionPopupModule::class;
