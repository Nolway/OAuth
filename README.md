
# OAuth

> OAuth PHP module

## How to test the module ?

```sh
docker-compose up -d
```
Launch test environment on localhost:8080

## Usage
*Only Facebook & Github providers are currently available.*

Create a new OAuth object with the new provider configuration to get the access to the provider :
```php
$oauthProvider = new \App\OAuth([
'provider_name' => 'facebook', // It's the provider who will be use
'client_id' => '613915182434272', // The API client ID
'client_secret' => '329956aa88fa854f7481a87a476f6978', // The API secret key
'redirect_uri' => 'http://localhost:8080/provider-callback', // Where users will be redirect after the authorize redirection
'scope' => 'email' // See the API documentation to find out which ones are available
]);
```

You can get the authorization link with the **getConnectionLink** function :
```php
$connectionLink = $oauthProvider->getConnectionLink();
```

You can get user data find by the scope with the **getUserInfo** function with the authorization code & the state return on the redirection URI :
```php
$userData = $oauthProvider->getUserInfos($_GET['state'], $_GET['code']);
```

## Add a new provider

To add a new provider if this doesn't have weird process you just create a new class with this layout :
```php
<?php

namespace App\Provider;

class MyProviderProvider extends GenericProvider
{
    /**
     * MyProviderProvider constructor.
     * @param string $clientId
     * @param string $clientSecret
     * @param string $scope
     * @param string $redirectUri
     */
    public function __construct(string $clientId, string $clientSecret, string $redirectUri, string $scope = "")
    {
        parent::__construct(
            'myprovider',
            $clientId,
            $clientSecret,
            $redirectUri,
            $scope
        );

        $this->authorizationEndPoint = "https://myprovider.com/oauth";
        $this->accessTokenEndpoint = "https://myprovider.com/oauth/access_token";
        $this->usersInfoEndpoint = "https://myprovider.com/userdata";
    }
}
```

Just change the provider name & these end points.
