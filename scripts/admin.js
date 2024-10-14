var textView;
var textEdit;
var saveButton;
var editButton;
var textEditorContents;


function main_admin() {
    saveButton = document.querySelector('.container-toolbar .save');
    editButton = document.querySelector('.container-toolbar .edit');
    textView = document.querySelector('.textView');    
    textEdit = document.querySelector('.textEdit');
    textView.innerHTML = convertTextToButtons(originalTextContent);  
    saveButton.style.display = 'none';
    new nicEditor().panelInstance('nEditor');
    textEditorContents = textEdit.querySelector('.nicEdit-main');
}


function edit(){
    if (textView.style.display  == 'none') {
        modeView();
    }  else {
        modeEdit();
    }     
}
function modeEdit(){
    saveButton.style.display = '';
    editButton.style.display = 'none';
    textView.style.display = 'none';
    textEdit.style.display = 'block';
    textEdit.style.visibility = 'visible';
}
function modeView(){
    saveButton.style.display = 'none';
    editButton.style.display = '';
    textView.style.display = 'block';
    textEdit.style.display = 'none';
    var newText = textEditorContents.innerHTML;
    textView.innerHTML = convertTextToButtons(newText);;
}


function save(){   
    var formData = new FormData();
    formData.append('text-content', textEditorContents.innerHTML);
    Property.setPost(formData, function(){
        modeView();
    })

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


function createBigButton(script_onclick, label, color){
    
    return '<button onclick="' + script_onclick + '" class="big ' + color + '">\
                        <span class="shadow"></span>\
                        <span class="edge"></span>\
                        <span class="front text">' + label + '</span>\
            </button>';
}

window.addEventListener("load", main_admin);