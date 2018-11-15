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


use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;
use HeimrichHannot\ContaoPwaBundle\HeaderTag\ManifestLinkTag;
use HeimrichHannot\ContaoPwaBundle\HeaderTag\ThemeColorMetaTag;
use HeimrichHannot\ContaoPwaBundle\Model\PwaConfigurationsModel;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouterInterface;

class HookListener
{
	/**
	 * @var ManifestLinkTag
	 */
	private $manifestLinkTag;
	/**
	 * @var ThemeColorMetaTag
	 */
	private $colorMetaTag;
	/**
	 * @var \Twig_Environment
	 */
	private $twig;
	/**
	 * @var RouterInterface
	 */
	private $router;


	/**
	 * HookListener constructor.
	 */
	public function __construct(ManifestLinkTag $manifestLinkTag, ThemeColorMetaTag $colorMetaTag, \Twig_Environment $twig, RouterInterface $router)
	{
		$this->manifestLinkTag = $manifestLinkTag;
		$this->colorMetaTag = $colorMetaTag;
		$this->twig = $twig;
		$this->router = $router;
	}

	/**
	 * @param PageModel $page
	 * @param LayoutModel $layout
	 * @param PageRegular $pageRegular
	 * @throws \Twig_Error_Loader
	 * @throws \Twig_Error_Runtime
	 * @throws \Twig_Error_Syntax
	 */
	public function onGeneratePage(PageModel $page, LayoutModel $layout, PageRegular $pageRegular)
	{
		$rootPage = PageModel::findByPk($page->rootId);

		if ($rootPage->addPwa && $rootPage->pwaConfiguration)
		{
			$config = PwaConfigurationsModel::findByPk($rootPage->pwaConfiguration);
			if (!$config)
			{
				return;
			}

			$this->manifestLinkTag->setContent('/manifest/' . $rootPage->alias . '_manifest.json');
			$this->colorMetaTag->setContent('#'.$config->pwaThemeColor);

			$serviceWorker = 'sw_'.$rootPage->alias.'.js';

			$GLOBALS['TL_HEAD'][] =
				"<script type='text/javascript'>"
				.$this->twig->render('@HeimrichHannotContaoPwa/translation/translation.js.twig')
				."</script>";
			$GLOBALS['TL_HEAD'][] = '<script src="bundles/heimrichhannotcontaopwa/js/PushNotificationSubscription.js"></script>';
			$GLOBALS['TL_HEAD'][] =
				"<script type='text/javascript'>"
				.$this->twig->render('@HeimrichHannotContaoPwa/registration/default.js.twig', [
					'alias' => $rootPage->alias,
					'serviceWorkerPath' => $serviceWorker,
					'subscribePath' => $this->router->generate('push_notification_subscription', ['config' => $config->id]),
					'unsubscribePath' => $this->router->generate('push_notification_unsubscription', ['config' => $config->id]),
					'debug' => (bool) $config->addDebugLog,
				])
				."</script>";
		}
	}
}