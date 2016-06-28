<?php /*
Copyright (c) 2010 Dave Miller
Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:
The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE. */
/**
 * @author Dave Miller
 * @copyright Copyright (c) 2010 Dave Miller
 * @license http://www.dave-miller.com/mit-license MIT License
 */
/**
 * Generate <form>...</form> tags.
 *
 * The attributes can either be set in the template:
 *
 * <pre>
 * {form action="file.php" method="post" upload=true}
 *   ...
 * {/form}
 * </pre>
 *
 * Or they can be set in the controller (recommended):
 *
 * <code>
 * $formData = array(
 *   'action' => 'file.php',
 *   'method' => 'post',
 *   'upload' => true,
 *   'hidden' => array('id' => $id),
 *   'class'  => 'myForm'              // Adds a class="myForm" attribute
 * );
 * $smarty->assign('formData', $formData);
 * </code>
 *
 * You can still add (or override) parameters in the template:
 *
 * <pre>
 * {form data=$formData id="myForm" class="alternateForm"}
 *   ...
 * {/form}
 * </pre>
 *
 * @param array Smarty parameters
 *
 * - string <b>action</b>:
 *    The URL to submit the form to.
 * - string <b>method</b> = 'post':
 *    The form method ('get' or 'post'). Automatically converted to lowercase.
 * - string <b>upload</b> = false:
 *    Set to true to set <var>enctype="multipart/form-data"</var>.
 * - array <b>hidden</b> = array():
 *    An array of hidden fields to output after the <form> tag.
 * - array <b>data</b> = false:
 *    Any parameters passed from the controller as an array.
 * - string <b>assign</b> = null:
 *    The name of a variable to assign the output to. (default: output it}
 * - Any other parameters are used as attributes (e.g. class="myclass").
 *
 * @param string|null The block content (or null for the opening tag).
 * @param Smarty $smarty The Smarty object.
 *
 * @return string|void
 */
function smarty_block_form($params, $content, $smarty)
{
    if ($content === null) {
        return false;
    }

    if (!empty($params['data'])) {
        if (is_array($params['data'])) {
            $params = array_merge($params['data'], $params);
        } else {
            trigger_error("form: 'data' parameter must be an array", E_USER_NOTICE);
        }
        unset($params['data']);
    }

    if (empty($params['action'])) {
        trigger_error("form: missing 'action' parameter", E_USER_NOTICE);
        $params['action'] = '';
    }

    $method = $params['method'];

    if (empty($params['method']) || in_array($params['method'], array('patch', 'put', 'delete'))) {
        $params['method'] = 'post';
    } else {
        $params['method'] = strtolower($params['method']);
    }

    if (!empty($params['upload'])) {
        if (empty($params['enctype'])) {
            $params['enctype'] = 'multipart/form-data';
        }
        unset($params['upload']);
    }

    $params['hidden'] = isset($params['hidden']) && !empty($params['hidden']) ?
        array_merge($params['hidden'], array('_method' => $method)) : array('_method' => $method);

    $hidden = '';

    foreach ($params['hidden'] as $fieldName => $fieldValue) {
        $hidden .= '<input type="hidden" '
            .  'name="' . htmlspecialchars($fieldName, ENT_COMPAT, 'UTF-8') . '" '
            .  'value="' . htmlspecialchars($fieldValue, ENT_COMPAT, 'UTF-8') . '" />';
    }

    if ($hidden) {
        $hidden = '<div style="display:none;">' . $hidden . '</div>';
    }

    unset($params['hidden']);

    if (isset($params['assign'])) {
        $assign = $params['assign'];
        unset($params['assign']);
    } else {
        $assign = false;
    }

    $retVal = '<form';

    foreach ($params as $attrName => $attrValue) {
        $retVal .= " $attrName=\"" . htmlspecialchars($attrValue, ENT_COMPAT, 'UTF-8') . "\"";
    }

    $retVal .= ">{$hidden}{$content}</form>";


    if ($assign) {
        $smarty->assign($assign, $retVal);
    } else {
        return $retVal;
    }

    return false;
}