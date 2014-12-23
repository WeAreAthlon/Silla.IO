{if isset($attributes.$field.value) and is_array($attributes.$field.value)}
    {foreach from=$resource->$field item=k}
        <span class="label label-default">{$attributes.$field.value[$k]|default:''}</span>
    {/foreach}
{else}
    <span class="label label-default">{$_labels.status[$resource->$field]}</span>
{/if}
