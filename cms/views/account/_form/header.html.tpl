<div class="media account-profile-preview-wrapper">
    <a class="pull-left" href="#">
        <img src="{'CMS\Helpers\CMSUsers::getGravatar'|call_user_func_array:[$user->email, 75]}" class="img-circle pull-left" alt="{$user->name|escape}"/>
    </a>
    <div class="media-body">
        <h2 class="media-heading text-thin">{$_labels.modules.account.title} / <strong>{$user->name}</strong></h2>
        <p class="font-size-smaller no-margin-bottom">
            <span class="text-muted">{$_labels.general.last_login}</span>
            {$user->login_on|date_format:#datetime#}
        </p>
        <p class="text-thin">{$_labels.modules.account.desc}</p>
    </div>
</div>