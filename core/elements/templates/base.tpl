<!DOCTYPE html>
<html lang="en">
<head>
    {include 'file:chunks/base/head.tpl'}
    {block 'head'}

    {/block}
</head>
<body>
<div class="wrapper">
    <header class="page-header">
        {include 'file:chunks/base/header.tpl'}
    </header>
    {include 'file:chunks/base/main-navigation.tpl'}
    {block 'content'}

    {/block}
    {include 'file:chunks/base/footer.tpl'}
</div>
<!--Modals-->
    {include 'file:chunks/base/modals.tpl'}
{block 'modals'}

{/block}
<!---Scripts-->
<script src="[[++assets_url]]didanceTpl/js/jquery.js"></script>
<script src="[[++assets_url]]didanceTpl/js/jquery.viewportchecker.min.js"></script>
<script src="[[++assets_url]]didanceTpl/js/bootstrap.js"></script>
<script src="[[++assets_url]]didanceTpl/js/owl.carousel.min.js"></script>
<script src="[[++assets_url]]didanceTpl/js/script.js"></script>
</body>
</html>