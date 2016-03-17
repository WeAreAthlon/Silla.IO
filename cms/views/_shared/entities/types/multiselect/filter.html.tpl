<div class="form-input-wrapper-sm">
    <select name="filtering[{$field}][]" id="filter-attribute-{$field}" data-attribute="{$field}" class="form-control input-sm" multiple="multiple" title="{$_labels.general.select_option|escape}" data-placeholder="{$_labels.general.select_option|escape}">
        <option value=""> </option>
        {if isset($attr.value) and $attr.value|is_array}
            {* Show regular values from the entitity *}
            {html_options options=$attr.value selected=$_get.filtering.$field|default:''}
        {else}
            {* Analyze filtering field associations *}
            {custom_class var=current_model class=get_class($resource)}
            {if isset($current_model->hasAndBelongsToMany.$field) and is_array($current_model->hasAndBelongsToMany.$field)}
                {* Show multiselect populated with all related resource values *}
                {custom_class var=related_resource class=$current_model->hasAndBelongsToMany.$field.class_name}
                {html_object_options options=$related_resource->find() selected=$_get.filtering.{$field}|default:''}
            {/if}
        {/if}
    </select>
</div>