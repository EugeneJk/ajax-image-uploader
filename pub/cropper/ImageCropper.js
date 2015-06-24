function ImageCropper(options)
{
    var self = this;
    this.iWidth = 0;
    this.iHeight = 0;
    this.cropWidth = options.cropWidth;
    this.cropHeight = options.cropHeight;
    this.cropX = 0;
    this.cropY = 0;
    var imageToCrop = document.getElementById(options.cropImageId);
    var thumnailField = document.getElementById(options.thumbnailId);
    var thumnailPreview = document.getElementById(options.thumbnailPreviewId);
    var notificationArea = document.getElementById(options.notificationAreaId);
    var originalThumb = null;

    var cropperOverlayId = 'cropper-overlay';
    var cropperImageId = 'cropper-image';
    var cropperOverlayBodyId = 'cropper-overlay-body';
    var cropperOverlayFooterId = 'cropper-overlay-footer';
    
    this.activateCrop = function(){
        if(notificationArea) {
            notificationArea.innerHTML = '';
            if(notificationArea.parentElement.classList.contains('form-group')){
                notificationArea.parentElement.classList.remove('has-error');
            }
        }
        if( $(imageToCrop).attr('src') !== ''){
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
        } else {
            if(notificationArea) {
                notificationArea.innerHTML = 'Please upload image first!';
                if(notificationArea.parentElement.classList.contains('form-group')){
                    notificationArea.parentElement.classList.add('has-error');
                }
            }
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
            thumnailPreview.style.display = '';
            self.close();
        } else {
            if(!document.getElementById('cropper-notification')){
                $("<div/>",{
                    id: 'cropper-notification',
                    class:"alert alert-warning alert-dismissible",
                    role:"alert"
                }).css({
                    width: "75%"
                }).appendTo($("#" + cropperOverlayFooterId));
            }
            document.getElementById('cropper-notification').innerHTML = '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> ' + data.error;
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
    
    this.clear = function(isRestoreOriginal){
        this.field.value = isRestoreOriginal ? this.originalImage :'';
        if(this.notification.parentElement.classList.contains('form-group')){
            this.notification.parentElement.classList.remove('has-success');
            this.notification.parentElement.classList.remove('has-error');
        }
        this.changeImagePreview();
    };
    this.clear = function(isRestoreOriginal){
        if(originalThumb === null){
            originalThumb = thumnailField.value;
        }
        if(isRestoreOriginal){
            thumnailField.value = originalThumb;
            thumnailPreview.src = originalThumb;
            thumnailPreview.style.display = '';
        } else {
            thumnailField.value = '';
            thumnailPreview.src = '';
            thumnailPreview.style.display = 'none';
        }
    };
}
