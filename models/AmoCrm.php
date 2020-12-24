<?php 

namespace app\models;

use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\TextareaCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\TextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\TextareaCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\TextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\TextareaCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\TextCustomFieldValueModel;
use League\OAuth2\Client\Token\AccessToken;
use Yii;
use \AmoCRM\Client\AmoCRMApiClient;


class AmoCrm
{
    /**
     * @var
     */
    public static $access_token;

    private static $phoneCustomFieldId = 192729;
    private static $emailCustomFieldId = 192731;
    private static $positionCustomField = 192727;
    private static $addressCustomField = 192735;

    public static function getApiClient(): AmoCRMApiClient
    {
        $amo = Yii::$app->params['amocrm'];

        $clientId = $amo['resource_owner_id'];
        $clientSecret = $amo['secret_key'];
        $redirectUri = 'https://test.test';

        $apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);
        self::$access_token = $accessToken = new AccessToken($amo);

        $apiClient->setAccessToken($accessToken) 
        ->setAccountBaseDomain($accessToken->getValues()['baseDomain']);

        return $apiClient;
    }

    public static function setPhoneCustomField(CustomFieldsValuesCollection $customFieldsValuesCollection, string $phone): CustomFieldsValuesCollection
    {
        $fieldModel = new MultitextCustomFieldValuesModel();
        $fieldModel->setFieldId(self::$phoneCustomFieldId);
        $fieldModel = $fieldModel->setValues(
            (new MultitextCustomFieldValueCollection())
                ->add(
                    (new MultitextCustomFieldValueModel())->setValue($phone)
                        ->setEnum('WORK')));
        return $customFieldsValuesCollection->add($fieldModel);
    }

    public static function setEmailCustomField(CustomFieldsValuesCollection $customFieldsValuesCollection, string $email): CustomFieldsValuesCollection
    {
        $fieldModel = new MultitextCustomFieldValuesModel();
        $fieldModel->setFieldId(self::$emailCustomFieldId);
        $fieldModel = $fieldModel->setValues(
            (new MultitextCustomFieldValueCollection())
                ->add(
                    (new MultitextCustomFieldValueModel())->setValue($email)
                        ->setEnum('WORK')));
        return $customFieldsValuesCollection->add($fieldModel);
    }

    public static function setPositionCustomField(CustomFieldsValuesCollection $customFieldsValuesCollection, string $position): CustomFieldsValuesCollection
    {
        $fieldModel = new TextCustomFieldValuesModel();
        $fieldModel->setFieldId(self::$positionCustomField);
        $fieldModel = $fieldModel->setValues(
            (new TextCustomFieldValueCollection())
                ->add(
                    (new TextCustomFieldValueModel())->setValue($position)));
        return $customFieldsValuesCollection->add($fieldModel);
    }

    public static function setAddressCustomField(CustomFieldsValuesCollection $customFieldsValuesCollection, string $address): CustomFieldsValuesCollection
    {
        $fieldModel = new TextareaCustomFieldValuesModel();
        $fieldModel->setFieldId(self::$addressCustomField);
        $fieldModel = $fieldModel->setValues(
            (new TextareaCustomFieldValueCollection())
                ->add(
                    (new TextareaCustomFieldValueModel())->setValue($address)));
        return $customFieldsValuesCollection->add($fieldModel);

    }
}