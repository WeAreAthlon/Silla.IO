<h1 class="text-thinner">
    {$_labels.modules.$_controller.title}

    {if {user_can controller=$_controller action=create}}
        <a href="{link_to controller=$_controller action=create}" class="btn btn-link no-padding-left modal-trigger-inline" title="{$_labels.modules.$_controller.create|escape}">
            <i class="glyphicon glyphicon-plus"></i> {$_labels.modules.$_controller.create}
        </a>
    {/if}
</h1>
<p class="text-thin">{$_labels.modules.$_controller.desc|default:''}</p>
