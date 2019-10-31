si4.object.si4FileUploader = function(args)
{
    // Init
    var _p = this;
    this._cons = si4.widget.si4Element;
    this._cons({ parent:$("body"), tagName:"input", attr: { type:"file" },
        style:{ display: "none" }, tagId:si4.object._nextFileUploadId()});

    this._ebase = si4.object.si4EventBase;
    this._ebase();

    // Settings
    this.autoUpload = si4.getArg(args, "autoUpload", true);
    this.fileNamePrefix = si4.getArg(args, "fileNamePrefix", "");

    // Events
    this.onUploadComplete = function(f) { _p.subscribe("onUploadComplete", f); };

    // Implementation
    this.pickedFileName = "";

    this.chooseFile = function() {
        _p.selector.click();
    };

    this.upload = function(e) {
        si4.loading.show();

        var data = new FormData();
        $.each(e.target.files, function(key, value) {
            data.append(key, value);
        });
        data.append("fileNamePrefix", _p.fileNamePrefix);

        $.ajax({
            url: "/uploadFile",
            type: "POST",
            data: data,
            cache: false,
            dataType: "json",
            processData: false,
            contentType: false,
            success: function(data, textStatus, jqXHR)
            {
                //si4.dump(data);
                _p.trigger("onUploadComplete", data);
                si4.loading.hide();
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                alert("Error: " + textStatus);
                si4.loading.hide();
            }
        });
    };

    this.getFileName = function() {
        return _p.fileNamePrefix + _p.pickedFileName;
    };

    if (this.autoUpload) {
        this.selector.change(function(e){
            var fileName = _p.selector.val().replace(/\\/g, '/');
            _p.pickedFileName = fileName.substring(fileName.lastIndexOf('/') +1);
            _p.upload(e);
        });
        this.chooseFile();
    }

};

// Id Generator
si4.object._lastFileUploadId = 0;
si4.object._nextFileUploadId = function(){
    si4.object._lastFileUploadId += 1;
    return "file"+si4.object._lastFileUploadId;
};
