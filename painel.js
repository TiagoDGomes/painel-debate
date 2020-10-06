var Painel = {
    tratarAtualizacoes: function(dados) {
        if (dados['timestamp']) {
            Cronometro.tratarAtualizacoes(dados);
            Roleta.tratarAtualizacoes(dados);
            Mensagem.tratarAtualizacoes(dados);
        }
    }
}

var Relogio = {
    tempoLocal: 0,
    tempoServidor: 0,
    sincronizar: function(callback_sincronizar) {
        elem('cronometro').innerHTML = '--:--';
        elem('mensagem').readonly = true;
        Relogio.sincronizando = true;
        document.body.classList.add('sincronizando');
        Relogio.tempoServidor = 0;
        Relogio.tempoLocal = 0;
        Relogio._contadorSync = 0;
        Relogio._diffServer = 0;
        Relogio._somaDiff = 0;
        clearInterval(Relogio._intervalo1s);
        Relogio._sync(callback_sincronizar);
    },
    _sync: function(callback_sincronizar) {
        enviarDados('ping.php?id=' + Relogio._contadorSync + '&tl=' + Relogio.tempoLocal, function(dadosRecebidos) {
            var diff = dadosRecebidos['diff'];
            var diferencaParaZerar = 0;
            Relogio.tempoServidor = dadosRecebidos['ts'] * 1;
            if (diff != Relogio.tempoServidor) {
                Relogio._somaDiff += diff;
            }
            diferencaParaZerar = Relogio.tempoServidor % 1000;
            Relogio.tempoLocal = Relogio.tempoServidor;
            setTimeout(function() {
                if (Relogio._contadorSync > Relogio.numeroPings) {
                    var mediaDiff = Relogio._somaDiff / Relogio._contadorSync;
                    console.log("mediaDiff", mediaDiff);
                    var miliRedondo = Math.round(mediaDiff / 1000) * 1000;
                    Relogio.tempoLocal += miliRedondo;
                    setTimeout(function() {
                        Relogio.iniciar();
                        try {
                            callback_sincronizar();
                        } catch (e) {
                            console.error(e);
                        }
                    }, miliRedondo - mediaDiff * 2);
                } else {
                    Relogio._sync(callback_sincronizar);
                }
            }, 1000 - diferencaParaZerar);
            Relogio._contadorSync++;
        });
    },

    iniciar: function() {
        clearInterval(Relogio._intervalo1s);
        Relogio.tempoLocal = Math.round(Relogio.tempoLocal / 1000);
        Relogio.sincronizando = false;
        document.body.classList.remove('sincronizando');
        delete Relogio._contadorSync;
        delete Relogio._diffServer;
        delete Relogio._somaDiff;
        delete Relogio.tempoServidor;

        elem('mensagem').readonly = false;

        Relogio._intervalo1s = setInterval(function() {
            Relogio.tempoLocal += 1;
            //console.log('Relogio', Relogio);
            Cronometro.atualizar();
        }, 1000);
    },
    _intervalo1s: null,
    numeroPings: 3,
    _init: function() {

    }
};


var Cronometro = {
    tempoInicio: null,
    tempoFim: null,
    tempoPreparado: null,
    modoRelogio: true,
    tratarAtualizacoes: function(dados) {
        var info = dados['update_info'];
        var cronometro_tempo_preparado = info['cronometro_tempo_preparado'] * 1;
        var cronometro_tempo_inicio = info['cronometro_tempo_inicio'] * 1;
        var cronometro_tempo_fim = info['cronometro_tempo_fim'] * 1;

        if (dados['simul']) {
            Cronometro.modoRelogio = false;
        }
        if (cronometro_tempo_preparado > 0) {
            Cronometro.modoRelogio = false;
            Cronometro.tempoPreparado = cronometro_tempo_preparado;
        } else {
            Cronometro.tempoPreparado = null;
        }
        Cronometro.tempoInicio = cronometro_tempo_inicio > 0 ? cronometro_tempo_inicio : null;
        Cronometro.tempoFim = cronometro_tempo_fim > 0 ? cronometro_tempo_fim : null;

        if (acessando_como_gerencia) {
            if (Cronometro.tempoFim >= Relogio.tempoLocal) {
                elem('chk_iniciar').checked = true;
            } else {
                elem('chk_iniciar').checked = false;
            }
        }
    },
    preparar: function(tempoPreparado) {
        tempoPreparado = tempoPreparado * 1;
        if (Relogio.sincronizando) {
            throw new Error('O relógio está sendo sincronizado.');
        }
        if (tempoPreparado >= 0) {
            Cronometro.tempoPreparado = tempoPreparado;
            Cronometro.tempoInicio = null;
            Cronometro.tempoFim = null;
            Cronometro.atualizar();
        } else {
            throw new Error('Valor inválido para preparo de cronômetro.')
        }
        Cronometro.modoRelogio = false;
    },
    iniciar: function() {
        if (Relogio.sincronizando) {
            throw new Error('O relógio está sendo sincronizado.');
        }
        var adicional = 3;
        Cronometro.tempoInicio = Relogio.tempoLocal + adicional;
        Cronometro.tempoFim = Cronometro.tempoInicio + Cronometro.tempoPreparado;
        Cronometro.modoRelogio = false;
        Cronometro.atualizar();
    },
    pausar: function() {
        if (Relogio.sincronizando) {
            throw new Error('O relógio está sendo sincronizado.');
        }
        if (Cronometro.tempoInicio) {
            Cronometro.tempoPreparado = Cronometro.tempoFim - Relogio.tempoLocal;
            Cronometro.tempoInicio = null;
            Cronometro.tempoFim = null;
            Cronometro.atualizar();
        } else {
            throw new Error('Cronômetro não iniciado.')
        }
    },
    parar: function() {

    },
    atualizar: function() {
        var segundos = 0;

        if (Cronometro.modoRelogio) {
            var relogio = new Date(null);
            relogio.setSeconds(Relogio.tempoLocal);
            elem('cronometro').innerHTML = (relogio + '').substr(16, 8);
            document.body.classList.add('modo-relogio');
        } else {
            //console.log("CF", Cronometro.tempoFim);
            //console.log("RL", Relogio.tempoLocal);
            document.body.classList.remove('modo-relogio');
            if (Cronometro.tempoInicio > Relogio.tempoLocal) {
                document.body.classList.add('cronometro-semaforo');
                segundos = Cronometro.tempoPreparado;
                var segundosRegressiva = Cronometro.tempoInicio - Relogio.tempoLocal;
                //console.log("segundosRegressiva", segundosRegressiva);
                if (segundosRegressiva == 2) {
                    document.body.classList.remove('amarelo');
                    document.body.classList.add('vermelho');
                } else if (segundosRegressiva == 1) {
                    document.body.classList.remove('vermelho');
                    document.body.classList.add('amarelo');
                }
            } else if (Cronometro.tempoPreparado && Cronometro.tempoFim == null) {
                segundos = Cronometro.tempoPreparado;
                document.body.classList.add('cronometro-preparado');
                document.body.classList.remove('cronometro-ignorado');
            } else {
                document.body.classList.remove('cronometro-semaforo');
                document.body.classList.remove('cronometro-preparado');
                document.body.classList.remove('cronometro-ignorado');
                document.body.classList.remove('amarelo');
                document.body.classList.remove('vermelho');
                segundos = Cronometro.tempoFim - Relogio.tempoLocal;
            }
            document.body.classList.remove('cronometro-alerta-fim');
            if (segundos >= 0 && segundos < 10) {
                document.body.classList.add('cronometro-alerta-fim');
            }
            //console.log("segundos", segundos);
            var valorMostrado = '??:??';

            if (segundos >= 0 && segundos < 86400) {
                var tempoMensurado = new Date(null);
                tempoMensurado.setSeconds(segundos);
                if (segundos < 86400) {
                    pos = 11;
                    tam = 8;
                }
                if (segundos < 36000) {
                    pos = 12;
                    tam = 7;
                }
                if (segundos < 3600) {
                    pos = 14;
                    tam = 5;
                }
                if (segundos < 600) {
                    pos = 15;
                    tam = 4;
                }
                // if (segundos < 60) {
                //     pos = 17;
                //     tam = 2;
                // }
                valorMostrado = tempoMensurado.toISOString().substr(pos, tam);
                document.body.classList.remove('cronometro-zerado');
                elem('cronometro').innerHTML = valorMostrado;
                document.title = valorMostrado;
            } else if (segundos < 0) {
                document.body.classList.add('cronometro-zerado');
                elem('cronometro').innerHTML = "0:00";
                document.title = '0:00';
                if (segundos < -3) {
                    document.body.classList.add('cronometro-ignorado');
                }
            }

        }
    },
    _init: function() {


    }
};


var Mensagem = {
    definir: function(titulo, conteudo) {
        if (acessando_como_gerencia) {
            enviarDados(url_base + 'json=true&mt=' + encodeURIComponent(titulo) + '&mc=' + encodeURIComponent(conteudo), function(dados) {

            });
        } else {
            if (elem('titulo').innerHTML != titulo) {
                elem('titulo').innerHTML = titulo;
            }
            if (elem('mensagem').innerHTML != conteudo) {
                elem('mensagem').innerHTML = conteudo;
            }
            document.body.classList.remove('sem-mensagem');
        }

    },
    limpar: function() {
        if (acessando_como_gerencia) {
            enviarDados(url_base + 'json=true&mz=1', function(dados) {

            });
        } else {
            elem('titulo').innerHTML = '';
            elem('mensagem').innerHTML = '';
            document.body.classList.add('sem-mensagem');
        }
    },
    tratarAtualizacoes: function(dados) {
        var info = dados['update_info'];
        if (acessando_como_usuario) {
            if (info['mensagem_titulo'] != null || info['mensagem_conteudo'] != null) {
                Mensagem.definir(info['mensagem_titulo'], info['mensagem_conteudo']);
            } else {
                Mensagem.limpar();
            }
        } else {
            if (info['mensagem_titulo'] != elem('txt_mensagem_titulo').value || info['mensagem_conteudo'] != elem('txt_mensagem_conteudo').value) {
                elem('btn_enviar_mensagem_manual').classList.add('destaque');
            } else {
                elem('btn_enviar_mensagem_manual').classList.remove('destaque');
            }
        }
    }
}


var Roleta = {
    _sorteioAtivo: false,
    numeros: [],
    numeroMaximo: 0,
    _ultimoNumeroSorteado: null,
    contadorMovimentacao: 0,
    contadorExibicaoNumero: 0,
    bolasAleatorias: false,
    tratarAtualizacoes: function(dados) {
        var info = dados['update_info'];
        if (info['codigo_sorteio_atual'] > 0) {
            Roleta.iniciarSorteio(999);
            Roleta.limparNumeroSorteado();
            Roleta.contadorExibicaoNumero = 0;
            document.body.classList.remove('exibicao-numero-sorteado');
        } else {
            Roleta.terminarSorteio();
            var numero = info['ultimo_numero_sorteado'];
            var codigo_sorteio = info['codigo_sorteio_anterior'];
            if (numero > 0) {
                elem('numero_sorteado').innerHTML = numero;
                elem('titulo_numero_sorteado').innerHTML = codigo_sorteio + 'º sorteio';
                if (acessando_como_gerencia) {
                    var el = 'chk_roleta_item_' + elem('select_roleta_ativa').value + '_' + numero
                    console.log(el);
                }
            }
            Roleta._ultimoNumeroSorteado = info['ultimo_numero_sorteado'];
            Roleta.contadorExibicaoNumero++;
            if (Roleta.contadorExibicaoNumero < 15) {
                document.body.classList.add('exibicao-numero-sorteado');
            } else {
                document.body.classList.remove('exibicao-numero-sorteado');
            }
        }

    },
    limparNumeroSorteado: function() {
        elem('numero_sorteado').innerHTML = '';
        elem('titulo_numero_sorteado').innerHTML = '';
    },
    eventoNovoNumero: function(numero) {
        Roleta.numeros.push(numero);
    },
    iniciarSorteio: function(numeroMaximo) {
        if (!Roleta._sorteioAtivo) {
            if (numeroMaximo > 0) {
                Roleta.bolasAleatorias = numeroMaximo < 15;
                Roleta.numeroMaximo = numeroMaximo;
                Roleta._3seg = setInterval(function() {
                    if (Roleta.numeros.length > 0) {
                        enviarDados(url_base + 'json=true&n=' + Roleta.numeros.join('+'), function(dados) {

                        });
                        Roleta.numeros = [];
                    }
                }, 2000);
                Roleta.contadorMovimentacao = 0;
                Roleta.limparBolasSorteadas();
                Roleta._sorteioAtivo = true;
                document.body.classList.add('sorteio-ativo');
            } else {
                throw new Error('Número inválido para sorteio.');
            }
        }

    },
    terminarSorteio: function() {
        if (Roleta._sorteioAtivo) {
            Roleta._sorteioAtivo = false;
            document.body.classList.remove('sorteio-ativo');
            Roleta.limparBolasSorteadas();
            clearInterval(Roleta._3seg);
        }
    },
    limparBolasSorteadas: function() {
        var bolasAtivas = document.querySelectorAll('.bola-sorteio');
        //console.log('bolasAtivas', bolasAtivas);
        for (i = 0; i < bolasAtivas.length; i++) {
            bolasAtivas[i].remove();
        }
    }
}





var maximo_bolas = screen.width > 480 ? 100 : 10;


(function() {
    document.onmousemove = handleMouseMove;

    function handleMouseMove(event) {
        if (Roleta._sorteioAtivo) {
            var eventDoc, doc, body;
            event = event || window.event;
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
            // console.log('x', event.pageX, 'y', event.pageY)
            if (Roleta.bolasAleatorias) {
                numeroMaximo = 999;
            } else {
                numeroMaximo = Roleta.numeroMaximo;;
            }
            var bola_sorteada = Math.abs((event.pageX + Roleta.contadorMovimentacao) * (event.pageY + Roleta.contadorMovimentacao) + Roleta.contadorMovimentacao);
            bola_sorteada = Math.abs(bola_sorteada) % numeroMaximo + 1;
            var span;

            if (Roleta.contadorMovimentacao % (maximo_bolas / 10) == 0) {
                // if (Roleta.contadorMovimentacao >= maximo_bolas) {
                //     elem("bola-sorteio-" + Roleta.contadorMovimentacao % maximo_bolas).remove();
                // }
                span = document.createElement('span');
                span.id = "bola-sorteio-" + Roleta.contadorMovimentacao; // % maximo_bolas;
                span.className = 'bola-sorteio meio';
                if (Roleta.bolasAleatorias) {
                    span.classList.add('aleatoria')
                }
                document.body.appendChild(span);
                span.style.top = event.clientY + 10 + 'px';
                span.style.left = event.clientX + 10 + 'px';
                span.innerHTML = bola_sorteada;
                Roleta.eventoNovoNumero(bola_sorteada);
            }
            Roleta.contadorMovimentacao++;
            //console.log('Roleta.contadorMovimentacao', Roleta.contadorMovimentacao)
        }



    }
})();


window.addEventListener('load', function() {
    if (acessando_como_gerencia || acessando_como_usuario) {
        Relogio._init();
        Cronometro._init();
        setInterval(function() {
            if (!Relogio.sincronizando) {
                enviarDados(url_base + 'json=true&up=1&random=' + ((Math.random() * 1000) % 1000), function(dados) {
                    Painel.tratarAtualizacoes(dados);
                });
            }
        }, 650);
    }
})

var contadorPerdaFocus = 0;

function checkFocus() {
    if (acessando_como_gerencia || acessando_como_usuario) {
        contadorPerdaFocus++;
        if (contadorPerdaFocus > 4) {
            if (document.hasFocus() && !Relogio.sincronizando) {
                elem('titulo').innerHTML = 'Você saiu várias vezes dessa janela. Aguarde o novo sincronismo...'
                console.log('focus', document.hasFocus());
                Relogio.sincronizar(function() {
                    if (acessando_como_gerencia) {
                        elem('titulo').innerHTML = 'Painel de gerência';
                    } else {
                        elem('titulo').innerHTML = 'Relógio sincronizado. Aguardando atualizações...';
                    }

                });
            }
            contadorPerdaFocus = 0;
        }
    }

}
document.addEventListener("visibilitychange", checkFocus);
window.addEventListener("focus", checkFocus);
window.addEventListener("blur", checkFocus);