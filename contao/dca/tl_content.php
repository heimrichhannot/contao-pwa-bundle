<?php

use HeimrichHannot\PwaBundle\Controller\ContentElement\InstallPwaButtonElementController;
use HeimrichHannot\PwaBundle\Controller\ContentElement\PushSubscriptionElement;

$dca = &$GLOBALS['TL_DCA']['tl_content'];

$dca['palettes'][PushSubscriptionElement::TYPE] = <<< PALETTE
    {type_legend},type,headline;
    {template_legend:hide},customTpl;
    {protected_legend:hide},protected;
    {expert_legend:hide},guests;
    {invisible_legend:hide},invisible,start,stop'
PALETTE;


$dca['palettes'][InstallPwaButtonElementController::TYPE] = <<< PALETTE
    {type_legend},type;
    {link_legend},linkTitle,pwaButtonCssClasses;
    {text_legend},text;
    {template_legend:hide},customTpl;
    {protected_legend:hide},protected;
    {expert_legend:hide},guests,cssID;
    {invisible_legend:hide},invisible;';
PALETTE;


$dca['fields']['pwaButtonCssClasses'] = [
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 128, 'tl_class' => 'w50'],
    'sql' => "varchar(128) NOT NULL default ''",
];
