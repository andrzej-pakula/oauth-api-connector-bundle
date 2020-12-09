<?php


namespace Andreo\OAuthClientBundle\Client\AccessToken\Query;


use Andreo\OAuthClientBundle\Client\RedirectUri\RedirectUri;

trait GetAccessTokenTrait
{
    private string $clientId;

    private string $clientSecret;

    private string $code;

    private string $redirectUri;

    public function __construct(string $clientId, string $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    public function withCode(string $code): GetAccessTokenInterface
    {
        /** @var self&GetAccessTokenInterface $new */
        $new = clone $this;
        $new->code = $code;

        return $new;
    }

    public function withRedirectUri(RedirectUri $redirectUri): GetAccessTokenInterface
    {
        /** @var self&GetAccessTokenInterface $new */
        $new = clone $this;
        $new->redirectUri = $redirectUri->getUri();

        return $new;
    }
}
