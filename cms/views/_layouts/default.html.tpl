<!DOCTYPE html>
<html lang="{$_language}">
<head>
  <meta charset="utf-8">
  <title>{$_labels.modules.$_controller.$_action|default:$_labels.sections.$_action}
    | {$_labels.modules.$_controller.title} | {$_labels.title|default:'CMS'}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link href="//fonts.googleapis.com/css?family=Open+Sans:400&amp;subset=latin" rel="stylesheet" type="text/css">
  {assets source=$_assets.styles media="all"}
  <link href="{$_urls.assets}img/favicon.ico" rel="shortcut icon" type="image/x-icon">
  <script>
    var Silla = {
      token: '{$_request->token()}',
      labels: {$_labels.js|json_encode}
    }
  </script>
</head>
<body>
{include '_shared/navigation-top.html.tpl' inline}
<div class="row no-margin row-offcanvas row-offcanvas-left">
  <div class="col-md-2 no-padding" role="navigation">
    {include file='_shared/navigation-sidebar.html.tpl' inline}
  </div>
  <div class="col-md-10 col-sm-12 col-xs-12">
    <div class="caption">
      {$smarty.capture.caption|default:{include file='_shared/caption.html.tpl'}}
    </div>

    {include '_shared/flash-message.html.tpl' inline}

    {$_content_view_yield}

    {include '_shared/footer.html.tpl' inline}
  </div>
</div>
{include '_shared/modals/default.html.tpl' type='inline'}
{include '_shared/modals/default.html.tpl' type='external'}
<!--[if lt IE 9]>
{assets source=['vendor/afarkas/html5shiv/dist/html5shiv.min.js','vendor/rogeriopradoj/respond/dest/respond.min.js']}
<![endif]-->
{assets source=$_assets.scripts}
</body>
</html>
