<?php
/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license   LGPL-3.0-or-later
 */

namespace HeimrichHannot\PwaBundle\HeaderTag;

use HeimrichHannot\HeadBundle\Head\AbstractTag;

/**
 * @deprecated Head Bundle AbstractTag is deprecated.
 */
class PwaHeadScriptTags extends AbstractTag
{
    protected array $scripts;

    /**
     * Generate the tag output.
     *
     * @return string
     */
    public function generate(): string
    {
        if (empty($this->content))
        {
            $content = "";
            if (!empty($this->scripts))
            {
                foreach ($this->scripts as $script)
                {
                    $content .= "<script>" . $script . "</script>";
                }
            }
            $this->content = $content;
        }
        return $this->content;
    }

    /**
     * Add head javascript for PWA
     */
    public function addScript($script): void
    {
        $this->scripts[] = $script;
    }

    /**
     * Get head javascript for PWA
     */
    public function getScripts(): array
    {
        return $this->scripts;
    }

    public function getContent(): string
    {
        return $this->generate();
    }

    public function setContent($content): void
    {
        \trigger_error(
            "You should not use setContent in conjunction with PwaHeadSchriptTags. May lead to unexpected results. Use addScript instead!",
            E_WARNING
        );

        parent::setContent($content);
    }
}