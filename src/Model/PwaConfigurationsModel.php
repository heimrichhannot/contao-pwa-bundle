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
 * Class PwaConfigurationsModel
 *
 * @package HeimrichHannot\PwaBundle\Model
 *
 * @property int $id
 * @property int $tstamp
 * @property int $dateAdded
 * @property string $title
 * @property string $addDebugLog
 * @property bool|string $hideInstallPrompt
 * @property string $serviceWorkerTemplate
 * @property string $offlinePage
 * @property string $supportPush
 * @property string $sendWithCron
 * @property string $cronIntervall
 * @property string $pwaName
 * @property string $pwaCustomName
 * @property string $pwaShortName
 * @property string $pwaBackgroundColor
 * @property string $pwaThemeColor
 * @property string $pwaDescription
 * @property string $pwaDirection
 * @property string $pwaDisplay
 * @property string $pwaIcons
 * @property string $pwaOrientation
 * @property string $pwaStartUrl
 * @property string $pwaScope
 * @property string $pwaPreferRelatedApplication
 * @property string $pwaRelatedApplications
 */
class PwaConfigurationsModel extends Model
{
    protected static $strTable = 'tl_pwa_configurations';

    public const PWA_NAME_CUSTOM = 'custom';
    public const PWA_NAME_PAGETITLE = 'title';
    public const PWA_NAME_META_PAGETITLE = 'pageTitle';
    public const PWA_NAME_OPTIONS = [self::PWA_NAME_PAGETITLE, self::PWA_NAME_META_PAGETITLE, self::PWA_NAME_CUSTOM];
}