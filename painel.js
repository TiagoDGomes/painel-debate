var numero_aleatorio = 0;
var numero_aleatorio_anterior = null;
var numero_aleatorio_anterior_2 = null;


function mandar_aleatorio() {
    simple_ajax('mandar_aleatorio.json.php?id=' + global_id + '&codigo_rodada_atual=' + codigo_rodada_atual + '&numero_aleatorio=' + numero_aleatorio);
}




setInterval(function() {
    if (numero_aleatorio_anterior_2 != numero_aleatorio_anterior) {
        if (numero_aleatorio_anterior == numero_aleatorio) {
            if (codigo_rodada_atual) {
                mandar_aleatorio();
            }
        }
    }
    numero_aleatorio_anterior_2 = numero_aleatorio_anterior;
    numero_aleatorio_anterior = numero_aleatorio;
}, 500);



(function() {
    document.onmousemove = handleMouseMove;

    function handleMouseMove(event) {
        var eventDoc, doc, body;

        event = event || window.event; // IE-ism

        // If pageX/Y aren't available and clientX/Y are,
        // calculate pageX/Y - logic taken from jQuery.
        // (This is to support old IE)
        if (event.pageX == null && event.clientX != null) {
            eventDoc = (event.target && event.target.ownerDocument) || document;
            doc = eventDoc.documentElement;
            body = eventDoc.body;

            event.pageX = event.clientX +
                (doc && doc.scrollLeft || body && body.scrollLeft || 0) -
                (doc && doc.clientLeft || body && body.clientLeft || 0);
            event.pageY = event.clientY +
                (doc && doc.scrollTop || body && body.scrollTop || 0) -
                (doc && doc.clientTop || body && body.clientTop || 0);
        }

        // Use event.pageX / event.pageY here
        numero_aleatorio = (Math.floor(event.pageX * event.pageY)) % 1000;
        var div_numero_aleatorio = document.getElementById('numero_aleatorio')
        if (numero_aleatorio == 0) {
            div_numero_aleatorio.innerHTML = '--';
        } else {
            div_numero_aleatorio.innerHTML = numero_aleatorio;
        }
    }
})();