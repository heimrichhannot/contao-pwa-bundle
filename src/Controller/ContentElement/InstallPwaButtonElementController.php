<?php

namespace HeimrichHannot\ContaoPwaBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ContentElement(InstallPwaButtonElementController::TYPE, category="miscellaneous")
 */
class InstallPwaButtonElementController extends AbstractContentElementController
{
    public const TYPE = 'install_pwa_button';

    protected function getResponse(Template $template, ContentModel $model, Request $request): ?Response
    {
        return $template->getResponse();
    }
}