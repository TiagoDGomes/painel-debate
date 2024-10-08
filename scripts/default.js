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

window.addEventListener("load", main);