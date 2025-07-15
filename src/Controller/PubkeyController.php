<?php

/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Eric Gesemann <e.gesemann@heimrich-hannot.de>
 * @license   http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\PwaBundle\Controller;

use Contao\CoreBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/_huh_pwa', name: 'huh_pwa.', defaults: ['_scope' => 'frontend', '_token_check' => false])]
class PubkeyController extends AbstractController
{
    #[Route('/.pub', name: 'pubkey', methods: ['GET'])]
    public function returnPublicKeyAction(): Response
    {
        $config = $this->getParameter('huh_pwa');

        if (\is_array($config) && $key = $config['vapid']['publicKey'] ?? null)
        {
            return new Response($key, Response::HTTP_OK, [
                'Content-Type' => 'application/x-x509-cert',
                'Content-Disposition' => 'attachment; filename="public-key.der"',
                'Content-Transfer-Encoding' => 'binary',
                'Content-Length' => \strlen($key),
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}