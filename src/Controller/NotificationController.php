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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class NotificationController
 * @package HeimrichHannot\ContaoPwaBundle\Controller
 *
 * @Route("/api/notifications")
 */
class NotificationController extends Controller
{
	/**
	 * @Route("/subscribe", name="push_notification_subscription")
	 */
	public function subscribeAction(?Request $request)
	{
		return new Response("A", 200);
	}
}