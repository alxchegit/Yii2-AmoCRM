<?php

namespace app\models;

use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Models\ContactModel;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ContactForm extends Model
{
    public $name;
    public $email;
    public $position;
    public $phone;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            ['email', 'email'],
            [['name','phone','position','email'], 'string', 'min' => 3],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'name' => 'ФИО',
            'phone' => 'Телефон',
            'position' => 'Должность',
        ];
    }

    /**
     * @return bool
     */
    public function create()
    {
        if(!$this->validate()){
            return false;
        }

        $apiClient = AmoCrm::getApiClient();
        $newContact = new ContactModel();
        $newContact = $newContact->setName($this->name);

        $customFieldsValuesCollection = new CustomFieldsValuesCollection();

        if($this->phone){
            $customFieldsValuesCollection = AmoCrm::setPhoneCustomField($customFieldsValuesCollection, $this->phone);
        }

        if($this->email){
            $customFieldsValuesCollection = AmoCrm::setEmailCustomField($customFieldsValuesCollection, $this->email);
        }

        if($this->position){
            $customFieldsValuesCollection = AmoCrm::setPositionCustomField($customFieldsValuesCollection, $this->position);
        }

        $newContact->setCustomFieldsValues($customFieldsValuesCollection);
        try {
            $apiClient->contacts()->addOne($newContact);
        } catch (AmoCRMApiException $e) {
            return false;
        }

        return true;

    }
}
