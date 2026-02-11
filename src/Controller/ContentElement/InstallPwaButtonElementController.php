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
use Contao\CoreBundle\String\HtmlAttributes;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(type: self::TYPE, category: 'pwa', template: 'content_element/pwa_install_button')]
class InstallPwaButtonElementController extends AbstractContentElementController
{
    public const TYPE = 'pwa_install_button';

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $buttonAttrs = new HtmlAttributes();

        if ($model->pwaButtonCssClasses) {
            $buttonAttrs->addClass($model->pwaButtonCssClasses);
        }

        if ($model->text) {
            $template->set('installNotSupportedMessage', $model->text);
        }

        $template->set('button_attrs', $buttonAttrs);

        return $template->getResponse();
    }
}