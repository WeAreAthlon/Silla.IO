<h1 class="text-center">{$_labels.login.title}</h1>
<form action="{$smarty.server.REQUEST_URI}" method="post" role="form">
    <div class="form-group">
        <label for="email"><i class="glyphicon glyphicon-user"></i> {$_labels.login.username}</label>
        <input name="email" type="email" id="email" class="form-control text-thin" placeholder="..." tabindex="1" value="{$smarty.post.email|escape|default:''}">
    </div>
    <div class="form-group">
        <label for="password"><i class="glyphicon glyphicon-cog"></i> {$_labels.login.password}</label>
        <input name="password" type="password" id="password" class="form-control text-thin" placeholder="..." autocomplete="off" tabindex="2">
    </div>
    {if $captcha|default:false}
        {include file='cms/_captcha.html.tpl'}
    {/if}
    <div class="form-group clearfix">
        <div class="text-center">
            <label class="forgotten-password text-thin"><a href="{link_to controller=cms action=reset}" tabindex="5">{$_labels.login.forgotten_password}</a></label>
        </div>
    </div>
    <div class="text-center">
        <button class="btn btn-primary text-thin" type="submit" tabindex="4"><i class="glyphicon glyphicon-log-in"></i> &nbsp;{$_labels.buttons.login}</button>
    </div>
    <input type="hidden" name="_token" value="{$_request->token()}" />
</form>