<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Http\Provider;


use Andreo\GuzzleBundle\Configurator\ConfigProviderInterface;
use Andreo\OAuthApiConnectorBundle\Client\MetaDataProviderRegistry;

final class ApiConfigProviderFactory
{
    private MetaDataProviderRegistry $metaDataProviderRegistry;

    public function __construct(MetaDataProviderRegistry $metaDataProviderRegistry)
    {
        $this->metaDataProviderRegistry = $metaDataProviderRegistry;
    }

    public function __invoke(string $type, ?string $version): ConfigProviderInterface
    {
        $metaDataProvider = $this->metaDataProviderRegistry->get($type, $version);

        return new ApiConfigProviderAdapter($metaDataProvider);
    }
}
