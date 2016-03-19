<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>{$_labels.modules.$_controller.$_action|default:$_labels.sections.$_action} | {$_labels.modules.$_controller.title} | {$_labels.title|default:'CMS'}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link href="//fonts.googleapis.com/css?family=Open+Sans:400&amp;subset=latin" rel="stylesheet" type="text/css">
    {assets source=$_assets.styles media="all"}
    <link href="{$_urls.assets}img/favicon.ico" rel="shortcut icon" type="image/x-icon"/>
    <script type="text/javascript">
        var Silla = {
            token : '{$_request->token()}',
            labels: {$_labels.js|json_encode}
        }
    </script>
</head>
    <body>
        {include file='_shared/navigation-top.html.tpl'}
        <div class="row no-margin row-offcanvas row-offcanvas-left">
            <div class="col-md-2 no-padding" role="navigation">
                {include file='_shared/navigation-sidebar.html.tpl'}
            </div>
            <div class="col-md-10 col-sm-12 col-xs-12">
                <div class="caption">
                    {$smarty.capture.caption|default:{include file='_shared/caption.html.tpl'}}
                </div>

                {include file='_shared/flash-message.html.tpl'}

                {$_content_view_yield}

                {include file='_shared/footer.html.tpl'}
            </div>
        </div>
        {include file='_shared/modals/default.html.tpl' type='inline'}
        {include file='_shared/modals/default.html.tpl' type='external'}
        <!--[if lt IE 9]>
        {assets source=['vendor/html5shiv/dist/html5shiv.min.js','vendor/respond/dest/respond.min.js']}
        <![endif]-->
        {assets source=$_assets.scripts}
    </body>
</html>
