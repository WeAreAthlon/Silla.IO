<ul class="nav navbar-nav navbar-right navbar-user text-thin no-margin-right">
    <li class="dropdown user-dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="glyphicon glyphicon-user"></i>&nbsp;{$user->name}&nbsp;<b class="caret"></b>
        </a>
        <ul class="dropdown-menu">
            <li class="text-center">
              <a href="{link_to controller=account}" title="{$user->name|escape}">
                  <img src="{'CMS\Helpers\CMSUsers::getGravatar'|call_user_func_array:[$user->email, 75]}" class="img-circle" alt="{$user->name|escape}"/><br />
                  <strong>{$user->name}</strong><br />
                  <span class="font-size-smaller">
                      <span class="text-muted">{$_labels.general.last_login}</span><br />
                      {$user->login_on|date_format:#datetime#}
                  </span>
              </a>
            </li>
            <li class="divider"></li>
            <li>
                <a href="{link_to controller=account}"><i class="glyphicon glyphicon-user"></i> {$_labels.modules.account.index}</a>
            </li>
            {if {user_can controller=account action=credentials}}
            <li>
                <a href="{link_to controller=account action=credentials}"><i class="glyphicon glyphicon-lock"></i> {$_labels.modules.account.credentials}</a>
            </li>
            {/if}
            <li class="divider"></li>
            <li>
                <a href="{link_to controller=authentication action=logout}"><i class="glyphicon glyphicon-off"></i> {$_labels.sections.logout}</a>
            </li>
        </ul>
    </li>
</ul>