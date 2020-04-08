<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Util;

use Andreo\OAuthApiConnectorBundle\Security\StateGeneratorInterface;
use Andreo\OAuthApiConnectorBundle\Provider\ApiConnectorProviderInterface;

final class ApiURLBuilder
{
    private ApiConnectorProviderInterface $provider;
    private string $clientId;
    private string $clientSecret;
    private string $connectURL;

    public function __construct(ApiConnectorProviderInterface $provider, string $clientId, string $clientSecret, string $connectURL)
    {
        $this->provider = $provider;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->connectURL = $connectURL;
    }

    public function buildAccessTokenQuery(string $code): string
    {
        return $this->buildQuery([
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'redirect_uri' => $this->connectURL,
        ]);
    }

    private function buildLoginQuery(StateGeneratorInterface $stateGenerator): string
    {
        $state = $stateGenerator->generate();

        return $this->buildQuery([
            'client_id' => $this->clientId,
            'state' => $state,
            'response_type' => 'code',
            'redirect_uri' => $this->connectURL,
        ]);
    }

    public function buildLoginURL(StateGeneratorInterface $stateGenerator): string
    {
        return $this->provider->getLoginURI() . '/' . $this->provider->getApiVersion() . $this->buildLoginQuery($stateGenerator);
    }

    public function buildQuery(array $params): string
    {
        return '?' . http_build_query($params);
    }
}