<?php
/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license   LGPL-3.0-or-later
 */

namespace HeimrichHannot\PwaBundle\Model;

use Contao\Model;
use Contao\Model\Collection;
use HeimrichHannot\PwaBundle\Notification\DefaultNotification;

/**
 * Class PwaPushNotificationsModel
 *
 * @package HeimrichHannot\PwaBundle\Model
 *
 * @property int $id
 * @property int $pid
 * @property int $tstamp
 * @property int $dateAdded
 * @property string $title
 * @property string $body
 * @property string $icon
 * @property string $iconSize
 * @property string $sent
 * @property int $receiverCount
 * @property string $clickEvent
 * @property string $clickJumpTo
 * @property int $dateSent
 * @property boolean $published
 * @property int $start
 */
class PwaPushNotificationsModel extends Model
{
    protected static $strTable = 'tl_pwa_pushnotifications';

    /**
     * @param array $options
     * @return Collection|PwaPushNotificationsModel|PwaPushNotificationsModel[]|null
     */
    public static function findUnsentPublishedNotifications(array $options = [])
    {
        $t = static::$strTable;
        $time = \Date::floorToMinute();

        $columns = [
            "$t.sent=''",
            "($t.start='' OR $t.start<='$time') AND $t.published='1'",
        ];

        return static::findBy($columns, null, $options);
    }

    /**
     * @param int $pid
     * @param array $options
     * @return Collection|PwaPushNotificationsModel|PwaPushNotificationsModel[]|null
     */
    public static function findUnsentPublishedNotificationsByPid(int $pid, array $options = [])
    {
        $t = static::$strTable;
        $time = \Date::floorToMinute();
        $columns = [
            "$t.pid=?",
            "$t.sent=''",
            "($t.start='' OR $t.start<='$time') AND $t.published='1'",
        ];
        return static::findBy($columns, $pid, $options);
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
        $time = \Date::floorToMinute();
        $columns = [
            "$t.id=?",
            "$t.sent=''",
            "($t.start='' OR $t.start<='$time') AND $t.published='1'",
        ];
        return static::findOneBy($columns, $id, $columns);
    }
}