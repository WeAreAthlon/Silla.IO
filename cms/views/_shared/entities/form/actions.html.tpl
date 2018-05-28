{if $_action eq 'edit' and {user_can controller=$_controller action=delete}}
  <div class="btn-group pull-left" role="toolbar">
    <a href="{link_to controller=$_controller action=delete id=$resource->getPrimaryKeyValue()}" data-controller="{$_controller}" class="btn btn-outline btn-danger navbar-btn action-delete">
      <span class="glyphicon glyphicon-trash"></span> {$_labels.buttons.delete}
    </a>
  </div>
{/if}
<div class="btn-group pull-right" role="toolbar">
  <button class="btn btn-outline btn-default navbar-btn cancel" type="reset">
    <span class="glyphicon glyphicon-menu-left"></span> {$_labels.buttons.cancel}
  </button>
  <button class="btn btn-outline btn-success navbar-btn" type="submit">
    <span class="glyphicon glyphicon-pencil"></span> {if $_action eq 'create'}{$_labels.buttons.create}{else}{$_labels.buttons.save}{/if}
  </button>
</div>
