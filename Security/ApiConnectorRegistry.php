<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Security;


use InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ApiConnectorRegistry
{
    private ContainerInterface $container;

    /** @var string[] */
    private array $clientIds;

    /**
     * @param string[] $clientIds
     */
    public function __construct(ContainerInterface $container, array $clientIds)
    {
        $this->container = $container;
        $this->clientIds = $clientIds;
    }

    public function get(string $name): ApiConnectorInterface
    {
        if (in_array($name, $this->clientIds, true)) {
            $client = $this->container->get($name);
            if (!$client instanceof ApiConnectorInterface) {
                throw new InvalidArgumentException(sprintf('Somehow the "%s" client is not an instance of ApiConnectorInterface.', $name));
            }

            return $client;
        }

        throw new InvalidArgumentException(
            sprintf('There is no oauth api connector called "%s". Available are: %s', $name, implode(', ', $this->clientIds))
        );
    }

    public function isEmpty(): bool
    {
        return [] === $this->clientIds;
    }
}