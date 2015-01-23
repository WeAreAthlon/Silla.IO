<select id="{$attr.id}" name="{$attr.name}" class="form-control" data-placeholder="{$_labels.general.select|escape}..."{$attr.disabled}>
    <option value=""></option>
    {if isset($attr.value) and $attr.value|is_array}
        {html_options options=$attr.value selected=$attr.default}
    {else}
        {foreach from=$resource->belongsTo key=name item=relation}
            {append var=relations value=array_merge($relation, ['name' => $name]) index=$relation.key}
        {/foreach}

        {if $relations.{$attr.name}|default:false}
            {$property = $relations.$field.name}
            {if not $resource->$property|is_object}
                {custom_class var=related_resource class=$relations.$field.class_name}
            {else}
                {assign var=related_resource value=$resource->$property()}
            {/if}

            {html_object_options options=$related_resource->find() selected=$attr.default}
        {/if}
    {/if}
</select>
