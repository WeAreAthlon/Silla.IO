<div>
    <div class="input-group date datetimepicker-component" data-format="{#datetimepicker_format#}" data-default-date="{$attr.default|date_format:'U'}" data-stepping="{#time_minute_step#}">
        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
        <input type="text" class="form-control" id="{$attr.id}" value="{$attr.default|default:$smarty.now|date_format:#datetime#}"{$attr.disabled}/>
    </div>
    <input type="hidden" name="{$attr.name}" value="{$attr.default|default:$smarty.now|date_format:'%Y-%m-%d %H:%M'}:00">
</div>