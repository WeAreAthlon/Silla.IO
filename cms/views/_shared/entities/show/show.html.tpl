{if "{$_controller}/_show/header.html.tpl"|template_exists}
    {include "{$_controller}/_show/header.html.tpl"}
{else}
    {include '_shared/entities/show/header.html.tpl'}
{/if}

{foreach from=$sections key=section_key item=section}
{if $section.meta.show|default:true}
    {if $section.fields|default:false}
    {$section.serialize = $section.meta.serialize|default:false}
    <h3 class="no-margin-top text-thin"><i class="glyphicon glyphicon-{$section.meta.icon}"></i> {$section.meta.title}</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>{$_labels.general.attribute}</th>
                <th>{$_labels.general.value}</th>
            </tr>
        </thead>
        <tfoot></tfoot>
        <tbody>
        {foreach from=$section.fields item=attr key=field}
            {$attr.hidden = $attr.hidden|default:false}

            {if $section.serialize}
                {$attr.section = $resource->$section_key}
                {$attr.default = $attr.section.$field}
            {else}
                {$attr.default = $resource->$field}
            {/if}

            {if not $attr.hidden}
            <tr>
                <th scope="row">{$attr.title}</th>
                <td>
                {if "{$_controller}/_show/fields/{$field}.html.tpl"|template_exists}
                    {include file="{$_controller}/_show/fields/{$field}.html.tpl"}
                {elseif "{$_controller}/_list/fields/{$field}.html.tpl"|template_exists}
                    {include file="{$_controller}/_list/fields/{$field}.html.tpl"}
                {elseif "_shared/entities/types/{$attr.type}/show.html.tpl"|template_exists}
                    {include file="_shared/entities/types/{$attr.type}/show.html.tpl"}
                {elseif "_shared/entities/types/{$attr.type}/list.html.tpl"|template_exists}
                    {include file="_shared/entities/types/{$attr.type}/list.html.tpl"}
                {else}
                    <div class="well well-small">
                        Missing field type show template: <code>{$_mode}views/_shared/entities/types/{$attr.type}/show.html.tpl</code>
                    </div>
                {/if}
                </td>
            </tr>
            {/if}
        {/foreach}
        </tbody>
    </table>
    <hr />
    {/if}
{/if}
{/foreach}

{if "{$_controller}/_show/footer.html.tpl"|template_exists}
    {include "{$_controller}/_show/footer.html.tpl"}
{else}
    {include '_shared/entities/show/footer.html.tpl' inline}
{/if}

{if "{$_controller}/_show/actions.html.tpl"|template_exists}
    {include "{$_controller}/_show/actions.html.tpl"}
{else}
    {include '_shared/entities/show/actions.html.tpl' inline}
{/if}
