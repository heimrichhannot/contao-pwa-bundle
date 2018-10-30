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

class SubscribeButtonElement extends ContentElement
{
	const TYPE = 'subscriptionLinkButton';

	protected $strTemplate = 'ce_subscribelinkbutton';
	protected $scopeMatcher;
	protected $request;

	public function __construct(ContentModel $objElement, string $strColumn = 'main')
	{
		parent::__construct($objElement, $strColumn);
		$this->scopeMatcher = System::getContainer()->get('contao.routing.scope_matcher');
		$this->request = System::getContainer()->get('request_stack')->getCurrentRequest();
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
			$this->Template->title = "Web Notification Subscribe Button";
			return $this->Template->parse();
		}

		$this->Template->button = '<button class="huhPwaWebSubscription" disabled="disabled">Notification</button>';

		return $this->Template->parse();
	}
}