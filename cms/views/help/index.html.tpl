{capture caption}
<h1 class="text-thinner">{$_labels.modules.$_controller.title|default:''}
    <a href="{link_to controller=$_controller action=edit}" class="btn btn-link no-padding-left"><i class="glyphicon
    glyphicon-plus"></i> {$_labels.modules.$_controller.edit}</a>
</h1>
<p class="text-thin">{$_labels.modules.$_controller.desc|default:''}</p>
{/capture}
<div class="content help">{$content.formatted}</div>