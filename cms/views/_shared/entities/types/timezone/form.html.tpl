{$timezone = $attr.default|default:'Europe/London'}
{$timezones = 'Core\Helpers\DateTime::getTimezonesList'|call_user_func}
<select name="{$attr.name}" class="form-control" id="{$attr.id}" data-placeholder="{$_labels.general.select|escape}...">
    <option></option>
    {foreach from=$timezones key=zone item=locations}
        <optgroup label="{$zone|escape}">
            {foreach from=$locations item=location}
                <option value="{$zone|escape}/{$location.title|escape}"{if $timezone eq "{$zone}/{$location.title}"} selected="selected"{/if}>[GMT {$location.offset}] {$location.title|replace:['_', '/']:[' ', ' / ']}</option>
            {/foreach}
        </optgroup>
    {/foreach}
</select>
