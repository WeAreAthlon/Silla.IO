<!DOCTYPE html>
<html lang="en">
  <head>
    <title>{$_labels.title}</title>
    <meta name="description" content=""/>
    <meta name="keywords" content=""/>
    {assets source=$_assets.styles}
  </head>
  <body>
    <div class="wrap">
      {$_content_view_yield}
    </div>
    {assets source=$_assets.scripts}
  </body>
</html>