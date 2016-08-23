<ul class="pagination no-margin" data-range="{$pagination.range}" data-page-first="{$pagination.first}" data-page-last="{$pagination.last}">
{if $pagination.boundaries}
    <li class="first{if {$pagination.current} eq $pagination.first} disabled{/if}">
        <a href="{link_to action=$_action page=$pagination.first}" data-page="{$pagination.first}">{$_labels.pagination.first}</a>
    </li>
{/if}
    <li class="first{if $pagination.current eq $pagination.first} disabled{/if}">
        <a href="{link_to action=$_action page=$pagination.prev}" data-page="{$pagination.prev}">{$_labels.pagination.prev}</a>
    </li>
{foreach from=$pagination.pages item=page}
    <li{if ({$pagination.current} eq $page) or not {$page}} class="disabled"{/if}>
        <a href="{link_to action=$_action page=$page}" data-page="{$page}">{$page}</a>
    </li>
{/foreach}
    <li class="last{if ({$pagination.current} eq {$pagination.last}) or not {$page}} disabled{/if}">
        <a href="{link_to action=$_action page=$pagination.next}" data-page="{$pagination.next}">{$_labels.pagination.next}</a>
    </li>
{if $pagination.boundaries}
    <li class="last{if ({$pagination.current} eq {$pagination.last}) or not {$page}} disabled{/if}">
        <a href="{link_to action=$_action page=$pagination.last}" data-page="{$pagination.last}">{$_labels.pagination.last}</a>
    </li>
{/if}
</ul>
