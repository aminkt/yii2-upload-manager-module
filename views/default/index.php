<?php
/** @var $this \yii\web\View */

use yii\grid\CheckboxColumn;
use yii\grid\DataColumn;

/** @var $model aminkt\uploadManager\models\UploadmanagerFiles */
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
    <?= $form->field($model, 'fileName')->textInput(['placeholder'=>'نام فایل']) ?>
    <?= $form->field($model, 'fileType')->dropDownList([
        null => 'همه',
        $model::FILE_TYPE_IMAGE => 'تصویر',
        $model::FILE_TYPE_VIDEO => 'ویدیو',
        $model::FILE_TYPE_AUDIO => 'صوت',
        $model::FILE_TYPE_DOCUMENT => 'داکیومنت',
        $model::FILE_TYPE_ARCHIVE => 'فایل فشرده',
        $model::FILE_TYPE_APPLICATION => 'نرم افزار',
        $model::FILE_TYPE_UNDEFINED => 'نا مشخص',
    ]) ?>
    <?= $form->field($model, 'createTime')->widget(\faravaghi\jalaliDatePicker\jalaliDatePicker::class, [
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
                    if ($model->fileType == aminkt\uploadManager\models\UploadmanagerFiles::FILE_TYPE_IMAGE)
                        $url = Yii::$app->getModule('uploadManager')->image($model->id, 'thumb');
                    else
                        $url = \Yii::$app->getModule('uploadManager')->fileIcon;
                    $data = $model->tags;
                    $name = $data['name'];
                    $size = number_format($data['size']/1000, 2);
                    $type = $data['type'];
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
                    if ($model->status == aminkt\uploadManager\models\UploadmanagerFiles::STATUS_ENABLE)
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
