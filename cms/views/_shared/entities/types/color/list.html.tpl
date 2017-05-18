{if $attributes.escape|default:true}
  {$resource->$field|escape}
{else}
  {$resource->$field}
{/if}