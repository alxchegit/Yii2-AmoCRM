<?php
namespace app\models;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\LinksCollection;
use AmoCRM\Collections\TasksCollection;
use AmoCRM\Models\NoteType\CommonNote;
use AmoCRM\Models\TaskModel;
use Yii;
use yii\base\Model;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Models\LeadModel;

class LeedsForm extends Model
{
    public $company;
    public $contact;
    public $settask;
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
            [['contact', 'company'], 'integer'],
            ['settask', 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'leadName' => 'Название сделки',
            'notes' => 'Примечание',
            'company' => 'Компания',
            'contacts' => 'Контакт',
            'settask' => 'Создать задачу',
        ];
    }

    /**
     * @return bool
     * @throws AmoCRMApiException
     * @throws \AmoCRM\Exceptions\AmoCRMoAuthApiException
     */
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
            if($this->settask){
                $tasksCollection = new TasksCollection();
                $task = new TaskModel();
                $day = date('d') ;
                $month = date('m');
                $year = date('Y');
                $task->setTaskTypeId(TaskModel::TASK_TYPE_ID_CALL)
                    ->setText('Тестовая задача')
                    ->setCompleteTill(mktime(23, 59, 59, $month, $day, $year))
                    ->setEntityType(EntityTypesInterface::LEADS)
                    ->setEntityId($id)
                    ->setResponsibleUserId($lead->getResponsibleUserId())
                    ->setDuration(24*60*60);
                $tasksCollection->add($task);
                $apiClient->tasks()->add($tasksCollection);
            }
            $apiClient->leads()->link($lead, $links);
        } catch (AmoCRMApiException $e) {
            Yii::$app->session->setFlash('error', "Ошибка создания сделки - " . $e->getMessage());
            return false;
        }

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