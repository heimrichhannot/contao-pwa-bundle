<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\DataContainer;


use HeimrichHannot\UtilsBundle\Template\TemplateUtil;

class ContentContainer
{
	/**
	 * @var TemplateUtil
	 */
	private $templateUtil;

	/**
	 * ContentContainer constructor.
	 */
	public function __construct(TemplateUtil $templateUtil)
	{
		$this->templateUtil = $templateUtil;
	}

	public function getPwaSubscriptionButtonTemplate()
	{
		return $this->templateUtil->getTemplateGroup('ce_pushsubscription_');
	}
}