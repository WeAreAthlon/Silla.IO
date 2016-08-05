<div class="form-input-wrapper-sm">
    <select name="filtering[{$field}][]" id="filter-attribute-{$field}" data-attribute="{$field}" class="form-control input-sm" multiple="multiple" title="{$_labels.general.select_option|escape}" data-placeholder="{$_labels.general.select_option|escape}">
        <option value=""></option>
        {if isset($attr.value) and $attr.value|is_array}
            {* Show regular values from the entitity *}
            {html_options options=$attr.value selected=$_get.filtering.$field|default:''}
        {else}
            {* Analyze filtering field associations *}
            {if isset($resource->hasAndBelongsToMany[$field]) and is_array($resource->hasAndBelongsToMany[$field])}
                {* Show multiselect populated with all related resource values *}
                {assign var=related_resource value=call_user_func(array($resource->hasAndBelongsToMany[$field]['class_name'], 'find'))}
                {if $user->hasOwnershipOver($resource->hasAndBelongsToMany[$field]['class_name'])}
                    {assign var=related_resource value=call_user_func(array('\CMS\Helpers\CMSUsers', 'filterOwnResources'), $resource->hasAndBelongsToMany[$field]['class_name'])}
                {/if}
                {html_object_options options=$related_resource selected=$_get.filtering.$field|default:''}
            {/if}
        {/if}
    </select>
</div>