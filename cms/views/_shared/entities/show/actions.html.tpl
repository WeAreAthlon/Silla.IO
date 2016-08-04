<div class="modal-footer no-padding-bottom">
    <div class="btn-group pull-left">
    {if {user_can controller=$_controller action=delete}}
        <a href="{link_to controller=$_controller action=delete id={$resource->{$resource->primaryKeyField()}}}" class="btn btn-outline btn-danger action-delete" data-resource="{$resource->{$resource->primaryKeyField()}}" data-controller="{$_controller}">
            <i class="glyphicon glyphicon-trash"></i> {$_labels.buttons.delete}
        </a>
    {/if}
    </div>
    <div class="btn-group pull-right">
        <button class="btn btn-outline btn-default" data-dismiss="modal"><i class="glyphicon glyphicon-menu-left"></i> {$_labels.buttons.close}</button>
    {if {user_can controller=$_controller action=edit}}
        <a href="{link_to controller=$_controller action=edit id={$resource->{$resource->primaryKeyField()}}}" class="btn btn-outline btn-success">
            <i class="glyphicon glyphicon-pencil"></i> {$_labels.buttons.edit}
        </a>
    {/if}
    </div>
</div>
