/**
 * CMS Help JS.
 *
 * @package    Silla.IO
 * @subpackage Core
 * @author     Rozaliya Stoilova <rozalia@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

$(function() {
    $('.help-link').on('click', function(e) {
        e.preventDefault();

        window.open($(this).attr('href'), '_blank', 'width=600,scrollbars=yes');
    });

    $('a[data-section="preview"]').on('click', function() {
        var content = $('textarea[name="content"]');
        $.post(content.data('urlPreview'), {content: content.val()}, function(response) {
            $('fieldset[data-section="preview"] .form-fields-wrapper').html('').append(response).fadeIn();
        });
    });
});