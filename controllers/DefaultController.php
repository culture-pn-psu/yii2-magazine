<?php

namespace culturePnPsu\magazine\controllers;

use Yii;
use culturePnPsu\magazine\models\Magazine;
use culturePnPsu\magazine\models\MagazineSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use tpmanc\imagick\Imagick;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

/**
 * DefaultController implements the CRUD actions for Magazine model.
 */
class DefaultController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Magazine models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new MagazineSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Magazine model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    public function actionPreview($id) {
        $this->layout = 'preview.php';
        $model = $this->findModel($id);
        $files = \yii\helpers\Json::decode($model->image_id);
//$files = '';
        $initial = [];
        if (is_array($files)) {
            foreach ($files as $key => $value) {
                $initial[] = Yii::$app->img->getUploadUrl(Magazine::UPLOAD_FOLDER . '/' . $model->id) . $value;
            }
        }
        
        //print_r($initial);
        return $this->renderAjax('preview', [
                    'model' => $model,
                    'noOfPagesInPDF' => count($initial),
                    'width' => 800,
                    'height' => 600,
                    'initial' => $initial
        ]);
    }

    public function actionUpload($id) {
        $model = $this->findModel($id);
        $image = \backend\modules\image\models\Image::findOne($model->image_id);
        return $this->render('upload', [
                    'model' => $model,
        ]);
    }

    /**
     * Creates a new Magazine model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Magazine();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['upload', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Magazine model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    public function actionSetFile($id, $image_id) {
        $model = $this->findModel($id);
        $model->image_id = $image_id;
        if ($model->save()) {
            $image = \backend\modules\image\models\Image::findOne($model->image_id);
            if ($image) {
                $path = Yii::$app->img->getUploadPath($image->path_file);
                $pdf_file = $path . $image->id;
                $im = new \imagick($pdf_file);
                $noOfPagesInPDF = $im->getNumberImages();
                if ($noOfPagesInPDF) {
                    for ($i = 0; $i < $noOfPagesInPDF; $i++) {
                        $file = $pdf_file . '[' . $i . ']';
                        $img = new \imagick();
                        $img->readImage($file);
                        if ($img) {
                            $img->scaleImage(1000, 0);
                            $img->setImageFormat('jpg');
                            $img = $img->flattenImages();
                            $img->writeImages($path . ($i + 1) . '-large.jpg', false);
                            $img->scaleImage(600, 0);
                            $img->writeImages($path . ($i + 1) . '.jpg', false);
                        }
                    }
                }
            }
            echo "1";
        } else {
            print_r($model->getErrors());
        }
    }

    /**
     * Deletes an existing Magazine model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Magazine model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Magazine the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Magazine::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionPages($image) {
        $response = Yii::$app->getResponse();
        $response->headers->set('Content-Type', 'image/jpeg');

        $response->format = \yii\web\Response::FORMAT_RAW;
///$response->format = \yii\web\Response::FORMAT_RAW;
        $imgFullPath = Yii::$app->img->getUploadPath('magazine/1') . $image . ".jpg";
//echo \yii\helpers\Html::img($imgFullPath);
        if (!is_resource($response->stream = fopen($imgFullPath, 'r'))) {
            throw new \yii\web\ServerErrorHttpException('file access failed: permission deny');
        }
        return $response->send();
    }

    public function actionUploadajax() {
        $this->uploadMultipleFile();
    }

    private function uploadMultipleFile() {
        $files = [];
        $json = '';
        if (Yii::$app->request->isPost) {
            $img = Yii::$app->img;
            $UploadedFiles = \yii\web\UploadedFile::getInstancesByName('Magazine[image_id]');
            $upload_folder = Yii::$app->request->post('upload_folder');
            $id = Yii::$app->request->post('id');
            $model = $this->findModel($id);
            $tempFile = Json::decode($model->image_id);

             $pathFile = $img->getUploadPath() . $upload_folder;
//            print_r($tempFile);
//            exit();
            if ($UploadedFiles !== null) {
                $img->CreateDir($upload_folder);
                foreach ($UploadedFiles as $key => $file) {
                    try {
                        $oldFileName = $file->basename . '.' . $file->extension;
                        $newFileName = ltrim($file->basename, '0') . '.' . $file->extension;
                        $newFileNameLarge = ltrim($file->basename, '0') . '-large.' . $file->extension;
                       
                        $file->saveAs($pathFile . '/' . $newFileName);
                        $files[$newFileName] = $newFileName;

                        $image = Yii::$app->image->load($pathFile . '/' . $newFileName);
                        $image->resize(1000);
                        $image->save($pathFile . '/thumbnail/' . $newFileNameLarge);
                        $image->resize(800);
                        $image->save($pathFile . '/' . $newFileName);
                    } catch (Exception $e) {
                        
                    }
                }
                
                //print_r($json);
                $model = $this->findModel($id);
                $model->image_id =  Magazine::findFiles($pathFile);
                if ($model->save(false)) {
                    echo json_encode(['success' => 'true', 'file' => $files, 'temp' => $tempFile, 'json' => $json]);
                } else {
                    echo json_encode(['success' => 'false', 'error' => $model->getErrors()]);
                }
            } else {
                echo json_encode(['success' => 'false',]);
            }
        }
    }

    public function initialPreview($data, $field, $type = 'file') {
        $initial = [];
        $files = Json::decode($data);
        if (is_array($files)) {
            foreach ($files as $key => $value) {
                if ($type == 'file') {
                    $initial[] = "<div class='file-preview-other'><h2><i class='glyphicon glyphicon-file'></i></h2></div>";
                } elseif ($type == 'config') {
                    $initial[] = [
                        'caption' => $value,
                        'width' => '120px',
                        'url' => Url::to(['/freelance/deletefile', 'id' => $this->id, 'fileName' => $key, 'field' => $field]),
                        'key' => $key
                    ];
                } else {
                    $initial[] = Html::img(self::getUploadUrl() . $this->ref . '/' . $value, ['class' => 'file-preview-image', 'alt' => $model->file_name, 'title' => $model->file_name]);
                }
            }
        }
        return $initial;
    }

    public function actionDeletefileAjax($id, $folder = null, $fileName = null) {
        $file = Yii::$app->img->getUploadPath($folder . '/' . $id) . $fileName;
        $pathFile = Yii::$app->img->getUploadPath($folder . '/' . $id);
        $model = Magazine::findOne($id);
//        $data = Json::decode($model->image_id);
//        unset($data[$fileName]);
        
        if (@unlink($file)) {
            $model->image_id = Magazine::findFiles($pathFile);
            if ($model->save(false)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => $model->getErrors()]);
        }
    }

}
