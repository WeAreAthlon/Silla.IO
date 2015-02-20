<form action="{link_to controller=$_controller action=create}" method="post" class="dropzone" >
    <div class="dz-message">
        <p class="text-muted font-size-bigger text-thin">
            <span class="glyphicon glyphicon-paperclip"></span><br /> {$_labels.dropzone.default} <br />
            <span class="font-size-smaller">{$_labels.dropzone.size|sprintf:{$limitations.size / 1048576}}</span>
        </p>
    </div>
    <input type="hidden" name="_token" value="{$_request->token()}" />
</form>

<script>
    $(".dropzone").dropzone({
        paramName: 'media',
        filesizeBase: 1024,
        uploadMultiple: false,
        acceptedFiles: '{', '|implode:$limitations.mimeType}',
        maxFilesize: {$limitations.size / 1048576},
        dictDefaultMessage : "{$_labels.dropzone.default}",
        dictFallbackMessage: "{$_labels.dropzone.fallback}",
        dictFallbackText   : "{$_labels.dropzone.fallbackText}",
        dictInvalidFileType: "{$_labels.dropzone.invalid}",
        dictFileTooBig     : "{$_labels.dropzone.toobig}",
        dictResponseError  : "{$_labels.dropzone.response}",
        dictCancelUpload   : "{$_labels.dropzone.cancel}",
        dictCancelUploadConfirmation: "{$_labels.dropzone.cancel_confirmation}",
        dictRemoveFile     : "{$_labels.dropzone.remove}",
        dictMaxFilesExceeded:"{$_labels.dropzone.max}"
    });
</script>
