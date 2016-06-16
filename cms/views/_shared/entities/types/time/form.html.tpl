<div>
    <div class="input-group date timepicker-component" data-default-date="{$attr.default|date_format:#time#}" data-stepping="{#time_minute_step#}" data-format="{#timepicker_format#}">
        <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
        <input type="text" class="form-control" name="{$attr.name}" id="{$attr.id}" value="{$attr.default|default:$smarty.now|date_format:#time#}"{$attr.disabled}/>
    </div>
</div>