<?php

namespace aminkt\uploadManager\components;

use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\InputWidget;

class FilePondUploader extends InputWidget
{
    public $pluginOptions = [];
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if(isset($this->options['id'])){
            $this->id = $this->options['id'];
        }else{
            $this->options['id'] = $this->id;
        }

        if(!isset($this->pluginOptions['id'])){
            $this->pluginOptions['id'] = $this->getId().'_file_input';
        }

        if(isset($this->pluginOptions['class'])){
            $this->pluginOptions['class'] .= ' filepond';
        }else{
            $this->pluginOptions['class'] = 'filepond';
        }

        if(!isset($this->pluginOptions['data']['instant-upload'])){
            $this->pluginOptions['data']['instant-upload'] = 'false';
        }

        if(!isset($this->pluginOptions['data']['max-file-size'])){
            $this->pluginOptions['data']['max-file-size'] = '2MB';
        }

        if(isset($this->options['multiple'])){
            $this->pluginOptions['multiple'] = $this->options['multiple'];
            unset($this->options['multiple']);
        }

        FilePondAssets::register($this->getView());
    }

    /**
     * Render js codes of plugin.
     */
    protected function registerJs(){
        $uploadUrl = Url::to(['/uploadManager/api/upload'], true);
        $fetch = Url::to(['/uploadManager/api/fetch'], true);
        $load = Url::to(['/uploadManager/api/load'], true);
        $delete = Url::to(['/uploadManager/api/delete'], true);
        $restore = Url::to(['/uploadManager/api/restore'], true);

        $files = [];
        if($this->value){
            $ids = explode(',', $this->value);
            foreach ($ids as $id){
                $files[] = [
                    'source'=> (string)$id,
                    'options' => [
                        'type' => 'local'
                    ]
                ];
            }
        }
        $files = Json::encode($files);

        $js = <<<JS
let inputElement = document.getElementById('{$this->pluginOptions['id']}');
FilePond.registerPlugin(
  FilePondPluginImagePreview,
  FilePondPluginImageExifOrientation,
  FilePondPluginFileValidateSize
);
let pond = FilePond.create( inputElement );
pond.setOptions({
    labelIdle: 'لطفا عکس مورد نظر را انتخاب کنید',
    labelFileWaitingForSize: 'در حال محسابه اندازه فایل',
    labelFileSizeNotAvailable: 'اندازه فایل مشخص نیست',
    labelFileLoading: 'درحال بارگزاری',
    labelFileLoadError: 'خطا در بازیابی فایل',
    labelFileProcessing: 'در حال ارسال به سرور',
    labelFileProcessingComplete: 'فایل ارسال شد',
    labelFileProcessingAborted: 'آپلود لغو شد',
    labelFileProcessingError: 'خطا در ارسال فایل',
    labelTapToCancel: 'برای لغو ضربه بزنید',
    labelTapToRetry: 'برای تلاش مجدد ضربه بزنید',
    labelTapToUndo: 'برای بازگشت ضربه بزنید',
    labelButtonRemoveItem: 'حذف',
    labelButtonAbortItemLoad: 'رها کردن',
    labelButtonRetryItemLoad: 'تلاش مجدد',
    labelButtonAbortItemProcessing: 'لفو',
    labelButtonUndoItemProcessing: 'بازگشت',
    labelButtonRetryItemProcessing: 'تلاش مجدد',
    labelButtonProcessItem: 'آپلود',
    server: {
        process: '{$uploadUrl}',
        revert: '{$delete}',
        restore: '{$restore}',
        load: '{$load}?id=',
        fetch: '{$fetch}?id='
    },
    files: {$files}
});

document.addEventListener('FilePond:processfile', e => {
    let inputs = $('input[name="{$this->pluginOptions['id']}"]');
    console.log(inputs);
    let ids = "";
    let delayInMilliseconds = 50; //1 second
    setTimeout(function() {
        for (let i=0; i < inputs.length; i++){
            let val = inputs[i].getAttribute("value");
            if(val){
                if(i > 0 ){
                    ids += ',';
                }
                ids += val;
            }
        }
        $("#{$this->getId()}").val(ids);
    }, delayInMilliseconds);
});
JS;
        $this->getView()->registerJs($js);
    }

    /**
     * Render html code of plugin.
     *
     * @return string
     */
    protected function renderHtml(){
        $html = Html::fileInput($this->pluginOptions['id'], null, $this->pluginOptions);
        $html .= $this->renderInputHtml('hidden');
        return $html;
    }


    /**
     * @inheritdoc
     */
    public function run()
    {
        parent::run();

        $this->registerJs();
        return $this->renderHtml();
    }


}