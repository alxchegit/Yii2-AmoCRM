<?php
namespace app\models;

use AmoCRM\Models\CompanyModel;
use AmoCRM\Models\NoteType\CommonNote;
use Yii;
use yii\base\Model;
use AmoCRM\Helpers\EntityTypesInterface;
use app\models\AmoCrm;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Collections\LinksCollection;
use AmoCRM\Collections\NullTagsCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\CustomFieldsValues\TextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\NullCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\TextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\TextCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use League\OAuth2\Client\Token\AccessTokenInterface;

class LeedsForm extends Model
{
    public $responsibleUser;
    public $companyName;
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
        ];
    }

    public function attributeLabels()
    {
        return [
            'leadName' => 'Название сделки',
            'notes' => 'Примечание',
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
        $company = new CompanyModel();
        $note = new CommonNote();


        try {
            $lead->setName($this->leadName);
            $lead = $leadsService->addOne($lead);
            $id = $lead->getId();
            if($id){
                $note->setEntityId($id)
                    ->setText($this->notes);
                $leadNotesService = $apiClient->notes(EntityTypesInterface::LEADS);
                $note = $leadNotesService->addOne($note);


            }
        } catch (AmoCRMApiException $e) {
            echo "<pre>";
            printError($e);
            echo "</pre>";
            die;
        }

        return true;
    }

    private function addCompany(CompanyModel $companyModel)
    {
        $contacts = new ContactsCollection();
        $companyModel->setName($this->companyName)
                ->setContacts();


    }

}