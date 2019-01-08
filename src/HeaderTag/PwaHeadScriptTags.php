<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\HeaderTag;


use HeimrichHannot\HeadBundle\Head\AbstractTag;

class PwaHeadScriptTags extends AbstractTag
{

    /**
     * @var array
     */
    protected $scripts;



    /**
     * Generate the tag output.
     *
     * @return string
     */
    public function generate()
    {
        $content = "";
        foreach ($this->scripts as $script)
        {
            $content .= "<script type='text/javascript'>".$script."</script>";
        }
        return $content;
    }

    /**
     * Add head javascript for pwa
     *
     * @param $script
     */
    public function addScript($script)
    {
        $this->scripts[] = $script;
    }

    /**
     * Get head javascript for pwa
     *
     * @return array
     */
    public function getScripts()
    {
        return $this->scripts;
    }
}