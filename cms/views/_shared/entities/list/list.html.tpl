{if "{$_controller}/_list/header.html.tpl"|template_exists}
    {capture name='caption'}{include file="{$_controller}/_list/header.html.tpl"}{/capture}
{else}
    {capture name='caption'}{include file="_shared/entities/list/header.html.tpl"}{/capture}
{/if}

{if not $attributes|default:false}
    {$attributes = []}
    {foreach from=$_labels.attributes item=section}
        {$attributes = array_merge($attributes, $section.fields)}
    {/foreach}
{/if}
<div class="data-table-wrapper row">
    {foreach from=$attributes item=attr}
        {if $attr.filter|default:false}
            {include file='_shared/entities/filter/filter.html.tpl'}
            {break}
        {/if}
    {/foreach}
    <div class="table-responsive">
        <table class="table table-striped table-hover data-table data-table-{$_controller}" data-offset-top="50" data-url-source="{link_to controller=$_controller}" data-url-export="{link_to controller=$_controller action=export}" data-controller="{$_controller}" data-type="{$type|default:'xhr'}" data-default-filtering='{$filtering_default|default:false|json_encode}'>
            {include file='_shared/entities/list/_table.html.tpl'}
        </table>
    </div>
</div>

{if "{$_controller}/_list/footer.html.tpl"|template_exists}
    {include file="{$_controller}/_list/footer.html.tpl"}
{else}
    {include file="_shared/entities/list/footer.html.tpl"}
{/if}

{if {user_can controller=$_controller action=show}}
    {include file='_shared/modals/default.html.tpl' type='preview'}
{/if}
