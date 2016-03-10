{* Analyze resource belongsTo associations *}
{foreach from=$resource->belongsTo key=name item=association}
    {append var=associations value=array_merge($association, ['name' => $name]) index=$association.key}
{/foreach}

{if $associations.$field|default:false}
    {* If the field is part of a association, assign the related resource title/name. *}
    {$property = $associations.$field.name}
    {if isset($attributes.association_title)}
        {$value = $resource->$property()->first()->{$attributes.association_title}|default:$resource->$property()->first()->title|default:''}
    {else}
        {$value = $resource->$property()->first()->title|default:$resource->$property()->first()->name|default:''}
    {/if}
{else}
    {* The field is not part of a association, so assign the available values. *}
    {$value = $attributes.value[$resource->$field]|default:{$resource->$field}|default:$_labels.general.none}
{/if}

{* Show the assigned value *}
{if $attributes.escape|default:true}
    <em>{$value|escape|default:$_labels.general.none}</em>
{else}
    <em>{$value|default:$_labels.general.none}</em>
{/if}
