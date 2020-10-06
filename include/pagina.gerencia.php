<?php if (!defined('PAINEL_ROOT')) die("Nope"); ?>
<fieldset class="gerencia-cronometro">
    <legend>Cronômetro</legend>
    <span class="input">
        <select class="item" id="select_tempo_definido" name="select_tempo_definido">
            <option value="60">1 minuto</option>
            <option value="120">2 minutos</option>
            <option value="180">3 minutos</option>
            <option value="240">4 minutos</option>
            <option value="300">5 minutos</option>
            <option value="600">10 minutos</option>
            <option disabled>--</option>
            <option value="5">5 segundos</option>
            <option value="30">30 segundos</option>
        </select></span>
    <span class="input">
        <button class="item" id="btn_preparar">Preparar/Mostrar</button>
    </span>
    <span class="input">
        <label class="chk_container item">
            <input id="chk_iniciar" type="checkbox">
            <span class="iniciar">Iniciar cronômetro</span>
            <span class="pausar">Pausar cronômetro</span>
            <span class="checkmark"></span>
        </label>
    </span>

</fieldset>
<fieldset class="gerencia-mensagem">
    <legend>Mensagem pública</legend>
    <p class="titulo">
        <input type="text" name="txt_mensagem_titulo" id="txt_mensagem_titulo">
    </p>
    <p class="conteudo">
        <textarea style="width: 100%" rows="3" id="txt_mensagem_conteudo"></textarea>
    </p>


    <button id="btn_enviar_mensagem_manual">Enviar</button>
    <button id="btn_limpar_mensagem_manual">Limpar</button>

    <span id="cont_auto_enviar">
        <input id="chk_auto_enviar" type="checkbox" style="width: 2em">
        <label for="chk_auto_enviar">Auto-enviar mensagem após a escolha do número sorteado</label>
    </span>
</fieldset>
<fieldset>
    <legend>Roleta para sorteio</legend>
    <p class="meio">

        <select id="select_roleta_ativa" name="roleta_ativa">
            <option value="-1">(roleta predefinida: 1 ou 2)</option>
            <?php $numero_anterior = ''; ?>
            <?php foreach ($itens_roleta as $linha) : ?>
                <?php if ($linha['titulo'] != $numero_anterior) : ?>
                    <?php $identificador_questao =  $linha['id_roleta']; ?>

                    <option value="<?= $identificador_questao ?>"><?= $linha['titulo'] ?></option>
                <?php endif; ?>
                <?php $numero_anterior = $linha['titulo']; ?>
            <?php endforeach; ?>

        </select>
        <script>
            var roleta_ativa = document.getElementById('select_roleta_ativa');
            roleta_ativa.addEventListener('change', function(event) {
                escolher_roleta(this.value);
            });
        </script>



    </p>
    <p class="meio">
        <span class="input" id="cont_ativar_sorteio">
            <label for="chk_ativar_sorteio" class="chk_container item">
                <input id="chk_ativar_sorteio" type="checkbox">
                <span class="iniciar">Iniciar</span>
                <span class="pausar"><strong style="color:red">Parar</strong></span> sorteio de números aleatórios aos participantes
                <span class="checkmark"></span>
            </label>
        </span>

    </p>
    <div id="itens_roleta_container">
        <table>

            <?php foreach ($itens_roleta as $linha) : ?>

                <tr style="display:none" id="roleta_item_<?= $linha['id_roleta'] . '_' . $linha['numero'] ?>" class="roleta_item roleta_<?= $linha['id_roleta'] ?>">
                    <td class="check">
                        <!--<input  id="chk_roleta_item_<?= $linha['id_roleta'] . '_' . $linha['numero'] ?>" type="checkbox">-->
                    </td>
                    <th class="numero"><?= $linha['numero'] ?></th>
                    <td class="conteudo"><?= $linha['conteudo'] ?></td>

                </tr>

            <?php endforeach; ?>

        </table>

    </div>
    <hr>
    <p>
        <a href="#" onclick="escolher_novo_arquivo_roleta()">
            Escolher um arquivo texto com perguntas para uma nova roleta
        </a>

        <form action="<?= $url_base ?>" method="POST" id="form_roleta" enctype="multipart/form-data">
            <label for="roleta_upload"></label>
            <input style="display:none" type="file" accept="text/plain" name="roleta_upload" id="roleta_upload">
        </form>
        <script>
            function escolher_novo_arquivo_roleta() {
                elem('roleta_upload').click();
                elem('roleta_upload').onchange = function() {
                    if (this.value) {
                        elem('form_roleta').submit();
                    }
                };
            }
        </script>
    </p>
</fieldset>