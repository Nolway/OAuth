<?php

namespace App\Provider;

abstract class GenericProvider
{
    protected $clientId;
    protected $clientSecret;
    protected $name;
    protected $scope;

    protected $redirectUri;
    protected $authorizationEndPoint;
    protected $accessTokenEndpoint;
    protected $usersInfoEndpoint;

    /**
     * GenericProvider constructor.
     * @param string $name
     * @param string $clientId
     * @param string $clientSecret
     * @param string $scope
     * @param string $redirectUri
     */
    public function __construct(string $name, string $clientId, string $clientSecret, string $redirectUri, string $scope)
    {
        $this->name = $name;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->scope = $scope;
        $this->redirectUri = $redirectUri;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * @return string
     */
    public function getProviderName(): string
    {
        return $this->name;
    }

    /**
     * Generate a random state
     *
     * @return string
     */
    public function generateState(): string
    {
        try {
            $state = bin2hex(random_bytes(40));
            $_SESSION['OAUTH_' . strtoupper($this->name) . '_STATE'] = $state;
            return $state;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return "error";
        }
    }

    /**
     * Create CURL request
     *
     * @param string $url
     * @return false|resource
     */
    protected function createCurlRequest(string $url)
    {
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_HEADER, 0);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $t_vers = curl_version();
        curl_setopt( $handle, CURLOPT_USERAGENT, 'curl/' . $t_vers['version'] );
        return $handle;
    }

    /**
     * Execute a CURL request and return the result
     *
     * @param string $url
     * @return array
     */
    protected function request(string $url): array
    {
        $handle = $this->createCurlRequest($url);
        $result = curl_exec($handle);
        if (curl_errno($handle)) {
            echo 'Curl error: ' . curl_error($handle);
        }
        curl_close($handle);

        $data = json_decode($result, true);
        if (empty($data)) {
            parse_str($result, $data);
        }

        return $data;
    }

    /**
     * Get authorization data
     * @return string
     */
    public function getAuthorizationUrl(): string
    {
        $parameters = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'state' => $this->generateState(),
            'scope' => $this->scope ?? 'email'
        ];
        return $this->authorizationEndPoint . '?' . http_build_query($parameters);
    }

    /**
     * Check if the state is the same on session
     *
     * @param string $state
     * @return bool
     */
    public function checkState(string $state): bool
    {
        return $_SESSION['OAUTH_' . strtoupper($this->name) . '_STATE'] == $state;
    }

    /**
     * Get access token
     *
     * @param string $code
     * @return array
     */
    public function getAccessTokenUrl(string $code): array
    {
        $parameters = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'code' => $code
        ];

        $url = $this->accessTokenEndpoint . '?' . http_build_query($parameters);
        return $this->request($url);
    }

    /**
     * Get user data
     *
     * @param string $token
     * @return array
     */
    public function getUsersInfo(string $token): array
    {
        $parameters = [
            'client_id' => $this->clientId,
            'access_token' => $token
        ];

        if ($this->scope != "") {
            $parameter['scope'] = $this->scope;
        }

        $url = $this->usersInfoEndpoint . '?' . http_build_query($parameters);
        return $this->request($url);
    }
}
