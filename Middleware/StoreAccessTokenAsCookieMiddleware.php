<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Middleware;


use Andreo\OAuthClientBundle\AccessToken\AccessToken;
use Andreo\OAuthClientBundle\Client\RequestContext\Context;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class StoreAccessTokenAsCookieMiddleware implements MiddlewareInterface
{
    use AccessTokenAttributeTrait;

    public function __invoke(Request $request, Response $response, MiddlewareStackInterface $stack): Response
    {
        $context = Context::get($request);
        if (!$context->hasCallbackResponse()) {
            return $stack->next()($request, $response, $stack);
        }

        /** @var AccessToken $accessToken */
        $accessToken = $this->fromAttributes($request, $context);

        $now = new DateTimeImmutable('now', new DateTimeZone('Europe/Warsaw'));
        $expiredAt = $now->add(new DateInterval("PT{$accessToken->getExpiresIn()}S"));

        $accessTokenStorageKey = $accessToken::getKey($context->getClientId());
        $cookie = Cookie::create($accessTokenStorageKey)
            ->withSecure(true)
            ->withValue($accessToken->encrypt())
            ->withExpires($expiredAt);

        $response->headers->setCookie($cookie);

        $request->attributes->remove($accessTokenStorageKey);

        return $response;
    }
}
