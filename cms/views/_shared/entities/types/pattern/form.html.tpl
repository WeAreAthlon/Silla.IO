<textarea class="form-control" name="{$attr.name}" rows="{$attr.rows|default:5}" cols="{$attr.cols|default:40}"
id="{$attr.id}"{if isset($attr.length)} maxlength="{$attr.length}"{/if}{$attr.disabled}>{$attr.default.raw}</textarea>
