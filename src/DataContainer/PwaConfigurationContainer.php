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


use Symfony\Component\Routing\RouterInterface;

class PwaConfigurationContainer
{
	/**
	 * @var RouterInterface
	 */
	private $router;


	/**
	 * PwaConfigurationContainer constructor.
	 */
	public function __construct(RouterInterface $router)
	{
		$this->router = $router;
	}

	public function onControlActionButtonCallback($href, $label, $title, $class, $attributes, $table, $root)
	{
		$route = $this->router->generate($href);
		return '<a href="'.$route.'" title="'.specialchars($title).'" class="'.$class.'" '.$attributes.'>'.$label.'</a>';
	}
}