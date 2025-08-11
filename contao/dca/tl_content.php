<?php

use HeimrichHannot\PwaBundle\ContentElement\PushSubscriptionElement;
use HeimrichHannot\PwaBundle\Controller\ContentElement\InstallPwaButtonElementController;

$dca = &$GLOBALS['TL_DCA']['tl_content'];

$dca['palettes'][PushSubscriptionElement::TYPE] =
    '{type_legend},type,headline;{template_legend:hide},pwaSubscribeButtonTemplate,customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests;{invisible_legend:hide},invisible,start,stop';

$dca['palettes'][InstallPwaButtonElementController::TYPE] =
    '{type_legend},type;{link_legend},linkTitle,pwaButtonCssClasses;{text_legend},text;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible;';

$dca['fields']['pwaSubscribeButtonTemplate'] = [
    'exclude' => true,
    'inputType' => 'select',
    'eval' => ['tl_class' => 'w50 clr', 'includeBlankOption' => true],
    'sql' => "varchar(128) NOT NULL default ''",
];

$dca['fields']['pwaButtonCssClasses'] = [
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 128, 'tl_class' => 'w50'],
    'sql' => "varchar(128) NOT NULL default ''",
];
