<div>
{if $attr.inline|default:false}
    <div class="datepicker-component" data-inline="true"{if $attr.min|default:false} data-min-date="{$attr.min|strtotime|date_format:'m/d/Y H:i'}"{/if}{if $attr.max|default:false} data-max-date="{$attr.max|strtotime|date_format:'m/d/Y'}"{/if} data-format="{#datepicker_format#}" data-default-date="{$attr.default|date_format:'Y-m-d'}"></div>
{else}
    <div class="input-group date datepicker-component"{if $attr.min|default:false} data-min-date="{$attr.min|strtotime|date_format:'m/d/Y'}"{/if}{if $attr.max|default:false} data-max-date="{$attr.max|strtotime|date_format:'m/d/Y'}"{/if} data-format="{#datepicker_format#}" data-default-date="{$attr.default|date_format:'Y-m-d'}">
        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
        <input type="text" class="form-control" id="{$attr.id}" value="{$attr.default|default:$smarty.now|date_format:#date#}"{$attr.disabled}/>
    </div>
{/if}
    <input type="hidden" name="{$attr.name}" value="{$attr.default|default:$smarty.now|date_format:'%Y-%m-%d'}">
</div>
