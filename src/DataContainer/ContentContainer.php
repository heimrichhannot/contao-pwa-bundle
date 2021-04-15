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


use HeimrichHannot\TwigSupportBundle\Filesystem\TwigTemplateLocator;

class ContentContainer
{
    /**
     * @var TwigTemplateLocator
     */
    protected $templateLocator;

    /**
	 * ContentContainer constructor.
	 */
	public function __construct(TwigTemplateLocator $templateLocator)
	{
        $this->templateLocator = $templateLocator;
    }

	public function getPwaSubscriptionButtonTemplate()
	{
		return $this->templateLocator->getTemplateGroup('subscribe_button_');
	}
}