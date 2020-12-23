<?php
namespace app\models;

use AmoCRM\Models\NoteType\CommonNote;
use yii\base\Model;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Models\LeadModel;

class LeedsForm extends Model
{
    public $responsibleUser;
    public $company;
    public $companyPhone;
    public $companyEmail;
    public $companyAddress;
    public $contact;
    public $task;
    public $leadName;
    public $notes;

    /**
    * @return array the validation rules.
    */
    public function rules()
    {
        return [
            [['leadName'], 'required'],
            [['notes', 'leadName'], 'string'],
            [['company', 'contact'], 'exist'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'leadName' => 'Название сделки',
            'notes' => 'Примечание',
            'company' => 'Компания',
            'contacts' => 'Контакт',
        ];
    }

    public function create()
    {
        if(!$this->validate()){
            return false;
        }
        $apiClient = AmoCrm::getApiClient();
        $leadsService = $apiClient->leads();
        $lead = new LeadModel();
        $note = new CommonNote();


        try {
            $lead->setName($this->leadName);
            $lead = $leadsService->addOne($lead);
            $id = $lead->getId();
            if($id){
                $note->setEntityId($id)
                    ->setText($this->notes);
                $leadNotesService = $apiClient->notes(EntityTypesInterface::LEADS);
                $leadNotesService->addOne($note);
            }
        } catch (AmoCRMApiException $e) {
            echo "<pre>";
            printError($e);
            echo "</pre>";
            die;
        }

        return true;
    }

}