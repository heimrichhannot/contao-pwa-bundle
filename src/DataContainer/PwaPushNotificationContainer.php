<?php
/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @author    Eric Gesemann <e.gesemann@heimrich-hannot.de>
 * @license   http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\PwaBundle\DataContainer;

use Contao\Config;
use Contao\Controller;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\Date;
use Contao\NewsModel;
use Contao\PageModel;
use Contao\StringUtil;
use HeimrichHannot\PwaBundle\Model\PwaPushNotificationsModel;
use Symfony\Contracts\Translation\TranslatorInterface;

class PwaPushNotificationContainer
{
    public const TABLE = 'tl_pwa_pushnotifications';
    public const CLICKEVENT_OPEN_PAGE = 'openPage';
    public const CLICKEVENT_OPEN_URL = 'openUrl';

    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {}

    #[AsCallback(self::TABLE, 'list.sorting.child_record')]
    public function onChildRecordCallback(array $row): string
    {
        $dateFormat = Config::get('datimFormat');
        $time = Date::floorToMinute();
        $label = $row['title'];
        $label .= ' <span style="color:#999;padding-left:3px">(';

        if ($row['sent'] ?? false)
        {
            $label .= $this->translator->trans(
                'huh.pwa.tl_pwa_pushnotifications.label.notificationSent',
                ["%date%" => Date::parse($dateFormat, $row['dateSent'])]
            );
        }
        elseif ($row['published'] ?? false)
        {
            if ($row['start'] > 0 && $row['start'] > $time)
            {
                $label .= $this->translator->trans(
                    'huh.pwa.tl_pwa_pushnotifications.label.notificationUnsentPublishedDate',
                    ['%date%' => Date::parse($dateFormat, $row['start'])]
                );
            } else
            {
                $label .= $this->translator->trans(
                    'huh.pwa.tl_pwa_pushnotifications.label.notificationUnsentPublished'
                );
            }
        }
        else
        {
            $label .= $this->translator->trans(
                'huh.pwa.tl_pwa_pushnotifications.label.notificationUnsentNotPublished'
            );
        }

        $label .= ')</span>';
        return $label;
    }

    #[AsCallback(self::TABLE, 'list.label.group')]
    public function onGroupCallback(string $group, $mode, string $field, array $row, $dcTable): string
    {
        if ($field !== 'send') {
            return $group;
        }

        return ($row['sent'] ?? false)
            ? $this->translator->trans('huh.pwa.tl_pwa_pushnotifications.label.groupSent')
            : $this->translator->trans('huh.pwa.tl_pwa_pushnotifications.label.groupUnsent');
    }

    public function notificationClickEvent(PwaPushNotificationsModel $notificationsModel, array &$payload): void
    {
        $callback = match ($notificationsModel->clickEvent) {
            static::CLICKEVENT_OPEN_PAGE => $this->handleClickEventOpenPage(...),
            static::CLICKEVENT_OPEN_URL => $this->handleClickEventOpenUrl(...),
            default => null,
        };

        if ($callback) {
            $callback($notificationsModel, $payload);
        }
    }

    protected function handleClickEventOpenPage(PwaPushNotificationsModel $notificationsModel, array &$payload): void
    {
        if ($page = PageModel::findByPk($notificationsModel->clickJumpTo))
        {
            $payload['data']['clickJumpTo'] = $page->getAbsoluteUrl();
        }
    }

    protected function handleClickEventOpenUrl(PwaPushNotificationsModel $notificationsModel, array &$payload): void
    {
        $tags = \preg_split(
            '~{{([a-zA-Z0-9\x80-\xFF][^{}]*)}}~',
            $notificationsModel->clickUrl,
            -1,
            \PREG_SPLIT_DELIM_CAPTURE
        );

        $tags = \array_values(\array_filter($tags));

        if (\count($tags) === 1 && \str_contains($tags[0], 'news_url'))
        {
            $tag = explode('::', $tags[0]);

            if (isset($tag[1])
                && \is_numeric($tag[1])
                && (!$news = NewsModel::findById($tag[1]))
                && ($page = PageModel::findByPk($news->getRelated('pid')->jumpTo)))
            {
                $params = (Config::get('useAutoItem') ? '/' : '/items/') . ($news->alias ?: $news->id);
                $url = StringUtil::ampersand($page->getAbsoluteUrl($params));
                $payload['data']['clickJumpTo'] = $url;

                return;
            }
        }

        $url = Controller::replaceInsertTags($notificationsModel->clickUrl);
        $payload['data']['clickJumpTo'] = $url;
    }
}