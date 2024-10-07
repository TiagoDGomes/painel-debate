var countLostFocus = 0;

function main(){
    document.getElementById("visible").style.display = '';
    Status.setMessageError('');
    Timer.syncTicTac();
    if (window.obsstudio) {
        document.body.classList.add('obs');
        window.obsstudio.getStatus(function(status) {
            document.getElementById('debug').innerHTML = (status);
        })
    }
}

function checkFocus() {
    console.log('checkFocus', countLostFocus);
    countLostFocus++;
    if (countLostFocus > 4) {
        if (document.hasFocus() && !Timer.isSyncing()) {
            Timer.syncTicTac(function () {
                console.log('focus', document.hasFocus());
            })
        }
        countLostFocus = 0;
    }
}

document.addEventListener("visibilitychange", checkFocus);
window.addEventListener("focus", checkFocus);
window.addEventListener("blur", checkFocus);
window.addEventListener("load", main);