/**
 * CMS Help JS.
 *
 * @package    Silla.IO
 * @subpackage Core
 * @author     Rozaliya Stoilova <rozalia@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

$(function(){
    $('.help-link').on('click', function(){
        window.open($(this).attr('data-help-link'), '_blank', 'width=400,scrollbars=yes');
    });
})

$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    if ($(e.target).parents('li').hasClass('help-preview')) {
        var form = $(e.target).parents('form');
        $.post(form.data('preview-action'), form.serialize(), function(response) {
            $('.preview-content').html('').append(response).fadeIn();
        });
    }
});