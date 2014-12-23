<div class="modal-footer no-padding-bottom">
    <div class="btn-group pull-right">
    {if {user_can controller=$_controller action=delete}}
        <a href="{link_to controller=$_controller action=delete id={$resource->{$resource->primaryKeyField()}}}" class="btn btn-link action-delete" data-resource="{$resource->id}" data-controller="{$_controller}">
            <i class="glyphicon glyphicon-trash"></i> {$_labels.buttons.delete}
        </a>
    {/if}
    {if {user_can controller=$_controller action=edit}}
        <a href="{link_to controller=$_controller action=edit id={$resource->{$resource->primaryKeyField()}}}" class="btn btn-link">
            <i class="glyphicon glyphicon-pencil"></i> {$_labels.buttons.edit}
        </a>
    {/if}
        <button class="btn btn-link" data-dismiss="modal"><i class="glyphicon glyphicon-share-alt"></i> {$_labels.buttons.close}</button>
    </div>
</div>
