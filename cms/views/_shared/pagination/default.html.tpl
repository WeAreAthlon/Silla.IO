<ul class="pagination no-margin" data-range="{$pagination.range}" data-page-first="{$pagination.first}" data-page-last="{$pagination.last}">
    {if $pagination.boundaries}<li class="first{if {$pagination.current} eq $pagination.first} disabled{/if}"><a href="{link_to action=$_action page=$pagination.first}" data-page="{$pagination.first}" class="text-thin">{$_labels.pagination.first}</a></li>{/if}
    <li class="first{if $pagination.current eq $pagination.first} disabled{/if}"><a href="{link_to action=$_action page=$pagination.prev}" data-page="{$pagination.prev}" class="text-thin">{$_labels.pagination.prev}</a></li>
    {foreach from=$pagination.pages item=page}
    <li{if {$pagination.current} eq $page} class="disabled"{/if}><a href="{link_to action=$_action page=$page}" data-page="{$page}" class="text-thin">{$page}</a></li>
    {/foreach}
    <li class="last{if $pagination.current eq $pagination.last} disabled{/if}"><a href="{link_to action=$_action page=$pagination.next}" data-page="{$pagination.next}" class="text-thin">{$_labels.pagination.next}</a></li>
    {if $pagination.boundaries}<li class="last{if {$pagination.current} eq $pagination.last} disabled{/if}"><a href="{link_to action=$_action page=$pagination.last}" data-page="{$pagination.last}" class="text-thin">{$_labels.pagination.last}</a></li>{/if}
</ul>
