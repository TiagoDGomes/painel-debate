
var alternarQRCodeStatus = false;



function main() {
    document.getElementById("visible").style.display = '';
    Status.setMessageError('');
    Timer.syncTicTac();
    if (window.obsstudio) {
        window.obsstudio.getStatus(function (status) {
            document.getElementById('debug').innerHTML = (status);
        })
    }
    var qrcode = new QRCode(document.getElementById("qrcode"), {
        text: CURRENT_URL,
        width: 128,
        height: 128,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
    var qrcode_elem = document.getElementById("qrcode"); 
    qrcode_elem.title = "";   
}

function alternarQRCode() {
    var qrcode = document.getElementById('qrcode');
    alternarQRCodeStatus = !alternarQRCodeStatus;
    qrcode.style.display = alternarQRCodeStatus ? 'block' : 'none';
}

function fullScreen() {
    if (document.fullscreenElement != null) {
        document.exitFullscreen();
    } else {
        document.body.requestFullscreen();
    }
}
function str_pad_left(string, pad, length) {
    return (new Array(length + 1).join(pad) + string).slice(-length);
  }
  
function convertToHumanTimeFormat(minutes, seconds){
    return minutes + ':' + str_pad_left(seconds, '0', 2);
} 

function removeHtmlTags(originalText){
    return originalText.replace(/(<([^>]+)>)/ig, "");
}

function nl2br(originalText){
    return originalText.replaceAll('\n', '<br>');
}



window.addEventListener("load", main);