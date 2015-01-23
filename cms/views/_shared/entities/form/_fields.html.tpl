{foreach from=$attributes key=field item=attr}
    {if not $attr.readonly|default:false}

        {if $serialize|default:false}
            {$attr.name    = "{$section}[{$field}]"}
            {$attr.section = $resource->$section}
            {$attr.default = $attr.section[$field]|default:''}
        {else}
            {$attr.name    = $field}
            {$attr.default = $resource->{$field}}
        {/if}

        {$attr.disabled = $attr.disabled|default:false}
        {$attr.id = "{$section}-{$field}"}

        {if $attr.disabled}
            {$attr.disabled = ' disabled'}
        {/if}

        {$has_custom_template = false}
        {if "{$_controller}/_form/fields/{$section}/{$field}.html.tpl"|template_exists}
            {$has_custom_template = true}
            {include file="{$_paths.views.templates}{$_controller}/_form/fields/{$section}/{$field}.html.tpl" assign=field_template}
        {/if}

        {if not $has_custom_template || $field_template|default:false}
        <div class="form-group">
            <label class="col-lg-2 control-label" for="{$attr.id|escape}">{$attr.title}:</label>
            <div class="col-lg-8">
            {if $has_custom_template}
                {$field_template}
            {else}
                {if "_shared/entities/types/{$attr.type}/form.html.tpl"|template_exists}
                    {include file="_shared/entities/types/{$attr.type}/form.html.tpl"}
                {else}
                    <div class="well well-small">
                        Missing field type template: <code>{$_mode}views/_shared/entities/types/{$attr.type}/form.html.tpl</code>
                    </div>
                {/if}
            {/if}
                <p class="muted text-thin help-block">{$attr.desc|default:''}</p>
            </div>
        </div>
        {/if}
    {/if}
{/foreach}
