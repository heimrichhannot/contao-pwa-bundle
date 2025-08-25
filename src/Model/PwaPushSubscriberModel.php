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

/**
 * Class PwaSubscriberModel
 * @package HeimrichHannot\PwaBundle\Model
 *
 * @property int $id
 * @property int $pid
 * @property int $tstamp
 * @property int $dateAdded
 * @property int $lastSuccessfulSend
 * @property string $endpoint
 * @property string $publicKey
 * @property string $authToken
 */
class PwaPushSubscriberModel extends Model
{
	protected static $strTable = 'tl_pwa_pushsubscriber';
}