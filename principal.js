var tempo_referencia = 0;
var tempo_inicio = 0;
var inicio = 0;
var fim = 0;
var codigo_rodada_atual = null;
var codigo_rodada_anterior = null;
var rodada_atual = null;
var mensagem_anterior = 0;

var reg_alternador_mensagem = 0;

var tempo_referencia_anterior = 0;
var sync = false;

function touch_server() {

    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            var dados = JSON.parse(xhr.responseText);
            //vdebug(dados);

            if (tempo_referencia == 0) {
                tempo_referencia = dados['tempo_referencia'];
            }
            tempo_inicio = 1 * dados['tempo_inicio'];
            inicio = 1 * dados['inicio'];
            fim = 1 * dados['fim'];
            var bloco_esquerda = document.querySelectorAll('.bloco.esquerda')[0];

            if (dados['codigo_rodada_atual']) {
                bloco_esquerda.classList.add('ativo');
                codigo_rodada_atual = dados['codigo_rodada_atual'];
                definir_numero_sorteado(0);
            } else {
                bloco_esquerda.classList.remove('ativo');
                codigo_rodada_atual = null;
            }

            if (codigo_rodada_atual != codigo_rodada_anterior) {
                obter_resultado_rodada();
            }

            if (pagina_atual == 'painel') {
                console.log('mensagem', dados['mensagem']);
                var mensagem_painel = document.getElementById('mensagem');
                var marcador_painel = document.getElementById('marcador');
                if (dados['mensagem'] != '&nbsp;' && dados['mensagem'].length > 1) {



                    // if (dados['mensagem'] != mensagem_anterior) {
                    //     reg_alternador_mensagem = reg_alternador_mensagem % 2;
                    //     marcador_painel.classList.add('alt0');
                    // } else {
                    //     marcador_painel.classList.remove('alt0');
                    // }
                    mensagem_anterior = dados['mensagem'];

                    mensagem_painel.classList.remove('oculto');
                    marcador_painel.classList.remove('oculto');
                } else {
                    mensagem_painel.classList.add('oculto');
                    marcador_painel.classList.add('oculto');
                }
                mensagem_painel.innerHTML = dados['mensagem'];
                marcador_painel.innerHTML = dados['marcador'];
                //marcador_painel.style.display = mensagem_painel.style.display;

            } else {

            }


            if (dados['rodada_atual'].length > 0) {
                limpar_numeros_rodada()
                rodada_atual = dados['rodada_atual'];
                for (var i = 0; i < rodada_atual.length; i++) {
                    adicionar_numero_aleatorio_rodada(rodada_atual[i]['numero'], rodada_atual[i]['pessoal']);
                }
            }
            codigo_rodada_anterior = codigo_rodada_atual;

        }
    };
    xhr.open('GET', 'painel.json.php?id=' + global_id);
    xhr.send();

    // Relogio



}

function limpar_numeros_rodada() {
    var div_numeros_contidos = document.getElementById('numeros_obtidos');
    div_numeros_contidos.innerHTML = '';
}

function adicionar_numero_aleatorio_rodada(numero, pessoal) {
    var div_numeros_contidos = document.getElementById('numeros_obtidos');
    var span_numero_obtido = document.createElement('span');
    span_numero_obtido.innerHTML = numero;
    span_numero_obtido.classList.add('numero_obtido');
    if (pessoal == '1') {
        span_numero_obtido.classList.add('pessoal');
    }
    div_numeros_contidos.appendChild(span_numero_obtido);
}


function touch_1s() {
    if (tempo_referencia != 0) {
        tempo_referencia++;
        if (fim > 0) {
            segundos = fim - tempo_referencia;
        } else {
            segundos = tempo_inicio;
        }
        atualizar_contador(segundos);
    }
}




function atualizar_contador(segundos) {

    var display = document.getElementById('cronometro');
    if (tempo_inicio < segundos - 2) {
        return;
    }
    var preparar = tempo_inicio == segundos - 2;
    var apontar = tempo_inicio == segundos - 1;
    if (preparar || segundos < 0) {
        display.style.backgroundColor = 'red';
    } else if (apontar) {
        display.style.backgroundColor = 'yellow';
    } else if (segundos <= 4) {
        display.style.backgroundColor = '#79b100';
    } else {
        if (inicio == 0) {
            display.style.backgroundColor = 'silver';
        } else {
            display.style.backgroundColor = 'green';
        }

    }

    var valor_mostrado = '0:00';
    if (segundos >= 0) {

        //console.log('segundos', segundos);
        //if (segundos > 0) {    
        var tempo_mensurado = new Date(null);
        tempo_mensurado.setSeconds(segundos);
        if (segundos < 600) {
            pos = 15;
            tam = 4;
        } else {
            pos = 14;
            tam = 5;
        }
        valor_mostrado = tempo_mensurado.toISOString().substr(pos, tam);
    }

    display.innerHTML = valor_mostrado;
    document.title = valor_mostrado;
    //}
}

function definir_numero_sorteado(numero) {
    var c_numero_sorteado = document.getElementById('numero_sorteado');
    bloco_direita = document.querySelectorAll('.bloco.direita')[0];
    //console.log(bloco_direita);
    if (numero != 0) {
        bloco_direita.classList.add('ativo');
        c_numero_sorteado.innerHTML = numero;
    } else {
        bloco_direita.classList.remove('ativo');
        //c_numero_sorteado.innerHTML = '--';
    }
}


function obter_resultado_rodada() {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            var dados = JSON.parse(xhr.responseText);
            if (dados['numero_perguntas'] > 0) {
                //limpar_numeros_rodada();
                for (var i = 0; i < dados['numeros']; i++) {
                    adicionar_numero_aleatorio_rodada(dados['numeros'][i]);

                }
                numero_sorteado = dados['resto_divisao'] + 1;
                definir_numero_sorteado(numero_sorteado);
                if (pagina_atual == 'gerenciar') {
                    executar_transferencia_sorteio_em_mensagem(numero_sorteado, dados['codigo_rodada']);
                }
            }
            //vdebug(dados);
        }
    };
    xhr.open('GET', 'obter_resultado_rodada.json.php?id=' + global_id + '&codigo_rodada=' + codigo_rodada_anterior);
    xhr.send();
}


function simple_ajax(url) {
    var xhr = new XMLHttpRequest();
    // xhr.onreadystatechange = function() {
    //     if (xhr.readyState === 4) {

    //     }
    // };
    xhr.open('GET', url);
    xhr.send();
}

function vdebug(obj) {
    try {
        console.log(obj);
        document.getElementById('debug').innerHTML = JSON.stringify(obj);
    } catch (e) {

    }
}

var touch1s_init;

var touch_server_init;


// Sincronismo de tempo:

var count_sync_init = 0;

var real_timestamp_server = 0;
var last_diff = 0;
var next_timestamp_sec = 0;
var interval_start = 0;
var sum_interval_start = 0;

function ping(timestamp) {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            var dados = JSON.parse(xhr.responseText);
            var result = document.getElementById('result');
            //result.innerHTML = dados['diff'] + 'ms';
            real_timestamp_server = dados['t'];
            last_diff = dados['diff'];
            if (timestamp == 1000) {
                ping(real_timestamp_server);
            }
            next_timestamp_sec = Math.floor(real_timestamp_server / 1000) * 1000 + 1000;
            interval_start = next_timestamp_sec - real_timestamp_server;
            sum_interval_start += interval_start;
            console.log('next_timestamp_sec', next_timestamp_sec);
            console.log('real_timestamp_server', real_timestamp_server);
            console.log('interval_start', interval_start);
        }
    };
    xhr.open('GET', 'ping.php?t=' + timestamp);
    xhr.send();
}

var sync_init = setInterval(function() {
    if (count_sync_init >= 5) {
        var avg_interval_start = sum_interval_start / 5;
        setTimeout(function() {
            touch1s_init = setInterval(touch_1s, 1000);
            touch_server_init = setInterval(touch_server, 500);
        }, avg_interval_start);
        clearInterval(sync_init);
        if (pagina_atual == 'painel') {
            document.getElementById('mensagem').innerHTML = '';
        }
    } else {
        ping(real_timestamp_server + 1000 - last_diff, true);
    }
    count_sync_init++;
}, 1000);