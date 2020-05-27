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


use Contao\Config;
use Contao\Controller;
use Contao\Date;
use Contao\News;
use Contao\NewsModel;
use Contao\PageModel;
use Contao\System;
use HeimrichHannot\ContaoPwaBundle\Model\PwaPushNotificationsModel;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Url;

class PwaPushNotificationContainer
{
	const CLICKEVENT_OPEN_PAGE = 'openPage';
	const CLICKEVENT_OPEN_URL = 'openUrl';

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

	public function onChildRecordCallback(array $row)
	{
        $dateFormat = Config::get('datimFormat');
        $time = \Date::floorToMinute();
		$label = $row['title'];
		$label .= ' <span style="color:#999;padding-left:3px">(';
		if ($row['sent'])
		{
			$label .= $this->translator->trans('huh.pwa.tl_pwa_pushnotifications.label.notificationSent', ["%date%" => Date::parse($dateFormat, $row['dateSent'])]);
		}
		else {
		    if ($row['published'])
            {
                if ($row['start'] > 0 && $row['start'] > $time)
                {
                    $label .= $this->translator->trans(
                        'huh.pwa.tl_pwa_pushnotifications.label.notificationUnsentPublishedDate',
                        ['%date%' => Date::parse($dateFormat, $row['date'])]
                    );
                }
                else {
                    $label .= $this->translator->trans('huh.pwa.tl_pwa_pushnotifications.label.notificationUnsentPublished');
                }
            }
		    else {
                $label .= $this->translator->trans('huh.pwa.tl_pwa_pushnotifications.label.notificationUnsentNotPublished');
            }


		}
		$label .= ')</span>';
		return $label;
	}

	public function onGroupCallback($group, $mode, $field, $row, $dcTable)
    {
	    if ($field === 'sent')
        {
            if ($row['sent'] === "1")
            {
                $group = $this->translator->trans('huh.pwa.tl_pwa_pushnotifications.label.groupSent');
            }
            else {
                $group = $this->translator->trans('huh.pwa.tl_pwa_pushnotifications.label.groupUnsent');
            }
        }
	    return $group;
    }

	/**
	 *
	 *
	 * @param $notificationsModel
	 * @param $payload
	 */
	public function notificationClickEvent(PwaPushNotificationsModel $notificationsModel, array &$payload): void
	{
		switch ($notificationsModel->clickEvent)
		{
			case static::CLICKEVENT_OPEN_PAGE:
				$page = PageModel::findByPk($notificationsModel->clickJumpTo);
				if ($page)
				{
					$payload['data']['clickJumpTo'] = $page->getAbsoluteUrl();
				}
				break;
		}
	}


}