<?php

include_once 'database.php';
$painel_valido = TRUE;
if (isset($_GET['id'])) {
    $c = ler_painel($_GET['id']);
    if (!isset($c['id'])) {
        $painel_valido = FALSE;
    }
} else {
    $painel_valido = FALSE;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>--:--</title>
    <link rel="stylesheet" href="principal.css">
    <?php if ($painel_valido) : ?>

        <script>
            var global_id = <?= @(int)$_GET['id'] ?>;
            var pagina_atual = 'painel';
        </script>
        <script type="text/javascript" src="principal.js"></script>
        <script type="text/javascript" src="painel.js"></script>

    <?php endif; ?>
</head>

<body>
    <div id="painel">
        <div id="cronometro" class="meio">--:--</div>
        <div id="marcador">Aguardando marcador...</div>
        <div id="mensagem">

            <?php if (!$painel_valido) : ?>

                Painel inválido

            <?php else : ?>

                Aguardando mensagem...

            <?php endif; ?>

        </div>

        <?php if ($painel_valido) : ?>

            <div id="sorteador">
                <div class="bloco esquerda">
                    <div class="titulo meio">Seu número<br>de roleta</div>
                    <div class="numero" id="numero_aleatorio">0</div>
                </div>
                <div class="bloco centro">
                    <p id="numeros_obtidos">

                    </p>
                    <div style="display:none">
                        <p id="formula">
                            Fórmula: soma-se todos números aleatórios
                            enviados pelos participantes e divide pela quantidade de questões.
                            O resto da divisão mais 1 é o número sorteado.
                        </p>
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

        <?php endif; ?>

    </div>
    <pre id="debug"></pre>

</body>

</html>