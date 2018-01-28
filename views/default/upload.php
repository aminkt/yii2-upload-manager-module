<?php
/** @var $this \yii\web\View */
/** @var $model aminkt\uploadManager\models\UploadmanagerFiles */
/** @var $counter integer|null */

$this->title = "بارگذاری فایل";
$this->registerCss(<<<CSS
.dropzone {
    border: 2px dashed #028AF4;
    background: #fff;
    padding: 20px;
    margin: 0 auto;
    text-align: center;
    min-height: 300px;
}
CSS
);
?>
<div class="row">
    <div class="col-md-12">
        <?php
            if(isset($counter) and $counter!=null) {
                $id = "dropzone_uploadmanager_$counter";
            }else{
                $id = "dropzone_uploadmanager";
            }
            $acceptedFiles = \Yii::$app->getModule('uploadManager')->acceptedFiles;
            $options = [
                'dictDefaultMessage' => 'اینجا را لمس کنید و یا فایل را بر روی این ناحیه بکشید و رها کنید.',
                    'dictFallbackMessage'=>'متاسفانه مرورگر شما قابلیت کشیدن و رها کردن را پشتیبانی نمیکند.',
                    'dictFallbackText'=>'کلیک کنید و سپس آپلود را شروع کنید.',
                    'dictInvalidFileType'=>'فایل مورد نظر مجاز نیست.',
                    'dictFileTooBig'=>'حجم فایل بیش از اندازه است.',
                ];
            if($acceptedFiles != "*" and $acceptedFiles)
                $options['acceptedFiles'] = $acceptedFiles;

            $afterUploadEvent = Yii::$app->request->get('afterUpload');
            if($afterUploadEvent){
                $event = [
                    'complete' => $afterUploadEvent,
                ];
            }else
                $event = null;
            echo \kato\DropZone::widget([
                'id'=>$id,
                'dropzoneContainer'=>$id.'_container',
                'uploadUrl'=>\yii\helpers\Url::to(['/uploadManager/default/upload']),
                'autoDiscover'=>true,
                'options'=>$options,
                'clientEvents'=>$event,
            ]);
        ?>
    </div>

</div>

