<?php
namespace app\models;

use Yii;
use yii\base\Model;
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
    public $contact;
    public $task;
    public $leedName;
    public $notes;

    /**
    * @return array the validation rules.
    */
    public function rules()
    {
        return [
            [['leedName'], 'required'],
            [['notes', 'leedName'], 'string'],
        ];
    }

    public function create()
    {
        $apiClient = AmoCrm::construct();
        $leadsService = $apiClient->leads();
        $lead = new LeadModel();
        $leadCustomFieldsValues = new CustomFieldsValuesCollection();
        $textCustomFieldValueModel = new TextCustomFieldValuesModel();
        $textCustomFieldValueModel->setFieldId(null);
        $textCustomFieldValueModel->setValues(
            (new TextCustomFieldValueCollection())
                ->add((new TextCustomFieldValueModel())->setValue($this->notes))
        );
        $leadCustomFieldsValues->add($textCustomFieldValueModel);
        $lead->setCustomFieldsValues($leadCustomFieldsValues);
        $lead->setName($this->leedName);

        $leadsCollection = new LeadsCollection();
        $leadsCollection->add($lead);
        try {
            $lead = $leadsService->addOne($lead);
        } catch (AmoCRMApiException $e) {
            var_dump($e);
            die;
        }

        return true;
    }


}