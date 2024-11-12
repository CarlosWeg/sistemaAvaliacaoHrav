<?php
require_once '../src/perguntas.php';
$perguntas = obterPerguntas();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avaliação HRAV</title>
    <link rel="stylesheet" href="css/estilos.css">
    <script src="js/scripts.js"></script>
</head>
<body>

    <h1>Sistema de Avaliação HRAV</h1>

    <form id="formulario" action="../src/respostas.php" method="POST">
            <?php foreach ($perguntas as $index => $pergunta): ?>
                <div class="pergunta">
                    <label><?=$pergunta['texto'];?></label>
                    <div class="escala">
                        <?php for ($i = 0; $i <= 10; $i++): ?>
                            <label class="label-escala">
                                <input type="radio" name="respostas[<?= $pergunta['id']; ?>]" value="<?= $i; ?>" required>
                                <?= $i; ?>
                            </label>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Feedback adicional aparece após as perguntas -->
            <div class="feedback">
                <label for="feedback">Feedback adicional (opcional):</label>
                <textarea name="feedback" id="feedback"></textarea>
            </div>

            <br>

            <button type="button" id="botao-perguntas" onclick="proxPergunta()">Próxima</button>
            
        </form>

    <footer>
            <p>Sua avaliação espontânea é anônima, nenhuma informação pessoal é solicitada ou armazenada.</p>
    </footer>

</body>


</html>