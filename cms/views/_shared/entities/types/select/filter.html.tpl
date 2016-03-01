<div class="form-input-wrapper-sm">
    <select name="filtering[{$field}]" id="filter-attribute-{$field}" data-attribute="{$field}" class="form-control input-sm" title="{$_labels.general.select_option|escape}" data-placeholder="{$_labels.general.select_option|escape}">
        <option value=""> </option>
        {if isset($attr.value) and $attr.value|is_array}
            {* Show regular values from the entitity *}
            {html_options options=$attr.value selected=$_get.filtering.$field|default:''}
        {else}
            {* Analyze filtering field relations *}
            {custom_class var=current_model class=$model}
            {foreach from=$current_model->belongsTo key=name item=relation}
                {append var=relations value=array_merge($relation, ['name' => $name]) index=$relation.key}
            {/foreach}

            {if $relations.$field|default:false}
                {* Show dropdown populated with all related resource values *}
                {custom_class var=related_resource class=$relations.$field.class_name}
                {if isset($attributes.association_title)}
                    {html_object_options options=$related_resource->find() selected=$_get.filtering.$field|default:'' obj_name=$attributes.association_title|default:'title'}
                {else}
                    {html_object_options options=$related_resource->find() selected=$_get.filtering.$field|default:''}
                {/if}
            {/if}
        {/if}
    </select>
</div>
