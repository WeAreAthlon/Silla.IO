{if isset($attr.value) and is_array($attr.value)}
    {foreach from=$attr.value key=k item=v}
        <label class="checkbox inline" for="{$field}_checkbox_{$k}">
            <input type="hidden" name="{$attr.name}[{$k}]" value="0"{$attr.disabled}>
            <input type="checkbox" id="{$field}_checkbox_{$k}" name="{$attr.name}[{$k}]"{if $attr.default and $k|in_array:(array)$attr.default} checked="checked"{/if} value="{$k}"{$attr.disabled}>
            {$v}
        </label>
    {/foreach}
{else}
    <input type="hidden" name="{$attr.name}" value="0"{$attr.disabled}>
    <input type="checkbox" name="{$attr.name}" id="{$attr.id}"{if $attr.default} checked="checked"{elseif $_action eq 'add' and $attr.default|default:false eq 'checked'} checked="checked"{/if} value="1"{$attr.disabled}>
{/if}
