<?php


namespace app\models;

use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Models\CustomFieldsValues\TextareaCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\TextareaCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\TextareaCustomFieldValueModel;
use yii\base\Model;
use AmoCRM\Models\CompanyModel;


class CompanyForm extends Model
{
    public $name;
    public $address;
    public $phone;
    public $email;

    public function rules()
    {
        return [
            ['email', 'email'],
            ['address', 'string'],
            [['name','phone'], 'string', 'min'=>3],

            ['name', 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name'=>'Название',
            'address'=>'Адрес',
            'phone' => 'Телефон',
        ];
    }

    public function create()
    {
        if(!$this->validate()){
            return false;
        }
        $apiClient = AmoCrm::getApiClient();
        $company = new CompanyModel();
        $company->setName($this->name);

        $customFieldsValuesCollection = new CustomFieldsValuesCollection();

        if($this->phone){
            $customFieldsValuesCollection = AmoCrm::setPhoneCustomField($customFieldsValuesCollection, $this->phone);
        }

        if($this->email){
            $customFieldsValuesCollection = AmoCrm::setEmailCustomField($customFieldsValuesCollection, $this->email);
        }

        if($this->address){
            $emailcf = new TextareaCustomFieldValuesModel();
            $emailcf->setFieldId(192735);
            $emailcf = $emailcf->setValues(
                (new TextareaCustomFieldValueCollection())
                    ->add(
                        (new TextareaCustomFieldValueModel())->setValue($this->address)));
            $customFieldsValuesCollection->add($emailcf);
        }

        try {
            $company->setCustomFieldsValues($customFieldsValuesCollection);
            $apiClient->companies()->addOne($company);
            return true;
        } catch (AmoCRMApiException $e) {
            print_r($e);
            die;
        }


    }
}