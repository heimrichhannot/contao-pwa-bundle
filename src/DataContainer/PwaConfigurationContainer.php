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
use Symfony\Component\Routing\RouterInterface;

class PwaConfigurationContainer
{
	/**
	 * @var RouterInterface
	 */
	private $router;
    /**
     * @var TemplateUtil
     */
    private $templateUtil;


    /**
	 * PwaConfigurationContainer constructor.
	 */
	public function __construct(RouterInterface $router, TemplateUtil $templateUtil)
	{
		$this->router = $router;
        $this->templateUtil = $templateUtil;
    }

	public function onControlActionButtonCallback($href, $label, $title, $class, $attributes, $table, $root)
	{
		$route = $this->router->generate($href);
		return '<a href="'.$route.'" title="'.specialchars($title).'" class="'.$class.'" '.$attributes.'>'.$label.'</a>';
	}

	public function getServiceWorkerTemplates()
    {
        return $this->templateUtil->getTemplateGroup('pwa_serviceworker', 'js.twig');
    }
}