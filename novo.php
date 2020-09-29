<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo painel</title>
</head>

<body>
    <h1>Novo painel</h1>

    <?php

    if (isset($_POST['codigo_chave'])) {
        include 'database.php';
        $max_id = criar_painel($_POST['codigo_chave'], $_POST['regressiva']);
        //header('Location: gerenciar.php?codigo_chave=' . $_POST['codigo_chave'] . '&id=' . $max_id);
    ?>
        <p>Link para compartilhar aos participantes: <a target="_blank" href="painel.php?id=<?= $max_id ?>">clique aqui</a></p>
        <p>Link de gerência (NÃO COMPARTILHE! Guarde com segurança): <a target="_blank" href="gerenciar.php?id=<?= $max_id ?>&codigo_chave=<?= $_POST['codigo_chave'] ?>">clique aqui</a></p>
        <p>
            <a href="novo.php">Novo painel</a>
        </p>

    <?php
    } else {
    ?>
        <script>
            function somenteCaracteresValidos(e) {
                var caracteresIlegais = /[\W_]/; // permite somente letras e números                
                if (caracteresIlegais.test(e.key)) {
                    return false;
                }
            }
        </script>
        <form action="" method="post">
            <p>
                <label for="codigo_chave">Código chave: </label>
                <input onkeypress="return somenteCaracteresValidos(event)" name="codigo_chave" id="codigo_chave" type="text" style="min-width: 50%;" value="<?= hash('sha256', rand(0, 999999999) . microtime(1) . rand(0, 999999999)) ?>"><br>
                <small>
                    Esta chave será incluida no link de gerência e só quem tem autorização pode saber desse código.
                    <br>Caso você queira alterar este código, utilize uma chave difícil de ser descoberta.
                </small>
            </p>
            <p>
                <input type="checkbox" checked="checked" name="regressiva" id="regressiva">
                <label for="regressiva">Ativar regresiva</label><br>
                <small>Inclui um tempo de 2 segundos antes de cada início do cronômetro, com representação de cores (vermelho e amarelo) antes do valor correto.</small>
            </p>
            <p>Importante: após a criação do painel, envie as perguntas da roleta.</p>
            <p>
                <input type="submit" value="Criar painel">
            </p>
        </form>
    <?php
    }

    ?>
    </p>

</body>

</html>