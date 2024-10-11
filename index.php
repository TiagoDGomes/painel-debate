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
    </script>
    
    <script src="scripts/default.js?v=<?= @$APP_VERSION ?>"></script>
    <script src="scripts/classes.js?v=<?= @$APP_VERSION ?>"></script>
    <script src="scripts/qrcode.js?v=<?= @$APP_VERSION ?>"></script>
</head>
<body class="<?= Style::getCurrentStyle() . ' ' . (AccessCheck::isValidAdminPage() ? 'admin': '') ?>">
    <div id="main">        
        <div id="visible" style="display:none">
            
            <div class="container-timer">
                <div id="timer"></div>
            </div>
            
            <div class="container-toolbar">

                <div class="item full-screen">
                    <a title="Mostrar em tela inteira" href="javascript:fullScreen();"><i class="icon full-screen"></i></a>
                </div>

                <div class="item qrcode">
                    <a title="Mostrar QRCode" href="javascript:alternarQRCode();"><i class="icon qrcode"></i></a>
                </div>

                <?php if (AccessCheck::isValidAdminPage()): ?>

                <?php Style::HTMLMenu(); ?>

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
                    <p>
                    <div class="timer-buttons command-buttons">
                        <button onclick="Timer.start()" class="big green start">
                            <span class="shadow"></span>
                            <span class="edge"></span>
                            <span class="front text">▶️</span>   
                        </button>
                        <button onclick="Timer.prepareTime(Timer.getRemainingSeconds())" class="big pause">
                            <span class="shadow"></span>
                            <span class="edge"></span>
                            <span class="front text"><i class="icon pause"></i></span>      
                        </button>
                    </div>
                    <div class="timer-buttons prepare-buttons">
                        <button onclick="Timer.prepareTime(30)" class="big">
                            <span class="shadow"></span>
                            <span class="edge"></span>
                            <span class="front text">0:30</span>    
                        </button>
                        <button onclick="Timer.prepareTime(60)" class="big">
                            <span class="shadow"></span>
                            <span class="edge"></span>
                            <span class="front text">1:00</span>
                        </button>
                        <button onclick="Timer.prepareTime(90)" class="big">
                            <span class="shadow"></span>
                            <span class="edge"></span>
                            <span class="front text">1:30</span>                        
                        </button>
                        <button onclick="Timer.prepareTime(120)" class="big">
                            <span class="shadow"></span>
                            <span class="edge"></span>
                            <span class="front text">2:00</span>                        
                        </button>
                        <button onclick="Timer.prepareTime(180)" class="big">
                            <span class="shadow"></span>
                            <span class="edge"></span>
                            <span class="front text">3:00</span>                        
                        </button>
                        <button onclick="Timer.prepareTime(240)" class="big">
                            <span class="shadow"></span>
                            <span class="edge"></span>
                            <span class="front text">4:00</span>                        
                        </button>
                        <button onclick="Timer.prepareTime(300)" class="big">
                            <span class="shadow"></span>
                            <span class="edge"></span>
                            <span class="front text">5:00</span>                        
                        </button>
                        <button onclick="Timer.prepareTime(600)" class="big">
                            <span class="shadow"></span>
                            <span class="edge"></span>
                            <span class="front text">10:00</span>                        
                        </button>
                        <button onclick="Timer.prepareTime(900)" class="big">
                            <span class="shadow"></span>
                            <span class="edge"></span>
                            <span class="front text">15:00</span>                        
                        </button>
                    </div>   
                    <div class="timer-buttons">
                        <button style="display: none" onclick="window.open('?i=<?= $_GET['i'] ?>')" class="big blue">
                            <span class="shadow"></span>
                            <span class="edge"></span>
                            <span class="front text">Tela inteira</span>                        
                        </button>
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