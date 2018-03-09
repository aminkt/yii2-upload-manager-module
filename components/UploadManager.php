<?php

namespace aminkt\uploadManager\components;

use yii\base\Widget;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\View;
use yii\widgets\InputWidget;

class UploadManager extends InputWidget
{
    public $titleTxt = 'Select Media';

    public $btnTxt = 'Upload Media';

    public $helpBlockEnable = true;

    /** @var array $btnOptions Tag options of btn*/
    public $btnOptions = [];

    /** @var string $textAreaId Jquery selector of textarea that you want plugin insert id of uploaded images as a text. */
    public $textAreaId = null;

    /** @var bool $multiple Get one image or more by plugin */
    public $multiple = false;

    /** @var  $showImagesTemplate string Use {url} for return url of picture. */
    public $showImagesTemplate = "<div class='item'><img src='{url}'></div>";

    /** @var string $showImageContainer jquery selector of container of show image. */
    public $showImageContainer = null;

    /** @var string $sizeOfImageInImageContainer Size of image that should use to render image in container. */
    public $sizeOfImageInImageContainer = 'thumb';

    /** @var string $loadingSelector Jquery selector for loading container. */
    public $loadingSelector = null;

    private $_url;

    /** @var  $_module \aminkt\uploadManager\UploadManager */
    private $_module;

    public function init()
    {
        parent::init();

        $this->_module = \Yii::$app->getModule('uploadManager');
        if(count($this->btnOptions)==0){
            $this->btnOptions = [
                'class'=>'btn btn-primary tooltiped '.$this->id.'-modal-btn',
                'type'=>'button',
                'data' => [
                    'target'=>'#'.$this->id.'-modal',
                    'toggle' => 'modal',
                    'pjax'=>'#'.$this->id.'-ajax',
                ],
            ];
        }

        if($this->id){
            $this->options['id'] = $this->id;
        }

        $this->_url = Url::to(['/uploadManager/default/ajax', 'multiple'=>$this->multiple, 'counter'=>$this->id], true);
        Assets::register($this->getView());

        if($this->model and $this->attribute){
            $this->value = $this->model->{$this->attribute};
        }

        if ($this->value)
            $this->initialShowImages();
    }

    /**
     *  When using an active filed and fill it by pictures ids this method show thumb of images.
     */
    public function initialShowImages(){
        if($this->value and $container = $this->showImageContainer){
            $ids = explode(',', $this->value);
            $pictures = [];
            if (count($ids) == 0) {
                return;
            } else {
                foreach ($ids as $id) {
                    try {
                        $file = $this->_module->getFile($id);
                        $pictures[] = ['id' => $id, 'url' => $file->getUrl($this->sizeOfImageInImageContainer), 'extension' => $file->extension];
                    } catch (NotFoundHttpException $e) {
                        $noImage = \Yii::$app->getModule('uploadManager')->noImage;
                        $pictures[] = ['id' => $id, 'url' => $noImage, 'extension' => 'jpg'];
                        \Yii::error("Record of file by id={$id} not found", self::className());
                    }
                }
            }

            $this->generateInitialShowImagesJs($pictures, $container);
        }
    }

    /**
     * Generate js code for initialize show image container.
     * @param $pictures
     * @param $container
     */
    private function generateInitialShowImagesJs($pictures, $container){
        $jqueryArray = json_encode($pictures);
        $template = trim(preg_replace('/\s\s+/', ' ', $this->showImagesTemplate));
        $js = <<<JS
var pictures = $jqueryArray;

var template = "$template";
var html = "";
jQuery.each(pictures, function( index, value ) {
    var t = template.replace("{url}", value.url);
    var t = t.replace("{file_extension}", value.extension);
    html += t;
});

jQuery('$container').html(html);
JS;
        $this->getView()->registerJs($js, View::POS_READY);
    }


    /**
     * Generate Html codes of widget.
     */
    public function generateHtml(){
        echo Html::a($this->btnTxt, '#', $this->btnOptions);
        if($this->helpBlockEnable)
            echo '<div id="'.$this->id.'-help-block"></div>';
        $modal = Modal::begin([
            'id'=>$this->id.'-modal',
            'size'=>Modal::SIZE_LARGE,
            'header' => '<h4 class="modal-title">'.$this->titleTxt.'</h4>',
        ]);
        if(!$this->loadingSelector){
            echo '<div class="upload-manager-loading-'.$this->id.'" style="display: none">درحال بارگزاری ...</div>';
        }
        echo '<div id="'.$this->id.'-ajax-container"></div>';

        echo '<a href="#" id="addto-'.$this->id.'" class="btn btn-primary">درج</a>';
        Modal::end();
        echo $this->renderInputHtml('hidden');
    }


    /**
     * Create javascript code for showing upload manager modal.
     * @return string
     */
    private function showModalJs(){
        $loadingSelector = $this->loadingSelector?$this->loadingSelector:'.upload-manager-loading-'.$this->id;
        return <<<JS
jQuery(document).on('click', '.$this->id-modal-btn', function() {
    jQuery("$loadingSelector").show();
    jQuery("#$this->id-ajax-container").load("$this->_url", function(responseTxt, statusTxt, xhr){
        if(statusTxt == "success")
            jQuery("$loadingSelector").hide();
        if(statusTxt == "error")
            alert("Error: " + xhr.status + ": " + xhr.statusText);
    });
});
JS;
    }


    /**
     * Js code for show selected images in to show image container.
     * @return string
     */
    private function showImageContainerJs(){
        if($showImageContainer = $this->showImageContainer){
            $template = $this->showImagesTemplate;
            return <<<JS
var template = "$template";
var html = "";
jQuery(select).find(":selected").each(function() {
    html += template.replace("{url}", jQuery(this).data('url'));
});
jQuery('$showImageContainer').html(html);
JS;
        }
    }

    /**
     * Js code for adding code into text area to define selected images.
     * @return string
     */
    private function addImageToTextAreaJs(){
        $txtId = $this->textAreaId;
        if($txtId){
            return <<<JS
    insertAtCaret('$txtId', 'UploadManager['+select.val()+']');
JS;
        }
    }

    /**
     * Js code for show help block text.
     * @return string
     */
    private function helpBlockTextJs(){
        if($this->helpBlockEnable){
            return <<<JS
  jQuery("#{$this->id}-help-block").text(select.val().length+" مورد انتخاب شده است.");
JS;
        }
    }

    /**
     * Js code for actions needed when user click on add btn.
     * @return string
     */
    private function addButtonClickJs(){
        $counter = $this->id;
        $js = <<<JS

$(document).on('click', '#addto-$this->id', function() {
  var select = jQuery('#$this->id-modal').find('#image-picker-select-$counter');
  console.log(jQuery('#$this->id'));
  jQuery('#$this->id').val(select.val()).trigger("change");
JS;
        $js.= $this->showImageContainerJs();
        $js.= $this->addImageToTextAreaJs();
        $js.= $this->helpBlockTextJs();
        $js .=<<<JS
  jQuery("#$this->id-modal").modal('hide');
});
JS;
        return $js;
    }

    private function enableTabsinModalJs(){
        return <<<JS
jQuery(document).on("click",".modal-body li a",function()
    {
        tab = jQuery(this).attr("href");
        jQuery(".modal-body .tab-content div").each(function(){
            jQuery(this).removeClass("active");
        });
        jQuery(".modal-body .tab-content "+tab).addClass("active");
    });
JS;

    }


    /**
     * Register js code of widget.
     */
    public function generateJs(){

        $js = $this->showModalJs();
        $js .= $this->addButtonClickJs();
        $js .= $this->enableTabsinModalJs();
        $this->getView()->registerJs($js, View::POS_READY);
    }


    public function run()
    {
        $this->generateHtml();
        $this->generateJs();
    }



}