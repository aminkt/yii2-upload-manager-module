<?php
/** @var $this \yii\web\View */

use aminkt\uploadManager\components\Assets;

/** @var $model aminkt\uploadManager\models\UploadmanagerFiles */
/** @var $dataProvider \yii\data\ActiveDataProvider */
/** @var $multiple boolean */
/** @var $counter integer|null */

$this->title = "آرشیو فایل ها";
$dataProvider->setPagination(false);
$models = $dataProvider->getModels();
Assets::register($this);
?>
<select <?= $multiple?'multiple="multiple"':'' ?> id="image-picker-select-<?= $counter ?>">
    <?php foreach ($models as $model):
        if ($model->fileType == \aminkt\uploadManager\models\UploadmanagerFiles::FILE_TYPE_IMAGE)
            $url = Yii::$app->getModule('uploadManager')->image($model->id, 'thumb');
        else
            $url = \Yii::$app->getModule('uploadManager')->fileIcon;
    ?>
        <option data-img-src="<?= $url ?>" value="<?= $model->id ?>"><?= $model->name ?></option>
    <?php endforeach; ?>
</select>

<?php
$this->registerJs(<<<JS
$("#image-picker-select-$counter").imagepicker()
JS
);
