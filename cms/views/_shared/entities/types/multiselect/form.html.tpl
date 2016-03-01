<select id="{$attr.id}" class="form-control" data-limit="{$attr.limit|default:0}" name="{$attr.name}[]"{if not $attr.select_one|default:false} multiple="multiple"{/if} data-placeholder="{$_labels.general.select}..."{$attr.disabled}>
    <option value=""></option>
    {if isset($attr.value) and $attr.value|is_array}
        {html_options options=$attr.value selected=$attr.default}
    {else}
        {if isset($resource->hasAndBelongsToMany.$field) and $resource->hasAndBelongsToMany.$field|is_array}
            {custom_class var=related_obj class=$resource->hasAndBelongsToMany.$field.class_name}
            {$related_object_ids = []}
            {foreach from=$resource->$field()->all() item=related_object}
                {append var='related_object_ids' value=$related_object->id}
            {/foreach}
            {if $related_object_ids}
                {html_object_options options=$related_obj->find() selected=$related_object_ids obj_name=$attr.association_title|default:'title'}
            {else}
                {html_object_options options=$related_obj->find() selected=$smarty.post.{$attr.name}|default:[] obj_name=$attr.association_title|default:'title'}
            {/if}
        {/if}
    {/if}
</select>
