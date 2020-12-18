<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use \AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Models\LeadModel;
use yii\web\BadRequestHttpException;
use app\models\LeedsForm;
use app\models\AmoCrm;

class SiteController extends Controller
{
    /**
     * @var \League\OAuth2\Client\Token\AccessToken $access_token
     */
    private $access_token;
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $apiClient = $this->AmoCrmConstruct();
        
        try {
            $leadsService = $apiClient->leads();
            $leadsCollection = $leadsService->get($filter = null, $with = [
                'contacts',
                ]);            
            $usersService = $apiClient->users();
            $contactsService = $apiClient->contacts();
            $companiesService = $apiClient->companies();

        } catch (\Throwable $th) {
           
           $error = json_decode($th->getMessage(),true);
           if($error['status'] === 401){
               $this->getAccessTokenByRefresh();
           }

           return $this->render('error', [
               'message' => $th->getMessage(),
           ]);
        }   
        

        return $this->render('index', [
            'leads' => $leadsCollection,
            'users' => $usersService,
            'contacts' => $contactsService,
            'companies' => $companiesService,
        ]);
    }

    /**
     * Action добавление сделок
     */

    public function actionCreate()
    {
        $apiClient = $this->AmoCrmConstruct();

        $model = new LeedsForm();

        if ($model->load(Yii::$app->request->post()) && $model->create()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);

    }

    private function AmoCrmConstruct():  AmoCRMApiClient
    {
        $this->access_token = AmoCrm::$access_token;
       return AmoCrm::construct();
    }

    /**
     * get Access Token by refresh_token
     */    
    private function getAccessTokenByRefresh() 
    {
        $amo = Yii::$app->params['amocrm'];
        $subdomain = 'alxche'; //Поддомен нужного аккаунта
        $link = 'https://' . $subdomain . '.amocrm.ru/oauth2/access_token'; //Формируем URL для запроса
        /** Соберем данные для запроса */
        $data = [
            'client_id' => $amo['resource_owner_id'],
            'client_secret' => $amo['secret_key'],
            'grant_type' => 'refresh_token',
            'refresh_token' => $amo['refresh_token'],
            'redirect_uri' => 'https://u96417.test-handyhost.ru/',
        ];

        $curl = curl_init(); 
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
        curl_setopt($curl,CURLOPT_URL, $link);
        curl_setopt($curl,CURLOPT_HTTPHEADER,['Content-Type:application/json']);
        curl_setopt($curl,CURLOPT_HEADER, false);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
        $out = curl_exec($curl);  
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $code = (int)$code;
        $errors = [
            400 => 'Bad request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not found',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable',
        ];
        try
        {
            /** Если код ответа не успешный - возвращаем сообщение об ошибке  */
            if ($code < 200 || $code > 204) {
                throw new BadRequestHttpException(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
            }
        }
        catch(\Exception $e)
        {
            die('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode());
        }

        $response = json_decode($out, true);
        $amo['access_token'] = $response['access_token'];
        $amo['refresh_token'] = $response['refresh_token'];
        $amo['expires_in'] = $response['expires_in'];
        $amo['expires'] = time() + $response['expires_in'];
        $amo_json = json_encode($amo);

        if(
            file_put_contents('tmp/token1.json', $amo_json)
        ) {
            $this->goHome();
        } 
    }


}
