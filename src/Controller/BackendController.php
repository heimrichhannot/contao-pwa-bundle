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


use Minishlink\WebPush\VAPID;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BackendController
 * @package HeimrichHannot\ContaoPwaBundle\Controller
 *
 * @Route("/contao", defaults={
 *     "_scope" = "backend",
 *     "_token_check" = true,
 *     "_custom_backend_view" = true,
 *     "_backend_module" = "huh_pwa"
 * })
 */
class BackendController extends Controller
{
	/**
	 * @Route("/pwa", name="huh.pwa.backend")
	 */
	public function testAction()
	{
		$this->container->get('contao.framework')->initialize();

		$config = $this->container->getParameter('huh.pwa');

		$keys = isset($config["vapid_keys"]) ? $config['vapid_keys'] : null;
		$generatedKeys = null;

		if (!$keys)
		{
			$generatedKeys = VAPID::createVapidKeys();
		}

		$content = $this->container->get('twig')->render("@HeimrichHannotContaoPwa/backend/backend.html.twig", [
			"vapidkeys" => $keys,
			"generatedKeys" => $generatedKeys,
			"content" => "Content",
		]);

		return new Response($content);
	}
}