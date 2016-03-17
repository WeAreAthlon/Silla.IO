<ul class="nav navbar-nav navbar-right navbar-user text-thin no-margin-right">
    <li class="dropdown user-dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-user"></i>&nbsp;{$user->name}&nbsp;<b class="caret"></b></a>
      <ul class="dropdown-menu">
      {if {user_can controller=users action=account}}
        <li class="text-center">
            <a href="{link_to controller=users action=account}" title="{$user->name|escape}">
                <img src="{'CMS\Helpers\CMSUsers::getGravatar'|call_user_func_array:[$user->email, 75]}" class="img-circle" alt="{$user->name|escape}"/><br />
                <span class="text-thin">
                    <strong>{$user->name}</strong><br />
                    <span class="font-size-smaller">
                        <span class="text-muted">{$_labels.general.last_login}</span><br />
                        {$user->login_on|date_format:#datetime#}
                    </span>
                </span>
            </a>
        </li>
        <li class="divider"></li>
        <li>
             <a href="{link_to controller=users action=account}"><i class="glyphicon glyphicon-user"></i> {$_labels.modules.users.account}</a>
        </li>
        {/if}
        <li><a href="{link_to controller=authentication action=logout}"><i class="glyphicon glyphicon-log-out"></i> {$_labels.sections.logout}</a></li>
      </ul>
    </li>
</ul>