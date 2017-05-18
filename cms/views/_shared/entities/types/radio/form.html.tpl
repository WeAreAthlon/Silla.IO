{if isset($attr.value) and is_array($attr.value)}
  {foreach from=$attr.value key=k item=v}
    <label class="radio" for="{$field}_radio_{$k}">
      <input type="radio" id="{$field}_radio_{$k}" name="{$attr.name}"{if $attr.default eq $k} checked="checked"{/if} value="{$k}"{$attr.disabled}>
      {$v}
    </label>
  {/foreach}
{/if}
