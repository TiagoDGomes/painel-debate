<?php

if (isset($_GET['t'])) {
    header('Content-Type: application/json');
    $dados = array();
    $microtime_user = (int) @$_GET['t'];
    $dados['t'] = (int) (microtime(1) * 1000);
    $dados['diff'] =  $dados['t'] - $microtime_user;
    echo json_encode($dados);
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ping</title>

</head>

<body>
    <div id="result"></div>

    <script>
        var real_timestamp_server = 0;
        var last_diff = 0;
        var next_timestamp_sec = 0;
        var interval_start = 0;
        function ping(timestamp) {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    var dados = JSON.parse(xhr.responseText);
                    var result = document.getElementById('result');
                    result.innerHTML = dados['diff'] + 'ms';
                    real_timestamp_server = dados['t'];
                    last_diff = dados['diff'];
                    if (timestamp == 1000) {
                        ping(real_timestamp_server);
                    }
                    next_timestamp_sec = Math.round(real_timestamp_server / 1000) * 1000 ;
                    interval_start =  next_timestamp_sec - real_timestamp_server;
                    console.log('next_timestamp_sec', next_timestamp_sec);
                    console.log('real_timestamp_server', real_timestamp_server);
                    console.log('interval_start', interval_start);
                }
            };
            xhr.open('GET', 'ping.php?t=' + timestamp);
            xhr.send();
        }

        setInterval(function() {
            ping(real_timestamp_server + 1000, true);
        }, 1000);
    </script>
</body>

</html>