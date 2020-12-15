<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Http\Provider;

use Andreo\GuzzleBundle\Configurator\ConfigProviderInterface;

final class HttpClientConfigProvider implements ConfigProviderInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $config;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getConfig(): array
    {
        return [
            'base_uri' => $this->config['api_uri'],
            'headers' => $this->config['headers'] ?? [],
        ];
    }

    /**
     * @param array<string, mixed> $config
     */
    public static function fromConfig(array $config): self
    {
        return new self($config);
    }
}
