<?php


namespace Andreo\OAuthApiConnectorBundle\Middleware;


use Andreo\OAuthApiConnectorBundle\AccessToken\AccessToken;
use Andreo\OAuthApiConnectorBundle\AccessToken\AccessTokenInterface;
use Andreo\OAuthApiConnectorBundle\Client\Attribute\AttributeBag;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;

trait StoreAccessTokenTrait
{
    private function getAccessToken(Request $request, AttributeBag $attributeBag): AccessTokenInterface
    {
        $accessTokenStorageKey = AccessToken::getKey($attributeBag->getClientId());
        if (!$request->attributes->has($accessTokenStorageKey)) {
            throw new RuntimeException('Can not save access token because it not exist.');
        }

        /** @var AccessToken $accessToken */
        $accessToken = $request->attributes->get($accessTokenStorageKey);
        if (!$accessToken instanceof AccessTokenInterface) {
            throw new RuntimeException('Invalid access token.');
        }

        return $accessToken;
    }
}
