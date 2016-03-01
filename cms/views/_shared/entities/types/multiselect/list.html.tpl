{* Analyze resource hasAndBelonsToMany relations *}
{$values = []}
{if isset($resource->hasAndBelongsToMany.$field)}
    {* If the field is part of a relation assign the relation title/name values *}
    {foreach from=$resource->$field()->all() item=related_resource}
        {append var='values' value=$related_resource->title|default:$related_resource->name|default:'-'}
    {/foreach}
{else}
    {* The field is not part of a relation, so assign the available values. *}
    {foreach from=$resource->$field item=value}
        {append var='values' value=$attributes.$field.value[$value]}
    {/foreach}
{/if}

{* Show the assigned values *}
{foreach from=$values item=value}
    {if $attributes.escape|default:true}
        <span class="label label-default">{$value|escape}</span>
    {else}
        <span class="label label-default">{$value}</span>
    {/if}
{foreachelse}
    <em class="text-muted">{$_labels.general.never}</em>
{/foreach}
