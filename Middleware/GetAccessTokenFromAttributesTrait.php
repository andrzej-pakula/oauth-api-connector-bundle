<?php


namespace Andreo\OAuthClientBundle\Middleware;


use Andreo\OAuthClientBundle\Client\AccessToken\AccessToken;
use Andreo\OAuthClientBundle\Client\AccessToken\AccessTokenInterface;
use Andreo\OAuthClientBundle\Client\ClientContext;
use Andreo\OAuthClientBundle\Client\HTTPContext;
use Andreo\OAuthClientBundle\Exception\MissingAccessTokenException;

trait GetAccessTokenFromAttributesTrait
{
    private function fromAttributes(HTTPContext $httpContext, ClientContext $clientContext): AccessTokenInterface
    {
        $request = $httpContext->getRequest();
        $accessTokenStorageKey = AccessToken::getKey($clientContext->getClientName());
        if (!$request->attributes->has($accessTokenStorageKey)) {
            throw new MissingAccessTokenException('Can not save access token because it not exist.');
        }

        return $request->attributes->get($accessTokenStorageKey);
    }
}
