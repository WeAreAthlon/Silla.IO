{if $flash.message|default:false}
<div class="alert fade in alert-{$flash.layout}{if $flash.layout eq 'danger'} save-errors{/if}">
    <button type="button" id="flash-message-close" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <p class="no-margin-bottom font-size-bigger">{$flash.message}</p>
    {if $flash.additional|default:false}
    <dl class="clearfix no-padding-left">
{if $flash.layout eq 'danger'}
    {foreach from=$sections key=section_key item=section}
        {if array_intersect_key($flash.additional, $section.fields)}
            <dt class="block clear font-size-bigger text-thin">
                <i class="glyphicon glyphicon-remove-circle"></i> {$section.meta.title}
            </dt>
        {/if}
        {foreach from=$flash.additional key=item item=reason}
            {if $section.fields.$item|default:false}
                <dt class="{$item} field clear-left padding-left" rel="{$section_key}-{$item}" data-section="{$section_key}">
                    <span class="padding-left">{$section.fields.$item.title}:</span>
                </dt>
                <dd class="pull-left">{$_labels.errors.$reason|default:false}</dd>
            {/if}
        {/foreach}
    {/foreach}
{else}
    {foreach from=$flash.additional key=item item=reason}
        <dt class="{$item} no-margin">{$item}</dt>
        <dd>{$reason}</dd>
    {/foreach}
{/if}
    </dl>
    {/if}
    {if $flash.layout neq 'danger'}
        <script>setTimeout(function() { document.getElementById('flash-message-close').click(); }, 3500);</script>
    {/if}
</div>
{/if}