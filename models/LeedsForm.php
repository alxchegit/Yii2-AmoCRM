<?php
namespace app\models;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\LinksCollection;
use AmoCRM\Models\NoteType\CommonNote;
use Yii;
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
            [['leadName','company', 'contact'], 'required'],
            [['notes', 'leadName'], 'string'],
            [['notes', 'leadName'], 'trim'],
            [['contact', 'company'], 'integer']
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
        $company = $this->getCompany($apiClient);
        $contact = $this->getContact($apiClient);

        $links = new LinksCollection();
        $links->add($company)->add($contact);

        try {
            $lead->setName($this->leadName);
            $lead = $leadsService->addOne($lead);
            $id = $lead->getId();
            if($id && $this->notes){
                $note = new CommonNote();
                $note->setEntityId($id)
                    ->setText($this->notes);
                $leadNotesService = $apiClient->notes(EntityTypesInterface::LEADS);
                $leadNotesService->addOne($note);
            }
        } catch (AmoCRMApiException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return false;
        }

        $apiClient->leads()->link($lead, $links);

        return true;
    }

    /**
     * @param AmoCRMApiClient $apiClient
     * @return \AmoCRM\Models\CompanyModel|null
     * @throws AmoCRMApiException
     * @throws \AmoCRM\Exceptions\AmoCRMoAuthApiException
     */
    public function getCompany(AmoCRMApiClient $apiClient)
    {
        return $apiClient->companies()->getOne($this->company);
    }

    /**
     * @param AmoCRMApiClient $apiClient
     * @return \AmoCRM\Models\ContactModel|null
     * @throws AmoCRMApiException
     * @throws \AmoCRM\Exceptions\AmoCRMoAuthApiException
     */
    public function getContact(AmoCRMApiClient $apiClient)
    {
        return $apiClient->contacts()->getOne($this->contact);
    }

}