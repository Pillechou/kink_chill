<?php

namespace App\Controller;

use phpDocumentor\Reflection\Types\True_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class OauthController extends AbstractController
{
    private const DISCORD_ENDPOINT = "https://discord.com/api/oauth2/authorize";

    /**
     * @Route("/oauth/discord", name="oauth_discord" , methods={"GET"})
     */
    public function loginWithDiscord(CsrfTokenManagerInterface $csrfTokenManager,UrlGeneratorInterface $urlGenerator): RedirectResponse
    {

        //http://localhost:8000/login?discord-oauth-provider=1

        $redirectURL = $urlGenerator->generate("login",['discord-oauth-provider' => true], UrlGeneratorInterface::ABSOLUTE_URL);

        $queryParams = http_build_query([
            'client_id' => $this->getParameter('app.discord_client_id'),
            'prompt' => 'consent',
            'redirect_uri' => $redirectURL,
            'response_type' => 'code' ,
            'scope' => 'identify guilds' ,
            'state' => $csrfTokenManager->getToken('oauth-discord-SF')->getValue()
        ]);
        return new RedirectResponse(self::DISCORD_ENDPOINT. "?" . $queryParams);
    }
}
