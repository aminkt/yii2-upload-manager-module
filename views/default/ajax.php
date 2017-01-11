<?php
/** @var $this \yii\web\View */
/** @var $dataProvider \yii\data\ActiveDataProvider */
/** @var $multiple boolean */
/** @var $counter integer|null */


$this->title = "آرشیو فایل ها";

?>
<div>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#upload" aria-controls="upload" role="tab" data-toggle="tab">بارگزاری پرونده</a></li>
        <li role="presentation"><a href="#archive" aria-controls="archive" role="tab" data-toggle="tab">آرشیو مطالب</a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="upload">
            <?= $this->render('upload', [
                'counter'=>$counter
            ]) ?>
        </div>
        <div role="tabpanel" class="tab-pane" id="archive">
            <?= $this->render('list', [
                'dataProvider'=>$dataProvider,
                'multiple'=>$multiple,
                'counter'=>$counter
            ]) ?>
        </div>
    </div>

</div>

