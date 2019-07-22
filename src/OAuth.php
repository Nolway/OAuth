<?php

namespace App;

use App\Provider\GenericProvider;

class OAuth
{
    private $provider;

    /**
     * OAuth constructor.
     * @param array $conf
     */
    public function __construct(array $conf)
    {
        $this->provider = $this->getProvider($conf);
    }

    /**
     * Get provider
     *
     * @param array $conf
     * @return GenericProvider|null
     */
    private function getProvider(array $conf): ?GenericProvider
    {
        if (!$this->hasRequiredParameters($conf)) {
            return null;
        }

        $classPath = "App\\Provider\\" . ucfirst(strtolower($conf['provider_name'])) . "Provider";

        if (!class_exists($classPath)) {
            return null;
        }

        return new $classPath(
            $conf['client_id'],
            $conf['client_secret'],
            $conf['redirect_uri'],
            $conf['scope'] ?? ""
        );
    }

    /**
     * Check if there required parameters
     *
     * @param array $conf
     * @return bool
     */
    private function hasRequiredParameters(array $conf): bool
    {
        if (empty($conf['provider_name'])) {
            return false;
        }

        if (empty($conf['client_id'])) {
            return false;
        }

        if (empty($conf['client_secret'])) {
            return false;
        }

        if (empty($conf['redirect_uri'])) {
            return false;
        }

        return true;
    }

    /**
     * Get connection link of the provider
     *
     * @return string
     */
    public function getConnectionLink(): string
    {
        return $this->provider->getAuthorizationUrl();
    }

    /**
     * Get user data
     *
     * @param string $state
     * @param string $code
     * @return array|null
     */
    public function getUserInfos(string $state, string $code): ?array
    {
        if (!$this->provider->checkState($state)) {
            return null;
        }

        $tokenInformation = $this->provider->getAccessTokenUrl($code);
        return $this->provider->getUsersInfo($tokenInformation['access_token']);
    }
}
