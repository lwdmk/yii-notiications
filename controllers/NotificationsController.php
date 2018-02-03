<?php

namespace app\modules\notifications\controllers;

use app\models\Users;
use app\modules\notifications\models\forms\ForceSendForm;
use app\modules\notifications\Module;
use Yii;
use app\modules\notifications\models\Notifications;
use app\modules\notifications\models\search\SearchNotifications;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * NotificationsController implements the CRUD actions for Notifications model.
 */
class NotificationsController extends Controller
{

    /**
     * @var array $receiver_fields
     */
    public $receiver_fields = [];

    /**
     * @var array $receiver_methods
     */
    public $receiver_methods = [];

    /**
     * @var array $receiver_methods_list
     */
    public $receiver_methods_list = [];

    /**
     * @var array $receiver_fields_list
     */
    public $receiver_fields_list = [];

    /**
     * @var string $receiver_class
     */
    public $receiver_class;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $this->receiver_fields = ArrayHelper::getColumn(Module::getInstance()->receiverFields, 'fieldName');
        $this->receiver_methods = ArrayHelper::getColumn(Module::getInstance()->receiverFields, 'fieldValuesMethod');
        $this->receiver_fields_list = array_combine($this->receiver_fields, $this->receiver_fields);
        $this->receiver_methods_list = array_combine($this->receiver_fields, $this->receiver_methods);
        $this->receiver_class = Module::getInstance()->receiverClass;;

        return parent::beforeAction($action);
    }

    /**
     * Lists all Notifications models.
     * @return mixed
     */
    public function actionIndex()
    {
        $notification = Notifications::findOne(2);
        $notification->compile('text', Users::findOne(13));

        $searchModel = new SearchNotifications();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Notifications model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionCreate($id = null)
    {
        $model = (null === $id) ? new Notifications() : $this->findModel($id);

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ActiveForm::validate($model);
        }

        $class = Module::getInstance()->ownerClass;
        $owner_list = (empty($class)) ? [] : ArrayHelper::map($class::find()->all(),
            Module::getInstance()->ownerPkField, Module::getInstance()->ownerTitleField);

        $receiver_values_method = ($model->isNewRecord || ! array_key_exists($model->receiver_field,
                $this->receiver_methods_list)) ? '' : $this->receiver_methods_list[$model->receiver_field];

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('form', [
                'model'                  => $model,
                'list'                   => $owner_list,
                'receiver_fields_list'   => $this->receiver_fields_list,
                'receiver_values_method' => $receiver_values_method,
                'receiver_class'         => $this->receiver_class,
            ]);
        }
    }

    /**
     * Updates an existing Notifications model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        return $this->actionCreate($id);
    }

    /**
     * Updates an existing Notifications model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionForceSend($id)
    {
        $notification = $this->findModel($id);
        $model = new ForceSendForm();

        if ($model->load(Yii::$app->request->post()) && $model->send($notification)) {
            return $this->redirect(['/notifications']);
        }

        return $this->render('force-send', [
            'model'                => $model,
            'receiver_fields_list' => $this->receiver_fields_list,
        ]);
    }

    /**
     * Deletes an existing Notifications model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionGetValues()
    {
        $options = '';
        $class = $this->receiver_class;

        $field = Yii::$app->request->post('receiver_field');
        if (! array_key_exists($field, $this->receiver_methods_list)) {
            throw new NotFoundHttpException();
        }
        $method = $this->receiver_methods_list[$field];
        $values = $class::$method();
        foreach ($values as $id => $title) {
            $options .= '<option value="'.$id.'">'.$title.'</option>';
        }

        return $options;
    }

    /**
     * Finds the Notifications model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Notifications the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Notifications::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
