{if $scope.ownership}
  <div class="checkbox">
    {foreach from=$scope.ownership item=module}
      <label class="checkbox inline" for="{$attr.name}_checkbox_{$module.name}">
        <input type="hidden" name="{$attr.name}[{$module.model}]" value="0"{$attr.disabled}>
        <input type="checkbox" id="{$attr.name}_checkbox_{$module.name}" name="{$attr.name}[{$module.model}]"{if $attr.default.{$module.model}|default:false} checked="checked"{/if} value="1"{$attr.disabled}>
        {$_labels.modules.{$module.name}.title}
      </label>
    {/foreach}
  </div>
{else}
  <em class="text-muted">{$_labels.general.no_supported_entities}</em>
{/if}
