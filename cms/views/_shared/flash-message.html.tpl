{if $flash.message|default:false}
<div class="alert fade in alert-{$flash.layout}{if $flash.layout eq 'danger'} save-errors{/if}">
    <button type="button" id="flash-message-close" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <p class="no-margin-bottom font-size-bigger">{$flash.message}</p>
    {if isset($flash.additional)}
    <dl class="clearfix no-padding-left">
    {foreach from=$flash.additional key=item item=reason}
        {if $flash.layout eq 'danger'}
            {foreach from=$_labels.attributes key=section_key item=section}
                {if isset($section.fields.{$item}.title)}
                {if not {$sections.$section_key|default:0}}<dt class="block clear font-size-bigger text-thin"><i class="glyphicon glyphicon-remove-circle"></i> {$section.meta.title}</dt>{append var=sections value=1 index=$section_key}{/if}
                <dt class="{$item} field clear-left padding-left" rel="{$section_key}-{$item}" data-section="{$section_key}">
                    <span class="padding-left">{$section.fields.{$item}.title}:</span>
                </dt>
                <dd class="pull-left">{$_labels.errors.{$reason}}</dd>
                {/if}
            {/foreach}
        {else}
            <dt class="{$item} no-margin">{$item}</dt>
            <dd>{$reason}</dd>
        {/if}
    {/foreach}
    </dl>
    {/if}
    {if $flash.layout neq 'danger'}
    <script>setTimeout(function() { document.getElementById('flash-message-close').click(); }, 3500);</script>
    {/if}
</div>
{/if}