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


use Symfony\Component\Translation\TranslatorInterface;

class PwaPushNotificationContainer
{
	/**
	 * @var TranslatorInterface
	 */
	private $translator;


	/**
	 * PwaPushNotificationContainer constructor.
	 */
	public function __construct(TranslatorInterface $translator)
	{
		$this->translator = $translator;
	}

	public function onLabelCallback(array $row)
	{
		$label = $row['title'];
		$label .= ' <span style="color:#999;padding-left:3px">(';
		if ($row['sent'])
		{
			$label .= $this->translator->trans('huh.pwa.tl_pwa_pushnotifications.label.sent');
		}
		else {
			$label .= $this->translator->trans('huh.pwa.tl_pwa_pushnotifications.label.sendAt');
		}
		$label .= ' ';
		$dateFormat = $this->translator->trans('huh.pwa.tl_pwa_pushnotifications.label.dateFormat');
		$label .= date($dateFormat, $row['sendDate']);
		$label .= ')</span>';
		return $label;
	}
}