{foreach from=$resource->permissions key=section item=permissions}
  <dl class="dl-horizontal">
    <dt>{$_labels.modules.$section.title}:</dt>
    <dd>
      {foreach from=$permissions item=permission}
        <span class="label label-success">{$_labels.modules.$section.$permission|default:$_labels.sections.$permission|default:''}</span>
        {foreachelse}
        <span class="label label-danger">{$_labels.general.none}</span>
      {/foreach}
    </dd>
  </dl>
{/foreach}