function preparar_cronometro(tempo_definido) {
    simple_ajax('gerenciar.json.php?acao=preparar&id=' + global_id + '&codigo_chave=' + codigo_chave + '&tempo_definido=' + tempo_definido);
}

function pausar_cronometro() {
    simple_ajax('gerenciar.json.php?acao=preparar&id=' + global_id + '&codigo_chave=' + codigo_chave + '&tempo_definido=' + (fim - tempo_referencia));
}

function iniciar_cronometro() {
    simple_ajax('gerenciar.json.php?acao=iniciar&id=' + global_id + '&codigo_chave=' + codigo_chave);
}

function ativar_rodada(cod_rodada_atual) {
    simple_ajax('gerenciar.json.php?acao=rodada&id=' + global_id + '&codigo_chave=' + codigo_chave + '&codigo_rodada_atual=' + cod_rodada_atual);
    if (cod_rodada_atual) {
        enviar_mensagem_manual('Atenção! Sorteio de número aleatório!', 'Mova o cursor do seu mouse para sortear um número. Assim que parar de movimentar, seu número aleatório será registrado. Você pode fazer isso quantas vezes desejar.');
        document.getElementById('roleta_ativa').disabled = true;
    } else {
        document.getElementById('roleta_ativa').disabled = false;

        if (!document.getElementById('chk_auto_enviar').checked) {
            enviar_mensagem_manual('Aguarde...', 'Aguardando mediador...')
        }
    }
}

function ev_alt_msg() {
    document.getElementById('btn_enviar_mensagem_manual').style.fontWeight = 'bold';
    document.getElementById('btn_enviar_mensagem_manual').style.color = 'yellow';
    document.getElementById('btn_enviar_mensagem_manual').style.backgroundColor = 'green';

    //document.getElementById('btn_enviar_mensagem_manual').disabled = false;
}

function enviar_mensagem_manual(marcador_manual, mensagem_manual) {
    simple_ajax('gerenciar.json.php?acao=mensagem&id=' + global_id +
        '&codigo_chave=' + codigo_chave +
        '&marcador_manual=' + encodeURIComponent(marcador_manual) +
        '&mensagem_manual=' + encodeURIComponent(mensagem_manual)
    );
    document.getElementById('btn_enviar_mensagem_manual').style.fontWeight = 'normal';
    document.getElementById('btn_enviar_mensagem_manual').style.color = 'black';
    document.getElementById('btn_enviar_mensagem_manual').style.backgroundColor = '';
    //document.getElementById('btn_enviar_mensagem_manual').disabled = true;
}

function escolher_roleta(valor) {
    console.log('roleta', valor);
    //var btn_apagar_roleta = document.getElementById('btn_apagar_roleta');
    var cont_ativar_rodada = document.getElementById('cont_ativar_rodada');

    var form_roleta_container = document.getElementById('form_roleta_container');
    var itens_roleta_container = document.getElementById('itens_roleta_container');
    if (valor == '-') {
        cont_ativar_rodada.style.display = 'none';
        form_roleta_container.style.display = 'block';
    } else {
        cont_ativar_rodada.style.display = 'initial';
        form_roleta_container.style.display = 'none';
        linhas = document.querySelectorAll('#itens_roleta_container tr');
        var index = 0,
            length = linhas.length;
        for (; index < length; index++) {
            // linhas[index].style.transition = "opacity 0.5s linear 0s";
            // linhas[index].style.opacity = 0.5;
            linhas[index].style.display = 'none';
        }
        linhas = document.querySelectorAll('#itens_roleta_container .' + valor);
        index = 0;
        length = linhas.length;
        for (; index < length; index++) {
            linhas[index].style.display = 'table-row';
        }

    }

    itens_roleta_container.style.display = cont_ativar_rodada.style.display;
}

function arquivo_roleta_escolhido() {
    console.log('arquivo', document.getElementById('roleta_upload'));
}

function escolher_novo_arquivo_roleta() {
    document.getElementById('roleta_upload').click();
    document.getElementById('roleta_upload').onchange = function() {
        //alert('Selected file: ' + this.value);
        if (this.value) {
            document.getElementById('form_roleta').submit();
        }
    };

}


function executar_transferencia_sorteio_em_mensagem(numero_sorteado, cod_rodada) {
    var linha = document.getElementById(cod_rodada + '_' + numero_sorteado);
    var marcador = roleta_ativa.value.split('_').join(' ');
    var conteudo = linha.querySelectorAll('.conteudo')[0].innerText;
    var marcador_manual = document.getElementById('marcador_manual');
    var mensagem_manual = document.getElementById('mensagem_manual');

    marcador_manual.value = marcador;
    mensagem_manual.value = conteudo;

    var chk_auto_enviar = document.getElementById('chk_auto_enviar');
    if (chk_auto_enviar.checked) {
        enviar_mensagem_manual(marcador_manual.value, mensagem_manual.value)
    } else {
        ev_alt_msg();
    }
}