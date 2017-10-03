<div class="form-input-wrapper-sm">
  <select name="filtering[{$field}]" id="filter-attribute-{$field}" data-attribute="{$field}" class="form-control input-sm" title="{$_labels.general.select_option|escape}" data-placeholder="{$_labels.general.select_option|escape}">
    <option value=""></option>
    {if isset($attr.value) and $attr.value|is_array}
      {* Show regular values from the entitity *}
      {html_options options=$attr.value selected=$_get.filtering.$field|default:''}
    {else}
      {* Analyze filtering field associations *}
      {custom_class var=current_model class=get_class($resource)}
      {foreach from=$current_model->belongsTo key=name item=association}
        {append var=associations value=array_merge($association, ['name' => $name]) index=$association.key}
      {/foreach}

      {if $associations.$field|default:false}
        {* Show dropdown populated with all related resource values *}
        {assign var=related_resource value=call_user_func(array($associations.$field.class_name, 'find'))}
        {if $user->owns($associations.$field.class_name)}
          {assign var=related_resource value=call_user_func(array('\CMS\Helpers\Ownership', 'filter'), $associations.$field.class_name)}
        {/if}
        {if $attr.association_title|default:false}
          {html_object_options options=$related_resource selected=$_get.filtering.$field|default:'' obj_name=$attr.association_title|default:'title'}
        {else}
          {html_object_options options=$related_resource selected=$_get.filtering.$field|default:''}
        {/if}
      {/if}
    {/if}
  </select>
</div>
