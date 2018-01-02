To install this module add `"aminkt/yii2-uploadManager-module" : "@dev"` in your `composer.json` in your project.


Then add flowing lines into your application config:
```php
'uploadManager' => [
    'class' => aminkt\uploadManager\UploadManager::className(),
    'uploadPath'=>Yii::getAlias("@frontendWeb")."/upload",
    'uploadUrl'=> $params['site']."/upload",
    'acceptedFiles'=>"*",
    'fileIcon' => $params['site'] . "/upload/image_not_found.jpg"
],
```

|   If you want to edit your files, open `['uploadManager/default/index`]` in your browser.


How user upload manager widget?
-----------

Add flowing lines into your view file to load upload manager widget:

```php
<?php echo \aminkt\uploadManager\components\UploadManager::widget([
    'id'=>'upload-user-pic-'.$model->id,
    'model'=>$model,
    'attribute'=>'picture',
    'titleTxt'=>'تصویر ربات را  کنید.',
    'helpBlockEnable'=>false,
    'showImageContainer'=>'#avatar-'.$model->id,
    'showImagesTemplate'=>"<img src='{url}' class='img-responsive'>",
    'btnTxt'=>'<i class="fa fa-edit"></i>'
]);
echo Html::hiddenInput('target', $model->id, ['class'=>'target']);
?>
<div id="avatar-<?= $model->id ?>"></div>
```

