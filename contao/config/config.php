<?php

use HeimrichHannot\PwaBundle\ContentElement\PushSubscriptionElement;
use HeimrichHannot\PwaBundle\FrontendModule\PushSubscriptionPopupFrontendModule;
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
 * Content Elements
 */
$GLOBALS['TL_CTE']['pwa'][PushSubscriptionElement::TYPE] = PushSubscriptionElement::class;

/**
 * Frontend Modules
 */
$GLOBALS['FE_MOD']['miscellaneous'][PushSubscriptionPopupFrontendModule::TYPE] = PushSubscriptionPopupFrontendModule::class;
