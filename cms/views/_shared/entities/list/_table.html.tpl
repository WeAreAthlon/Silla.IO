{if not $attributes|default:false}
    {$attributes = []}
    {foreach from=$_labels.attributes item=section}
        {$attributes = array_merge($attributes, $section.fields)}
    {/foreach}
{/if}
{assign var=fields_to_display value=$fields_to_display|default:[]}
<thead>
    <tr>
    {foreach from=$attributes key=field item=attr}
        {$attr.list = $attr.list|default:true}
        {$attr.sort = $attr.sort|default:true}
        {if $attr.list !== false}
        {append var=fields_to_display index=$field value=$attr.type}
        <th data-field="{$field}" class="column-caption column-type-heading-{$attr.type} accent">
           {if $attr.sort !== false}
           <a href="{link_to action=index page={$_get.page|default:1} sort=$field order="{if $_get.order|default:'' neq 'asc'}asc{else}desc{/if}"}" class="row sort{if $_get.sort|default:'' eq $field} {$_get.order|default:'asc'}{/if}">
               <span class="col-lg-9 col-md-9 no-padding-right"><i class="glyphicon glyphicon-{$attr.icon|default:'th'}"></i> {$attr.title}</span>
               <span class="col-lg-3 col-md-3 text-right"><i class="sort-btn text-muted glyphicon {if $_get.sort|default:false and $_get.sort eq $field}accent-cta glyphicon-chevron-{if $_get.order eq 'asc'}up{else}down{/if}{else}glyphicon-sort{/if}"></i></span>
           </a>
           {else}
           <i class="glyphicon glyphicon-{$attr.icon|default:'th'}"></i> {$attr.title}
           {/if}
        </th>
        {/if}
    {/foreach}
    {if {user_can controller=$_controller action=show} or {user_can controller=$_controller action=edit} or {user_can controller=$_controller action=delete}}
        <th class="column-caption column-type-heading-actions accent"><i class="glyphicon glyphicon-cog"></i> {$_labels.general.actions}</th>
    {/if}
    </tr>
</thead>
<tfoot>
    <tr>
        <td colspan="{1 + $fields_to_display|@count}" class="table-actions">
            <div class="navbar navbar-default navbar-fixed-bottom navbar-listing-tools" role="navigation">
                <div class="navbar-header width-full padding-full">
                    <div class="row">
                        <div class="col-lg-4 col-lg-offset-4 col-md-5 col-md-offset-1 col-sm-5 col-sm-offset-1 col-xs-12">
                            <div class="pagination-wrapper text-center">
                                {include file='_shared/entities/list/_pagination.html.tpl'}
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 col-md-6 col-xs-12">
                            <div class="datatable-tools pull-right bootstrap-select-wrapper">
                                <label class="muted text-thin">{$_labels.pagination.per_page}: </label>
                                <div class="pagination-per-page-selector form-input-wrapper-sm dropup">
                                   {html_options name='pagination[limit]' options=$_labels.pagination.limits selected=$_get.pagination.limit|default:current($_labels.pagination.limits)}
                                </div>
                                <div class="daterange btn btn-default btn-sm dropup" data-date-format="{#datepicker_format#}" data-attribute="created_on" data-attribute-title="{$_labels.daterange.ranges.all|escape}" data-range-labels='{$_labels.daterange.ranges|json_encode}' data-locale-labels='{$_labels.daterange.locales|json_encode}'>
                                    <i class="glyphicon glyphicon-align-justify glyphicon glyphicon-large text-danger"></i>
                                    <span class="text-danger">{$_labels.daterange.ranges.all}</span>
                                    <b class="caret"></b>
                                    <input type="hidden" name="filtering[created_on][start]" class="daterange-start" value="{$_get.filtering.created_on.start|default:''}">
                                    <input type="hidden" name="filtering[created_on][end]" class="daterange-end" value="{$_get.filtering.created_on.end|default:''}">
                                </div>
                                {if {user_can controller=$_controller action=export}}
                                <div class="btn-group data-export dropup">
                                    <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="javascript:;">
                                        <i class="glyphicon glyphicon-download-alt"></i> {$_labels.export.title}
                                        <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu pull-right">
                                    {foreach from=$_labels.export.formats key=type item=title}
                                        <li><a href="{link_to action=export type=$type filtering=$_get.filtering|default:'' sort=$_get.sort|default:'' order=$_get.order|default:''}" data-export-type="{$type}"><i class="glyphicon glyphicon-share"></i> {$title}</a></li>
                                    {/foreach}
                                    </ul>
                                </div>
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="collapse navbar-collapse" id="form-actions-collapse"></div>
            </div>
        </td>
    </tr>
</tfoot>
<tbody>
    {include file='_shared/entities/list/_tbody.html.tpl'}
</tbody>
