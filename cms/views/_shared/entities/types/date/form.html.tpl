<div>
    <div class="input-group date datepicker-component" data-default-date="{$attr.default|date_format:#date#}" data-pickTime="false">
        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
        <input type="text" class="form-control" id="{$attr.id}" value="{$attr.default|default:$smarty.now|date_format:#date#}" data-format="{#datepicker_format#}" data-date-ymd="{$attr.default|date_format:'%Y-%m-%d'}" />
    </div>
    <input type="hidden" name="{$attr.name}" value="{$attr.default|default:$smarty.now|date_format:'%Y-%m-%d'}">
</div>
