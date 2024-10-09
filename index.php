<?php require_once 'core/init.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= @$APP_TITLE ?></title>
    <link rel="stylesheet" href="media/default.css?v=<?= @$APP_VERSION ?>">
    <link rel="stylesheet" href="media/button.css?v=<?= @$APP_VERSION ?>"> 
    <script>
        var GLOBAL_ID = '<?= $_GET['i'] ?>';
        var SYNC_PING_COUNT = <?= $SYNC_PING_COUNT * 1 ?>;    
        var CURRENT_URL = window.location.href;
    </script>
    </script>
    <script src="scripts/default.js?v=<?= @$APP_VERSION ?>"></script>
    <script src="scripts/classes.js?v=<?= @$APP_VERSION ?>"></script>
    <link rel="stylesheet" href="media/<?= $flag_access ?>.css?v=<?= @$APP_VERSION ?>">  
    <meta name="theme-color" content="var(--timer-default-color)">
    <script src="scripts/qrcode.js?v=<?= @$APP_VERSION ?>"></script>
</head>
<body class="<?= $flag_access ?>">
    <div id="main">        
        <div id="visible" style="display:none">
            
            <div class="container-timer">
                <div id="timer"></div>
            </div>
            <div class="container-toolbar">
                <?php if (AccessCheck::isValidAdminPage()): ?>
                <div class="item">
                    <a title="Acessar versão em tela inteira" target="_blank" href="?i=<?= $_GET['i'] ?>"><i class="icon full-screen"></i></a>
                </div>
                <?php endif; ?> 
                <div class="item">
                    <a id="qcode" href="#" title="Mostrar QRCode" onclick="alternarQRCode()"><i class="icon qrcode"></i></a>
                </div>
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
                            <span class="front text">⏸️</span>      
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