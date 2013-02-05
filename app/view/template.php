<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo $temp->getTitle() ?></title>

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

    <?php echo $temp->getStyles(); ?>
    <?php echo $temp->getInjectedData(); ?>
    <?php echo $temp->getHeaderScripts(); ?>
</head>
<body>
<!--[if lt IE 7]>
<p class="chromeframe"> You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
<![endif]-->

<!-- Add your site or application content here -->
<header>
    <h1>Mauritz.com</h1>
    <nav>
        <?php echo $temp->getMenu('mainMenu'); ?>
    </nav>
    <loginArea>
        <?php if($temp->getIsAdmin()) { ?>
            <p>Velkommen <?php echo $temp->getCurrentUser()->getName() ?></p>
                <a href='<?php echo $temp->getBaseDir() ?>main/createNewAuction'>Opret ny auktion</a>
                <a href='<?php echo $temp->getBaseDir() ?>main/logout'>Log ud</a>
        <?php } else { ?>
        <p>Bruger login</p>
        <form method="post">
            <input type="text" placeholder="Brugernavn" name='userLoginName' />
            <input type="password" placeholder="Kodeord" name=userLoginPassword />
            <input type="submit" value="Login" />
        </form>
        <a href='<?php echo $temp->getBaseDir() ?>main/createNewUser'>Opret ny bruger</a>
         <?php } ?>
    </loginArea>
</header>
<content>
    <?php echo $temp->getContent(); ?>
</content>
<footer>
</footer>
<?php echo $temp->getFooterScripts(); ?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.8.2.min.js"><\/script>')</script>
<?php echo $temp->getFooterScripts(); ?>

<!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
<script>
    var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
    (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
        g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
        s.parentNode.insertBefore(g,s)}(document,'script'));
</script>
</body>
</html>
