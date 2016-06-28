<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle toggle-navigation-user" data-toggle="collapse" data-target=".navbar-main-collapse">
          <span class="sr-only">{$_labels.general.toggle_nav}</span>
          <i class="glyphicon glyphicon-user"></i>
      </button>
      <button type="button" class="navbar-toggle pull-left margin-full visible-xs visible-sm" data-toggle="offcanvas">
          <span class="sr-only">{$_labels.general.toggle_nav}</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand text-thin" href="{$_urls.mode}">{$_labels.title}</a>
        {if $_action neq '404'}
        <ol class="navbar-nav breadcrumb no-margin no-background text-thin hidden-xs">
            <li></li>
            <li><a href="{url controller=$_controller action=index}">{$_labels.modules.$_controller.title|default:''}</a></li>
            <li class="active">{$_labels.modules.$_controller.$_action|default:$_labels.sections.$_action|default:''}</li>
        </ol>
        {/if}
    </div>
    <div class="collapse navbar-collapse navbar-main-collapse">
        {if $user|default:false}{include file='_shared/cmsusers/account.html.tpl'}{/if}
    </div>
</div>