<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\PwaBundle\HeaderTag;


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
        if (empty($this->content))
        {
            $content = "";
            if (!empty($this->scripts))
            {
                foreach ($this->scripts as $script)
                {
                    $content .= "<script>".$script."</script>";
                }
            }
            $this->content = $content;
        }
        return $this->content;
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

    public function getContent()
    {
        return $this->generate();
    }

    public function setContent($content)
    {
        trigger_error("You should not use setContent in conjunction with PwaHeadSchriptTags. May lead to unexpected results. Use addScript instead!", E_WARNING);
        parent::setContent($content);
    }


}