<div class="modal fade" id="modal-{$type|default:'default'}">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">{$title|default:$_labels.general.details}</h4>
      </div>
      <div class="modal-body">
        <p class="font-size-bigger text-center padding-full text-thin">{$_labels.general.loading}</p>
      </div>
      <div class="modal-footer modal-actions-wrapper">
        <button type="button" class="btn btn-outline btn-default" data-dismiss="modal">{$_labels.buttons.close}</button>
      </div>
    </div>
  </div>
</div>
