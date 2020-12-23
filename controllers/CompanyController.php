<?php


namespace app\controllers;

use Yii;
use app\models\CompanyForm;
use yii\web\Controller;

class CompanyController extends Controller
{

    public function actionCreate()
    {
        $model = new CompanyForm();

        if($model->load(Yii::$app->request->post()) && $model->create()){
            return $this->redirect('/web/site/create');
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
}