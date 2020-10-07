<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\ClientType\GitHub\Provider;


use Andreo\OAuthApiConnectorBundle\Client\MetaDataProviderInterface;

final class BaseMetaDataProvider implements MetaDataProviderInterface
{
    use MetaDataProviderTrait;
}
