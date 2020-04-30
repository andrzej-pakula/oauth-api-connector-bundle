<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Security;


use Andreo\OAuthApiConnectorBundle\Provider\ApiConnectorProviderInterface;
use Andreo\OAuthApiConnectorBundle\Util\ApiURLBuilder;
use LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use GuzzleHttp\ClientInterface;

final class ApiConnector implements ApiConnectorInterface
{
    private RequestStack $requestStack;
    private ApiConnectorProviderInterface $provider;
    private StateGeneratorInterface $stateGeneratorInterface;
    private ClientInterface $client;
    private ApiURLBuilder $urlBuilder;
    private string $redirectRoute;

    public function __construct(
        RequestStack $requestStack,
        ApiConnectorProviderInterface $provider,
        ClientInterface $client,
        string $clientId,
        string $clientSecret,
        string $connectURL,
        string $redirectRoute
    ){
        $this->requestStack = $requestStack;
        $this->provider = $provider;
        $this->client = $client;
        $this->redirectRoute = $redirectRoute;
        $this->urlBuilder = new ApiURLBuilder($provider, $clientId, $clientSecret, $connectURL);
    }

    public function getCode(): string
    {
        if (!$this->hasCode()) {
            throw new LogicException('There is no code.');
        }

        return $this->getCurrentRequest()->query->get('code');
    }

    public function hasCode(): bool
    {
        return $this->getCurrentRequest()->query->has('code');
    }

    public function isValidState(): bool
    {
        return true;
    }

    public function askAccessToken(): AccessToken
    {
        $accessTokenPath = $this->provider->getAccessTokenPath();

        /** @var AccessToken $accessToken */
        $accessToken = $this->client->get($accessTokenPath, [
            'query' =>  $this->urlBuilder->buildAccessTokenQuery($this->getCode()),
            'dto' => null
        ]);

        return $accessToken;
    }

    public function keepToken(AccessToken $accessToken): void
    {
    }

    public function getLoginURL(): string
    {
        return $this->urlBuilder->buildLoginURL($this->stateGeneratorInterface);
    }

    private function getCurrentRequest(): Request
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            throw new \LogicException('There is no request.');
        }

        return $request;
    }

    public function getRedirectRoute(): string
    {
        return $this->redirectRoute;
    }
}