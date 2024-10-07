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
    </script>
    <script src="scripts/default.js?v=<?= @$APP_VERSION ?>"></script>
    <script src="scripts/classes.js?v=<?= @$APP_VERSION ?>"></script>
    <?php if (AccessCheck::isValidAdminPage()): ?>

    <link rel="stylesheet" href="media/admin.css?v=<?= @$APP_VERSION ?>"> 

    <?php else: ?>

    <link rel="stylesheet" href="media/user.css?v=<?= @$APP_VERSION ?>"> 

    <?php endif; ?>     
</head>
<body class="<?= $body_admin_class ?>">
    <div id="main">        
        <div id="visible" style="display:none">
            <div class="container-timer">
                <div id="timer"></div>
            </div>
            <?php if (AccessCheck::isSystemMessageActive()): ?>

                <div class="container-title">
                    <h1 id="title"><?= $body_admin_class ?></h1>
                </div>
                <div class="container-message">
                    <p id="message"><?= $body_admin_class ?></p>
                </div>                
                <?php endif; ?>  

            <?php if (AccessCheck::isValidAdminPage()): ?>

            <!--<admin>--> 
                <div class="container-admin">
                    <p>
                    <!--<button onclick="Timer.syncTicTac()" class="red" role="button">
                        <span class="shadow"></span>
                        <span class="edge"></span>
                        <span class="front text">Sync</span>
                    </button>
                    <button onclick="Timer.prepareTime(11)" class="blue">
                        <span class="shadow"></span>
                        <span class="edge"></span>
                        <span class="front text">0:11</span>                        
                    </button>-->
                    <div class="timer-buttons command-buttons">
                        <button onclick="Timer.start()" class="big green start">
                            <span class="shadow"></span>
                            <span class="edge"></span>
                            <span class="front text">▶️</span>   
                        </button>
                        <button onclick="index_prepareTime(Timer.getRemainingSeconds())" class="big pause">
                            <span class="shadow"></span>
                            <span class="edge"></span>
                            <span class="front text">⏸️</span>      
                        </button>
                    </div>
                    <div class="timer-buttons prepare-buttons">
                        <button onclick="index_prepareTime(30)" class="big">
                            <span class="shadow"></span>
                            <span class="edge"></span>
                            <span class="front text">0:30</span>    
                        </button>
                        <button onclick="index_prepareTime(60)" class="big">
                            <span class="shadow"></span>
                            <span class="edge"></span>
                            <span class="front text">1:00</span>
                        </button>
                        <button onclick="index_prepareTime(90)" class="big">
                            <span class="shadow"></span>
                            <span class="edge"></span>
                            <span class="front text">1:30</span>                        
                        </button>
                        <button onclick="index_prepareTime(120)" class="big">
                            <span class="shadow"></span>
                            <span class="edge"></span>
                            <span class="front text">2:00</span>                        
                        </button>
                        <button onclick="index_prepareTime(180)" class="big">
                            <span class="shadow"></span>
                            <span class="edge"></span>
                            <span class="front text">3:00</span>                        
                        </button>
                        <button onclick="index_prepareTime(240)" class="big">
                            <span class="shadow"></span>
                            <span class="edge"></span>
                            <span class="front text">4:00</span>                        
                        </button>
                        <button onclick="index_prepareTime(300)" class="big">
                            <span class="shadow"></span>
                            <span class="edge"></span>
                            <span class="front text">5:00</span>                        
                        </button>
                        <button onclick="index_prepareTime(600)" class="big">
                            <span class="shadow"></span>
                            <span class="edge"></span>
                            <span class="front text">10:00</span>                        
                        </button>
                        <button onclick="index_prepareTime(900)" class="big">
                            <span class="shadow"></span>
                            <span class="edge"></span>
                            <span class="front text">15:00</span>                        
                        </button>
                    </div>
                    
                </div>  
                <script>
                    function index_prepareTime(t){
                        m = Math.floor(t / 60);
                        s = t % 60; ss = s > 9 ? s : "0" + s;
                        Timer.prepareTime(t);
                        document.querySelectorAll("button.start .text")[0].innerHTML = m + ":" + ss;
                    }
                </script>
            <!--</admin>-->

            <?php endif; ?>         
        </div>  
        <div class="container-status">
            <div id="status"></div>
            <div id="status-error"><noscript>O Javascript está desativado.</noscript></div>
        </div>
        <div class="container-debug">
            <pre id="debug"></pre>
        </div>       
    </div>    
</body>
</html>