<?php /** @noinspection JsonEncodingApiUsageInspection */

/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @author    Eric Gesemann <e.gesemann@heimrich-hannot.de>
 * @license   http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\PwaBundle\Controller;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Model\Collection;
use HeimrichHannot\PwaBundle\Model\PwaPushSubscriberModel;
use HeimrichHannot\PwaBundle\Model\PwaConfigurationsModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/_huh_pwa/notification/{config}', name: 'huh_pwa.notification.', defaults: ['_scope' => 'frontend', '_token_check' => false])]
class NotificationController extends AbstractController
{
    public function __construct(
        private readonly ContaoFramework $contaoFramework,
    ) {}

    #[Route('/subscribe', name: 'subscribe', methods: ['POST'])]
    public function subscribeAction(Request $request, int $config): Response
    {
        $this->contaoFramework->initialize();

        /** @var PwaConfigurationsModel $pwaConfig */
        if (!$pwaConfig = PwaConfigurationsModel::findByPk($config))
        {
            return new Response('Subscription not found', Response::HTTP_NOT_FOUND);
        }

        if (!($data = \json_decode($request->getContent(), true)) || !\is_array($data))
        {
            return new Response('Invalid request data', Response::HTTP_BAD_REQUEST);
        }

        $endpoint = $data['subscription']['endpoint'] ?? null;
        $p256dh = $data['subscription']['keys']['p256dh'] ?? null;
        $auth = $data['subscription']['keys']['auth'] ?? null;

        if (!$endpoint || !$p256dh || !$auth)
        {
            return new Response('Missing parameters in request data', Response::HTTP_BAD_REQUEST);
        }

        /** @var PwaPushSubscriberModel $subscriber */
        if (!$subscriber = PwaPushSubscriberModel::findByEndpoint($endpoint))
        {
            $subscriber = new PwaPushSubscriberModel();
            $subscriber->dateAdded = $subscriber->tstamp = time();
            $subscriber->endpoint = $endpoint;
            $subscriber->publicKey = $p256dh;
            $subscriber->authToken = $auth;
            $subscriber->pid = $pwaConfig->id;
            $subscriber->save();

            return new Response('Subscription successfully created', Response::HTTP_OK);
        }

        $subscriber->tstamp = time();
        $subscriber->publicKey = $p256dh;
        $subscriber->authToken = $auth;
        $subscriber->save();

        return new Response('Subscription successfully updated', Response::HTTP_OK);
    }

    #[Route('/update', name: 'update', methods: ['POST'])]
    public function updateSubscriptionAction(Request $request, $config): Response
    {
        $this->contaoFramework->initialize();

        if (!$pwaConfig = PwaConfigurationsModel::findByPk($config))
        {
            return new Response('Subscription not found', Response::HTTP_NOT_FOUND);
        }

        if (!($data = json_decode($request->getContent(), true)) || !\is_array($data)) {
            return new Response('Invalid request data', Response::HTTP_BAD_REQUEST);
        }

        $oldEndpoint = $data['oldSubscription']['endpoint'] ?? null;

        $new = $data['newSubscription'] ?? null;
        $newEndpoint = $new['endpoint'] ?? null;
        $newP256dh = $new['keys']['p256dh'] ?? null;
        $newAuth = $new['keys']['auth'] ?? null;

        if (!$oldEndpoint || !$newEndpoint || !$newP256dh || !$newAuth)
        {
            return new Response('Missing parameters in request data', Response::HTTP_BAD_REQUEST);
        }

        /** @var PwaPushSubscriberModel $subscriber */
        if (!$subscriber = PwaPushSubscriberModel::findByEndpoint($oldEndpoint))
        {
            return new Response('Could not find an existing subscription', Response::HTTP_NOT_FOUND);
        }

        if ($subscriber->pid !== $pwaConfig->id)
        {
            return new Response('Subscription does not belong to the specified notification', Response::HTTP_FORBIDDEN);
        }

        $subscriber->tstamp = time();
        $subscriber->endpoint = $newEndpoint;
        $subscriber->publicKey = $newP256dh;
        $subscriber->authToken = $newAuth;
        $subscriber->save();

        return new Response('Subscription successfully updated', Response::HTTP_OK);
    }

    #[Route('/unsubscribe', name: 'unsubscribe', methods: ['POST'])]
    public function unsubscribeAction(Request $request, int $config): Response
    {
        $this->contaoFramework->initialize();

        /** @var PwaConfigurationsModel $pwaConfig */
        if (!$pwaConfig = PwaConfigurationsModel::findByPk($config))
        {
            return new Response('Subscription not found', Response::HTTP_NOT_FOUND);
        }

        if (!($data = \json_decode($request->getContent(), true)) || !\is_array($data))
        {
            return new Response('Invalid request data', Response::HTTP_BAD_REQUEST);
        }

        if (!$endpoint = $data['subscription']['endpoint'] ?? null)
        {
            return new Response('Missing endpoint', Response::HTTP_BAD_REQUEST);
        }

        if (!$subscriber = PwaPushSubscriberModel::findBy(['endpoint=?', 'pid=?'], [$endpoint, $pwaConfig->id]))
        {
            return new Response('Subscriber not found', Response::HTTP_NOT_FOUND);
        }

        if ($subscriber instanceof Collection)
        {
            foreach ($subscriber as $entry)
            {
                if ($entry instanceof PwaPushSubscriberModel)
                {
                    $entry->delete();
                }
            }
        }
        elseif ($subscriber instanceof PwaPushSubscriberModel)
        {
            $subscriber->delete();
        }

        return new Response('Subscription successfully terminated', Response::HTTP_OK);
    }
}