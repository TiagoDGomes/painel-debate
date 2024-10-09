
var alternarQRCodeStatus = false;

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
    var qrcode = new QRCode(document.getElementById("qrcode"), {
        text: CURRENT_URL,
        width: 128,
        height: 128,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });    
    var qrcode_elem = document.getElementById("qrcode");
    qrcode_elem.title="";
}


function alternarQRCode(){
    var qrcode = document.getElementById('qrcode');
    alternarQRCodeStatus = ! alternarQRCodeStatus;
    qrcode.style.display = alternarQRCodeStatus ? 'block': 'none';    
}

function fullScreen(){
    if (document.fullscreenElement != null){
        document.exitFullscreen();
    } else {
        document.getElementById('main').requestFullscreen();
    }    
}

window.addEventListener("load", main);