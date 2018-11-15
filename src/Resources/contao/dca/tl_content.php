<?php

$dc = &$GLOBALS['TL_DCA']['tl_content'];
$dc['palettes'][\HeimrichHannot\ContaoPwaBundle\ContentElement\SubscribeButtonElement::TYPE] =
	'{type_legend},type,headline;{template_legend:hide},pwaSubscribeButtonTemplate;{protected_legend:hide},protected;{expert_legend:hide},guests;{invisible_legend:hide},invisible,start,stop';

$dc['fields']['pwaSubscribeButtonTemplate'] = [
	'label'            => &$GLOBALS['TL_LANG']['tl_content']['pwaSubscribeButtonTemplate'],
	'exclude'          => true,
	'inputType'        => 'select',
	'options_callback' => ['huh.pwalist.choice.template.item', 'getPwaSubscriptionButtonTemplate'],
	'eval'             => ['tl_class' => 'w50 clr', 'includeBlankOption' => true],
	'sql'              => "varchar(128) NOT NULL default ''",
];
