<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$oauthFacebook = new \App\OAuth([
    'provider_name' => 'facebook',
    'client_id' => '613915182434272',
    'client_secret' => '329956aa88fa854f7481a87a476f6978',
    'redirect_uri' => 'http://localhost:8080/facebook-callback',
    'scope' => 'email'
]);

$oauthGithub = new \App\OAuth([
    'provider_name' => 'github',
    'client_id' => '80638882c990d28898ca',
    'client_secret' => '29b74851267ccd1b0c1248ba1546e044e33035e1',
    'redirect_uri' => 'http://localhost:8080/github-callback',
    'scope' => 'user:email'
]);

$path = strtok($_SERVER['REQUEST_URI'], '?');
switch($path) {
    case '/facebook-callback':
        $userData = $oauthFacebook->getUserInfos($_GET['state'], $_GET['code']);
        echo "<p>Bienvenue " . $userData['name'] . "</p>";
        break;
    case '/github-callback':
        $userData = $oauthGithub->getUserInfos($_GET['state'], $_GET['code']);
        echo "<p>Bienvenue " . $userData['name'] . "</p>";
        break;
}
?>

<?php if ($path == "/") : ?>
    <a href="<?= $oauthFacebook->getConnectionLink() ?>">Se connecter à Facebook</a><br>
    <a href="<?= $oauthGithub->getConnectionLink() ?>">Se connecter à Github</a>
<? endif; ?>
