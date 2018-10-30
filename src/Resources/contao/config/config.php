<?php

/**
 * Backend modules
 */
$GLOBALS['BE_MOD']['system']['huh_pwa'] = [
	'tables' => ['tl_pwa_subscriber']
];


$GLOBALS['TL_HOOKS']['generatePage']['huh.pwa'] = ['huh.pwa.listener.hook', 'onGeneratePage'];
$GLOBALS['TL_HOOKS']['getUserNavigation'][] = ['huh.pwa.listener.usernavigation', 'onGetUserNavigation'];


$GLOBALS['TL_MODELS']['tl_pwa_subscriber'] = \HeimrichHannot\ContaoPwaBundle\Model\PwaSubscriberModel::class;
