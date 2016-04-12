{if "{$_controller}/_list/header.html.tpl"|template_exists}
    {capture name='caption'}{include "{$_controller}/_list/header.html.tpl"}{/capture}
{else}
    {capture name='caption'}{include '_shared/entities/list/header.html.tpl' inline}{/capture}
{/if}

<div class="data-table-wrapper row">
    {foreach from=$attributes item=attr}
        {if $attr.filter|default:false}
            {include file='_shared/entities/filter/filter.html.tpl'}
            {break}
        {/if}
    {/foreach}
    <div class="table-responsive">
        <table class="table table-striped table-hover data-table data-table-{$_controller}" data-offset-top="50" data-url-source="{$smarty.server.REQUEST_URI}" data-url-export="{link_to controller=$_controller action=export}" data-controller="{$_controller}" data-type="{$type|default:'xhr'}" data-default-filtering='{$filtering_default|default:false|json_encode}'>
            {include file='_shared/entities/list/_table.html.tpl' inline}
        </table>
    </div>
</div>

{if "{$_controller}/_list/footer.html.tpl"|template_exists}
    {include "{$_controller}/_list/footer.html.tpl"}
{else}
    {include '_shared/entities/list/footer.html.tpl' inline}
{/if}

{if {user_can controller=$_controller action=show}}
    {include '_shared/modals/default.html.tpl' type='preview'}
{/if}
