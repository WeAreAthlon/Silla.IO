<h1>{$_labels.reset.title}</h1>
<form action="{$smarty.server.REQUEST_URI}" method="post">
    <fieldset>
        <div class="form-group clearfix">
            <input name="email" type="email" id="email" class="form-control text-thin" placeholder="{$_labels.reset.email|escape}" value="{$smarty.post.email|escape|default:''}" />
            <p class="text-thin">{$_labels.reset.instructions}</p>
        </div>
        {if $captcha|default:false}
            {include 'cms/_captcha.html.tpl'}
        {/if}
        <div class="text-center">
            <button class="btn btn-primary text-thin" type="submit"><i class="glyphicon glyphicon-send"></i> {$_labels.reset.send}</button>
        </div>
        <input type="hidden" name="_token" value="{$_request->token()}" />
    </fieldset>
</form>