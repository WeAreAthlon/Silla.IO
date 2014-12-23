{* Analyze resource belongsTo relations *}
{foreach from=$resource->belongsTo key=name item=relation}
    {append var=relations value=array_merge($relation, ['name' => $name]) index=$relation.key}
{/foreach}

{if $relations.$field|default:false}
    {* If the field is part of a relation, assign the related resource title/name. *}
    {$property = $relations.$field.name}
    {$value = $resource->$property()->title|default:$resource->$property()->name|default:''}
{else}
    {if $_labels.attributes.$field.value|default:[]}
        {* The field is not part of a relation, so assign the available values. *}
        {$value = $_labels.attributes.$field.value[$resource->$field]}
    {else}
        {$value = $resource->$field}
    {/if}
{/if}

{* Show the assigned value *}
<em>{$value}</em>
