<?php
/** @var $this \yii\web\View */
/** @var $dataProvider \yii\data\ActiveDataProvider */
/** @var $multiple boolean */
/** @var $counter integer|null */
/** @var $selectedItems array   Items that selected. */

$this->title = "آرشیو فایل ها";

?>
<div>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation"><a href="#upload" aria-controls="upload" role="tab" data-toggle="tab">بارگذاری
                پرونده</a></li>

        <li role="presentation" class="active"><a href="#archive" aria-controls="archive" role="tab" data-toggle="tab">آرشیو
                مطالب</a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">

        <div role="tabpanel" class="tab-pane" id="upload">
            <?= $this->render('upload', [
                'counter'=>$counter
            ]) ?>
        </div>

        <div role="tabpanel" class="tab-pane active" id="archive">
                <?=  $this->render('list', [
                    'dataProvider' => $dataProvider,
                    'multiple' => $multiple,
                    'counter' => $counter,
                    'selectedItems' => $selectedItems,
                    'model'=>$model
                ])  ?>
        </div>
    </div>

</div>

