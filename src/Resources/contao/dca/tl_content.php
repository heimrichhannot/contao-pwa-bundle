<?php

use HeimrichHannot\ContaoPwaBundle\ContentElement\PushSubscriptionElement;
use HeimrichHannot\ContaoPwaBundle\Controller\ContentElement\InstallPwaButtonElementController;

$dca = &$GLOBALS['TL_DCA']['tl_content'];

$dca['palettes'][PushSubscriptionElement::TYPE] =
	'{type_legend},type,headline;{template_legend:hide},pwaSubscribeButtonTemplate,customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests;{invisible_legend:hide},invisible,start,stop';
$dca['palettes'][InstallPwaButtonElementController::TYPE] = '{type_legend},type;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible;';

$dca['fields']['pwaSubscribeButtonTemplate'] = [
	'label'            => &$GLOBALS['TL_LANG']['tl_content']['pwaSubscribeButtonTemplate'],
	'exclude'          => true,
	'inputType'        => 'select',
	'options_callback' => ['huh.pwalist.choice.template.item', 'getPwaSubscriptionButtonTemplate'],
	'eval'             => ['tl_class' => 'w50 clr', 'includeBlankOption' => true],
	'sql'              => "varchar(128) NOT NULL default ''",
];
