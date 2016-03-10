<select id="{$attr.id}" name="{$attr.name}" class="form-control" data-placeholder="{$_labels.general.select|escape}..."{$attr.disabled}>
    {if not $attr.empty|default:false}<option value=""></option>{/if}
    {if isset($attr.value) and $attr.value|is_array}
        {html_options options=$attr.value selected=$attr.default|default:$attr.default_value|default:''}
    {else}
        {foreach from=$resource->belongsTo key=name item=association}
            {append var=associations value=array_merge($association, ['name' => $name]) index=$association.key}
        {/foreach}

        {if $associations.{$attr.name}|default:false}
            {$property = $associations.$field.name}
            {if not $resource->$property|is_object}
                {custom_class var=related_resource class=$associations.$field.class_name}
            {else}
                {assign var=related_resource value=$resource->$property()}
            {/if}

            {html_object_options options=$related_resource->find() selected=$attr.default|default:$attr.default_value|default:'' obj_name=$attr.association_title|default:'title'}
        {/if}
    {/if}
</select>
