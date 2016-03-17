<div class="form-group">
    <div class="captcha">
        <div id="recaptcha_image"></div>
    </div>
</div>
<div class="form-group">
    <label class="recaptcha_only_if_image"><span class="glyphicon glyphicon-lock"></span> {$_labels.captcha.text}</label>
    <label class="recaptcha_only_if_audio"><span class="glyphicon glyphicon-lock"></span> {$_labels.captcha.audio}</label>
    <div class="input-group">
        <input type="text" id="recaptcha_response_field" name="recaptcha_response_field" class="form-control text-thin" tabindex="3" />
        <a class="btn btn-default input-group-addon" href="javascript:Recaptcha.reload();"><span class="glyphicon glyphicon-refresh"></span></a>
        <a class="btn btn-default input-group-addon recaptcha_only_if_image" href="javascript:Recaptcha.switch_type('audio');"><span class="glyphicon glyphicon-volume-up"></span></a>
        <a class="btn btn-default input-group-addon recaptcha_only_if_audio" href="javascript:Recaptcha.switch_type('image');"><span class="glyphicon glyphicon-picture"></span></a>
        <a class="btn btn-default input-group-addon" href="javascript:Recaptcha.showhelp();"><span class="glyphicon glyphicon-info-sign"></span></a>
    </div>
    <script type="text/javascript">
        var RecaptchaOptions = { theme: 'custom', custom_theme_widget: 'recaptcha_widget' };
    </script>
    {$captchaTemplate}
</div>