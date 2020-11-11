<?php


namespace Andreo\OAuthClientBundle\Middleware;


use Andreo\OAuthClientBundle\AccessToken\AccessToken;
use Andreo\OAuthClientBundle\AccessToken\AccessTokenInterface;
use Andreo\OAuthClientBundle\Client\RequestContext\Context;
use Andreo\OAuthClientBundle\Exception\InvalidAccessTokenException;
use Symfony\Component\HttpFoundation\Request;

trait AccessTokenAttributeTrait
{
    private function fromAttributes(Request $request, Context $context): AccessTokenInterface
    {
        $accessTokenStorageKey = AccessToken::getKey($context->getClientId());
        if (!$request->attributes->has($accessTokenStorageKey)) {
            throw new InvalidAccessTokenException('Can not save access token because it not exist.');

        }

        /** @var AccessToken $accessToken */
        $accessToken = $request->attributes->get($accessTokenStorageKey);
        if (!$accessToken instanceof AccessTokenInterface) {
            throw new InvalidAccessTokenException();
        }

        return $accessToken;
    }
}
