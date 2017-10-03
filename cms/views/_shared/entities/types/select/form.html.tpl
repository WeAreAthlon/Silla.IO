<select id="{$attr.id}" name="{$attr.name}" class="form-control" data-placeholder="{$_labels.general.select|escape}..."{$attr.disabled}>
  {if not $attr.empty|default:false}
    <option value=""></option>
  {/if}
  {if isset($attr.value) and $attr.value|is_array}
    {html_options options=$attr.value selected=$attr.default|default:$attr.default_value|default:''}
  {else}
    {foreach from=$resource->belongsTo key=name item=association}
      {append var=associations value=array_merge($association, ['name' => $name]) index=$association.key}
    {/foreach}

    {if $associations[$attr.name]|default:false}
      {if $user->owns($associations.$field.class_name)}
        {assign var=related_resource value=call_user_func(array('\CMS\Helpers\Ownership', 'filter'), $associations.$field.class_name)}
      {else}
        {assign var=related_resource value=call_user_func(array($associations.$field.class_name, 'find'))}
      {/if}

      {html_object_options options=$related_resource selected=$attr.default|default:$attr.default_value|default:'' obj_name=$attr.association_title|default:'title'}
    {/if}
  {/if}
</select>
