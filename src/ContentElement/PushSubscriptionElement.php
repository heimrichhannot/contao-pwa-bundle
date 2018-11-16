<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\ContentElement;


use Contao\BackendTemplate;
use Contao\ContentElement;
use Contao\ContentModel;
use Contao\System;

class PushSubscriptionElement extends ContentElement
{
	const TYPE = 'pushsubscription';

	protected $strTemplate = 'ce_pushsubscription_default';
	protected $scopeMatcher;
	protected $request;
	protected $twig;
	protected $templateUtil;

	public function __construct(ContentModel $objElement, string $strColumn = 'main')
	{
		parent::__construct($objElement, $strColumn);
		$this->scopeMatcher = System::getContainer()->get('contao.routing.scope_matcher');
		$this->request = System::getContainer()->get('request_stack')->getCurrentRequest();
		$this->twig = System::getContainer()->get('twig');
		$this->templateUtil = System::getContainer()->get('huh.utils.template');
	}


	/**
	 * Compile the content element
	 */
	protected function compile()
	{
		if ($this->scopeMatcher->isBackendRequest($this->request))
		{
			$this->strTemplate = 'be_wildcard';
			$this->Template = new BackendTemplate($this->strTemplate);
			$this->Template->title = "Web Push Notification Subscribe Button";
			return $this->Template->parse();
		}

		$template = $this->pwaSubscribeButtonTemplate ?: 'pwa_subscription_default';
		$templatePath = $this->templateUtil->getTemplate($template);

		try
		{
			$this->Template->button = $this->twig->render($templatePath);
		} catch (\Twig_Error $e){
			$this->Template->button = '<button class="huhPwaWebSubscription" disabled="disabled"><span class="label">Push Notifications</span></button>';
		}

		return $this->Template->parse();
	}
}