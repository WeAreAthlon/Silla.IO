<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>{$_labels.page_not_found.title} | {$_labels.title|default:'CMS'}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link href="//fonts.googleapis.com/css?family=Open+Sans:400&amp;subset=latin" rel="stylesheet" type="text/css">
    {assets source=['vendor/bootstrap/dist/css/bootstrap.css', 'css/bootstrap-theme.athlon.css', 'css/style.css'] media="all"}
    <link href="{$_urls.assets}img/favicon.ico" rel="shortcut icon" type="image/x-icon"/>
</head>
    <body>
        {include file='_shared/navigation-top.html.tpl'}
        <div class="container">
          <div class="row">
             <div class="col-lg-12">
                 <h1 class="text-thin">{$_labels.page_not_found.title}</h1>
                 <p class="text-thin">{$_labels.page_not_found.desc}</p>
                 <p class="text-thin"><a href="{link_to controller=users action=account}" class="btn btn-link btn-lg no-padding-left"><i class="glyphicon glyphicon-user"></i> {$_labels.modules.users.account}</a></p>
             </div>
          </div>
          {include file='_shared/footer.html.tpl'}
        </div>
        <!--[if lt IE 9]>
        {assets source=['vendor/html5shiv/dist/html5shiv.min.js','vendor/respond/dest/respond.min.js']}
        <![endif]-->
        {assets source=['vendor/jquery/dist/jquery.min.js', 'vendor/bootstrap/dist/js/bootstrap.min.js']}
    </body>
</html>
