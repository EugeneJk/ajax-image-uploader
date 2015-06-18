<?php
namespace eugenejk\ajaxImageUploader\widgets;

use Yii;
use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use eugenejk\ajaxImageUploader\assets\ImageCropperAsset;

/*
 * Ajax Image Uploader
 */

/**
 * Description of AjaxImageUploader
 *
 * @author elazarchuk
 */
class ImageCropper extends InputWidget{
    
    /**
     * @var string widget layout
     */
    public $layout = "{field}\n{image}\n{buttons}\n{notification}";
    
    /**
     * @var string hiddenInputId
     */
    public $hiddenInputId;

    /**
     * Preview id.
     */
    public $previewId;
    
    /**
     * Preview id.
     */
    public $cropImageId;

    /**
     * javascriptVariableName
     */
    private $javascriptVariableName;
    
    /**
     * Notification section id.
     */
    private $notificationId;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $uid = uniqid();
        $this->javascriptVariableName = 'cropper_' . $uid;
        if(empty($this->previewId)){
            $this->previewId = 'image-preview_' . $uid;
        }
//        if(empty($this->uploadInputId)){
//            $this->uploadInputId = 'uploadfile_' . $uid;
//        }
        $this->notificationId = 'notification_' . $uid;
        if(empty($this->hiddenInputId)){
            $this->hiddenInputId  = 'crop-hidden-field_' . $uid;
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerClientScript();
        return $this->renderArea();
    }
    
    /**
     * Renders widget
     * @return string
     */
    private function renderArea(){
        $content = preg_replace_callback("/{\\w+}/", function ($matches) {
            $content = $this->renderSection($matches[0]);

            return $content === false ? $matches[0] : $content;
        }, $this->layout);
        
        return $content;
    }
    
    /**
     * Registers javascript and css
     */
    private function registerClientScript(){
//        $originalImage = $this->model->{$this->attribute};
        $view = $this->getView();
        $view->registerJs(<<<JS
            {$this->javascriptVariableName} = new ImageCropper({
                cropImageId : '{$this->cropImageId}',
                thumbnailId : '{$this->hiddenInputId}',
                url: '/image/image-crop',
                applyButtonId: 'crop-apply-button',
                thumbnailPreviewId: '{$this->previewId}'
            });
JS
        );
        ImageCropperAsset::register($view);
    }

    public function renderSection($name)
    {
        switch ($name) {
            case '{field}':
                return $this->renderField();
            case '{image}':
                return $this->renderImage();
            case '{fileInput}':
                return $this->renderFileInput();
            case '{buttons}':
                return $this->renderButtons();
            case '{notification}':
                return $this->renderNotifications();
            default:
                return false;
        }
    }
//    
    public function renderField(){
        if ($this->hasModel()) {
            return Html::activeHiddenInput($this->model, $this->attribute, ['id' => $this->hiddenInputId]);
        } else {
            return Html::hiddenInput($this->name, $this->value, $this->options);
        }
    }
    
    public function renderImage(){
        return Html::tag(
            'div',
            Html::tag('img','',['id' => $this->previewId, 'src' => $this->model->{$this->attribute}]),
            ['class' => 'project-upload-element text-center']
        );
    }
    
//    public function renderFileInput(){
//        return Html::tag(
//            'div',
//            Html::fileInput($this->uploadInputId, null,[
//                'id'=> $this->uploadInputId,
//                'accept'=>'image/*'
//            ]),
//            ['class' => 'project-upload-element text-center']
//        );
//    }
    
    public function renderNotifications(){
        return Html::tag(
            'div',
            '',
            ['id' => $this->notificationId,'class' => 'help-block']
        );
    }
    
    public function renderButtons(){
        return <<<BTN
        <button class="btn btn-default" onclick="{$this->javascriptVariableName}.activateCrop();return false;">Create Thumbnail</button>
        <button id="crop-apply-button" class="btn btn-primary hidden" onclick="{$this->javascriptVariableName}.crop();return false;">Apply</button>
BTN;
        
        $content = preg_replace_callback("/{\\w+}/", function ($matches) {
            $content = $this->renderButton($matches[0]);

            return $content === false ? $matches[0] : $content;
        }, $this->buttonsLayout);
        
        return Html::tag('div', $content,[ 'class' => 'project-upload-element']);
    }
//    
//    public function renderButton($button)
//    {
//        switch ($button) {
//            case '{upload}':
//                $content = '<i class="glyphicon glyphicon-upload"></i>';
//                $options = [
//                    'class' => 'btn btn-primary',
//                    'title' => 'Upload',
//                    'onclick' => "{$this->javascriptVariableName}.upload();return false;",
//                ];
//                break;
//            case '{clear}':
//                $content = '<i class="glyphicon glyphicon glyphicon-trash"></i>';
//                $options = [
//                    'class' => 'btn btn-warning pull-right',
//                    'title' => 'Clear',
//                    'onclick' => "{$this->javascriptVariableName}.clear(false);return false;",
//                ];
//                break;
//            case '{reset}':
//                $content = '<i class="glyphicon glyphicon-picture"></i>';
//                $options = [
//                    'class' => 'btn btn-default pull-right',
//                    'title' => 'Original',
//                    'onclick' => "{$this->javascriptVariableName}.clear(true);return false;",
//                ];
//                break;
//            default:
//                $content = '';
//                $options = [];
//                break;
//        }
//        
//        return Html::button($content, $options);
//    }
}
