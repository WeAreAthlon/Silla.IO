{* Analyze resource belongsTo associations *}
{foreach from=$resource->belongsTo key=name item=association}
    {append var=associations value=array_merge($association, ['name' => $name]) index=$association.key}
{/foreach}

{if $associations.$field|default:false}
    {* If the field is part of a association, assign the related resource title/name. *}
    {$property = $associations.$field.name}
    {if isset($attr.association_title)}
        {$value = $resource->$property()->first()->{$attr.association_title}|default:''}
    {else}
        {$value = $resource->$property()->first()->title|default:$resource->$property()->first()->name|default:''}
    {/if}
{else}
    {if $section.fields.$field.value|default:array()}
        {* The field is not part of a association, so assign the available values. *}
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
