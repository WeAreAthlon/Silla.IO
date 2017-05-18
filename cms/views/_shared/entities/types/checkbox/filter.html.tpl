<div class="form-input-wrapper-sm">
  <select name="filtering[{$field}]" id="filter-attribute-{$field}" data-attribute="{$field}" class="form-control input-sm" title="{$_labels.general.select_option|escape}" data-placeholder="{$_labels.general.select_option|escape}">
    <option value=""></option>
    {if isset($attr.value) and $attr.value|is_array}
      {html_options options=$attr.value selected=$_get.filtering.$field|default:''}
    {else}
      {* Show default Yes/No values *}
      {html_options options=$_labels.status selected=$_get.filtering.$field|default:''}
    {/if}
  </select>
</div>
