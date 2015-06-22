function ImageUploader (initObject) {
    var self = this;
    this.input = document.getElementById(initObject.uploadInputId);
    this.field = document.getElementById(initObject.fieldId);
    this.preview = document.getElementById(initObject.previewId);
    this.notification = document.getElementById(initObject.notificationId);
    this.emptyImageLink = initObject.emptyImageLink;
    this.url = initObject.actionUrl;
    this.form = initObject.formId !== '' ? document.getElementById(initObject.formId) : null;
    this.originalImage = initObject.originalImage;

    var uploader;
    var initUploader = function(){
        uploader = new XMLHttpRequest();
        uploader.onreadystatechange = function(){
            if (uploader.readyState === 4){
                if(uploader.status === 200){
                    self.success(JSON.parse(uploader.responseText));
                } else {
                    self.failure();
                }
            }
        };

    };

    this.changeImagePreview = function(){
        this.preview.src = (this.field.value !== '') ? this.field.value : this.emptyImageLink;
    };

    this.upload = function(){
        if(this.input.files.length === 0){
            return;
        }
        initUploader();

        /* Create a FormData instance */
        var formData = new FormData();
        /* Add the file */ 
        formData.append("file", this.input.files[0]);
        if(this.form){
            for(var i in this.form.elements){
                var element = this.form.elements[i];
                if(element.name === '_csrf'){
                    formData.append(element.name,element.value);
                }
            }
        }

        uploader.open("post", this.url, true);
        uploader.send(formData);  /* Send to server */ 
    };

    this.success = function(data){
        if(data.error === undefined){
            this.notification.innerHTML = '';
            this.field.value = data.filelink;
            this.changeImagePreview();
            
            if(this.notification.parentElement.classList.contains('form-group')){
                this.notification.parentElement.classList.remove('has-error');
                this.notification.parentElement.classList.add('has-success');
            }
        } else {
            this.notification.innerHTML = data.error;
            
            if(this.notification.parentElement.classList.contains('form-group')){
                this.notification.parentElement.classList.remove('has-success');
                this.notification.parentElement.classList.add('has-error');
            }
        }
    };
    this.failure = function(){
        console.log(uploader);
    };
    
    this.clear = function(isRestoreOriginal){
        this.field.value = isRestoreOriginal ? this.originalImage :'';
        if(this.notification.parentElement.classList.contains('form-group')){
            this.notification.parentElement.classList.remove('has-success');
            this.notification.parentElement.classList.remove('has-error');
        }
        this.changeImagePreview();
    };

    this.changeImagePreview();
}
