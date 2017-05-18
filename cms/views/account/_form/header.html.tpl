<div class="media account-profile-preview-wrapper">
  <a class="media-left" href="{link_to controller=account}">
    <img src="{$user->getAvatar(75)}" class="media-object img-circle" alt="{$user->name|escape}"/>
  </a>
  <div class="media-body">
    <h1 class="media-heading text-thinner">&nbsp;{$_labels.modules.account.title} /
      <strong class="text-thin">{$user->name}</strong></h1>
    <p class="font-size-smaller no-margin-bottom">
      <span class="text-muted">&nbsp;&nbsp;{$_labels.general.last_login}</span> {$user->login_on|date_format:#datetime#}
    </p>
    <p class="text-thin">&nbsp;&nbsp;{$_labels.modules.account.desc}</p>
  </div>
</div>