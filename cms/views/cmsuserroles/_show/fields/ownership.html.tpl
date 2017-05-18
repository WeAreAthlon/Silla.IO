{foreach from=$scope.ownership|default:[] item=module}
  {$ownership = array_key_exists($module.model, $resource->ownership)}
  <dl class="dl-horizontal">
    <dt>{$_labels.modules.{$module.name}.title}:</dt>
    <dd>
      <span class="label label-{if $ownership}danger{else}info{/if}">{$_labels.state.$ownership|default:0}</span>
    </dd>
  </dl>
{/foreach}
