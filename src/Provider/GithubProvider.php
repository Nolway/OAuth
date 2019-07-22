<?php

namespace App\Provider;

class GithubProvider extends GenericProvider
{
    /**
     * GithubProvider constructor.
     * @param string $clientId
     * @param string $clientSecret
     * @param string $scope
     * @param string $redirectUri
     */
    public function __construct(string $clientId, string $clientSecret, string $redirectUri, string $scope = "")
    {
        parent::__construct(
            'github',
            $clientId,
            $clientSecret,
            $redirectUri,
            $scope
        );

        $this->authorizationEndPoint = "https://github.com/login/oauth/authorize";
        $this->accessTokenEndpoint = "https://github.com/login/oauth/access_token";
        $this->usersInfoEndpoint = "https://api.github.com/user";
    }

    /**
     * Get user data with a bearer authorization
     *
     * @param string $url
     * @param string $accessToken
     * @return array
     */
    private function userDataRequest(string $url, string $accessToken): array
    {
        $handle = $this->createCurlRequest($url);
        $authorization = "Authorization: Bearer ".$accessToken;
        curl_setopt($handle, CURLOPT_HTTPHEADER, array($authorization));

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
     * Get user data
     *
     * @override
     * @param string $token
     * @return array
     */
    public function getUsersInfo(string $token): array
    {
        return $this->userDataRequest($this->usersInfoEndpoint, $token);
    }
}
