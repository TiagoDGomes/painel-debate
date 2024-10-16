var Timer = {
    localTime: 0,
    localTimeMillis: 0,
    serverTimeMillis: 0,
    _syncing: false,
    pingCount: SYNC_PING_COUNT,
    _interval1s: null,
    _interval1sUpdate: null,
    startTime: null,
    endTime: null,
    preparedTime: null,
    updateFailed: false,

    isSyncing: function () {
        return Timer._syncing;
    },
    setSyncing: function (s) {
        if (s) {
            Status.setMessage("Sincronizando...");
        } else {
            Status.setMessage("");
        }
        Timer._syncing = s;
    },
    prepareTime: function (time_value) {
        Timer.preparedTime = time_value;
        Property.set('timer-prepared', (time_value), function (data) {
            document.body.classList.add('timer-ready');
            Timer.preparedTime = data['timer-prepared'];
            Timer.refreshInterface();
        });
    },
    syncTicTac: function (callback_func) {
        clearInterval(Timer._interval1s);
        clearInterval(Timer._interval1sUpdate);
        Timer.setSyncing(true);
        document.body.classList.add('timer-sync');
        document.body.classList.remove('timer-sync-error');
        document.body.classList.remove('timer-ignored');
        document.body.classList.remove('timer-zero');
        Timer.preparedTime = 0;
        Timer.startTime = 0;
        Timer.endTime = 0;
        Timer.serverTime = 0;
        Timer.localTimeMillis = 0;
        Timer.serverTimeMillis = 0;
        Timer._syncCount = 0;
        Timer._diffServer = 0;
        Timer._diffSum = 0;
        Timer.setText('');
        Timer._syncTicTacLoop(function (resultSuccess) { });
    },
    _syncTicTacLoop: function (callback_result_success) {
        HTTPRequest.getJSON('?timer=1&syncCount=' + Timer._syncCount + "&localTime=" + Timer.localTimeMillis, function (data) {
            if (data === null){
                // console.error("Sincronização falhou.");
                Timer._syncTicTacLoop(callback_result_success);
            } else {
                
                var diff = data['diff'];
                Timer.serverTimeMillis = data['serverTimeMillis'];
                if (diff != Timer.serverTimeMillis) {
                    Timer._diffSum += diff;
                }
                var diffToZero = Timer.serverTimeMillis % 1000;
                Timer.localTimeMillis = Timer.serverTimeMillis + 1000;
                Timer._syncCount++;
                var nextTimeout = 1000 - diffToZero;
                console.log("_syncCount         ", Timer._syncCount,
                            "\ndiff             ", diff, 
                            "\ndiffToZero       ", diffToZero, 
                            "\n_diffSum         ", Timer._diffSum, 
                            "\nnextTimeout      ", nextTimeout,
                            "\nserverTimeMillis ", Timer.serverTimeMillis,
                            "\nlocalTimeMillis  ", Timer.localTimeMillis,
                       );
                
                setTimeout(function () {
                    var requireNewPing = Timer._syncCount >= Timer.pingCount;                     
                    if (requireNewPing) {
                        var avgDiff = Timer._diffSum / Timer._syncCount;
                        var miliRounded = Math.floor(avgDiff / 1000) * 1000;
                        Timer.localTimeMillis += miliRounded;
                        setTimeout(function () {
                            Timer.initTicTac();
                            try {
                                callback_result_success(true);
                            } catch (e) {
                                console.error(e);
                            }
                        }, miliRounded - avgDiff * 2)
                    } else {
                        Timer._syncTicTacLoop(callback_result_success);
                    }
                }, nextTimeout);
            }            
        });
    },
    initTicTac: function () {
        console.log("initTicTac:",
                  "\nTimer.localTime:  ", Timer.localTime, 
                  "\nTimer.serverTime: ", Timer.serverTime);
        Timer.setSyncing(false);
        delete Timer._syncCount;
        delete Timer._diffServer;
        delete Timer._diffSum;
        delete Timer.serverTimeMillis;
        Timer.localTime = Math.round(Timer.localTimeMillis / 1000);
        Timer._interval1s = setInterval(Timer.tic, 1000);
        Timer._interval1sUpdate = setInterval(Timer.updateTicTac, 500);
        Timer.updateData(Timer.refreshInterface);
    },
    tic: function(){
        Timer.localTime += 1;
        Status.setDebugMessage('L: ' + Timer.localTime + '\nS: ' + Timer.serverTime);
    },
    updateTicTac: function(){
        Timer.updateData();
        Timer.refreshInterface();        
        if (Timer.isRunning()) {
            //console.log('Timer is Running!');
        } else {
            //console.log('Timer is not Running!');
            //console.log("Timer.serverTime: ", Timer.serverTime, "\nTimer.localTime:  ", Timer.localTime)
            if (Timer.isOutOfSync()) {
                Timer.syncTicTac();
            }
        }
    },
    updateData: function (callback) {
        Property.getAll(function (data) {
            if (data) {
                Timer.preparedTime = data['timer-prepared'];
                Timer.startTime = data['timer-start'];
                Timer.endTime = data['timer-end'];
                Timer.serverTime = data['serverTimeMillis'] / 1000;
                Timer.updateFailed = false;
            } else {
                Timer.updateFailed = true;                
                document.body.classList.add('timer-sync-error');                
            }
            if (callback) callback();
        });
    },
    refreshInterface: function () {
        var seconds = 0;
        document.body.classList.remove('timer-sync-error');
        document.body.classList.remove('timer-ignored');
        document.body.classList.remove('timer-sync');
        document.body.classList.remove('timer-zero');
        if (Timer.isSemaphored()) {
            document.body.classList.add('timer-semaphore');
            seconds = Timer.preparedTime;
            var secondsRegressive = Timer.startTime - Timer.localTime;
            Status.setMessage("Aguarde o sinal...");
            if (secondsRegressive == 2) {
                document.body.classList.remove('set');
                document.body.classList.add('ready');

            } else if (secondsRegressive == 1) {
                document.body.classList.remove('ready');
                document.body.classList.add('set');
            }
        } else if (Timer.isPaused()) {
            Status.setMessage("Em pausa");
            seconds = Timer.preparedTime;
            document.body.classList.add('timer-ready');
            document.body.classList.remove('timer-semaphore');
            Timer.updateButtonStartLabel(seconds);
        } else {
            document.body.classList.remove('timer-semaphore');
            document.body.classList.remove('timer-ready');
            document.body.classList.remove('timer-ignored');
            document.body.classList.remove('ready');
            document.body.classList.remove('set');
            seconds = Timer.getRemainingSeconds();
            Status.setMessage("No tempo");
            Timer.updateButtonStartLabel(Timer.endTime - Timer.startTime);
        }


        if (Timer.isEnding()) {
            document.body.classList.add('timer-ending');
            Status.setMessage("Terminando...");
            if (seconds <= 3) {
                document.body.classList.add('timer-alert');
            }
        } else {
            document.body.classList.remove('timer-ending');
            document.body.classList.remove('timer-alert');
        }

        var timeMeasured = new Date(null);
        timeMeasured.setSeconds(seconds);
        if (seconds < 86400) {
            pos = 11;
            tam = 8;
        }
        if (seconds < 36000) {
            pos = 12;
            tam = 7;
        }
        if (seconds < 3600) {
            pos = 14;
            tam = 5;
        }
        if (seconds < 600) {
            pos = 15;
            tam = 4;
        }

        var valueShow = '';
        valueShow = timeMeasured.toISOString().substr(pos, tam);
        Timer.setText(valueShow);
        document.title = valueShow;
        if (!Timer.isPrepared()) {
            seconds = Timer.getRemainingSecondsDiff();
            if (seconds < 0) {
                Status.setMessage("Tempo esgotado");
                document.body.classList.add('timer-zero');
                Timer.setText('0:00');
                document.title = '0:00';
                if (seconds < -3) {
                    document.body.classList.add('timer-ignored');
                }
            }
        }
    },
    setContent: function (content) {
        document.getElementById('timer').innerHTML = content;
    },
    setText: function (content) {
        Timer.setContent(content);
        document.title = content;
    },
    getRemainingSeconds: function () {
        seconds = Timer.getRemainingSecondsDiff();
        if (seconds < 0) {
            return 0;
        }
        return seconds;
    },
    getRemainingSecondsDiff: function () {
        return Timer.endTime - Timer.localTime
    },
    isSemaphored: function () {
        return Timer.startTime > Timer.localTime;
    },
    isPaused: function () {
        return Timer.preparedTime && Timer.endTime == 0;
    },
    isEnding: function () {
        seconds = Timer.getRemainingSecondsDiff();
        return seconds >= 0 && seconds < 10;
    },
    isRunning: function () {
        seconds = Timer.getRemainingSecondsDiff();
        return seconds > -3 && seconds < 86400;
    },
    isPrepared: function () {
        return Timer.endTime <= 0;
    },
    isOutOfSync: function(){
        return Math.abs(Timer.serverTime - Timer.localTime) >= 1.5 || Timer.localTime > Timer.serverTime;
    },
    start: function () {
        Property.set('timer-start', Timer.localTime + 3, function (data) {
            Timer.preparedTime = data['timer-prepared'];
            Timer.startTime = data['timer-start'];
            Timer.endTime = data['timer-end'];
            Timer.refreshInterface();
            console.log('Timer.start set', data);
        });
    },
    updateButtonStartLabel: function (seconds) {
        try {
            m = Math.floor(seconds / 60);
            s = seconds % 60;
            ss = s > 9 ? s : "0" + s;
            document.querySelectorAll("button.start .text").forEach(function(elem){
                elem.innerHTML = m + ":" + ss;
            })
        } catch (e) {

        }
    }
}

Timer.localTimeMillis = (new Date()).getTime();

var Property = {
    getAll: function (callbackdata) {
        HTTPRequest.getJSON('?up=1', function (data) {
            if (data) {
                Status.setMessageError('');
            } else {
                Status.setMessageError('A conexão com o servidor foi perdida. Verifique o status da rede.');
            }
            
            callbackdata(data);
        });
    },
    set: function (prop_name, prop_value, func) {
        HTTPRequest.getJSON("?set=1&prop_name=" + prop_name + "&prop_value=" + prop_value, func);
    
    },
    setPost: function (postdata, func) {
        HTTPRequest.postJSON("?set=1", postdata, func);
    }

}

var Status = {
    setMessage: function (message) {
        document.getElementById("status").innerHTML = message;
    },
    setMessageError: function (message) {
        document.getElementById("status-error").innerHTML = message;
    },
    setDebugMessage: function (message) {
        document.getElementById("debug").innerHTML = message;
    }
}

var HTTPRequest = {
    getJSON: function (url, callback_func) {
        HTTPRequest._send(url + '&json=1&_=' + (Math.floor(Math.random() * 1000000)) + "&i=" + GLOBAL_ID, callback_func, 'GET', null);
    },
    postJSON: function (url, postdata, callback_func) {
        HTTPRequest._send(url + '&json=1&_=' + (Math.floor(Math.random() * 1000000)) + "&i=" + GLOBAL_ID, callback_func, 'POST', postdata);
    },
    _send: function (url, callback_func, method, data) {
        var xhr = new XMLHttpRequest();
        if (callback_func) {
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    try {
                        var r = JSON.parse(xhr.responseText);
                        callback_func(r);
                    } catch (e) {
                        //console.info(url, '\n', e, xhr.responseText);                        
                        callback_func(null);
                    }
                }
            };
        }
        if (!method){
            method = 'GET';
        }
        xhr.open(method, url);
        if (data){
            xhr.send(data);
        } else {            
            xhr.send();
        }
    }
}

