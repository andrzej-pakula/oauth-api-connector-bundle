<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Client;


final class MetaDataProviderRegistry
{
    /**
     * @var iterable<MetaDataProviderInterface>
     */
    private iterable $providers;

    public function __construct(iterable $providers)
    {
        $this->providers = $providers;
    }

    public function get(string $type, ?string $version): MetaDataProviderInterface
    {
        if (null === $provider = $this->findProvider($type, $version)) {
            throw new \RuntimeException("Can not find client provider for type $type and version $version");
        }

        return $provider;
    }

    private function findProvider(string $type, ?string $version): ?MetaDataProviderInterface
    {
        /** @var MetaDataProviderInterface $provider */
        foreach ($this->providers as $provider) {
            if ($type === $provider::getType() && (null === $version || $version === $provider::getVersion())) {
                return $provider;
            }
        }

        return null;
    }
}
