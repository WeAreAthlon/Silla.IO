<div class="btn-group">
    <a href="{link_to controller=$_controller action=show id=$resource->{$resource->primaryKeyField()}}" title="{$resource->title|default:$resource->name|default:''}" class="btn btn-sm btn-default modal-trigger-preview{if not {user_can controller=$_controller action=show}} disabled{/if}">
        <i class="glyphicon glyphicon-search"></i> {$_labels.modules.$_controller.show}
    </a>
    {if {user_can controller=$_controller action=edit}}
        <a href="{link_to controller=$_controller action=edit id=$resource->{$resource->primaryKeyField()}}" class="btn btn-sm btn-default">
            <i class="glyphicon glyphicon-pencil"></i> {$_labels.modules.$_controller.edit}
        </a>
    {/if}
    {if {user_can controller=$_controller action=credentials}}
        <a href="{link_to controller=$_controller action=credentials id=$resource->{$resource->primaryKeyField()}}" class="btn btn-sm btn-default">
            <i class="glyphicon glyphicon-lock"></i> {$_labels.modules.$_controller.credentials}
        </a>
    {/if}
    {if {user_can controller=$_controller action=delete}}
        <button class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu pull-right" role="menu">
        {if {user_can controller=$_controller action=delete}}
            <li>
                <a href="{link_to controller=$_controller action=delete id=$resource->{$resource->primaryKeyField()}}" data-controller="{$_controller}" class="action-delete">
                    <i class="glyphicon glyphicon-trash"></i> {$_labels.modules.$_controller.delete}
                </a>
            </li>
        {/if}
        </ul>
    {/if}
</div>
