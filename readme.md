### Yii2 Upload manage module.

[![Latest Stable Version](https://poser.pugx.org/aminkt/yii2-upload-manager/v/stable)](https://packagist.org/packages/aminkt/yii2-upload-manager)
[![Total Downloads](https://poser.pugx.org/aminkt/yii2-upload-manager/downloads)](https://packagist.org/packages/aminkt/yii2-upload-manager)
[![Latest Unstable Version](https://poser.pugx.org/aminkt/yii2-upload-manager/v/unstable)](https://packagist.org/packages/aminkt/yii2-upload-manager)
[![License](https://poser.pugx.org/aminkt/yii2-upload-manager/license)](https://packagist.org/packages/aminkt/yii2-upload-manager)
[![Monthly Downloads](https://poser.pugx.org/aminkt/yii2-upload-manager/d/monthly)](https://packagist.org/packages/aminkt/yii2-upload-manager)
[![Daily Downloads](https://poser.pugx.org/aminkt/yii2-upload-manager/d/daily)](https://packagist.org/packages/aminkt/yii2-upload-manager)
[![composer.lock](https://poser.pugx.org/aminkt/yii2-upload-manager/composerlock)](https://packagist.org/packages/aminkt/yii2-upload-manager)

To install this module add `"aminkt/yii2-uploadManager-module" : ">=1.2.0"` in your `composer.json` in your project.


Then add flowing lines into your application config:
```php
'uploadManager' => [
    'class' => aminkt\uploadManager\UploadManager::class,
    'uploadPath'=>Yii::getAlias("@frontendWeb")."/upload",
    'uploadUrl'=> "/upload",
    'baseUrl' => 'http://localhost:800',
    'acceptedFiles'=>"*",
    'userClass' => 'user/class/namespcae',
    'fileClass' => 'file/class/namespcae', // Don't set this to use default active record.
    'fileSearchClass' => 'file/search/class/namespcae', // Don't set this to use default search active record.
],
```

Then run below code to migrate up your modules:
> Run migration if you are using defualt module models.
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

First add flowing routes to your api url manager:


```php
[
    'class' => 'yii\rest\UrlRule',
    'pluralize' => false,
    'controller' => ['v2/upload' => 'uploadManager/v1/upload'],
    'extraPatterns' => [
        'GET load/<id:\d+>' => 'load',
    ]
],
```

Top code means that use uploadManager api version 1 from v2/upload route.
Flowing routes now available:

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


Advanced configuration
--------

If you want implement your own models or search model or you wnat use Mongo db database you should flow below instractions.

1. Create your models.
2. Implement `\aminkt\uploadManager\interfaces\FileInterface`
3. If you want use default File constant implement `\aminkt\uploadManager\interfaces\FileConstantsInterface` or not
create your own constants named like defined interface.
4. Config you module like said in first part and define `fileClass` and `fileSearchClass`
5. You can use `\aminkt\uploadManager\traits\FileTrait` to implement some regular methods that defined in `FileInterface`


> If you wnat use mongodb active record you can just change `fileClass` and `fileSearchClass` discribed in configuration
part to use mongodb version as `\aminkt\uploadManager\models\mongo\File` and `\aminkt\uploadManager\models\mongo\FileSearch`