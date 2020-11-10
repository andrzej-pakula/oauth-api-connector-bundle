<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Http\Provider;


use Andreo\GuzzleBundle\Configurator\ConfigProviderInterface;

final class HttpClientConfigProvider implements ConfigProviderInterface
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getConfig(): array
    {
        return [
            'base_uri' => $this->config['api_uri'],
        ];
    }

    public static function fromConfig(array $config): self
    {
        return new self($config);
    }
}
