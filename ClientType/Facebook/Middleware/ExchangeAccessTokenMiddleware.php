<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\ClientType\Facebook\Middleware;


use Andreo\OAuthApiConnectorBundle\AccessToken\AccessToken;
use Andreo\OAuthApiConnectorBundle\Client\Attribute\AttributeBag;
use Andreo\OAuthApiConnectorBundle\Http\OAuthClientInterface;
use Andreo\OAuthApiConnectorBundle\Http\Query\ExchangeAccessTokenQuery;
use Andreo\OAuthApiConnectorBundle\Middleware\MiddlewareInterface;
use Andreo\OAuthApiConnectorBundle\Middleware\MiddlewareStackInterface;
use Andreo\OAuthApiConnectorBundle\Storage\StorageContext;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ExchangeAccessTokenMiddleware implements MiddlewareInterface
{
    private OAuthClientInterface $httpClient;

    private StorageContext $storageContext;

    public function __construct(OAuthClientInterface $httpClient, StorageContext $storageContext)
    {
        $this->httpClient = $httpClient;
        $this->storageContext = $storageContext;
    }

    public function __invoke(Request $request, MiddlewareStackInterface $stack): Response
    {
        $attributeBag = AttributeBag::get($request);
        if (!$attributeBag->hasCallbackResponse()) {
            return $stack->next()($request, $stack);
        }

        if (!$this->storageContext->has($attributeBag->getClientId(), AccessToken::class)) {
            throw new RuntimeException('Cannot exchange not existing access token.');
        }

        /** @var AccessToken $accessToken */
        $accessToken = $this->storageContext->get($attributeBag->getClientId(), AccessToken::class);
        $query = ExchangeAccessTokenQuery::from($attributeBag, $accessToken);

        $accessToken = $this->httpClient->getAccessToken($query);

        $this->storageContext->delete($attributeBag->getClientId(), AccessToken::class);
        $this->storageContext->store($attributeBag->getClientId(), $accessToken);

        return $stack->next()($request, $stack);
    }
}
