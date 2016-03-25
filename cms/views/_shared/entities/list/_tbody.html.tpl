{foreach from=$attributes key=field item=attr}
    {$attr.list = $attr.list|default:true}
    {if $attr.list !== false}
        {append var=fields_to_display index=$field value=$attr.type}
    {/if}
{/foreach}

{foreach from=$resources item=resource}
    <tr data-id="{$resource->{$resource->primaryKeyField()}}">
    {foreach from=$fields_to_display key=field item=type}
        <td class="column column-type-content-{$type}">
        {if "{$_controller}/_list/fields/{$field}.html.tpl"|template_exists}
            {include "{$_controller}/_list/fields/{$field}.html.tpl"}
        {else}
            {if $resource->{$field}|default:false || isset($resource->hasAndBelongsToMany.$field)}
                {if "_shared/entities/types/{$type}/list.html.tpl"|template_exists}
                    {include "_shared/entities/types/{$type}/list.html.tpl"}
                {else}
                    <div class="well well-small">
                        Missing listing template: <code>{$_mode}views/_shared/entities/types/{$type}/list.html.tpl</code>
                    </div>
                {/if}
            {else}
                <em class="text-muted">{$_labels.general.never}</em>
            {/if}
        {/if}
        </td>
    {/foreach}
    {if {user_can controller=$_controller action=show} or {user_can controller=$_controller action=edit} or {user_can controller=$_controller action=delete}}
        <td class="column column-actions">
            {if "{$_controller}/_list/actions.html.tpl"|template_exists}
                {include "{$_controller}/_list/actions.html.tpl"}
            {else}
                {include '_shared/entities/list/actions.html.tpl'}
            {/if}
        </td>
    {/if}
    </tr>
    {foreachelse}
    <tr>
       <td colspan="{1 + $fields_to_display|@count}" class="text-muted text-center text-thin">
           <p>
               <br />
               <i class="font-size-bigger glyphicon glyphicon-{$_labels.modules.$_controller.icon}"></i><br />
               {$_labels.general.no_results}
               <br />
           </p>
      </td>
    </tr>
{/foreach}
{if $resources->getCount()}
    <tr>
        <td colspan="{1 + $fields_to_display|@count}" class="table-summary text-thin accent text-muted">
            {assign var=per_page value={$_get.query.pagination.limit|default:current($_labels.pagination.limits)}}
            {$_labels.datatable.totals|sprintf:{max(($resources->paginate()->current()->pageNumber - 1) * $per_page + 1, 1)}:{min($resources->paginate()->current()->pageNumber * $per_page, $resources->paginate()->totalItems())}:{$resources->paginate()->totalItems()}}
        </td>
    </tr>
{/if}
