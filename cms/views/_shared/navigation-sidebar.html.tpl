<div class="panel-group" id="cms-modules">
{foreach from=$modules item=module}
    {if not $_labels.modules.$module.hidden|default:false}
    <div class="panel no-margin-top no-border no-border-radius no-box-shadow{if $_controller eq $module} panel-active{/if}">
        <div class="panel-heading no-border-radius">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#cms-modules" href="#collapse-module-{$module}" title="{$_labels.modules.$module.title|escape}">
                    <i class="glyphicon glyphicon-{$_labels.modules.$module.icon}"></i> {$_labels.modules.$module.title}
                </a>
            </h4>
        </div>
        <div id="collapse-module-{$module}" class="panel-collapse collapse{if $_controller eq $module} in{/if}">
            <div class="panel-body no-border">
                <ul class="list-unstyled no-margin">
                    <li{if $_controller eq $module and $_action eq 'index'} class="active"{/if}><a href="{link_to controller=$module}"><i class="glyphicon glyphicon-align-justify"></i> {$_labels.modules.$module.index}</a></li>
                    {if {user_can controller=$module action=create}}<li{if $_controller eq $module and $_action eq 'create'} class="active"{/if}><a href="{link_to controller=$module action=create}"><i class="glyphicon glyphicon-plus"></i> {$_labels.modules.$module.create}</a></li>{/if}
                </ul>
            </div>
        </div>
    </div>
    {/if}
{/foreach}
{foreach from=$_labels.navigation_groups key=group_slug item=group}
    {if array_intersect($group.modules, $modules)}
    <div class="panel no-margin-top no-border no-border-radius no-box-shadow{if $_controller|in_array:$group.modules} panel-active{/if}">
        <div class="panel-heading no-border-radius">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#cms-modules" href="#collapse-module-{$group_slug}" title="{$group.title|escape}">
                    <i class="glyphicon glyphicon-{$group.icon}"></i> {$group.title}
                </a>
            </h4>
        </div>
        <div id="collapse-module-{$group_slug}" class="panel-collapse collapse{if $_controller|in_array:$group.modules} in{/if}">
            <div class="panel-body no-border">
                <ul class="list-unstyled no-margin">
                {foreach from=$group.modules item=submodule}
                    {if {user_can controller=$submodule action=index}}
                    <li{if $_controller eq $submodule} class="active"{/if}>
                        <a href="{link_to controller=$submodule}">
                            <i class="glyphicon glyphicon-{$_labels.modules.$submodule.icon}"></i> {$_labels.modules.$submodule.title}
                        </a>
                    </li>
                    {/if}
                {/foreach}
                </ul>
            </div>
        </div>
    </div>
    {/if}
{/foreach}
</div>
