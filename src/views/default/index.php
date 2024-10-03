<?php
/** @var $this \yii\web\View */

use aminkt\uploadManager\models\File;
use aminkt\uploadManager\UploadManager;
use yii\grid\CheckboxColumn;
use yii\grid\DataColumn;

/** @var $model aminkt\uploadManager\models\File */
/** @var $dataProvider \yii\data\ActiveDataProvider */
$this->title = "آرشیو فایل ها";
?>

<div class="row">
    <?php
    $form = \yii\bootstrap\ActiveForm::begin([
        'method' => 'get',
        'layout' => 'inline'
    ]);
    ?>
    <?= $form->field($model, 'name')->textInput(['placeholder'=>'نام فایل']) ?>
    <?= $form->field($model, 'file_type')->dropDownList([
        null => 'همه',
        $model::FILE_TYPE_IMAGE => 'تصویر',
        $model::FILE_TYPE_VIDEO => 'ویدیو',
        $model::FILE_TYPE_AUDIO => 'صوت',
        $model::FILE_TYPE_DOCUMENT => 'داکیومنت',
        $model::FILE_TYPE_ARCHIVE => 'فایل فشرده',
        $model::FILE_TYPE_APPLICATION => 'نرم افزار',
        $model::FILE_TYPE_UNDEFINED => 'نا مشخص',
    ]) ?>
    <?= $form->field($model, 'create_at')->widget(\faravaghi\jalaliDatePicker\jalaliDatePicker::class, [
        'options' => array(
            'format' => 'yyyy-mm-dd',
            'viewformat' => 'yyyy/mm/dd',
            'autocomplete' => 'off',
            'placement' => 'left',
            'todayBtn' => 'linked',
            'class' => 'form-control',
            'placeholder' => 'تاریخ ایجاد'
        ),
    ]) ?>
    <?= \yii\helpers\Html::submitButton('جستجو', ['class'=>'btn btn-default']) ?>
    <?php \yii\bootstrap\ActiveForm::end(); ?>
</div>

<div class="row">
    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'id'=>'uploadManager-gridView',
        'tableOptions' => ['class' => 'table table-striped table-responsive'],
        'columns' => [
            ['class' => CheckboxColumn::className()],
            [
                'class' => DataColumn::className(), // this line is optional
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column)
                {
                    /** @var File $model */
                    if ($model->file_type == $model::FILE_TYPE_IMAGE)
                        $url = UploadManager::getInstance()->image($model->id, 'thumb');
                    else
                        $url = UploadManager::getInstance()->fileIcon;
                    $name = $model->getMeta('name');
                    $size = number_format($model->getMeta('size')/1000, 2);
                    $type = $model->getMeta('type');
                    return <<<HTML
<div class="row">
  <div class="col-xs-6 col-md-3">
    <a href="#" class="thumbnail">
      <img src="$url" alt="$model->name">
    </a>
  </div>
  <div class="col-xs-6 col-md-9">
    <h4><span class="text-danger text-direction-ltr"> $name </span></h4>
      <div class="row">
        <div class="pull-left text-direction-ltr" style="margin: auto 10px;">
          $size kb
        </div>
        <div class="pull-left text-direction-ltr"  style="margin: auto 10px;">
            $type
        </div>
    </div>
  </div>
</div>
HTML;

                },
            ],
            [
                'attribute'=>'type',
                'headerOptions'=>['style'=>"width:10%"]
            ],
            [
                'class' => DataColumn::className(), // this line is optional
                'attribute' => 'status',
                'headerOptions'=>['style'=>"width:10%"],
                'format' => 'text',
                'value' => function ($model, $key, $index, $column) {
                    /** @var File $model */
                    if ($model->status == $model::STATUS_ENABLE)
                        return "منتشر شده" ;
                    else
                        return "عدم انتشار";

                },
            ],
            'createTime:dateTime',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {delete}'
            ],
        ],
    ]) ?>
</div>
