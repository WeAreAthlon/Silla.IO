<select id="{$attr.id}" class="form-control" data-limit="{$attr.limit|default:0}" name="{$attr.name}[]"{if not $attr.select_one|default:false} multiple="multiple"{/if} data-placeholder="{$_labels.general.select}..."{$attr.disabled}>
  <option value=""></option>
  {if isset($attr.value) and $attr.value|is_array}
    {html_options options=$attr.value selected=$attr.default}
  {else}
    {if isset($resource->hasAndBelongsToMany.$field) and $resource->hasAndBelongsToMany.$field|is_array}
      {assign var=related_obj value=call_user_func(array($resource->hasAndBelongsToMany[$field]['class_name'], 'find'))}
      {if $user->hasOwnershipOver($resource->hasAndBelongsToMany[$field]['class_name'])}
        {assign var=related_obj value=call_user_func(array('\CMS\Helpers\CMSUsers', 'filterOwnResources'), $resource->hasAndBelongsToMany[$field]['class_name'])}
      {/if}
      {$related_object_ids = array()}
      {foreach from=$resource->$field()->all() item=related_object}
        {append var='related_object_ids' value=$related_object->getPrimaryKeyValue()}
      {/foreach}
      {if $related_object_ids}
        {html_object_options options=$related_obj selected=$related_object_ids obj_name=$attr.association_title|default:'title'}
      {else}
        {html_object_options options=$related_obj selected=$_post[$attr.name]|default:array() obj_name=$attr.association_title|default:'title'}
      {/if}
    {/if}
  {/if}
</select>
