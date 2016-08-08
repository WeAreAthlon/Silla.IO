{$timezones = Core\Helpers\DateTime::getTimezonesList()}
<div class="form-input-wrapper-sm">
    <select name="filtering[{$field}]" id="filter-attribute-{$field}" data-attribute="{$field}" class="form-control input-sm" title="{$_labels.general.select_option|escape}" data-placeholder="{$_labels.general.select_option|escape}">
        <option></option>
        {foreach from=$timezones key=zone item=locations}
            <optgroup label="{$zone|escape}">
                {foreach from=$locations item=location}
                    <option value="{$zone|escape}/{$location.title|escape}">[GMT {$location.offset}] {$location.title|replace:['_', '/']:[' ', ' / ']}</option>
                {/foreach}
            </optgroup>
        {/foreach}
    </select>
</div>
