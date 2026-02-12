<?php

namespace HeimrichHannot\PwaBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsContentElement(type: self::TYPE, category: 'pwa', template: 'content_element/pwa_offline_pages')]
class OfflinePagesElementController extends AbstractContentElementController
{
    public const TYPE = 'pwa_offline_pages';

    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {}

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $template->set('element_html_id', 'content-'.self::TYPE.'-'.$model->id);
        $template->set('emptyMessage', $model->text ?: $this->translator->trans('huh.pwa.offline_pages.empty'));
        $template->set('unsupportedMessage', $this->translator->trans('huh.pwa.offline_pages.unsupported'));

        return $template->getResponse();
    }
}
