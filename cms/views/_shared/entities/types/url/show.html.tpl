{if $attr.default}
<a href="{$attr.default}" target="_blank"><span class="glyphicon glyphicon-globe"></span> {$attr.default}</a>
{else}
<strong><em>{$_labels.general.none}</em></strong>
{/if}
