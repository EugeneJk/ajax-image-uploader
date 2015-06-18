<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace eugenejk\ajaxImageUploader\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ImageCropperAsset extends AssetBundle
{
    public $basePath = '@vendor/eugenejk/ajax-image-uploader/';
    public $sourcePath = '@vendor/eugenejk/ajax-image-uploader/js/';
    
    public $js = [
        'ImageCropper.js',
        'fc-cropresizer/fc-cropresizer.js'
    ];
    public $css = [
        'fc-cropresizer/fc-cropresizer.css'
    ];
    public $depends = [
    ];
}
