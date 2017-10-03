<!DOCTYPE html>
<html lang="{$_language}">
<head>
  <meta charset="utf-8">
  <title>{$_labels.page_not_found.title} | {$_labels.title|default:'CMS'}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link href="//fonts.googleapis.com/css?family=Open+Sans:400&amp;subset=latin" rel="stylesheet" type="text/css">
  {assets source=['vendor/components/bootstrap/css/bootstrap.css', 'cms/assets/css/bootstrap-theme.silla.css', 'cms/assets/css/default.silla.css'] media="all"}
  <link href="{$_urls.assets}img/favicon.ico" rel="shortcut icon" type="image/x-icon">
</head>
<body>
{include '_shared/navigation-top.html.tpl' inline}
<div class="container">
  <div class="row">
    <div class="col-lg-12">
      <h1 class="text-thin">{$_labels.page_not_found.title}</h1>
      <p class="text-thin">{$_labels.page_not_found.desc}</p>
      <p class="text-thin">
        <a href="{link_to controller=account}" class="btn btn-outline btn-link btn-lg no-padding-left">
          <i class="glyphicon glyphicon-user"></i> {$_labels.modules.account.title}
        </a>
      </p>
    </div>
  </div>
  {include '_shared/footer.html.tpl' inline}
</div>
<!--[if lt IE 9]>
{assets source=['vendor/afarkas/html5shiv/dist/html5shiv.min.js','vendor/rogeriopradoj/respond/dest/respond.min.js']}
<![endif]-->
{assets source=['vendor/components/jquery/jquery.min.js', 'vendor/components/bootstrap/js/bootstrap.min.js']}
</body>
</html>
