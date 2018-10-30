<?php

$dc = &$GLOBALS['TL_DCA']['tl_content'];
$dc['palettes'][\HeimrichHannot\ContaoPwaBundle\ContentElement\SubscribeButtonElement::TYPE] =
	'{type_legend},type,headline;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests;{invisible_legend:hide},invisible,start,stop';
