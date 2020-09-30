<?php

include_once 'database.php';

if (isset($_GET['codigo_chave']) && isset($_GET['id'])) {
    if (!validar_painel($_GET['id'], $_GET['codigo_chave'])) {
        die('Proibido');
    }
} else {
    die('Vazio');
}

$codigo_chave = filter_var($_GET['codigo_chave'], FILTER_SANITIZE_URL);
$global_id = (int) $_GET['id'];

$nomes_roleta = obter_nomes_roleta($global_id);

$itens_roleta = obter_itens_roleta($global_id);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador</title>
    <link rel="stylesheet" href="principal.css">
    <script>
        var global_id = <?= $global_id ?>;
        var codigo_chave = '<?= $codigo_chave ?>';
        var pagina_atual = 'gerenciar';
    </script>
    <script type="text/javascript" src="principal.js"></script>
    <script type="text/javascript" src="gerenciar.js"></script>
</head>

<body>
    <div id="gerenciador" class="gerenciador">
        <div id="cronometro" class="meio">--:--</div>
        <div id="preparado">
            <fieldset>
                <legend>Cronômetro</legend>
                <label for="tempo_definido">Definir manualmente: </label>
                <select id="tempo_definido" name="tempo_definido">
                    <option value="60">1 minuto</option>
                    <option value="120">2 minutos</option>
                    <option value="180">3 minutos</option>
                    <option value="240">4 minutos</option>
                    <option value="300">5 minutos</option>
                    <option value="600">10 minutos</option>
                    <option disabled>--</option>
                    <option value="5">5 segundos</option>
                    <option value="30">30 segundos</option>
                </select>
                <button onclick="preparar_cronometro(tempo_definido.value); document.getElementById('chk_iniciar').checked=false">Preparar</button>
                <input id="chk_iniciar" type="checkbox" onclick="if (this.checked) iniciar_cronometro(); else pausar_cronometro()">
                <label for="chk_iniciar">Iniciar cronômetro</label>
                <!--<button onclick="iniciar_cronometro()">Iniciar</button>
                <button onclick="pausar_cronometro()">Pausar</button>
               
                    <input type="checkbox"  checked="checked" name="regressiva" id="regressiva">
                <label for="regressiva">Regresiva</label>
                -->
                <script>
                    var tempo_definido = document.getElementById('tempo_definido');
                </script>

            </fieldset>
            <fieldset>
                <legend>Mensagem pública</legend>
                <input onkeypress="ev_alt_msg()" onchange="ev_alt_msg()" type="text" name="marcador_manual" id="marcador_manual">
                <textarea onkeypress="ev_alt_msg()" onchange="ev_alt_msg()" style="width: 100%" rows="3" id="mensagem_manual"></textarea>
                <br>
                <script>
                    var marcador_manual = document.getElementById('marcador_manual');
                    var mensagem_manual = document.getElementById('mensagem_manual');
                </script>
                <button id="btn_enviar_mensagem_manual" onclick="enviar_mensagem_manual(marcador_manual.value, mensagem_manual.value)">Enviar</button>
                <button id="btn_limpar_mensagem_manual" onclick="enviar_mensagem_manual('&nbsp;', '&nbsp;');marcador_manual.value='';mensagem_manual.value=''; ">Limpar</button>

                <span id="cont_auto_enviar" style="">
                    <input id="chk_auto_enviar" type="checkbox" style="width: 2em">
                    <label for="chk_auto_enviar">Auto-enviar mensagem após a escolha do número sorteado</label>
                </span>
            </fieldset>
            <fieldset>
                <legend>Roleta</legend>
                <pre><?php //var_dump($nomes_roleta)
                        ?></pre>
                <p>

                    <select id="roleta_ativa" name="roleta_ativa">
                        <option value="-">(novo grupo de sorteio)</option>
                        <?php foreach ($nomes_roleta as $nome) : ?>
                            <option value="<?= preg_replace('/[^0-9,A-Z,a-z,.]+/', '_',  $nome[0]) ?>"><?php echo $nome[0] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <script type="text/javascript">
                        var roleta_ativa = document.getElementById('roleta_ativa');
                        roleta_ativa.addEventListener('click', function(event) {
                            escolher_roleta(this.value);
                        });
                    </script>
                    <!--<button id="btn_apagar_roleta" onclick="apagar_roleta()" style="display:none">Apagar</button>-->
                    <span id="cont_ativar_rodada" style="display: none">
                        <input id="chk_ativar_rodada" type="checkbox" onchange="ativar_rodada((this.checked ? roleta_ativa.value : ''))" style="width: 2em">
                        <label for="chk_ativar_rodada"><strong style="color:red">Ativar roleta para os participantes</strong></label>
                    </span>

                </p>
                <div id="form_roleta_container">
                    <form action="enviar_roleta.php" method="POST" id="form_roleta" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $global_id; ?>" />
                        <input type="hidden" name="codigo_chave" value="<?php echo $codigo_chave; ?>" />
                        <label for="roleta_upload"></label>
                        <input style="display:none" type="file" accept="text/plain" name="roleta_upload" id="roleta_upload">
                        <br><small>Altamente recomendado deixar preparado os arquivos de roleta antes de começar.</small>
                    </form>
                    <button onclick="escolher_novo_arquivo_roleta()">Escolher arquivo texto</button>
                </div>
                <div id="itens_roleta_container">
                    <table>
                        
                        <?php foreach ($itens_roleta as $linha) : ?>

                            <?php $identificador_questao = preg_replace('/[^0-9,A-Z,a-z,.]+/', '_',  $linha['marcador']); ?>
                           
                            <tr id="<?= $identificador_questao . '_' . $linha['numero']  ?>" style="display: none" class="<?= $identificador_questao ?>">
                                <!--<td class="check"><input type="checkbox"></td>-->
                                <th class="numero"><?= $linha['numero'] ?></th>
                                <td class="conteudo"><?= $linha['conteudo'] ?></td>
                                <!--<td><button onclick="enviar_item_roleta_como_mensagem('')">Enviar como mensagem</button></td>
                        -->
                            </tr>

                        <?php endforeach; ?>

                    </table>

                </div>
            </fieldset>

        </div>
        <p id="pre_sorteador"></p>
        <div id="sorteador">
            <div class="bloco esquerda">
                <div class="titulo meio">Seu número<br>aleatório</div>
                <div class="numero" id="numero_aleatorio">

                </div>
            </div>
            <div class="bloco centro">
                <p id="numeros_obtidos">
                    <!--
                    <span class="numero_obtido">123</span>
                    <span class="numero_obtido">123</span>
                        -->
                </p>
                <div style="display:none">
                    <p id="formula">Fórmula: soma-se todos números aleatórios enviados pelos participantes e divide pela quantidade de questões. O resto da divisão mais 1 é o número sorteado. </p>
                    <p id="calculo" style="display:none;">Cálculo:
                        (<span id="soma_total_aleatorios">1200</span>
                        Mod
                        <span id="total_perguntas">10</span>) + 1 =
                        <span id="resultado_aleatorios">1</span>
                    </p>
                </div>
            </div>


            <div class="bloco direita">
                <div class="titulo meio">Sorteado</div>
                <div class="numero" id="numero_sorteado">--</div>
            </div>


        </div>

    </div>

</body>

</html>