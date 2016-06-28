<h1 class="text-center">{$_labels.login.title}</h1>
<form action="{url for=authentication_login}" method="post" role="form">
    <div class="form-group">
        <label for="email"><i class="glyphicon glyphicon-user"></i> {$_labels.login.username}</label>
        <input name="email" type="email" id="email" class="form-control text-thin" placeholder="..." tabindex="1" value="{$smarty.post.email|escape|default:''}">
    </div>
    <div class="form-group">
        <label for="password"><i class="glyphicon glyphicon-cog"></i> {$_labels.login.password}</label>
        <input name="password" type="password" id="password" class="form-control text-thin" placeholder="..." autocomplete="off" tabindex="2">
    </div>
{if $captchaTemplate|default:false}
    {include 'authentication/_captcha.html.tpl' inline}
{/if}
    <div class="form-group clearfix">
        <div class="text-center">
            <label class="forgotten-password text-thin"><a href="{url for=authentication_reset_form}" tabindex="5">{$_labels.login.forgotten_password}</a></label>
        </div>
    </div>
    <div class="text-center">
        <button class="btn btn-outline btn-primary text-thin" type="submit" tabindex="4"><i class="glyphicon glyphicon-log-in"></i> &nbsp;{$_labels.buttons.login}</button>
    </div>
    <input type="hidden" name="_token" value="{$_request->token()}" />
</form>