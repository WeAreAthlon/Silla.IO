<div>
  {if $attr.inline|default:false}
    <div class="datetimepicker-component" data-inline="true"{if $attr.min|default:false} data-min-date="{$attr.min|strtotime|date_format:'m/d/Y H:i'}"{/if}{if $attr.max|default:false} data-max-date="{$attr.max|strtotime|date_format:'m/d/Y H:i'}"{/if} data-side-by-side="{if $attr.aside|default:false}true{else}false{/if}" data-format="{#datetimepicker_format#}" data-default-date="{$attr.default|date_format:'Y-m-d H:i:s'}" data-stepping="{#time_minute_step#}"></div>
  {else}
    <div class="input-group date datetimepicker-component"{if $attr.min|default:false} data-min-date="{$attr.min|strtotime|date_format:'m/d/Y H:i'}"{/if}{if $attr.max|default:false} data-max-date="{$attr.max|strtotime|date_format:'m/d/Y H:i'}"{/if} data-format="{#datetimepicker_format#}" data-default-date="{$attr.default|date_format:'Y-m-d H:i:s'}" data-stepping="{#time_minute_step#}">
      <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
      <input type="text" class="form-control" id="{$attr.id}" value="{$attr.default|default:$smarty.now|date_format:#datetime#}"{$attr.disabled}/>
    </div>
  {/if}
  <input type="hidden" name="{$attr.name}" value="{$attr.default|default:$smarty.now|date_format:'%Y-%m-%d %H:%M'}:00">
</div>