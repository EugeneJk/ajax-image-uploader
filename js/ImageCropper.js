function ImageCropper(options)
{
    var self = this;
    this.iWidth = 0;
    this.iHeight = 0;
    this.cropWidth = 132;
    this.cropHeight = 164;
    this.cropX = 0;
    this.cropY = 0;
    var imageToCrop = document.getElementById(options.cropImageId);
    var thumnailField = document.getElementById(options.thumbnailId);
    var thumnailPreview = document.getElementById(options.thumbnailPreviewId);

    var cropperOverlayId = 'cropper-overlay';
    var cropperImageId = 'cropper-image';
    var cropperOverlayBodyId = 'cropper-overlay-body';
    var cropperOverlayFooterId = 'cropper-overlay-footer';
    
    
    this.getParams = function(){
        console.log("Изображение:" + imageToCrop.src);
        console.log("Длина изображения:" + this.iWidth);
        console.log("Высота изображения:" + this.iHeight);
        console.log("Длина кропа:" + this.cropWidth);
        console.log("Высота кропа:" + this.cropHeight);
        console.log("X кропа:" + this.cropX);
        console.log("Y кропа:" + this.cropY);
        
    };
    
    this.activateCrop = function(){
        if(imageToCrop.src !== ''){
            showCroppingLayout();
            cropresizer.getObject(cropperImageId).init({
                cropWidth : this.cropWidth,
                cropHeight : this.cropHeight,
                onUpdate : function() {
                    self.iWidth = this.iWidth;
                    self.iHeight = this.iHeight;
                    self.cropWidth = this.cropWidth;
                    self.cropHeight = this.cropHeight;
                    self.cropX = this.cropLeft - this.iLeft;
                    self.cropY = this.cropTop - this.iTop;
                }
            },1);
        }
    };

    this.crop = function(){
        $.ajax({
            type: "POST",
            url: options.url,
            data: {
                image:{
                    src: imageToCrop.src,
                    width: this.iWidth,
                    height: this.iHeight
                },
                crop:{
                    x: this.cropX,
                    y: this.cropY,
                    width: this.cropWidth,
                    height: this.cropHeight
                }
            },
            success: this.success,
            dataType: 'json'
        });            
    };
    
    this.success = function(data){
        if(data.file){
            thumnailField.value = data.file;
            thumnailPreview.src = thumnailField.value;
            self.close();
        } else {
            console.log(data);
        }
    };
    
    var showCroppingLayout = function(){
        $("<div />",{
            id: cropperOverlayId
        }).appendTo($("body"));
        
        drawBody();
        drawFooter();
    };
    
    var drawBody = function(){
        $("<div />",{
            id: cropperOverlayBodyId
        }).appendTo($("#" + cropperOverlayId));

        $("<img />",{
            id: cropperImageId,
            src: imageToCrop.src
        }).css({
            "max-width":"100%",
            "max-height":"100%"
        }).appendTo($("#" + cropperOverlayBodyId));

        
    };
    var drawFooter = function(){
        $("<div />",{
            id: cropperOverlayFooterId
        }).appendTo($("#" + cropperOverlayId));
        
        $("<button/>",{
            class:"cropper-button-close btn btn-default pull-right",
            onclick: options.objectVariableName + '.close();return false;'
        }).html('Close <i class="glyphicon glyphicon-remove"></i>').appendTo($("#" + cropperOverlayFooterId));
        
        $("<button/>",{
            class:"cropper-button-close btn btn-success pull-right",
            onclick: options.objectVariableName + '.crop();return false;'
        }).html('Apply <i class="glyphicon glyphicon-ok"></i>').appendTo($("#" + cropperOverlayFooterId));
    };
    
    this.close = function(){
        $('#' + cropperOverlayId).remove();
        $('#resizeDivId_' + cropperImageId).remove();
        $('#cropDivId_' + cropperImageId).remove();
    };
    
}
