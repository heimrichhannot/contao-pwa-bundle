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

/**
 * Class PwaConfigurationsModel
 * @package HeimrichHannot\ContaoPwaBundle\Model
 *
 * @property int $id
 * @property int $tstamp
 * @property int $dateAdded
 * @property string $title
 * @property string $addDebugLog
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

	const PWA_NAME_CUSTOM = 'custom';
	const PWA_NAME_PAGETITLE = 'title';
	const PWA_NAME_META_PAGETITLE = 'pageTitle';
	const PWA_NAME_OPTIONS = [self::PWA_NAME_PAGETITLE, self::PWA_NAME_META_PAGETITLE, self::PWA_NAME_CUSTOM];
}