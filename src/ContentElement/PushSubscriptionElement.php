<?php

/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license   http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\PwaBundle\ContentElement;

use Contao\BackendTemplate;
use Contao\ContentElement;
use Contao\System;
use HeimrichHannot\TwigSupportBundle\Renderer\TwigTemplateRenderer;

class PushSubscriptionElement extends ContentElement
{
    public const TYPE = 'pushsubscription';

    protected $strTemplate = 'ce_pushsubscription_default';

    protected function compile(): string
    {
        if (!$request = System::getContainer()->get('request_stack')?->getCurrentRequest()) {
            throw new \RuntimeException('Request stack not set');
        }

        if (!$scopeMatcher = System::getContainer()->get('contao.routing.scope_matcher')) {
            throw new \RuntimeException('Scope matcher not set');
        }

        return $scopeMatcher->isBackendRequest($request)
            ? $this->compileForBackend()
            : $this->compileForFrontend();
    }

    public function compileForBackend(): string
    {
        $this->strTemplate = 'be_wildcard';
        $this->Template = new BackendTemplate($this->strTemplate);
        $this->Template->title = "Web Push Notification Subscribe Button";

        return $this->Template->parse();
    }

    public function compileForFrontend(): string
    {
        $this->Template->button = System::getContainer()->get(TwigTemplateRenderer::class)
            ?->render($this->pwaSubscribeButtonTemplate ?: 'subscribe_button_default');

        return $this->Template->parse();
    }
}