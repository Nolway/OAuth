<?php

namespace App\Provider;

class FacebookProvider extends GenericProvider
{
    /**
     * FacebookProvider constructor.
     * @param string $clientId
     * @param string $clientSecret
     * @param string $scope
     * @param string $redirectUri
     */
    public function __construct(string $clientId, string $clientSecret, string $redirectUri, string $scope = "")
    {
        parent::__construct(
            'facebook',
            $clientId,
            $clientSecret,
            $redirectUri,
            $scope
        );

        $this->authorizationEndPoint = "https://www.facebook.com/v3.3/dialog/oauth";
        $this->accessTokenEndpoint = "https://graph.facebook.com/oauth/access_token";
        $this->usersInfoEndpoint = "https://graph.facebook.com/me";
    }
}
