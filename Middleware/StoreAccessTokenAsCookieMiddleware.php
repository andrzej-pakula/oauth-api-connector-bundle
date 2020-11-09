<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Middleware;


use Andreo\OAuthApiConnectorBundle\AccessToken\AccessToken;
use Andreo\OAuthApiConnectorBundle\Client\Attribute\AttributeBag;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class StoreAccessTokenAsCookieMiddleware implements MiddlewareInterface
{
    use StoreAccessTokenTrait;

    public function __invoke(Request $request, Response $response, MiddlewareStackInterface $stack): Response
    {
        $attributeBag = AttributeBag::get($request);
        if (!$attributeBag->hasCallbackResponse()) {
            return $stack->next()($request, $response, $stack);
        }

        /** @var AccessToken $accessToken */
        $accessToken = $this->getAccessToken($request, $attributeBag);

        $now = new DateTimeImmutable('now', new DateTimeZone('Europe/Warsaw'));
        $expiredAt = $now->add(new DateInterval("PT{$accessToken->getExpiresIn()}S"));

        $cookie =  Cookie::create($accessToken::getKey($attributeBag->getClientId()))
            ->withSecure(true)
            ->withValue($accessToken->encrypt())
            ->withExpires($expiredAt);

        $response->headers->setCookie($cookie);

        return $response;
    }
}
