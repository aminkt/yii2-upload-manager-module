<?php
use aminkt\uploadManager\components\Assets;
use \aminkt\uploadManager\UploadManager;

/** @var $this \yii\web\View */
/** @var $model \aminkt\uploadManager\models\File */
/** @var $dataProvider \yii\data\ActiveDataProvider */
/** @var $multiple boolean */
/** @var $counter integer|null */
/** @var $selectedItems array   Items that selected. */

$this->title = "آرشیو فایل ها";
//$dataProvider->setPagination(false);
$models = $dataProvider->getModels();
Assets::register($this);
$size = UploadManager::getInstance()->sizes['thumb'];

$videoFileBase64Img = 'data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDQwNy41MSA0MDcuNTEiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDQwNy41MSA0MDcuNTE7IiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iNTEycHgiIGhlaWdodD0iNTEycHgiPgo8Zz4KCTxnPgoJCTxnPgoJCQk8cGF0aCBkPSJNMjQwLjMyNywyMTUuMjQ5YzAtNS43NDctMi42MTItMTEuNDk0LTguMzU5LTExLjQ5NEgxMjQuMzQzYy01Ljc0NSwwLjU0NS05Ljk2LDUuNjQ1LTkuNDE0LDExLjM5ICAgICBjMC4wMDMsMC4wMzUsMC4wMDcsMC4wNjksMC4wMSwwLjEwNHYxMDEuODc4YzAsNS43NDcsMy42NTcsMTIuMDE2LDkuNDA0LDEyLjAxNmgxMDcuNjI0YzUuNzQ3LDAsOC4zNTktNi4yNjksOC4zNTktMTIuMDE2ICAgICB2LTIwLjg5OGwzNy42MTYsMTYuMTk2bDQuNzAyLDEuMDQ1YzEuODYzLDAuMDM4LDMuNjktMC41MSw1LjIyNC0xLjU2N2MyLjk4Mi0xLjk2Myw0Ljc1NS01LjMxMiw0LjcwMi04Ljg4MnYtNzMuMTQzICAgICBjMC4yNy0zLjQ5LTEuMzE4LTYuODY1LTQuMTgtOC44ODJjLTMuNDIxLTEuNzExLTcuNDA0LTEuOTAxLTEwLjk3MS0wLjUyMmwtMzcuMDk0LDE2LjE5NlYyMTUuMjQ5eiBNMjE5LjQyOSwzMDguMjQ1aC04My41OTIgICAgIHYtODMuNTkyaDgzLjU5MlYzMDguMjQ1eiBNMjcxLjY3MywyNDYuMDczdjQwLjc1MWwtMzEuMzQ3LTEzLjU4NHYtMTMuNTg0TDI3MS42NzMsMjQ2LjA3M3oiIGZpbGw9IiMwMDAwMDAiLz4KCQkJPHBhdGggZD0iTTM2Mi41OCwxMTMuMzcxTDI1MS44MiwzLjEzNWMtMS45MjktMS45NjktNC41NTgtMy4wOTUtNy4zMTQtMy4xMzVIOTQuMDQxQzY1LjE4NywwLDQxLjc5NiwyMy4zOTEsNDEuNzk2LDUyLjI0NXYzMDMuMDIgICAgIGMwLDI4Ljg1NCwyMy4zOTEsNTIuMjQ1LDUyLjI0NSw1Mi4yNDVoMjE5LjQyOWMyOC44NTQsMCw1Mi4yNDUtMjMuMzkxLDUyLjI0NS01Mi4yNDV2LTIzNC41OCAgICAgQzM2NS42NzUsMTE3LjkzLDM2NC41NDgsMTE1LjMwMSwzNjIuNTgsMTEzLjM3MXogTTI1NiwxMDQuNDlWMzYuNTcxbDczLjY2NSw3My4xNDNoLTY4LjQ0MSAgICAgYy0yLjU3OSwwLjMwNy00LjkxOC0xLjUzNS01LjIyNC00LjExNEMyNTUuOTU2LDEwNS4yMzIsMjU1Ljk1NiwxMDQuODU5LDI1NiwxMDQuNDl6IE0zNDQuODE2LDM1NS4yNjUgICAgIGMwLDE3LjMxMi0xNC4wMzUsMzEuMzQ3LTMxLjM0NywzMS4zNDdIOTQuMDQxYy0xNy4zMTIsMC0zMS4zNDctMTQuMDM0LTMxLjM0Ny0zMS4zNDdWNTIuMjQ1ICAgICBjMC0xNy4zMTIsMTQuMDM0LTMxLjM0NywzMS4zNDctMzEuMzQ3aDE0MS4wNjF2ODMuNTkyYzAsMTQuNDI3LDExLjY5NSwyNi4xMjIsMjYuMTIyLDI2LjEyMmg4My41OTJWMzU1LjI2NXoiIGZpbGw9IiMwMDAwMDAiLz4KCQk8L2c+Cgk8L2c+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==';
?>

<div class="row">
    <?= \yii\helpers\Html::activeTextInput($model, 'fileName', [
        'placeholder'=>'نام فایل',
        'class'=>'form-control',
        'style'=>'width:200px; display:inline-block; margin:5px;'
    ]) ?>
    <?= \yii\helpers\Html::activeDropDownList($model, 'file_type', [
        null => 'همه',
        $model::FILE_TYPE_IMAGE => 'تصویر',
        $model::FILE_TYPE_VIDEO => 'ویدیو',
        $model::FILE_TYPE_AUDIO => 'صوت',
        $model::FILE_TYPE_DOCUMENT => 'داکیومنت',
        $model::FILE_TYPE_ARCHIVE => 'فایل فشرده',
        $model::FILE_TYPE_APPLICATION => 'نرم افزار',
        $model::FILE_TYPE_UNDEFINED => 'نا مشخص',
    ], [
        'class'=>'form-control',
        'style'=>'width:100px; display:inline-block; margin:5px;'
    ]) ?>
    <?= \faravaghi\jalaliDatePicker\jalaliDatePicker::widget([
        'model' => $model,
        'attribute' => 'create_at',
        'options' => array(
            'format' => 'yyyy-mm-dd',
            'autocomplete' => 'off',
            'viewformat' => 'yyyy/mm/dd',
            'placement' => 'left',
            'todayBtn' => 'linked',
            'style'=>'width:100px; display:inline-block; margin:5px 5px 5px 10px;',
            'class' => 'form-control',
            'placeholder' => 'تاریخ ایجاد'
        ),
    ]) ?>
    <?= \yii\helpers\Html::button('جستجو', ['class'=>'btn btn-default search-uploadmanager-btn']) ?>
</div>

<style>
    .image_picker_image{
        width: <?= $size[0] ?>px !important;
        height: <?= $size[1] ?>px !important;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="uploadmanager-archive-container">
            <select <?= $multiple?'multiple="multiple"':'' ?> id="image-picker-select-<?= $counter ?>">
                <option value=""></option>
                <?php foreach ($models as $model):
                    $fileUrl = $model->getUrl();
                    if ($model->file_type == \aminkt\uploadManager\models\File::FILE_TYPE_IMAGE) {
                        $url = Yii::$app->getModule('uploadManager')->image($model->id, 'thumb');
                    } elseif($model->file_type == \aminkt\uploadManager\models\File::FILE_TYPE_VIDEO) {
                        $url = $videoFileBase64Img;
                    } else {
                        $url = \Yii::$app->getModule('uploadManager')->fileIcon;
                    }
                    $select = in_array($model->id, $selectedItems);
                    ?>
                    <option data-img-src="<?= $url ?>"
                            data-url="<?= $fileUrl ?>"
                            data-img-alt="<?= $model->fileName ?>"
                            <?php if ($model->file_type != $model::FILE_TYPE_IMAGE ) : ?>
                                data-img-label="<div class='caption'><?= $model->fileName ?></div>"
                            <?php endif; ?>
                            value="<?= $model->id ?>" <?= $select?'selected="true"':'' ?>>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?php echo \yii\widgets\LinkPager::widget([
            'pagination' => $dataProvider->getPagination()
        ]) ?>
    </div>
</div>
<?php
$this->registerJs(<<<JS
$("#image-picker-select-$counter").imagepicker({
  show_label  : true
})
JS
);