{* Analyze resource belongsTo relations *}
{foreach from=$resource->belongsTo key=name item=relation}
    {append var=relations value=array_merge($relation, ['name' => $name]) index=$relation.key}
{/foreach}

{if $relations.$field|default:false}
    {* If the field is part of a relation, assign the related resource title/name. *}
    {$property = $relations.$field.name}
    {if isset($attr.association_title)}
        {$value = $resource->$property()->first()->{$attr.association_title}|default:''}
    {else}
        {$value = $resource->$property()->first()->title|default:$resource->$property()->first()->name|default:''}
    {/if}
{else}
    {if $section.fields.$field.value|default:[]}
        {* The field is not part of a relation, so assign the available values. *}
        {$value = $section.fields.$field.value[$resource->$field]}
    {else}
        {$value = $resource->$field}
    {/if}
{/if}

{* Show the assigned value *}
{if $section.fields.$field.escape|default:true}
    <strong><em>{$value|escape|default:$_labels.general.none}</em></strong>
{else}
    <strong><em>{$value|default:$_labels.general.none}</em></strong>
{/if}
