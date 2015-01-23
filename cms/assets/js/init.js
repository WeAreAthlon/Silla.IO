/**
 * Silla Init JS.
 *
 * @package    Silla.IO
 * @subpackage Core
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

$(function() {
    CMS.attach.xhrLoader();
    CMS.attach.tooltips();
    CMS.attach.formEnhancements();
    CMS.attach.errorsHighlight();
    CMS.attach.stickyElements();
    CMS.attach.modals.init();
    CMS.attach.modals.delete();
    CMS.attach.modals.inline();
    CMS.attach.modals.external();
    CMS.attach.modals.preview();
    CMS.attach.dataTables();

    $('[data-toggle=offcanvas]').on('click', function() {
        $('.row-offcanvas').toggleClass('active');
    });

    $.ajaxSetup({data: {_token: Silla.token}});

    $(document).ajaxError(function(event, request) {
        switch(request.status) {
            case 403:
                bootbox.alert('<h4>' + Silla.labels.errors.access + '</h4>');
                break;

            default:
                bootbox.alert('<h4>' + Silla.labels.errors.general + '</h4><pre>' + request.responseText + '</pre>');
                break;
        }
    });
});