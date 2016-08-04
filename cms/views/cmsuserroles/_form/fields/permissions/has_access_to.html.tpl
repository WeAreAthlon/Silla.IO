<div class="panel-group" id="accordion-permissions-scope">
    {foreach from=$scope.permissions key=section_name item=section}
        <div class="panel panel-default">
            <div class="panel-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-permissions-scope" href="#collapse-{$section_name}">
                    <i class="glyphicon glyphicon-{if $resource->permissions[$section_name]|default:false}{if array_values($section) == array_values($resource->permissions[$section_name])}eye-open{else}eye-close{/if}{else}lock{/if}"></i> {$_labels.modules.$section_name.title}
                </a>
            </div>
            <div id="collapse-{$section_name}" class="panel-collapse collapse">
                <div class="panel-body">
                    {foreach from=$section item=action}
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="permissions[{$section_name}][]" value="{$action}"{if (isset($resource->permissions[$section_name]) && is_array($resource->permissions[$section_name]) && $action|in_array:$resource->permissions[$section_name])} checked="checked"{/if}> {$_labels.modules.$section_name.$action}
                            </label>
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>
    {/foreach}
</div>