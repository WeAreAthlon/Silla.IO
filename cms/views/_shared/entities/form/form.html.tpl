{if "{$_controller}/_form/header.html.tpl"|template_exists}
    {capture name='caption'}{include file="{$_controller}/_form/header.html.tpl"}{/capture}
{else}
    {capture name='caption'}{include file='_shared/entities/form/header.html.tpl'}{/capture}
{/if}
<div class="row">
    <div class="position-fixed-static" data-offset-top="50">
        <ul class="nav nav-tabs nav-justified form-sections-navigation-wrapper">
        {foreach from=$_labels.attributes key=section_key item=section name=form_sections_headings}
            <li{if $smarty.foreach.form_sections_headings.first} class="active"{/if}>
                <a href="#form-section-{$section_key}" data-toggle="tab" title="{$section.meta.title|escape}">
                   <span class="hidden-xs"><i class="glyphicon glyphicon-{$section.meta.icon|default:'cog'}"></i> {$section.meta.title}</span>
                   <span class="visible-xs text-center"><i class="glyphicon glyphicon-{$section.meta.icon|default:'cog'}"></i></span>
                </a>
            </li>
        {/foreach}
        </ul>
    </div>
</div>
<form action="{$smarty.server.REQUEST_URI}" method="post" role="form" class="data-form form-horizontal"{if {'Core\Utils::arraySearchRecursive'|call_user_func_array:[['file', 'photo'], $_labels.attributes]}} enctype="multipart/form-data"{/if} accept-charset="UTF-8">
    <div class="tab-content">
        {foreach from=$_labels.attributes key=section_key item=section name=form_sections_contents}
        <div class="tab-pane {if $smarty.foreach.form_sections_contents.first} in active{/if}" id="form-section-{$section_key}">
            <fieldset>
                <legend class="no-border no-margin-bottom font-size-normal">
                    <span class="row block">
                        <span class="ath-callout no-margin-top">
                            <span class="visible-xs"><strong class="font-size-bigger">{$section.meta.title}</strong></span>
                            <span class="text-thin">{$section.meta.desc}</span>
                        </span>
                    </span>
                </legend>
                {include file='_shared/entities/form/_fields.html.tpl' section=$section_key attributes=$section.fields serialize=$section.meta.serialize|default:false}
            </fieldset>
        </div>
        {/foreach}
    </div>

    <input type="hidden" name="_token" value="{$_request->token()}" />

    <div class="navbar navbar-default navbar-fixed-bottom" role="navigation">
        <div class="navbar-header width-full padding-horizontal">
        {if "{$_controller}/_form/actions.html.tpl"|template_exists}
            {include file="{$_controller}/_form/actions.html.tpl"}
        {else}
            {include file='_shared/entities/form/actions.html.tpl'}
        {/if}
        </div>
        <div class="collapse navbar-collapse" id="form-actions-collapse"></div>
    </div>
</form>

{if "{$_controller}/_form/footer.html.tpl"|template_exists}
    {include file="{$_controller}/_form/footer.html.tpl"}
{else}
    {include file='_shared/entities/form/footer.html.tpl'}
{/if}
