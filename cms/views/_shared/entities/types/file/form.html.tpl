{if $_action neq 'create' and $resource->{$field}}
    <div class="assets-preview">
        <div class="file-preview pull-left">
            <blockquote>
                <h4 class="text-thin">{$attr.default}</h4>
                <p><a href="{$resource->get_attachment($field)}" class="btn btn-outline btn-link no-padding-left" target="_blank"><i class="glyphicon glyphicon-download-alt"></i> {$_labels.files.download} ( {{{{$resource->get_attachment($field)|replace:$_urls.root:$_urls.root_path}|filesize} / 1024}|string_format:"%.2f"} KB )</a></p>
            </blockquote>
        </div>
    </div>
{/if}

<div class="input-group clear">
    <span class="input-group-btn">
        <span class="btn btn-outline btn-default btn-file{$attr.disabled}">
            <i class="glyphicon glyphicon-file"></i> {$_labels.files.select} <input type="file" id="{$attr.id}" name="{$attr.name}"{$attr.disabled}/>
        </span>
        {if $attr.default}
            <span class="btn btn-outline btn-danger btn-file-remove{$attr.disabled}">
            <i class="glyphicon glyphicon-remove-circle"></i> {$_labels.files.remove}
        </span>
        {/if}
    </span>
    <input type="text" class="form-control" name="{$attr.name}" value="{$attr.default}" readonly="readonly"/>
</div>