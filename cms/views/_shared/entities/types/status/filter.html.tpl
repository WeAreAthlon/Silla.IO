<div class="form-input-wrapper-sm">
    <select name="filtering[{$field}]" id="filter-attribute-{$field}" data-attribute="{$field}" class="form-control input-sm" title="{$_labels.general.select_option|escape}" data-placeholder="{$_labels.general.select_option|escape}">
        <option value=""> </option>
        {html_options options=$_labels.state selected=$_get.filtering.$field|default:''}
    </select>
</div>
