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
class AjaxImageUploaderAsset extends AssetBundle
{
    public $basePath = '@vendor/eugenejk/ajax-image-uploader';
    public $js = [
        'js/AjaxImageUploader.js'
    ];
    public $depends = [
    ];
}
