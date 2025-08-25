<?php
/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license   LGPL-3.0-or-later
 */

namespace HeimrichHannot\PwaBundle\EventListener\Contao;

use Contao\Controller;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;

#[AsHook('loadDataContainer')]
class LoadDataContainerListener
{
    public function __invoke(string $table): void
    {
        if ($table !== 'tl_module') {
            return;
        }

        $dca = &$GLOBALS['TL_DCA']['tl_module'];

        if (!isset($dca['fields']['addImage']))
        {
            Controller::loadLanguageFile('tl_content');
            $dca['palettes']['__selector__'][] = 'addImage';
            $dca['subpalettes']['addImage'] = 'singleSRC,imgSize';
            $dca['fields']['addImage'] = [
                'label'     => &$GLOBALS['TL_LANG']['tl_content']['addImage'],
                'exclude'   => true,
                'inputType' => 'checkbox',
                'eval'      => ['submitOnChange' => true],
                'sql'       => "char(1) NOT NULL default ''"
            ];
        }
    }
}