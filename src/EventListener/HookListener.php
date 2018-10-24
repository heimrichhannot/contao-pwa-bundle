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

class HookListener
{
	/**
	 * @var ManifestLinkTag
	 */
	private $manifestLinkTag;


	/**
	 * HookListener constructor.
	 */
	public function __construct(ManifestLinkTag $manifestLinkTag)
	{
		$this->manifestLinkTag = $manifestLinkTag;
	}

	public function onGeneratePage(PageModel $page, LayoutModel $layout, PageRegular $pageRegular)
	{
		$rootPage = PageModel::findByPk($page->rootId);

		$this->manifestLinkTag->setContent('/manifest/' . $rootPage->alias . '_manifest.json');

//		if($rootPage->createManifest)
//		{

			$GLOBALS['TL_HEAD'][] = "<script>
									// Check that service workers are registered
									if ('serviceWorker' in navigator) {
									  // Use the window load event to keep the page load performant
									  window.addEventListener('load', () => {
										navigator.serviceWorker.register('manifest/sw.js');
									  });
									}
									</script>";
//		}
	}
}