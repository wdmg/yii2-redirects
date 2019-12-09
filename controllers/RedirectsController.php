<?php

namespace wdmg\redirects\controllers;

use Yii;
use wdmg\redirects\models\Redirects;
use wdmg\redirects\models\RedirectsSearch;
use wdmg\redirects\models\RedirectsImport;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\httpclient\Client;

/**
 * RedirectsController implements the CRUD actions for Settings model.
 */
class RedirectsController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get', 'post'],
                    'view' => ['get'],
                    'delete' => ['post'],
                    'create' => ['get', 'post'],
                    'update' => ['get', 'post'],
                    'export' => ['get'],
                    'import' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'roles' => ['admin'],
                        'allow' => true
                    ],
                ],
            ],
        ];

        // If auth manager not configured use default access control
        if(!Yii::$app->authManager) {
            $behaviors['access'] = [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'roles' => ['@'],
                        'allow' => true
                    ],
                ]
            ];
        }

        return $behaviors;
    }

    /**
     * Lists all Redirects models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->request->isAjax) {
            if (Yii::$app->request->get('change') == "status") {
                if (Yii::$app->request->post('id', null)) {
                    $id = Yii::$app->request->post('id');
                    $status = Yii::$app->request->post('value', 0);
                    $model = $this->findModel(intval($id));
                    if ($model) {
                        $model->is_active = intval($status);
                        if ($model->update())
                            return true;
                        else
                            return false;
                    }
                }
            }
        }

        $searchModel = new RedirectsSearch();
        $importModel = new RedirectsImport();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'importModel' => $importModel,
            'dataProvider' => $dataProvider,
            'redirectsCodes' => $searchModel->getRedirectsCodesList(),
            'activeStatus' => $searchModel->getActiveStatusList(),
            'module' => $this->module
        ]);
    }

    /**
     * Displays a single Redirect model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->renderAjax('view', [
            'model' => $model,
            'redirectsCodes' => $model->getRedirectsCodesList(),
            'activeStatus' => $model->getActiveStatusList(),
        ]);
    }

    /**
     * Creates a new Redirect model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Redirects();

        // Validate any AJAX requests fired by the form
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        } else {
            // or process create form
            $model->code = 301;
            $model->is_active = true;
            $post = Yii::$app->request->post();
            if (isset($post["Redirects"]["list"])) {
                $data = array();
                $redirects = explode("\r\n", $post["Redirects"]["list"]);

                if (count($redirects) > 0) {
                    foreach ($redirects as $redirect) {
                        $redirect = explode(";", $redirect);
                        $data[] = [
                            'request_url' => $redirect[0],
                            'redirect_url' => $redirect[1],
                            'code' => $redirect[2],
                            'is_active' => '1',
                        ];
                    }
                }

                $model = new RedirectsImport();
                if (count($data) > 0) {
                    if ($model->import($data)) {
                        Yii::$app->getSession()->setFlash(
                            'success',
                            Yii::t(
                                'app/modules/redirects',
                                'OK! List of redirects successfully added.'
                            )
                        );
                    }
                } else {
                    Yii::$app->getSession()->setFlash(
                        'danger',
                        Yii::t(
                            'app/modules/redirects',
                            'An error occurred while add list of redirects.'
                        )
                    );
                }

            } else {
                if ($model->load($post)) {
                    if($model->save()) {
                        Yii::$app->getSession()->setFlash(
                            'success',
                            Yii::t(
                                'app/modules/redirects',
                                'OK! Redirect ({code}) for `{request_url}` to `{redirect_url}` successfully added.',
                                [
                                    'code' => $model->code,
                                    'request_url' => $model->request_url,
                                    'redirect_url' => $model->redirect_url,
                                ]
                            )
                        );
                        return $this->redirect(['index']);
                    } else {
                        Yii::$app->getSession()->setFlash(
                            'danger',
                            Yii::t(
                                'app/modules/redirects',
                                'An error occurred while adding a {code}-redirect for `{request_url}` to `{redirect_url}`.',
                                [
                                    'code' => $model->code,
                                    'request_url' => $model->request_url,
                                    'redirect_url' => $model->redirect_url,
                                ]
                            )
                        );
                    }
                }
            }

            return $this->render('create', [
                'model' => $model,
                'redirectsCodes' => $model->getRedirectsCodesList(),
                'activeStatus' => $model->getActiveStatusList(),
            ]);
        }
    }

    /**
     * Updates an existing Redirect model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            if($model->save()) {
                Yii::$app->getSession()->setFlash(
                    'success',
                    Yii::t(
                        'app/modules/redirects',
                        'OK! Redirect ({code}) for `{request_url}` to `{redirect_url}` successfully updated.',
                        [
                            'code' => $model->code,
                            'request_url' => $model->request_url,
                            'redirect_url' => $model->redirect_url,
                        ]
                    )
                );
            } else {
                Yii::$app->getSession()->setFlash(
                    'danger',
                    Yii::t(
                        'app/modules/redirects',
                        'An error occurred while updating a {code}-redirect for `{request_url}` to `{redirect_url}`.',
                        [
                            'code' => $model->code,
                            'request_url' => $model->request_url,
                            'redirect_url' => $model->redirect_url,
                        ]
                    )
                );
            }
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'redirectsCodes' => $model->getRedirectsCodesList(),
            'activeStatus' => $model->getActiveStatusList(),
        ]);
    }

    /**
     * Deletes an existing Redirect model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {

        $model = $this->findModel($id);
        if($model->delete()) {
            Yii::$app->getSession()->setFlash(
                'success',
                Yii::t(
                    'app/modules/redirects',
                    'OK! Redirect ({code}) for `{request_url}` to `{redirect_url}` successfully deleted.',
                    [
                        'code' => $model->code,
                        'request_url' => $model->request_url,
                        'redirect_url' => $model->redirect_url,
                    ]
                )
            );
        } else {
            Yii::$app->getSession()->setFlash(
                'danger',
                Yii::t(
                    'app/modules/redirects',
                    'An error occurred while deleting a {code}-redirect for `{request_url}` to `{redirect_url}`.',
                    [
                        'code' => $model->code,
                        'request_url' => $model->request_url,
                        'redirect_url' => $model->redirect_url,
                    ]
                )
            );
        }
        return $this->redirect(['index']);
    }

    public function actionExport() {
        $filename = 'redirects_'.date('dmY_His').'.json';
        $redirects = Redirects::find()->select('section, request_url, redirect_url, code, description, is_active')->asArray()->all();
        Yii::$app->response->sendContentAsFile(Json::encode($redirects), $filename, [
            'mimeType' => 'application/json',
            'inline' => false
        ])->send();
    }

    public function actionImport() {
        $model = new RedirectsImport();
        if (Yii::$app->request->isPost) {
            if($model->validate()) {
                $import = UploadedFile::getInstance($model, 'import');
                $redirects = file_get_contents($import->tempName);
                if ($data = Json::decode($redirects)) {
                    if ($model->import($data)) {
                        Yii::$app->getSession()->setFlash(
                            'success',
                            Yii::t(
                                'app/modules/redirects',
                                'OK! Redirects successfully imported/updated.'
                            )
                        );
                    }
                } else {
                    Yii::$app->getSession()->setFlash(
                        'danger',
                        Yii::t(
                            'app/modules/redirects',
                            'An error occurred while importing/updating redirects.'
                        )
                    );
                }
            }
        }
        return $this->redirect(['index']);
    }

    /**
     * Testing API.
     * @return mixed
     */
    public function actionTest()
    {
        $model = new \yii\base\DynamicModel(['request_url', 'redirect_url', 'code', 'status']);

        $acceptResponses = [
            'json' => 'application/json',
            'xml' => 'application/xml'
        ];

        $model->addRule(['request_url', 'redirect_url', 'code'], 'required');
        $model->addRule(['request_url', 'redirect_url'], 'string', ['min' => 3, 'max' => 128]);
        $model->addRule(['code'], 'integer');
        $model->addRule(['code'], 'in', ['range' => Redirects::$codeRange]);
        $model->addRule(['status'], 'boolean');

        $model->setAttributes([
            'request_url' => strval(Yii::$app->request->get('request_url')),
            'redirect_url' => strval(Yii::$app->request->get('redirect_url')),
            'code' => intval(Yii::$app->request->get('code')),
            'status' => false,
        ]);

        if ($model->validate()) {
            $client = new Client(['baseUrl' => \yii\helpers\Url::base(true)]);
            $response = $client->get($model->request_url)->send();
            if ($response->isOk) {
                if ((Yii::$app->urlManager->createAbsoluteUrl($model->redirect_url) == $response->headers["location"])) {
                    if ((intval($model->code) == intval($response->headers["http-code"]))) {
                        $model->status = true;
                    }
                }
            }
        }

        return $this->renderAjax('test', [
            'model' => $model,
            'redirectsCodes' => Redirects::getRedirectsCodesList(),
        ]);
    }

    /**
     * Finds the Option model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Settings the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Redirects::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app/modules/redirects', 'The requested page does not exist.'));
    }
}
