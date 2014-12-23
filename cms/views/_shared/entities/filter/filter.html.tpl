<div class="filtering">
    <nav class="navbar navbar-default no-border no-box-shadow no-border-radius accent-light">
        <div class="navbar-header text-center">
            <a class="collapsed font-size-bigger text-thin no-outline filtering-area-trigger" href="#filter-data" data-toggle="collapse" data-target=".navbar-filter-collapse" title="{$_labels.filtering.title|escape}">
                <i class="glyphicon glyphicon-search"></i> {$_labels.filtering.title}
            </a>
        </div>
        <div class="collapse navbar-collapse navbar-filter-collapse no-border">
            <form class="navbar-form navbar-left width-full text-center padding-vertical no-margin no-border" method="get" action="{$smarty.server.REQUEST_URI}" role="search">
                <ul class="filtering-container nav navbar-nav no-padding no-margin text-left">
                {foreach from=$attributes key=field item=attr}
                    {if $attr.filter|default:false}
                    <li class="filter-data-type-{$attr.type}">
                        <label for="filter-attribute-{$field}">{$attr.title}:</label>
                        {if "{$_controller}/_filter/{$field}.html.tpl"|template_exists}
                            {include file="{$_controller}/_filter/{$field}.html.tpl"}
                        {elseif "_shared/entities/types/{$attr.type}/filter.html.tpl"|template_exists}
                            {include file="_shared/entities/types/{$attr.type}/filter.html.tpl"}
                        {else}
                            Missing field type filter template: <code>{$_mode}views/_shared/entities/types/{$attr.type}/filter.html.tpl</code>
                        {/if}
                    </li>
                    {/if}
                {/foreach}
                    <li>
                         <label for="filtering-action">&nbsp;</label>
                         <button type="submit" id="filtering-action" class="btn btn-primary btn-sm filter-action-submit" title="{$_labels.filtering.submit|escape}"><i class="glyphicon glyphicon-play-circle"></i></button>
                         <button type="reset" class="btn btn-danger btn-sm filter-action-reset display-hidden" title="{$_labels.filtering.reset|escape}"><i class="glyphicon glyphicon-remove-circle"></i></button>
                    </li>
                </ul>
            </form>
        </div>
    </nav>
</div>