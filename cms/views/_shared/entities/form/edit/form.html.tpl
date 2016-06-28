{if "{$_controller}/_form/header.html.tpl"|template_exists}
    {capture name='caption'}{include "{$_controller}/_form/header.html.tpl"}{/capture}
{else}
    {capture name='caption'}{include '_shared/entities/form/header.html.tpl' inline}{/capture}
{/if}
<div class="row">
    <div class="position-fixed-static" data-offset-top="50">
        <ul class="nav nav-tabs nav-justified form-sections-navigation-wrapper">
        {foreach from=$sections key=section_key item=section name=form_sections_headings}
            <li{if $smarty.foreach.form_sections_headings.first} class="active"{/if}>
                <a href="#form-section-{$section_key}" data-toggle="tab" title="{$section.meta.title|escape}" data-section="{$section_key}">
                   <span class="hidden-xs"><i class="glyphicon glyphicon-{$section.meta.icon|default:'cog'}"></i> {$section.meta.title}</span>
                   <span class="visible-xs text-center"><i class="glyphicon glyphicon-{$section.meta.icon|default:'cog'}"></i></span>
                </a>
            </li>
        {/foreach}
        </ul>
    </div>
</div>
{form action={url resource=$resource action='update'} method="patch" role='form' class='data-form form-horizontal' accept-charset='UTF-8' upload={'Core\Utils::arraySearchRecursive'|call_user_func_array:[['file', 'photo'], $_labels.attributes]}}
    <div class="tab-content">
    {foreach from=$sections key=section_key item=section name=form_sections_contents}
        <div class="tab-pane {if $smarty.foreach.form_sections_contents.first} in active{/if}" id="form-section-{$section_key}">
            <fieldset data-section="{$section_key}">
                <legend class="no-border no-margin-bottom font-size-normal">
                    <span class="row block">
                        <span class="ath-callout no-margin-top">
                            <span class="visible-xs"><strong class="font-size-bigger">{$section.meta.title}</strong></span>
                            <span class="text-thin">{$section.meta.desc}</span>
                        </span>
                    </span>
                </legend>
                <div class="form-fields-wrapper">
                    {include '_shared/entities/form/_fields.html.tpl' section=$section_key attributes=$section.fields|default:[] serialize=$section.meta.serialize|default:false}
                </div>
            </fieldset>
        </div>
    {/foreach}
    </div>
    <input type="hidden" name="_token" value="{$_request->token()}" />
    <div class="navbar navbar-default navbar-fixed-bottom" role="navigation">
        <div class="navbar-header width-full padding-horizontal">
            <div class="col-sm-10 col-sm-offset-2">
            {if "{$_controller}/_form/actions.html.tpl"|template_exists}
                {include "{$_controller}/_form/actions.html.tpl"}
            {else}
                {include '_shared/entities/form/actions.html.tpl' inline}
            {/if}
            </div>
        </div>
        <div class="collapse navbar-collapse" id="form-actions-collapse"></div>
    </div>
{/form}

{if "{$_controller}/_form/footer.html.tpl"|template_exists}
    {include "{$_controller}/_form/footer.html.tpl"}
{else}
    {include '_shared/entities/form/footer.html.tpl' inline}
{/if}
