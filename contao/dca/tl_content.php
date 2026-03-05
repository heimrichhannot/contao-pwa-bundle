<?php

use HeimrichHannot\PwaBundle\Controller\ContentElement\InstallPwaButtonElementController;
use HeimrichHannot\PwaBundle\Controller\ContentElement\OfflinePagesElementController;
use HeimrichHannot\PwaBundle\Controller\ContentElement\PushSubscriptionElement;
use HeimrichHannot\PwaBundle\Controller\ContentElement\PushSubscriptionPopupElementController;

$dca = &$GLOBALS['TL_DCA']['tl_content'];

$dca['palettes'][PushSubscriptionElement::TYPE] = <<< PALETTE
    {type_legend},type,headline;
    {template_legend:collapsed},customTpl;
    {protected_legend:collapsed},protected;
    {expert_legend:collapsed},guests;
    {invisible_legend:collapsed},invisible,start,stop
PALETTE;


$dca['palettes'][InstallPwaButtonElementController::TYPE] = <<< PALETTE
    {type_legend},type;
    {link_legend},linkTitle,pwaButtonCssClasses;
    {text_legend},text;
    {template_legend:collapsed},customTpl;
    {protected_legend:collapsed},protected;
    {expert_legend:collapsed},guests,cssID;
    {invisible_legend:collapsed},invisible,start,stop
PALETTE;

$dca['palettes'][PushSubscriptionPopupElementController::TYPE] = <<< PALETTE
    {type_legend},type,headline;
    {text_legend},text;
    {image_legend},addImage;
    {template_legend:collapsed},customTpl;
    {protected_legend:collapsed},protected;
    {expert_legend:collapsed},cssID;
    {invisible_legend:collapsed},invisible,start,stop
PALETTE;

$dca['palettes'][OfflinePagesElementController::TYPE] = <<< PALETTE
    {type_legend},type,headline;
    {text_legend},text;
    {template_legend:collapsed},customTpl;
    {protected_legend:collapsed},protected;
    {expert_legend:collapsed},guests,cssID;
    {invisible_legend:collapsed},invisible,start,stop
PALETTE;


$dca['fields']['pwaButtonCssClasses'] = [
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 128, 'tl_class' => 'w50'],
    'sql' => "varchar(128) NOT NULL default ''",
];
