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
	 * @var KernelInterface
	 */
	private $kernel;


	/**
	 * HookListener constructor.
	 */
	public function __construct(ManifestLinkTag $manifestLinkTag, ThemeColorMetaTag $colorMetaTag, \Twig_Environment $twig, KernelInterface $kernel)
	{
		$this->manifestLinkTag = $manifestLinkTag;
		$this->colorMetaTag = $colorMetaTag;
		$this->twig = $twig;
		$this->kernel = $kernel;
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
//			$serviceWorker = 'sw_push.js';

			$GLOBALS['TL_HEAD'][] = '<script src="bundles/heimrichhannotcontaopwa/js/pushNotificationSubscription.js"></script>';
			$GLOBALS['TL_HEAD'][] =
				"<script type='text/javascript'>"
				.$this->twig->render('@HeimrichHannotContaoPwa/registration/default.js.twig', [
					'alias' => $rootPage->alias,
					'serviceWorkerPath' => $serviceWorker,
					'debug' => $this->kernel->isDebug(),
				])
				."</script>";
		}
	}
}