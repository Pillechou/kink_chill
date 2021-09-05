<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\UserProvider\UserProviderFactoryInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;

class DiscordAuthenticator extends AbstractAuthenticator
{

    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator , CsrfTokenManagerInterface $csrfTokenManager )
    {
       $this->csrfTokenManager = $csrfTokenManager;
       $this->urlGenerator = $urlGenerator;
    }

    public function supports(Request $request): ?bool
    {
        return $request->query->has('discord-oauth-provider');
    }

    public function getCredentials(Request $request): array
    {
        $state = $request->query->get('state');
        if (!$state || !$this->csrfTokenManager->isTokenValid(new CsrfToken('oauth-discord-SF' , $state))){
            throw new AccessDeniedException('No ! ');

        }
        return [
            'code' => $request->query->get('code')
        ];
    }


    public function getUser($credentials, UserProviderInterface $userProvider){
        if ($credentials ===null){
            return null;
        }
        //return $this->discordUserProvider->loadUserFromDiscord($credentials['code']);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->urlGenerator->generate('homepage'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'message' => 'Authentification refusée'
            ], Response::HTTP_UNAUTHORIZED);
    }

   public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new JsonResponse([
            'message' => 'Authentification REQUISE'
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function authenticate(Request $request): PassportInterface
    {
        // TODO: Implement authenticate() method.
    }
}
