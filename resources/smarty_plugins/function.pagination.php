<?php
/**
 * Pagination Function.
 *
 * @package    Silla.IO
 * @subpackage Vendor\Athlon\SmartyPlugins
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

/**
 * Paginate layout printing function.
 *
 * @param array                    $params   Specified params.
 * @param Smarty_Internal_Template $template Instance of Smarty template class.
 *
 * @uses   Core\Config()
 *
 * @return string Rendered pagination template.
 */
function smarty_function_pagination(array $params, Smarty_Internal_Template $template)
{
    $viewsPaths      = Core\Config()->paths('views');
    $params['range'] = isset($params['range']) ? abs($params['range']) : 2;
    $pathToTemplates = $viewsPaths['templates'] . '_shared' . DIRECTORY_SEPARATOR;
    $templateFile    = isset($params['template']) ? $pathToTemplates . $params['template'] . '.html.tpl' : null;

    if (file_exists($templateFile)) {
        $params['template'] = $templateFile;
    } else {
        $params['template'] = $pathToTemplates . 'pagination' . DIRECTORY_SEPARATOR . 'default.html.tpl';
    }


    $paginator   = $params['paginator'];
    $pages_range = $paginator->range($params['range']);

    $range          = array();
    $range['start'] = isset($pages_range['first']->pageNumber) ? $pages_range['first']->pageNumber : 0;
    $range['end']   = isset($pages_range['last']->pageNumber) ? $pages_range['last']->pageNumber : 0;

    $template->assign('pagination', array(
        'current'    => $paginator->current(),
        'first'      => $paginator->first(),
        'last'       => $paginator->last(),
        'prev'       => $paginator->prev(),
        'next'       => $paginator->next(),
        'boundaries' => isset($params['boundaries']) ? !!$params['boundaries'] : false,
        'range'      => $params['range'],
        'pages'      => range($range['start'], $range['end']),
    ));

    return $template->fetch($params['template']);
}
