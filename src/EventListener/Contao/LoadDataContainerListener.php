<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\EventListener\Contao;


use Contao\Controller;

/**
 * @Hook("loadDataContainer")
 */
class LoadDataContainerListener
{
    public function __invoke(string $table): void
    {
        switch ($table) {
            case 'tl_module':
                $this->prepareModuleTable();

        }
    }

    protected function prepareModuleTable()
    {
        $dca = &$GLOBALS['TL_DCA']['tl_module'];

        if (!isset($dca['fields']['addImage'])) {
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