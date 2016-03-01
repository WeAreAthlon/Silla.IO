{if $attributes.escape|default:true}
    {$attr.default|escape}
{else}
    {$attr.default}
{/if}