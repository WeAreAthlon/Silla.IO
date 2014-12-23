<div class="btn-group pull-right" role="toolbar">
    <button class="btn btn-default navbar-btn cancel text-thin" type="reset">{$_labels.buttons.cancel}</button>
{if $_action eq 'edit' and {user_can controller=$_controller action=delete}}
    <a href="{link_to controller=$_controller action=delete id=$resource->{$resource->primaryKeyField()}}" data-controller="{$_controller}" class="btn btn-danger navbar-btn text-thin action-delete">{$_labels.buttons.delete}</a>
{/if}
    <button class="btn btn-success navbar-btn text-thin" type="submit">{if $_action eq 'create'}{$_labels.buttons.create}{else}{$_labels.buttons.save}{/if}</button>
</div>
