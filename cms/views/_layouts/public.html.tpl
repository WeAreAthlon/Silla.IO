<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>{$_labels.sections.$_action} | {$_labels.title}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <!-- Style Sheets -->
    <link href="//fonts.googleapis.com/css?family=Open+Sans:300&amp;subset=latin" rel="stylesheet" type="text/css"/>
    {assets source=$_assets.styles media="all"}
    <link href="{$_urls.assets}img/favicon.ico" rel="shortcut icon" type="image/x-icon" />
</head>
    <body>
        <div class="container">
            <div class="content">
                <img src="{$_urls.assets}img/logo.png" alt="{$_labels.title|escape}" class="img-circle logo">
                <div class="status-alert-area text-thin">
                    {flash_message}
                </div>
                {$_content_view_yield}
            </div>
        </div>
        <!--[if lt IE 9]>
        {assets source=['vendor/html5shiv/dist/html5shiv.min.js','vendor/respond/dest/respond.min.js']}
        <![endif]-->
        {assets source=$_assets.scripts}
    </body>
</html>
