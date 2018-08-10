$(document).ready(function() {
    var hasInit = false;
    $(".addfile").colorbox({inline: true, width: "35%", height: "85%", onLoad: function() {
            var uploadUrl = $(this).data('url');
            initUploader(uploadUrl);
        }});

    var fileType = (typeof FILE_TYPE == 'undefined') ? '*.*' : FILE_TYPE;
    
    function uploadStartNew(file) {
        uploadStart(file);
        var uploadType = $("#upload_type").val();
        uploader.setPostParams({"type" : uploadType});
    }
    
    function initUploader(uploadUrl) {
        if (hasInit) {
            return;
        }
        hasInit = true;
        window.uploader = new SWFUpload({
            file_post_name: 'file',
            // Backend Settings
            upload_url: uploadUrl,
            // File Upload Settings
            file_size_limit: "0",
            file_types: fileType,
            file_types_description: "上传文件",
            file_upload_limit: 0,
            file_queue_limit: 0,
            // Event Handler Settings (all my handlers are in the Handler.js file)
            file_dialog_start_handler: fileDialogStart,
            file_queued_handler: fileQueued,
            file_queue_error_handler: fileQueueError,
            file_dialog_complete_handler: fileDialogComplete,
            upload_start_handler: uploadStartNew,
            upload_progress_handler: uploadProgress,
            upload_error_handler: uploadError,
            upload_success_handler: uploadSuccess,
            upload_complete_handler: uploadComplete,
            // Button Settings
            button_image_url: "/images/upload.png",
            button_placeholder_id: "btnUpload",
            button_width: 73,
            button_height: 23,
            // Flash Settings
            flash_url: "/js/swfupload/swfupload.swf",
            custom_settings: {
                progressTarget: "fsUploadProgress",
                cancelButtonId: "btnCancel"
            },
            debug: false
        });
    }
});
