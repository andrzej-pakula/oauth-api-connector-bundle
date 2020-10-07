<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Http\Provider;


use Andreo\GuzzleBundle\Configurator\ConfigProviderInterface;
use Andreo\OAuthApiConnectorBundle\Client\MetaDataProviderInterface;

final class ApiConfigProviderAdapter implements ConfigProviderInterface
{
    private MetaDataProviderInterface $clientProvider;

    /**
     * @param MetaDataProviderInterface $clientProvider
     */
    public function __construct(MetaDataProviderInterface $clientProvider)
    {
        $this->clientProvider = $clientProvider;
    }

    public function getConfig(): array
    {
        return [
            'base_uri' => $this->clientProvider::getApiUri(),
        ];
    }
}
