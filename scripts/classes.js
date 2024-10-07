var Timer = {
    localTime: 0,
    serverTimeMillis: 0,
    _syncing: false,
    pingCount: SYNC_PING_COUNT,
    _interval1s: null,
    startTime: null,
    endTime: null,
    preparedTime: null,
    isSyncing: function() {
        return Timer._syncing;
    },
    setSyncing: function(s) {
        if (s) {
            Status.setMessage("Sincronizando...");
        } else {
            Status.setMessage("");
        }
        Timer._syncing = s;
    },
    prepareTime: function(time_value) {
        //console.log('Timer.prepareTime time_value', time_value);
        Timer.preparedTime = time_value;
        Property.set('timer-prepared', (time_value), function(data) {
            document.body.classList.add('timer-ready');
            Timer.preparedTime = data['timer-prepared'];
            Timer.refreshInterface();
            //console.log('prepareTime set', data);
        });
    },
    syncTicTac: function() {
        //console.log("Timer.syncTicTac");
        document.body.classList.add('timer-sync');
        Timer.localTime = 0;
        Timer.serverTimeMillis = 0;
        Timer.setSyncing(true);
        Timer._syncCount = 0;
        Timer._diffServer = 0;
        Timer._diffSum = 0;
        clearInterval(Timer._interval1s);
        Timer.setContent('');
        Timer._syncTicTacLoop(function() {

        });
    },
    _syncTicTacLoop: function(callback_sync) {
        HTTPRequest.getJSON('?timer=1&syncCount=' + Timer._syncCount + "&localTime=" + Timer.localTime, function(data) {
            //console.log("Timer._syncTicTacLoop", data);
            var diff = data['diff'];
            Timer.serverTimeMillis = data['serverTimeMillis'];
            if (diff != Timer.serverTimeMillis) {
                Timer._diffSum += diff;
            }
            var diffToZero = Timer.serverTimeMillis % 1000;
            Timer.localTime = Timer.serverTimeMillis;
            Timer._syncCount++;
            var nextTimeout = 1000 - diffToZero;
            console.log("Timer._syncCount,diff, diffToZero, nextTimeout", Timer._syncCount, diff, diffToZero, nextTimeout);
            setTimeout(function() {
                if (Timer._syncCount >= Timer.pingCount) {
                    var avgDiff = Timer._diffSum / Timer._syncCount;
                    var miliRounded = Math.round(avgDiff / 1000) * 1000;
                    Timer.localTime += miliRounded;
                    setTimeout(function() {
                        Timer.initTicTac();
                        try {
                            callback_sync();
                        } catch (e) {
                            console.error(e);
                        }
                    }, miliRounded - avgDiff * 2)
                } else {
                    Timer._syncTicTacLoop(callback_sync);
                }
            }, nextTimeout);
        });
    },
    initTicTac: function() {
        console.log('Timer.initTicTac');
        clearInterval(Timer._interval1s);
        Timer.setSyncing(false);
        delete Timer._syncCount;
        delete Timer._diffServer;
        delete Timer._diffSum;
        delete Timer.serverTimeMillis;
        Timer.localTime = Math.round(Timer.localTime / 1000);
        Timer._interval1s = setInterval(function() {
            Timer.localTime += 1;
            Timer.updateData(Timer.refreshInterface);
        }, 1000);
        Timer.updateData(Timer.refreshInterface);
    },
    updateData: function(callback) {
        Property.getAll(function(data) {
            if (data) {
                Timer.preparedTime = data['timer-prepared'];
                Timer.startTime = data['timer-start'];
                Timer.endTime = data['timer-end'];
            } else {
                document.body.classList.add('timer-sync-error');
                if (!Timer.isRunning) {
                    Timer.syncTicTac();
                }
            }
            if (callback) callback();
        });
    },
    refreshInterface: function() {
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
        } else {
            document.body.classList.remove('timer-semaphore');
            document.body.classList.remove('timer-ready');
            document.body.classList.remove('timer-ignored');
            document.body.classList.remove('ready');
            document.body.classList.remove('set');
            seconds = Timer.getRemainingSeconds();
            Status.setMessage("No tempo");
        }
        var valueShow = '';

        if (Timer.isEnding()) {
            document.body.classList.add('timer-ending');
            Status.setMessage("Terminando...");
            if (seconds <= 3) {
                //if (seconds % 2 == 0) {
                document.body.classList.add('timer-alert');
                //} else {
                //    document.body.classList.remove('timer-alert');
                //}
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
    setContent: function(content) {
        document.getElementById('timer').innerHTML = content;
    },
    setText: function(content) {
        Timer.setContent(content);
        document.title = content;
    },
    getRemainingSeconds: function() {
        seconds = Timer.getRemainingSecondsDiff();
        if (seconds < 0) {
            return 0;
        }
        return seconds;
    },
    getRemainingSecondsDiff: function() {
        return Timer.endTime - Timer.localTime
    },
    isSemaphored: function() {
        return Timer.startTime > Timer.localTime;
    },
    isPaused: function() {
        return Timer.preparedTime && Timer.endTime == 0;
    },
    isEnding: function() {
        seconds = Timer.getRemainingSecondsDiff();
        return seconds >= 0 && seconds < 10;
    },
    isRunning: function() {
        seconds = Timer.getRemainingSeconds();
        return seconds > 0 && seconds < 86400;
    },
    isPrepared: function() {
        return Timer.endTime <= 0;
    },
    start: function() {
        Property.set('timer-start', Timer.localTime + 3, function(data) {
            Timer.preparedTime = data['timer-prepared'];
            Timer.startTime = data['timer-start'];
            Timer.endTime = data['timer-end'];
            Timer.refreshInterface();
            console.log('Timer.start set', data);
        });
    }
}

var Property = {
    getAll: function(callbackdata) {
        HTTPRequest.getJSON('?up=1', function(data) {
            if (data) {
                Status.setMessageError('');
            } else {
                Status.setMessageError('A conexão com o servidor foi perdida. Verifique o status da rede.');
            }
            callbackdata(data);
        });
    },
    set: function(prop_name, prop_value, func) {
        HTTPRequest.getJSON("?set=1&prop_name=" + prop_name + "&prop_value=" + prop_value, func);
    }
}

var Status = {
    setMessage: function(message) {
        document.getElementById("status").innerHTML = message;
    },
    setMessageError: function(message) {
        document.getElementById("status-error").innerHTML = message;
    },
}

var HTTPRequest = {
    getJSON: function(url, callback_func) {
        HTTPRequest._send(url + '&json=1&_=' + Timer.localTime + "&i=" + GLOBAL_ID, callback_func);
    },
    _send: function(url, callback_func) {
        var xhr = new XMLHttpRequest();
        if (callback_func) {
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    try {
                        var r = JSON.parse(xhr.responseText);
                        callback_func(r);
                    } catch (e) {
                        console.info(url, '\n', e, xhr.responseText);
                        callback_func(null);
                    }
                }
            };
        }
        xhr.open('GET', url);
        xhr.send();
    }
}