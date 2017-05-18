<div class="daterange btn btn-outline btn-default btn-sm col-lg-12" data-attribute="{$field}" data-attribute-title="{$_labels.general.select_option|escape}" data-date-format="{#datepicker_format#}" data-range-labels='{$_labels.daterange.ranges|json_encode}' data-locale-labels='{$_labels.daterange.locales|json_encode}'>
  <div class="text-left">
    <i class="glyphicon glyphicon-time"></i>
    <span>{$_labels.general.select_option}</span>
    <b class="caret"></b>
  </div>
  <input type="hidden" name="filtering[{$field}][start]" class="daterange-start" value="{$_get.filtering.$field.start|default:''}">
  <input type="hidden" name="filtering[{$field}][end]" class="daterange-end" value="{$_get.filtering.$field.end|default:''}">
</div>