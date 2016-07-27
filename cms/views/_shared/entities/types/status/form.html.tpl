<select id="{$attr.id}" name="{$attr.name}" class="form-control" data-placeholder="{$_labels.general.select|escape}..."{$attr.disabled}>
    {html_options options=$_labels.state selected=$attr.default|default:$attr.default_value|default:''}
</select>