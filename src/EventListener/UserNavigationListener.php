<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\EventListener;


use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class UserNavigationListener
{
	/**
	 * @var RequestStack
	 */
	private $requestStack;
	/**
	 * @var RouterInterface
	 */
	private $router;

	/**
	 * UserNavigationListener constructor.
	 */
	public function __construct(RequestStack $requestStack, RouterInterface $router)
	{
		$this->requestStack = $requestStack;
		$this->router = $router;
	}

	/**
	 * @param array $modules
	 * @param bool $showAll
	 * @return array
	 */
	public function onGetUserNavigation($modules, $showAll)
	{
		$modules['system']['modules']['huh_pwa'] = [
			'title' => 'A backend route test module',
			'label' => 'Test',
			'class' => 'navigation test',
			'href'  => $this->router->generate('huh.pwa')
		];

		if ($this->requestStack->getCurrentRequest()->attributes->get('_backend_module') === 'test') {
			$modules['system']['modules']['huh_pwa']['class'] .= ' active';
		}

		return $modules;
	}
}