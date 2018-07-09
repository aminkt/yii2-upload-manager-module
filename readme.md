To install this module add `"aminkt/yii2-uploadManager-module" : "@dev"` in your `composer.json` in your project.


Then add flowing lines into your application config:
```php
'uploadManager' => [
    'class' => aminkt\uploadManager\UploadManager::className(),
    'uploadPath'=>Yii::getAlias("@frontendWeb")."/upload",
    'uploadUrl'=> $params['site']."/upload",
    'acceptedFiles'=>"*",
    'userClass' => 'user/class/namespcae',
    'fileIcon' => $params['site'] . "/upload/image_not_found.jpg"
],
```

Then run below code to migrate up your modules:
```php
php yii migrate --migratePath="@vendor/aminkt/yii2-uploadManager-module"
```

> If you want to edit your files, open `['uploadManager/default/index']` in your browser.


How use upload manager widget?
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


Use Upload manager api to upload and fetch files
-------------

First add flowing rutes to your api url manager:


```php
[
    'class' => 'yii\rest\UrlRule',
    'pluralize' => false,
    'controller' => ['v2/upload' => 'uploadManager/apiV1/upload'],
    'extraPatterns' => [
        'GET load/<id:\d+>' => 'load',
    ]
],
```

Top code means that use uploadManager api version 1 from v2/upload rutes.
Flowing rutes now available:

```text
GET /v2/upload      // List of all files that user uploaded.
GET /v2/upload/[id]  // Detail of one single file.
GET /v2/upload/load/[id]  // Return file content of one single file by id
DELETE /v2/upload/[id]  // Delete a single file from server.
POST /v2/upload     // Upload a new file.

```


> Notice: 
> 1. All request except load and view shoud use at least one auth method to authrize user.
> 2. In upload route don't set content-type in header but in others you can.
> 3. In upload route send file as post, file multi part and file name is better to be 'file'


> Warning:
>   Cross origin is enabled by default. if you have any problem with this please report it.