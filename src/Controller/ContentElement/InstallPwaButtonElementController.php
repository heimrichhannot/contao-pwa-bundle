<?php

/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license   http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\PwaBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(self::TYPE, category: 'pwa')]
class InstallPwaButtonElementController extends AbstractContentElementController
{
    public const TYPE = 'install_pwa_button';

    protected function getResponse(Template $template, ContentModel $model, Request $request): ?Response
    {
        if ($model->pwaButtonCssClasses) {
            $template->buttonCssClasses = $model->pwaButtonCssClasses;
        }

        if ($model->text) {
            $template->installNotSupportedMessage = $model->text;
        }

        return $template->getResponse();
    }
}