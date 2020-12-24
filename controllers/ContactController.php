<?php


namespace app\controllers;


use app\models\ContactForm;
use Yii;
use yii\web\Controller;

class ContactController extends Controller
{

    public function actionCreate()
    {
        $model = new ContactForm();

        if ($model->load(Yii::$app->request->post()) && $model->create()) {
            return $this->redirect('/web/site/create' );
        }
        return $this->render('create',[
            'model' => $model,
        ]);
    }
}