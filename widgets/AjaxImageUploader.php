<?php
namespace eugenejk\ajaxImageUploader\widgets;

use Yii;
use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use eugenejk\ajaxImageUploader\assets\AjaxImageUploaderAsset;

/*
 * Ajax Image Uploader
 */

/**
 * Description of AjaxImageUploader
 *
 * @author elazarchuk
 */
class AjaxImageUploader extends InputWidget{
    /**
     * Hidden Input Id
     * @var string
     */
    public $hiddenInputId = '';
    
    /**
     * Field for active form is _csrf is needed for request
     * @var string
     */
    public $activeFormId = '';
    
    /**
     * Empty Image link.
     * Show this image if image is not 
     * @var string
     */
    public $emptyImageLink = '';
    
    /**
     * Upload Url.
     * @var string
     */
    public $uploadActionUrl = '';
    
    /**
     * Javascrpt variable name unique.
     */
    private $javascriptVariableName;
    
    /**
     * Upload input id.
     */
    public $uploadInputId;
    
    /**
     * Preview id.
     */
    public $previewId;
    
    /**
     * @var array Javascript code that will be executed after successfull execution;
     */
    public $afterSuccessUpload = [];
    
    /**
     * Notification section id.
     */
    private $notificationId = 'notification';
    
    /**
     * Layout for widget.
     */
    public $layout = "{field}\n{image}\n{fileInput}\n{buttons}\n{notification}";
    
    /**
     * Layout for buttons.
     */
    public $buttonsLayout = "{upload}\n{clear}\n{reset}";
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $uid = uniqid();
        $this->javascriptVariableName = 'uploader_' . $uid;
        if(empty($this->previewId)){
            $this->previewId = 'image-preview_' . $uid;
        }
        if(empty($this->uploadInputId)){
            $this->uploadInputId = 'uploadfile_' . $uid;
        }
        $this->notificationId = 'notification_' . $uid;
        if(empty($this->hiddenInputId)){
            $this->hiddenInputId  = 'w-aiu-hidden-field_' . $uid;
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
        if($this->hasModel()){
            $originalImage = $this->model->{$this->attribute};
        }   else {
            $originalImage = $this->value;
        }
        $afterSuccessUpload = !empty($this->afterSuccessUpload) ?  implode(";\n", $this->afterSuccessUpload) . ';' : '';
        $view = $this->getView();
        $view->registerJs(
        <<<JS
            {$this->javascriptVariableName} = new ImageUploader({
                uploadInputId: '{$this->uploadInputId}',
                fieldId: '{$this->hiddenInputId}',
                previewId:'{$this->previewId}',
                emptyImageLink:'{$this->emptyImageLink}',
                actionUrl:'{$this->uploadActionUrl}',
                formId: '{$this->activeFormId}',
                notificationId: '{$this->notificationId}',
                originalImage: '{$originalImage}',
                afterSuccessUpload: function(){
                    {$afterSuccessUpload}
                } 
            });
JS
        );
        AjaxImageUploaderAsset::register($view);
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
    
    public function renderField(){
        if ($this->hasModel()) {
            return Html::activeHiddenInput($this->model, $this->attribute, ['id' => $this->hiddenInputId]);
        } else {
            return Html::hiddenInput($this->name, $this->value, ['id' => $this->hiddenInputId]);
        }
    }
    
    public function renderImage(){
        return Html::tag(
            'div',
            Html::img(
                empty($this->model->{$this->attribute}) ?  null : $this->model->{$this->attribute},
                ['id' => $this->previewId]
            ),
            ['class' => 'project-upload-element text-center']
        );
    }
    
    public function renderFileInput(){
        return Html::tag(
            'div',
            Html::fileInput($this->uploadInputId, null,[
                'id'=> $this->uploadInputId,
                'accept'=>'image/*'
            ]),
            ['class' => 'project-upload-element text-center']
        );
    }
    
    public function renderNotifications(){
        return Html::tag(
            'div',
            '',
            ['id' => $this->notificationId,'class' => 'help-block']
        );
    }
    
    public function renderButtons(){
        $content = preg_replace_callback("/{\\w+}/", function ($matches) {
            $content = $this->renderButton($matches[0]);

            return $content === false ? $matches[0] : $content;
        }, $this->buttonsLayout);
        
        return Html::tag('div', $content,[ 'class' => 'project-upload-element']);
    }
    
    public function renderButton($button)
    {
        switch ($button) {
            case '{upload}':
                $content = '<i class="glyphicon glyphicon-upload"></i>';
                $options = [
                    'class' => 'btn btn-primary',
                    'title' => 'Upload',
                    'onclick' => "{$this->javascriptVariableName}.upload();return false;",
                ];
                break;
            case '{clear}':
                $content = '<i class="glyphicon glyphicon glyphicon-trash"></i>';
                $options = [
                    'class' => 'btn btn-warning pull-right',
                    'title' => 'Clear',
                    'onclick' => "{$this->javascriptVariableName}.clear(false);return false;",
                ];
                break;
            case '{reset}':
                $content = '<i class="glyphicon glyphicon-picture"></i>';
                $options = [
                    'class' => 'btn btn-default pull-right',
                    'title' => 'Original',
                    'onclick' => "{$this->javascriptVariableName}.clear(true);return false;",
                ];
                break;
            default:
                $content = '';
                $options = [];
                break;
        }
        
        return Html::button($content, $options);
    }
}
