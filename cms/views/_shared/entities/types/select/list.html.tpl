{* Analyze resource belongsTo relations *}
{foreach from=$resource->belongsTo key=name item=relation}
    {append var=relations value=array_merge($relation, ['name' => $name]) index=$relation.key}
{/foreach}

{if $relations.$field|default:false}
    {* If the field is part of a relation, assign the related resource title/name. *}
    {$property = $relations.$field.name}
    {if isset($attributes.association_title)}
        {$value = $resource->$property()->first()->{$attributes.association_title}|default:$resource->$property()->first()->title|default:''}
    {else}
        {$value = $resource->$property()->first()->title|default:$resource->$property()->first()->name|default:''}
    {/if}
{else}
    {* The field is not part of a relation, so assign the available values. *}
    {$value = $attributes.value[$resource->$field]|default:{$resource->$field}|default:$_labels.general.none}
{/if}

{* Show the assigned value *}
{if $attributes.escape|default:true}
    <em>{$value|escape|default:$_labels.general.none}</em>
{else}
    <em>{$value|default:$_labels.general.none}</em>
{/if}
