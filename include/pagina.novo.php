<?php if (!defined('PAINEL_ROOT')) die("Nope"); ?>
<?php if ($acessando_para_novo_em_branco) : ?>
    <form action="" method="post">
        <fieldset>
            <legend>Configurações básicas</legend>
            <p>
                <label for="descricao">Descrição: </label>
                <input type="text" name="descricao" id="descricao">
            </p>
            <p>
                <label for="chave_usuario">Chave de usuário: </label>
                <input value="<?= @$nova_chave_usuario_aleatoria ?>" type="text" name="chave_usuario" id="chave_usuario">
            </p>
            <p>
                <label for="chave_gerencia">Chave de gerência: </label>
                <input readonly value="<?= @$nova_chave_gerencia_aleatoria ?>" type="text" name="chave_gerencia" id="chave_gerencia">
            </p>
        </fieldset>
        <fieldset>
            <p class="meio">
                <input type="submit" value="Criar novo painel">
            </p>
        </fieldset>
    </form>
<?php else : ?>

    <fieldset>
        <legend>Confirmação</legend>
        <p class="meio">
            O painel foi criado.
        </p>
        <p></p>
        <p class="meio">
            Link de usuário:&nbsp;<br>
        </p>

        <p class="meio">
            <input onclick="this.select();" id="txt_link_usuario" type="text" readonly="readonly" style="text-align: center;width:80%">&nbsp;
            <a id="link_usuario" target="_blank" href="<?= $link_usuario ?>">aqui</a>
        </p>
        <script>
            var link_usuario = document.getElementById("link_usuario");
            var txt_link_usuario = document.getElementById("txt_link_usuario");
            txt_link_usuario.value = link_usuario.href;
        </script>
        <p></p>
        <p class="meio">
            Link de gerência:&nbsp;<br>
            <a target="_blank" href="<?= $link_gerencia ?>">acesse aqui e não compartilhe</a>
        </p>

    </fieldset>
<?php endif; ?>