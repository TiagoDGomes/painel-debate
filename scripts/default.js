
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
    document.querySelectorAll('.container-admin .textbox')[0].innerHTML = convertTextToButtons(originalTextContent)
    new nicEditor().panelInstance('nEditor');
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
function edit(){
    var textbox = document.querySelector('.textbox');
    if (textbox.style.display  == 'none') {
        modeView();
    }  else {
        modeEdit();
    }     
}
function modeEdit(){
    var textbox = document.querySelector('.textbox');
    var textedit = document.querySelector('.textedit');
    textbox.style.display = 'none';
    textedit.style.display = 'block';
    textedit.style.visibility = 'visible';
}
function modeView(){
    var textbox = document.querySelector('.textbox');
    var textedit = document.querySelector('.textedit');
    var texteditcontents = textedit.querySelector('.nicEdit-main');
    textbox.style.display = 'block';
    textedit.style.display = 'none';
    var newText = texteditcontents.innerHTML;
    textbox.innerHTML = convertTextToButtons(newText);;
}


function save(){    
    var textedit = document.querySelector('.textedit');
    var texteditcontents = textedit.querySelector('.nicEdit-main');
    var formData = new FormData();
    formData.append('text-content', texteditcontents.innerHTML);
    Property.setPost(formData, function(){
        modeView();
    })

}
function removeHtmlTags(originalText){
    return originalText.replace(/(<([^>]+)>)/ig, "");
}
function nl2br(originalText){
    return originalText.replaceAll('\n', '<br>');
}

function convertTextToButtons(originalText){
    //newText = nl2br(originalText);
    newText = originalText.replaceAll(/\[([0-9]*|iniciar|start|ss|pause|P|[0-9]*:[0-9]*)\]/gi, function(e){
        var time = e.replace(/(\[|\])/ig, "");
        if (time == 'iniciar' || time == 'start' ){
            return createBigButton("Timer.start()", "Iniciar", 'green');
        }
        if (time == 'ss'){
            return createBigButton("Timer.start()", "Iniciar", 'green start');
        }        
        if (time == 'pause'|| time == 'P'){
            return createBigButton("Timer.prepareTime(Timer.getRemainingSeconds())", "Pause", 'pause');
        }
        var minutes;
        var seconds;
        if (time.includes(':')){
            minutes = time.split(':')[0];
            seconds = time.split(':')[1];
            time = (seconds * 1) + (minutes * 60);
        } else {
            minutes = Math.floor(time / 60);
            seconds = time - minutes * 60;
        }
        var timeH = convertToHumanTimeFormat(minutes, seconds);
        console.log('time', time);
        return createBigButton("Timer.prepareTime(" + time + ")", timeH, 'prepared');
    });
    return newText;
}

function str_pad_left(string, pad, length) {
    return (new Array(length + 1).join(pad) + string).slice(-length);
  }
  
function convertToHumanTimeFormat(minutes, seconds){
    return minutes + ':' + str_pad_left(seconds, '0', 2);
} 

function createBigButton(script_onclick, label, color){
    
    return '<button onclick="' + script_onclick + '" class="big ' + color + '">\
                        <span class="shadow"></span>\
                        <span class="edge"></span>\
                        <span class="front text">' + label + '</span>\
            </button>';
}


window.addEventListener("load", main);