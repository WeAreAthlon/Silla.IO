/**
 * CMS JS.
 *
 * @package    Silla.IO
 * @subpackage Core
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

var CMS = {
    attach: {
        tooltips: function () {
            'use strict';
            $('[data-toggle="tooltip"]').tooltip();
        },

        stickyElements: function () {
            'use strict';
            $('.position-fixed-static').each(function (key, item) {
                var element = $(item);
                var elementScrollTop = element.offset();
                var offset = element.data('offsetTop') ? element.data('offsetTop') : '0';
                element.data('width', element.width()).data('zIndex', element.css('zIndex'));

                $(window).on('scroll', function () {
                    if ($('body,html').scrollTop() > (elementScrollTop.top - offset)) {
                        element.css({position: 'fixed', width: element.data('width'), top: offset, zIndex: '1000'});
                    } else {
                        element.css({position: 'static', zIndex: element.data('zIndex')});
                    }
                });

                $(window, document).on('resize', function () {
                    elementScrollTop = element.offset();
                });
            });
        },

        formEnhancements: function () {
            'use strict';

            /* Select boxes */
            if (typeof $.fn.chosen === 'function') {
                $('select:not(.basic)').chosen({
                    allow_single_deselect: true,
                    width: '100%',
                    no_results_text: Silla.labels.no_results
                }).on('chosen:showing_dropdown', function () {
                    $('body, html').css({'overflow-x': 'visible'});
                }).on('chosen:hiding_dropdown', function () {
                    $('body, html').css({'overflow-x': 'hidden'});
                });
            }

            /* MaxLength validation */
            if (typeof $.fn.maxlength === 'function') {
                $('input[maxlength], textarea[maxlength]').maxlength({
                    threshold: 30,
                    placement: 'top',
                    warningClass: 'label label-warning',
                    limitReachedClass: 'label label-danger',
                    message: Silla.labels.validations.maxlength
                });
            }

            /* ColorPickers */
            if (typeof $.fn.colorpicker === 'function') {
                $('.colorpicker-component').colorpicker();
            }

            /* DateTime pickers */
            if (typeof $.fn.datetimepicker === 'function') {
                $('.datetimepicker-component').each(function () {
                    var elem = $(this);
                    elem.datetimepicker(elem.data()).on('dp.change', function (ev) {
                        if (ev.date) {
                            $('input:hidden', elem.parent()).val(ev.date.format('YYYY-MM-DD HH:mm:00'));
                        }
                    });
                });

                /* Only Time pickers */
                $('.timepicker-component').each(function () {
                    var elem = $(this);
                    elem.datetimepicker(elem.data());
                });

                /* Only Date pickers */
                $('.datepicker-component').each(function () {
                    var elem = $(this);
                    elem.datetimepicker(elem.data()).on('dp.change', function (ev) {
                        if (ev.date) {
                            $('input:hidden', elem.parent()).val(ev.date.format('YYYY-MM-DD'));
                        }
                    });
                });
            }

            /* File fields */
            $(document.body).on('change', '.btn-file :file', function () {
                $(this).trigger('fileselect', $(this).val().replace(/\\/g, '/').replace(/.*\//, ''));
            });

            $('.btn-file :file').on('fileselect', function (event, label) {
                $(this).parents('.input-group').find(':text').val(label);
            });

            /* Cancel buttons */
            $('.cancel').on('click', function () {
                history.go(-1);
            });

            $('.btn-file-remove').on('click', function () {
                var btn_wrapper = $(this).parent().parent();
                $('input', btn_wrapper).val('');
                btn_wrapper.prev().fadeOut('fast');
                $(this).remove();
            });

            /* LightBox */
            $(document.body).on('click', '*[data-toggle="lightbox"]', function (e) {
                e.preventDefault();
                $(this).ekkoLightbox();
            });

            /* Disable form submit button */
            $('.data-form').on('submit', function () {
                $(this).attr('action', $(this).attr('action') + window.location.hash);
                $('button', $(this)).attr('disabled', 'disabled');
            });

            /* Focus form sections */
            var url = document.location.toString();
            if (url.match('#')) {
                $('.nav-tabs a[href=#' + url.split('#')[1] + ']').tab('show');
            }

            $('.nav-tabs a').on('shown.bs.tab', function (e) {
                window.location.hash = e.target.hash;
            });
        },

        errorsHighlight: function () {
            'use strict';

            var sectionFirst = false;

            if ($('.save-errors').length > 0) {
                $('.field').each(function () {
                    $('#' + $(this).attr('rel')).parents('.form-group:first').addClass('has-error');

                    if ($(this).data('section')) {
                        $('.nav-tabs li').removeClass('active');
                        var section = $('a[href=#form-section-' + $(this).data('section') + ']').addClass('text-danger');

                        if (!sectionFirst) {
                            sectionFirst = section;
                        }
                    }
                });

                if (sectionFirst) {
                    sectionFirst.tab('show');
                }
            }
        },

        modals: {
            init: function () {
                $(document).on('shown.bs.modal', function (e) {
                    $(window).resize();
                });

                $(document).on('hidden.bs.modal', function (e) {
                    var target = $(e.target);
                    target.removeData('bs.modal').find('.modal-body').html('');
                });
            },
            delete: function () {
                'use strict';
                $(document.body).on('click', '.action-delete', function (e) {
                    e.preventDefault();
                    var element = $(this);
                    var isInDataTableContext = (element.closest('table[data-type="xhr"]').length > 0);
                    var isInPreviewContext = (element.closest('.modal-dialog').length > 0);
                    var isProtected = (element.data('controller') === 'cmsusers' || element.data('controller') === 'cmsuserroles');
                    var passwordInput = $('<div>').append($('<div>', {'class': 'form-group has-warning margin-vertical'})
                        .append($('<label>', {
                            'class': 'sr-only',
                            'for': 'password-confirmation'
                        }).text(Silla.labels.delete_confirmation.password))
                        .append($('<input>', {
                            'type': 'password',
                            'class': 'form-control',
                            'id': 'password-confirmation',
                            'placeholder': Silla.labels.delete_confirmation.password
                        })));

                    bootbox.dialog({
                        title: Silla.labels.delete_confirmation.title,
                        message: Silla.labels.delete_confirmation.desc + (isProtected ? passwordInput.html() : ''),
                        buttons: {
                            cancel: {
                                label: Silla.labels.delete_confirmation.buttons.cancel,
                                className: 'btn-default btn-outline'
                            },
                            confirm: {
                                label: Silla.labels.delete_confirmation.buttons.confirm,
                                className: 'btn-danger btn-outline',
                                callback: function () {
                                    if (isInDataTableContext) {
                                        $.post(element.attr('href'), {
                                            method: 'delete',
                                            password: $('#password-confirmation').val()
                                        }, function () {
                                            element.closest('tr').remove();
                                            $(document).trigger(element.data('controller') + 'DataTableChanged');
                                        });
                                    } else if (isInPreviewContext) {
                                        $.post(element.attr('href'), {
                                            method: 'delete',
                                            password: $('#password-confirmation').val()
                                        }, function () {
                                            $('#modal-preview').modal('hide');
                                            $('.data-table tr[data-id="' + element.data('resource') + '"]').remove();
                                            $(document).trigger(element.data('controller') + 'DataTableChanged');
                                        });
                                    } else {
                                        var deleteFormAction = $('<form>', {
                                            'action': element.attr('href'),
                                            'method': 'post'
                                        })
                                            .append($('<input>', {
                                                'name': '_token',
                                                'value': Silla.token,
                                                'type': 'hidden'
                                            }))
                                            .append($('<input>', {
                                                'name': 'method',
                                                'value': 'delete',
                                                'type': 'hidden'
                                            }))
                                            .append($('<input>', {
                                                'name': 'password',
                                                'value': $('#password-confirmation').val(),
                                                'type': 'hidden'
                                            }));

                                        deleteFormAction.submit();
                                    }
                                }
                            }
                        }
                    });
                });
            },
            inline: function () {
                'use strict';
                $(document.body).on('click', '.modal-trigger-inline', function (e) {
                    e.preventDefault();
                    var modalElement = $('#modal-inline');
                    var eventTrigger = $(this);

                    if (eventTrigger.attr('title')) {
                        $('.modal-title', modalElement).html(eventTrigger.attr('title'));
                    }

                    $('.modal-body', modalElement).html('').show().load(eventTrigger.attr('href'), function () {
                        setTimeout(CMS.attach.formEnhancements, 200);
                    });

                    modalElement.modal();
                });
            },

            preview: function () {
                'use strict';
                $(document.body).on('click', '.modal-trigger-preview', function (e) {
                    e.preventDefault();
                    var modalElement = $('#modal-preview');
                    var eventTrigger = $(this);

                    if (eventTrigger.attr('title')) {
                        $('.modal-title', modalElement).html(eventTrigger.attr('title'));
                    }

                    $('.modal-body', modalElement).html('').show().load(eventTrigger.attr('href'), function () {
                        setTimeout(CMS.attach.formEnhancements, 200);
                    });

                    modalElement.modal();
                });
            },

            external: function () {
                'use strict';
                $(document.body).on('click', '.modal-trigger-external', function (e) {
                    e.preventDefault();
                    var eventTrigger = $(this);
                    var modalElement = $('#modal-external');
                    var contentHeight = eventTrigger.data('contentHegiht');

                    if (eventTrigger.attr('title')) {
                        $('.modal-title', modalElement).html($(this).attr('title'));
                    }

                    modalElement.on('show.bs.modal', function () {
                        $('.modal-body', modalElement).html('')
                            .append('<iframe src="' + eventTrigger.attr('href') + '" frameborder="0" width="100%" height="' + (contentHeight ? contentHeight : 400) + '"></iframe>');
                    });

                    modalElement.modal();
                });
            }
        },

        dataTables: function () {
            'use strict';
            $('.data-table[data-type="xhr"]').each(function (idx, element) {
                var table = new DataTable($(element));

                table.attach('initialize', function () {
                    this.enable('sorting');
                    this.enable('pagination');
                    this.enable('filtering');
                    this.enable('tools');
                    this.enable('preview');
                });

                table.attach('populate', function () {
                    this.fixCaption();
                    $(document).trigger(table.table.data('controller') + 'DataTablePopulated');
                });

                $(window).on('resize', function () {
                    table.fixCaption();
                });

                $(document).on(table.table.data('controller') + 'DataTableChanged', function () {
                    table._populate();
                });
            });
        },

        xhrLoader: function () {
            'use strict';
            var loadingSpinner = false;

            var spinnerOpts = {
                lines: 25, // The number of lines to draw
                length: 6, // The length of each line
                width: 2, // The line thickness
                radius: 45, // The radius of the inner circle
                corners: 1, // Corner roundness (0..1)
                rotate: 0, // The rotation offset
                color: '#000', // #rgb or #rrggbb
                speed: 1, // Rounds per second
                trail: 60, // Afterglow percentage
                shadow: false, // Whether to render a shadow
                hwaccel: false, // Whether to use hardware acceleration
                className: 'spinner', // The CSS class to assign to the spinner
                zIndex: 2e9, // The z-index (defaults to 2000000000)
                top: 'auto', // Top position relative to parent in px
                left: 'auto' // Left position relative to parent in px
            };

            $(document).on('ajaxStart', function () {
                $('<div id="spinner-loader"/>').appendTo(document.body).wrap('<div id="loading-in-progress"/>');
                $('#loading-in-progress')
                    .css({
                        display: 'none',
                        position: 'fixed',
                        top: '0',
                        left: '0',
                        width: '100%',
                        height: '100%',
                        background: '#FFF',
                        opacity: 0.6
                    })
                    .fadeIn(300, function () {
                        loadingSpinner = new Spinner(spinnerOpts).spin(document.getElementById('spinner-loader'));
                    });
            });

            $(document).on('ajaxComplete', function () {
                $('#loading-in-progress').fadeOut(300, function () {
                    loadingSpinner.stop();
                }).remove();
            });
        }
    },
    utils: {
        dateToYMD: function (date) {
            'use strict';
            var d = date.getDate();
            var m = date.getMonth() + 1;
            var y = date.getFullYear();

            return '' + y + '-' + (m <= 9 ? '0' + m : m) + '-' + (d <= 9 ? '0' + d : d);
        },
        attachDateRange: function (selector, placement, drops, callback, default_values) {
            var dateRangeLabel = {element: $('span', selector), title: selector.data('attributeTitle')};
            var dateRangeRanges = {};
            var dateRangeRangesLabels = selector.data('rangeLabels');
            var dateRangeDefaultValues = default_values || {start: '01/01/1970', end: '12/01/2100'};
            var d = new Date();

            dateRangeRanges[dateRangeRangesLabels.all] = [d.setFullYear(1970, 0, 1), d.setFullYear(2100, 11, 1)];
            dateRangeRanges[dateRangeRangesLabels.today] = [moment(), moment()];
            dateRangeRanges[dateRangeRangesLabels.yesterday] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
            dateRangeRanges[dateRangeRangesLabels.last_7_days] = [moment().subtract(6, 'days'), new Date()];
            dateRangeRanges[dateRangeRangesLabels.last_30_days] = [moment().subtract(29, 'days'), new Date()];
            dateRangeRanges[dateRangeRangesLabels.current_month] = [moment().startOf('month'), moment().endOf('month')];
            dateRangeRanges[dateRangeRangesLabels.current_year] = [moment().startOf('year'), moment().endOf('year')];
            selector.daterangepicker({
                opens: placement,
                drops: drops,
                startDate: moment(new Date(dateRangeDefaultValues.start)),
                endDate: moment(new Date(dateRangeDefaultValues.end)),
                ranges: dateRangeRanges,
                applyClass: 'btn btn-primary btn-outline',
                cancelClass: 'btn btn-default btn-outline',
                locale: selector.data('localeLabels')
            }, function (start, end) {
                if (start.toDate().getFullYear() !== 1970 && end.toDate().getFullYear() !== 2100) {
                    var date_format = selector.data('dateFormat').toUpperCase();
                    dateRangeLabel.element.html(start.format(date_format) + ' - ' + end.format(date_format));
                    $('input.daterange-start', selector).val(CMS.utils.dateToYMD(start.toDate()));
                    $('input.daterange-end', selector).val(CMS.utils.dateToYMD(end.toDate()));
                } else {
                    dateRangeLabel.element.html(dateRangeLabel.title);
                    $('input.daterange-start', selector).val('');
                    $('input.daterange-end', selector).val('');
                }

                if (callback && typeof(callback) === 'function') {
                    callback(start, end);
                }
            });
        },
        isObjectEmpty: function (obj) {
            for (var prop in obj) {
                if (obj.hasOwnProperty(prop)) {
                    if (typeof obj[prop] === 'object') {
                        if (!CMS.utils.isObjectEmpty(obj[prop])) {
                            return false;
                        }
                    } else {
                        if (obj[prop]) {
                            return false;
                        }
                    }
                }
            }

            return true;
        }
    }
};
