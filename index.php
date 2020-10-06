<?php
include_once 'core.php';
ob_end_flush() ;
if ($aguardando_resposta_json) {
    header('Content-Type: application/json');
    exit(json_encode($array_resposta_json));
}
?>
<!DOCTYPE html>

<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= @$titulo ?></title>
    <link rel="stylesheet" href="painel.css?v=<?= @$versao ?>">

    <script>
        var acessando_como_gerencia = <?= $acessando_como_gerencia ? 'true' : 'false' ?>;
        var acessando_como_usuario = <?= $acessando_como_usuario ? 'true' : 'false' ?>;
        var acessando_para_novo = <?= $acessando_para_novo_em_branco ? 'true' : 'false' ?>;
        var url_base = '<?= @$url_base ?>';
    </script>
    <script type="text/javascript" src="basico.js?v=<?= @$versao ?>"></script>
    <?php if ($acessando_como_usuario) : ?>
        <link rel="manifest" href="pwa.php<?= $url_base ?>">
    <?php endif; ?>

</head>

<body class="<?= $body_class ?>">
    <div id="painel">
        
        <div id="cronometro" class="meio"></div>
        <h1 id="titulo"><?= $painel_titulo ?></h1>
        <div id="mensagem" class="conteudo central">

            <?php if ($acessando_como_gerencia) : ?>

                <?php include_once 'include/pagina.gerencia.php'; ?>

            <?php elseif ($acessando_como_usuario) : ?>

                Aguardando sincronismo de relógio...

            <?php elseif ($acessando_para_novo_em_branco || $acessando_apos_cadastrar_novo) : ?>

                <?php include_once 'include/pagina.novo.php'; ?>

            <?php endif; ?>

        </div>

        <?php if (!$acessando_para_novo_em_branco && !$acessando_apos_cadastrar_novo) : ?>

            <div id="sorteador">
                <div class="bloco esquerda">
                    <!--<div class="titulo meio">Números<br>aleatórios</div>
                    <div class="numero" id="numero_aleatorio">0</div>-->

                </div>
                <div class="bloco centro">

                    <p id="numeros_obtidos">

                    </p>

                </div>


                <div class="bloco direita">
                    <div class="titulo meio" id="titulo_numero_sorteado">Sorteado</div>
                    <div class="numero" id="numero_sorteado">--</div>
                </div>


            </div>

        <?php endif; ?>

    </div>
    <pre id="debug"></pre>

    <?php if (!$acessando_para_novo_em_branco) : ?>

        <script type="text/javascript" src="painel.js?v=<?= @$versao ?>"></script>

    <?php endif; ?>


    <?php if ($acessando_como_usuario) : ?>

        <script type="text/javascript" src="usuario.js?v=<?= @$versao ?>"></script>

    <?php elseif ($acessando_como_gerencia) : ?>

        <script type="text/javascript" src="gerencia.js?v=<?= @$versao ?>"></script>

    <?php endif; ?>


</body>

</html>