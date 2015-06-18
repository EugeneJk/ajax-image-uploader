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
        $('#' + options.applyButtonId).removeClass('hidden');
        cropresizer.getObject(options.cropImageId).init({
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
        } else {
            console.log(data);
        }
    };
    
}
