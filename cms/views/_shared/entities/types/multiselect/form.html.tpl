<select id="{$attr.id}" class="form-control" name="{$attr.name}[]" multiple="multiple" data-placeholder="{$_labels.general.select}..."{$attr.disabled}>
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
            {html_object_options options=$related_obj->find() selected=$related_object_ids}
            {if $related_object_ids}
                {html_object_options options=$related_obj->find() selected=$related_object_ids}
            {else}
                {html_object_options options=$related_obj->find() selected=$smarty.post.{$attr.name}|default:[]}
            {/if}
        {/if}
    {/if}
</select>