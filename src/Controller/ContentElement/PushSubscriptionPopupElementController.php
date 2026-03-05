<?php

namespace HeimrichHannot\PwaBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(type: self::TYPE, category: 'pwa')]
class PushSubscriptionPopupElementController extends AbstractContentElementController
{
    public const TYPE = 'pwa_push_subscription_popup';
    public const TOGGLE_EVENT = 'event';
    public const TOGGLE_CUSTOM = 'custom';

    public function __construct(
        private readonly Studio $studio,
    ) {}

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $template->set('text', $model->text ?: '');

        $figure = !$model->addImage ? null : $this->studio
            ->createFigureBuilder()
            ->fromUuid($model->singleSRC ?: '')
            ->setSize($model->size)
            ->setOverwriteMetadata($model->getOverwriteMetadata())
            ->enableLightbox($model->fullsize)
            ->buildIfResourceExists()
        ;

        $template->set('image', $figure);
        $template->set('layout', $model->floating);

        if (!$template->has('element_html_id') || empty($template->get('element_html_id'))) {
            $template->set('element_html_id', 'content-'.self::TYPE.'-'.$model->id);
        }

        return $template->getResponse();

    }
}