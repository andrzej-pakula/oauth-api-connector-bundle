<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Middleware;


use Andreo\OAuthClientBundle\Client\AccessToken\AccessToken;
use Andreo\OAuthClientBundle\Client\ClientContext;
use Andreo\OAuthClientBundle\Client\HTTPContext;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

final class StoreAccessTokenAsCookieMiddleware implements MiddlewareInterface
{
    use GetAccessTokenFromAttributesTrait;

    public function __invoke(HTTPContext $httpContext, ClientContext $clientContext, MiddlewareStackInterface $stack): Response
    {
        if (!$httpContext->isCallback()) {
            return $stack->next()($httpContext, $clientContext, $stack);
        }

        /** @var AccessToken $accessToken */
        $accessToken = $this->fromAttributes($httpContext, $clientContext);

        $now = new DateTimeImmutable('now', new DateTimeZone('Europe/Warsaw'));
        $expiredAt = $now->add(new DateInterval("PT{$accessToken->getExpiresIn()}S"));

        $accessTokenStorageKey = $accessToken::getKey($clientContext->getClientName());
        $cookie = Cookie::create($accessTokenStorageKey)
            ->withSecure(true)
            ->withValue($accessToken->encrypt())
            ->withExpires($expiredAt);

        $response = $httpContext->getResponse();
        $request = $httpContext->getRequest();

        $response->headers->setCookie($cookie);

        $request->attributes->remove($accessTokenStorageKey);

        return $response;
    }
}
