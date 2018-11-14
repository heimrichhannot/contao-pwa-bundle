<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\Model;


use Contao\Model;
use Contao\Model\Collection;
use HeimrichHannot\ContaoPwaBundle\Notification\DefaultNotification;

/**
 * Class PwaPushNotificationsModel
 * @package HeimrichHannot\ContaoPwaBundle\Model
 *
 * @property int $id
 * @property int $pid
 * @property int $tstamp
 * @property int $dateAdded
 * @property string $title
 * @property string $body
 * @property string $icon
 * @property string $iconSize
 * @property string $sendDate
 * @property string $sent
 * @property int $receiverCount
 */
class PwaPushNotificationsModel extends Model
{
	protected static $strTable = 'tl_pwa_pushnotifications';

	/**
	 * @return Collection|PwaPushNotificationsModel|PwaPushNotificationsModel[]|null
	 */
	public static function findUnsentNotifications()
	{
		return static::findBy('sent','');
	}

	/**
	 * Find an unsent notification by id
	 *
	 * @param int $id
	 * @return PwaPushNotificationsModel|null
	 */
	public static function findUnsentNotificationById(int $id)
	{
		$t = static::$strTable;
		return static::findOneBy(["$t.id=?", "$t.sent=?"],[$id, ""]);
	}
}