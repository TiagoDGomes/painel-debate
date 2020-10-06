var somarArray = function(arr) {
    return arr.reduce((a, b) => a + b, 0);
}

var elem = function(id) {
    return document.getElementById(id);
}

function enviarDados(url, json_success) {
    var xhr = new XMLHttpRequest();
    if (json_success) {
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                try {
                    var r = JSON.parse(xhr.responseText);
                    //console.log(url, '\n', r);
                    json_success(r);
                } catch (e) {
                    console.error(url, '\n', e, xhr.responseText);

                    json_success({});
                }
            }
        };
    }
    xhr.open('GET', url);
    xhr.send();
}