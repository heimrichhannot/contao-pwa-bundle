<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class BackendController
 * @package HeimrichHannot\ContaoPwaBundle\Controller
 *
 * @Route("/contao", defaults={
 *     "_scope" = "backend",
 *     "_token_check" = true,
 *     "_backend_module" = "huh_pwa"
 * })
 */
class BackendController extends Controller
{
	/**
	 * @Route("/pwa", name="huh.pwa")
	 * @Template("HeimrichHannotContaoPwaBundle::backend::backend.html.twig")
	 */
	public function testAction()
	{
		return [
			'content' => 'Content'
		];
	}
}