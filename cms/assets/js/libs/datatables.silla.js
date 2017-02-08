/**
 * DataTable component.
 *
 * @package    Silla.IO
 * @subpackage Core
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

/**
 * Init function.
 *
 * @param table
 * @constructor
 */
var DataTable = function (table) {
    'use strict';
    var self = this;

    self.table = table;

    /*
     * Unique identifier of the current user session.
     * Useful when more than one user is using the same browser(sessionStorage)
     */
    self.session = Silla.token;

    self.preferences = window.sessionStorage;

    var preferences = {};

    if (self.preferences.getItem(self.session + 'sorting')) {
        preferences['sorting'] = JSON.parse(self.preferences.getItem(self.session + 'sorting'))[table.data('controller')] || null;
    }

    if (self.preferences.getItem(self.session + 'pagination')) {
        preferences['pagination'] = JSON.parse(self.preferences.getItem(self.session + 'pagination'))[table.data('controller')] || null;
    }

    if (self.preferences.getItem(self.session + 'filtering')) {
        preferences['filtering'] = JSON.parse(self.preferences.getItem(self.session + 'filtering'))[table.data('controller')] || null;
    }

    self.params = {
        sorting: preferences['sorting'] || {field: null, order: null},
        pagination: preferences['pagination'] || {limit: null, page: null},
        filtering: preferences['filtering'] || table.data('defaultFiltering') || {}
    };

    self._attach();
};

DataTable.prototype = new Obj();

DataTable.prototype.fixCaption = function () {
    'use strict';
    var i;
    var self = this;
    var thead = [];
    var fixed = false;
    var offsetTop = self.table.data('offsetTop') ? self.table.data('offsetTop') : 0;

    $('tbody tr:eq(1) td', self.table).each(function (i, v) {
        thead.push($(v).width());
    });

    for (i = 0; i < thead.length; i++) {
        $('thead th:eq(' + i + ')', self.table).width(thead[i]);
    }

    $(window).on('scroll', function () {
        var windowTop = $(window).scrollTop();

        if (fixed) {
            windowTop += $('thead', self.table).height();
        }
        
        if (windowTop > (self.table.offset().top - offsetTop)) {
            $('thead', self.table).addClass('fixed').css({top: offsetTop + 'px'});
            fixed = true;
        }
        else {
            $('thead', self.table).removeClass('fixed').css({top: 'auto'});
            fixed = false;
        }
    });
};

DataTable.prototype.enable = function (functionality) {
    'use strict';
    var self = this;

    switch (functionality) {
        case 'sorting':
            /* Apply preferences */
            if (self.params.sorting.field && self.params.sorting.order) {
                var handler = $('th[data-field="' + self.params.sorting.field + '"] a');
                handler.addClass(self.params.sorting.order);
                $('.sort-btn', handler).removeClass('glyphicon-sort').addClass('accent-cta glyphicon-chevron-' + (self.params.sorting.order === 'asc' ? 'up' : 'down'));
            }

            $('.sort', self.table).on('click', function (e) {
                e.preventDefault();
                var handler = $(this);
                var current_order = handler.hasClass('desc') ? 'desc' : 'asc';
                var preferences = {};

                self.params.sorting.order = handler.hasClass('asc') ? 'desc' : 'asc';
                self.params.sorting.field = handler.parent().data('field');

                preferences[self.table.data('controller')] = self.params.sorting;
                self.preferences.setItem(self.session + 'sorting', JSON.stringify(preferences));

                $('.sort, .sort-btn', self.table).removeClass('asc desc accent-cta glyphicon-chevron-down glyphicon-chevron-up');

                handler.removeClass(current_order).addClass(self.params.sorting.order);
                $('.sort-btn', handler).removeClass('glyphicon-sort').addClass('accent-cta glyphicon-chevron-' + (self.params.sorting.order === 'asc' ? 'up' : 'down'));

                self._populate();
            });
            break;
        case 'pagination':
            $('.pagination a', self.table).on('click', function (e) {
                e.preventDefault();
                var handler = $(this);
                var handler_wrapper = handler.parent();
                var preferences = {};
                self.params.pagination.page = handler.data('page');

                preferences[self.table.data('controller')] = self.params.pagination;
                self.preferences.setItem(self.session + 'pagination', JSON.stringify(preferences));

                if (!handler_wrapper.hasClass('disabled')) {
                    self._populate();
                }

                $('.pagination li', self.table).removeClass('disabled active');

                if (handler.data('page') === handler_wrapper.parent().parent().data('pageFirst')) {
                    handler_wrapper.addClass('disabled');
                    $('.pagination li.first', self.table).addClass('disabled');
                } else {
                    handler_wrapper.removeClass('disabled');
                    $('.pagination li.first', self.table).removeClass('disabled');
                }

                if (handler.data('page') === handler_wrapper.parent().parent().data('pageLast')) {
                    handler_wrapper.addClass('disabled');
                    $('.pagination li.last', self.table).addClass('disabled');
                } else {
                    handler_wrapper.removeClass('disabled');
                    $('.pagination li.last', self.table).removeClass('disabled');
                }

                handler_wrapper.addClass('disabled active');
            });
            break;
        case 'filtering':
            /* Apply preferences */
            var filter = self.table.parent().parent();

            if (!CMS.utils.isObjectEmpty(self.params.filtering)) {
                $('.filtering-area-trigger', filter).trigger('click');
                $('.filter-action-reset', filter).fadeIn('fast');

                for (var field in self.params.filtering) {
                    if (self.params.filtering.hasOwnProperty(field)) {
                        var filtering_field = $('.filtering *[data-attribute="' + field + '"]', filter);
                        if (filtering_field.closest('li').hasClass('filter-data-type-select') || filtering_field.closest('li').hasClass('filter-data-type-multiselect') || filtering_field.closest('li').hasClass('filter-data-type-checkbox') || filtering_field.closest('li').hasClass('filter-data-type-radio')) {
                            filtering_field.val(self.params.filtering[field]).trigger('chosen:updated');
                        } else if (filtering_field.parent().hasClass('filter-data-type-datetime')) {
                            var default_field_value = self.params.filtering[field];

                            if (default_field_value.start && default_field_value.end) {
                                $('span', filtering_field).html(moment(default_field_value.start).format(filtering_field.data('dateFormat').toUpperCase()) + ' - ' + moment(default_field_value.end).format(filtering_field.data('dateFormat').toUpperCase()));
                            }
                        } else {
                            filtering_field.val(self.params.filtering[field]);
                        }
                    }
                }
            }

            $('.filtering .daterange', filter).each(function (idx, element) {
                CMS.utils.attachDateRange($(element), 'left', 'down');
            });

            $('.filtering form', filter).on('submit', function (e) {
                e.preventDefault();

                var form_filtering_fields = $(this).serializeObject();
                var preferences = {};

                /**
                 * @TODO remove this code after updating to version 3.x of serializeObject()
                 * @see    https://github.com/macek/jquery-serialize-object/issues/38
                 */
                for (var param in self.params.filtering) {
                    if (param != 'created_on' && !form_filtering_fields.hasOwnProperty(param)) {
                        self.params.filtering[param] = [];
                    }
                }

                self.params.filtering = $.extend({}, self.params.filtering, form_filtering_fields.filtering);
                self.params.pagination.page = 1;

                preferences[self.table.data('controller')] = self.params.filtering;
                self.preferences.setItem(self.session + 'filtering', JSON.stringify(preferences));

                self._populate();

                $('.filter-action-reset', filter).fadeIn('fast');
            });

            $('.filter-action-reset', filter).on('click', function () {
                $('form input', filter).val('');
                $('form select:not(.basic)', filter).val(false).trigger('chosen:updated');
                $('.filtering form', filter).trigger('submit');

                $(this).fadeOut('fast');
            });
            break;
        case 'tools':
            var default_values = null;

            /* Apply preferences */
            if (typeof self.params.filtering.created_on != 'undefined' && self.params.filtering.created_on.start && self.params.filtering.created_on.end) {
                var tools = $('.datatable-tools .daterange', self.table);

                default_values = self.params.filtering.created_on;
                $('span', tools).html(moment(default_values.start).format(tools.data('dateFormat').toUpperCase()) + ' - ' + moment(default_values.end).format(tools.data('dateFormat').toUpperCase()));
            }

            $('.daterange', self.table).each(function (idx, element) {
                CMS.utils.attachDateRange($(element), 'left', 'up', function (start, end) {
                    if (start.toDate().getFullYear() != 1970 && end.toDate().getFullYear() != 2100) {
                        self.params.filtering[$(element).data('attribute')] = {
                            start: CMS.utils.dateToYMD(start.toDate()),
                            end: CMS.utils.dateToYMD(end.toDate())
                        };
                    } else {
                        self.params.filtering[$(element).data('attribute')] = {start: null, end: null};
                    }

                    self.params.filtering = $.extend({}, self.params.filtering);

                    var preferences = {};
                    preferences[self.table.data('controller')] = self.params.filtering;
                    self.preferences.setItem(self.session + 'filtering', JSON.stringify(preferences));

                    self._populate();
                }, default_values);
            });

            /* Apply preferences */
            if (self.params.pagination.limit) {
                $('.pagination-per-page-selector select', self.table).val(self.params.pagination.limit).trigger('chosen:updated');
            }

            $('.pagination-per-page-selector select', self.table).on('change', function () {
                self.params.pagination.limit = $(this).val();
                self.params.pagination.page = 1;

                var preferences = {};
                preferences[self.table.data('controller')] = self.params.pagination;
                self.preferences.setItem(self.session + 'pagination', JSON.stringify(preferences));

                self._populate();
            });

            $('.data-export ul a', self.table).on('click', function (e) {
                e.preventDefault();

                self._export($(this).data('exportType'));
            });

            break;
        case 'preview':
            $('tbody tr').on('click', function (e) {
                if ($(e.target).parents('.column-actions').length == 0) {
                    e.preventDefault();
                    e.stopPropagation();
                    $('.btn-group a:first', $(this)).trigger('click');
                }
            });

            break;
    }
};

DataTable.prototype._attach = function () {
    'use strict';
    var self = this;

    /* Default Tools options */
    self.params.pagination.limit = self.params.pagination.limit || $('.pagination-per-page-selector select', self.table).val();

    $.get(self.table.data('urlSource'), {view: 'table', query: self.params}, function (response) {
        self.table.html(response.data).fadeIn('slow');

        if (typeof $.fn.chosen === 'function') {
            $('select', self.table).chosen();
        }

        self.fire('initialize');
        self.fire('populate');
    }, 'json');
};

DataTable.prototype._populate = function () {
    'use strict';
    var self = this;

    $.get(self.table.data('urlSource'), {
        view: 'tbody',
        query: self.params,
        page: self.params.pagination.page
    }, function (response) {
        $('tbody', self.table).html(response.data);
        $('.pagination-wrapper', self.table).html(response.pagination);

        self.enable('pagination');
        self.enable('preview');
        self.fire('populate');
    }, 'json');
};

DataTable.prototype._export = function (type) {
    'use strict';
    var self = this;

    self.params['type'] = type;

    window.open(self.table.data('urlExport') + '?' + $.param(self.params));
};
