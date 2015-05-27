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
    public $sourcePath = '@vendor/eugenejk/ajax-image-uploader';
    
    public $js = [
        'js/AjaxImageUploader.js'
    ];
    public $depends = [
    ];
    
    public function init()
    {
        parent::init();
        $this->publishOptions['beforeCopy'] = function ($from, $to) {
            $dirname = basename(dirname($from));
            return $dirname === 'js';
        };
    }    
}
