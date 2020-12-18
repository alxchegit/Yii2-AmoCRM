<?php 

namespace app\models;

use Yii;
use \AmoCRM\Client\AmoCRMApiClient;


class AmoCrm
{
    /**
     * @var
     */
    public static $access_token;


    public static function construct()
    {
        $amo = Yii::$app->params['amocrm'];

        $clientId = $amo['resource_owner_id'];
        $clientSecret = $amo['secret_key'];
        $redirectUri = 'https://test.test';

        $apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);
        self::$access_token = $accessToken = new \League\OAuth2\Client\Token\AccessToken($amo);

        $apiClient->setAccessToken($accessToken) 
        ->setAccountBaseDomain($accessToken->getValues()['baseDomain']);

        return $apiClient;
    }
}