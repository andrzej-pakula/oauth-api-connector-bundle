<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Client;

use Andreo\OAuthApiConnectorBundle\Client\Attribute\Attributes;
use Andreo\OAuthApiConnectorBundle\Middleware\MiddlewareInterface;

final class ClientFactory implements ClientFactoryInterface
{
    private MetaDataProviderRegistry $metaDataProviderRegistry;

    public function __construct(MetaDataProviderRegistry $metaDataProviderRegistry)
    {
        $this->metaDataProviderRegistry = $metaDataProviderRegistry;
    }

    /**
     * @param iterable<MiddlewareInterface>
     */
    public function __invoke(array $config, iterable $middleware): ClientInterface
    {
        $metaDataProvider = $this->metaDataProviderRegistry->get(
            $config['type'],
            $config['version']
        );

        $attributes = Attributes::fromConfig(
            array_merge(
                $config,
                [
                    'callback_uri' => 'andreo.oauth.client.authorize',
                    'authorization_uri' => $metaDataProvider::getAuthorizationUri(),
                ]
            )
        );

        return new Client($middleware, $attributes);
    }
}
