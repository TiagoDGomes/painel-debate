<?php require_once 'core/init.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= @$APP_TITLE ?></title>

    <link rel="stylesheet" href="media/minimal.css?v=<?= @$APP_VERSION ?>">

    <?php Style::HTMLHeadCurrentStyle(); ?>

    <meta name="theme-color" content="var(--timer-default-color)">

    <script>
        var GLOBAL_ID = '<?= $_GET['i'] ?>'; 
        var CURRENT_URL = window.location.href;
        var SYNC_PING_COUNT = <?= $SYNC_PING_COUNT * 1 ?>;  
        var originalTextContent = <?= json_encode(Property::get('text-content'), JSON_PRETTY_PRINT) ?> ; 
    </script>
    
    <script src="scripts/default.js?v=<?= @$APP_VERSION ?>"></script>
    <script src="scripts/classes.js?v=<?= @$APP_VERSION ?>"></script>
    <script src="scripts/qrcode.js?v=<?= @$APP_VERSION ?>"></script>
    <script src="scripts/nicEdit/nicEdit.js"></script>
            
</head>
<body class="<?= Style::getCurrentStyle() . ' ' . (AccessCheck::isValidAdminPage() ? 'admin': '') . ' ' . (AccessCheck::inListMode() ? 'list' : '') ?>">
    <div id="main">        
        <div id="visible" style="display:none">
            
            <div class="container-timer">
                <div id="timer"></div>
            </div>      
                  
            <div class="container-timer-spacing"></div>

            <div class="container-toolbar">

                <?php Toolbar::HTMLLinkAnchor('javascript:fullScreen()','Mostrar em tela inteira','full-screen','');  ?>

                <?php Toolbar::HTMLLinkAnchor('javascript:alternarQRCode()','Mostrar QRCode','qrcode','');  ?>

                <?php if (AccessCheck::isValidAdminPage()): ?>

                <?php Toolbar::HTMLMenu(); ?>

                <?php endif; ?>

            </div>
            


            <?php if (AccessCheck::isSystemMessageActive()): ?>

                <div class="container-title">
                    <h1 id="title"></h1>
                </div>
                <div class="container-message">
                    <p id="message"></p>
                </div>                
                <?php endif; ?>  

            <?php if (AccessCheck::isValidAdminPage()): ?>
                
            <!--<admin>--> 
                <div class="container-admin"> 
                    <div class="textbox"></div> 
                    
                    <div class="textedit" style="visibility:hidden">
                        <textarea style="width: 100%" id="nEditor"><?= Property::get('text-content') ?></textarea> 
                    </div>
                    
                </div>   
                                     
            <!--</admin>-->

            <?php else: ?>  

            <!--<user>--> 

            <!--</user>-->  

            <?php endif; ?>         
        </div> 
        <div id="qrcode" style="display: none"></div>   
        <div class="container-status">
            <div id="status"></div>
            <div id="status-basic"></div>
            <div id="status-error"><noscript>O Javascript está desativado.</noscript></div>
        </div>
        <div class="container-debug">
            <pre id="debug"><?php // var_dump($_SERVER); ?></pre>
        </div> 
          
    </div>   
</body>
</html>