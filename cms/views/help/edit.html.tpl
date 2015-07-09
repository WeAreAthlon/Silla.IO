<form action="{$smarty.server.REQUEST_URI}" method="post" role="form" class="data-form form-horizontal"
      data-preview-action="{link_to action='preview'}" accept-charset="UTF-8">
    <div class="row position-fixed-static">
        <ul class="nav nav-tabs nav-justified form-sections-navigation-wrapper" role="tablist">
            {foreach from=$_labels.attributes key=section_key item=section name=form_sections_headings}
                <li role="presentation" class="help-{$section_key}{if $smarty.foreach.form_sections_headings.first} active{/if}">
                    <a href="#help-{$section_key}" data-toggle="tab" title="{$section.meta.title|escape}" role="tab">
                        <span class="hidden-xs"><i class="glyphicon glyphicon-{$section.meta.icon|default:'cog'}"></i> {$section.meta.title}</span>
                        <span class="visible-xs text-center"><i class="glyphicon glyphicon-{$section.meta.icon|default:'cog'}"></i></span>
                    </a>
                </li>
            {/foreach}
        </ul>
    </div>
    {$attr          = $_labels.attributes.write.fields.content}
    {$attr.default  = $resource->content}
    {$attr.id       = 'content'}
    {$attr.name     = 'content'}
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade in active" id="help-write">
            <legend class="no-border no-margin-bottom font-size-normal">
                <span class="row block">
                    <span class="ath-callout no-margin-top">
                        <span class="text-thin">{$_labels.attributes.write.meta.desc}</span>
                    </span>
                </span>
            </legend>
            <p class="muted text-thin help-block text-right">{$_labels.attributes.write.fields.content.desc|default:''}</p>
            {include file='_shared/entities/types/textarea/form.html.tpl' attr=$attr}
        </div>
        <div role="tabpanel" class="tab-pane fade action-preview" id="help-preview">
            <legend class="no-border no-margin-bottom font-size-normal">
                <span class="row block">
                    <span class="ath-callout no-margin-top">
                        <span class="text-thin">{$_labels.attributes.preview.meta.desc}</span>
                    </span>
                </span>
            </legend>
            <div class="preview-content"></div>
        </div>
    </div>
    <input type="hidden" name="_token" value="{$_request->token()}" />
    <div class="navbar navbar-default navbar-fixed-bottom" role="navigation">
        <div class="navbar-header width-full padding-horizontal">
            <div class="btn-group pull-right" role="toolbar">
                <button class="btn btn-success navbar-btn text-thin" type="submit">{$_labels.buttons.save}</button>
            </div>
        </div>
        <div class="collapse navbar-collapse" id="form-actions-collapse"></div>
    </div>
</form>