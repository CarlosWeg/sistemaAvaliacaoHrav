<?php

    require_once 'db.php';
    require_once 'perguntas.php';


    // Define a função que receberá os parâmetros para inserir a avaliação
    function inserirAvaliacao($setor_id, $pergunta_id, $dispositivo_id, $resposta, $feedback = null) {
        try {
            
            $conexao = conectarBD();
            
            if (!$conexao) {
                throw new Exception("Falha na conexão com o banco de dados");
            }

            // Validações dos dados recebidos, verifica se os IDs são números
            if (!is_numeric($setor_id) || !is_numeric($pergunta_id) || !is_numeric($dispositivo_id)) {
                throw new Exception("IDs inválidos fornecidos");
            }

            // Prepara a consulta SQL
            $consulta = "INSERT INTO avaliacoes (setor_id, pergunta_id, dispositivo_id, resposta, feedback) 
                        VALUES (?, ?, ?, ?, ?)";
            
            // Prepara a declaração SQL
            $stmt = $conexao->prepare($consulta);

            $resultado = $stmt->execute([
                $setor_id,
                $pergunta_id,
                $dispositivo_id,
                $resposta,
                $feedback
            ]);

            // Verifica se a inserção foi bem-sucedida
            // execute() retorna false se houver algum erro
            if (!$resultado) {
                throw new Exception("Erro ao inserir avaliação");
            }

        } catch (PDOException $e) {
            // Captura erros específicos do PDO (relacionados ao banco de dados)
            error_log("Erro PDO ao inserir avaliação: " . $e->getMessage());
            throw new Exception("Erro ao salvar a avaliação no banco de dados");
            
        } catch (Exception $e) {
            // Captura outros tipos de erros
            error_log("Erro ao inserir avaliação: " . $e->getMessage());
            throw new Exception($e->getMessage());
            
        } finally {
            $conexao = null;
        }
    }

    // Função para processar as respostas do formulário
    function processarRespostas() {
        try {
            // Verifica se o formulário foi enviado via POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método inválido");
            }

            // Verifica se existem respostas
            if (isset($_POST['feedback'])) {
                $feedback_geral = trim($_POST['feedback']);
            } else {
                $feedback_geral = null;
            }            

            // Pega o feedback (que é opcional)
            $feedback_geral = isset($_POST['feedback']) ? trim($_POST['feedback']) : null;

            
            $setor_id = $_POST['setor_id'] ?? null;
            $dispositivo_id = $_POST['dispositivo_id'] ?? null;

            if (!$setor_id || !$dispositivo_id) {
                throw new Exception("Setor ou dispositivo não selecionado.");
            }

            // Processa cada resposta
            foreach ($_POST['respostas'] as $pergunta_id => $resposta) {
                // Valida a resposta
                if (!is_numeric($resposta) || $resposta < 0 || $resposta > 10) {
                    throw new Exception("Resposta inválida para a pergunta $pergunta_id");
                }

                // Insere a avaliação
                inserirAvaliacao(
                    setor_id: $setor_id,
                    pergunta_id: $pergunta_id,
                    dispositivo_id: $dispositivo_id,
                    resposta: $resposta,
                    feedback: $feedback_geral
                );
            }

            header('Location: ../public/obrigado.php');
            exit;

        } catch (Exception $e) {
            error_log("Erro ao processar respostas: " . $e->getMessage());
            header('Location: ../public/erro.php');
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        processarRespostas();
    };