Relogio.sincronizar(function() {

});


var tempoPreparado;
try {
    elem('btn_enviar_mensagem_manual').addEventListener('click', function(event) {
        var mensagem_titulo = elem('txt_mensagem_titulo').value;
        var mensagem_conteudo = elem('txt_mensagem_conteudo').value;
        enviarDados(url_base + 'json=true&mt=' + encodeURIComponent(mensagem_titulo) + '&mc=' + encodeURIComponent(mensagem_conteudo), function(dados) {
            // Cronometro.iniciar();
        });

    });
    elem('btn_limpar_mensagem_manual').addEventListener('click', function(event) {
        elem('txt_mensagem_titulo').value = '';
        elem('txt_mensagem_conteudo').value = '';
        enviarDados(url_base + 'json=true&mz=1', function(dados) {
            // Cronometro.iniciar();
        });

    });
    elem('chk_ativar_sorteio').addEventListener('click', function(event) {
        console.log('chk_ativar_sorteio', this.checked);
        var roleta_ativa = elem('select_roleta_ativa');
        roleta_ativa.disabled = true;
        if (this.checked) {
            enviarDados(url_base + 'json=true&iniciarsorteio=' + roleta_ativa.value, function(dados) {

            });
        } else {
            enviarDados(url_base + 'json=true&terminarsorteio=1', function(dados) {
                roleta_ativa.disabled = false;

                if (dados['numero_sorteado']) {

                    if (roleta_ativa.value > 0) {
                        var qid = 'roleta_item_' + roleta_ativa.value + '_' + dados['numero_sorteado']
                        var item_roleta = elem(qid);
                        var item_roleta_conteudo = item_roleta.querySelectorAll('.conteudo')[0];
                        elem('txt_mensagem_titulo').value = roleta_ativa.querySelectorAll('option:checked')[0].innerHTML;
                        elem('txt_mensagem_conteudo').value = item_roleta_conteudo.innerHTML;

                        if (elem('chk_auto_enviar').checked) {
                            Mensagem.definir(elem('txt_mensagem_titulo').value, elem('txt_mensagem_conteudo').value);
                        }
                    }

                } else {
                    alert('Nenhum número foi sorteado. É preciso deixar um tempo suficiente para que os usuários possam gerar números com a movimentação do mouse.');
                }


            });
        }
    });
    elem('btn_preparar').addEventListener('click', function(event) {
        tempoPreparado = (elem('select_tempo_definido').value);
        enviarDados(url_base + 'json=true&pc=' + tempoPreparado, function(dados) {
            //Cronometro.preparar(tempoPreparado);
        });
    });
    elem('chk_iniciar').addEventListener('click', function(event) {
        if (this.checked) {
            enviarDados(url_base + 'json=true&st=1', function(dados) {
                // Cronometro.iniciar();
            });
        } else {
            tempoPreparado = Cronometro.tempoFim - Relogio.tempoLocal;
            enviarDados(url_base + 'json=true&pc=' + tempoPreparado, function(dados) {
                // Cronometro.pausar();
            });
        }
    });
} catch (error) {
    console.log("listener error", error);
}


function escolher_roleta(valor) {

    linhas = document.querySelectorAll('#itens_roleta_container tr');
    var index = 0,
        length = linhas.length;
    for (; index < length; index++) {
        linhas[index].style.display = 'none';
    }
    linhas = document.querySelectorAll('#itens_roleta_container .roleta_' + valor);
    index = 0;
    length = linhas.length;
    for (; index < length; index++) {
        linhas[index].style.display = 'table-row';
    }
}