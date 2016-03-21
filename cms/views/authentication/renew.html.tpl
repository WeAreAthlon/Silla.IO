<h1>{$_labels.reset_access.title}</h1>
<div class="fields-section text-thin">
{if $new_password}
     <strong>{$_labels.reset_access.success}</strong>
     <br />
     {$_labels.reset_access.new_password}: <strong>{$new_password}</strong><br /><br />
     <div class="text-center">
        <a href="{link_to controller=authentication action=login}" class="btn btn-outline btn-primary text-thin">{$_labels.sections.login}</a>
     </div>
{/if}
</div>