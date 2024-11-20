<?php

    require_once 'db.php';

    function obterDados($nomeTabela, $criterios = [], $colunas = '*', $ordem = '', $limite = null){
    try {
        $conexao = conectarBD();

        if (!$conexao) {
            throw new Exception("Falha na conexão com o banco de dados");
        }

        // Monta a consulta SQL dinamicamente
        $consulta = "SELECT $colunas
                       FROM $nomeTabela";

        // Adiciona critérios de filtro (WHERE) dinamicamente
        if (!empty($criterios)) {
            $condicoes = [];
            foreach ($criterios as $coluna => $valor) {
                $condicoes[] = "$coluna = :$coluna";
                //Exemplo de resultado: nome = :nome, idade = :idade;
            }
            $consulta .= " WHERE " . implode(' AND ', $condicoes);
            //Exemplo de resultado: SELECT * FROM usuarios WHERE nome = :nome AND idade = :idade;
        }

        // Adiciona ordenação, se especificado
        if (!empty($ordem)) {
            $consulta .= " ORDER BY $ordem";
        }

        // Adiciona limite, se especificado
        if ($limite) {
            $consulta .= " LIMIT $limite";
        }

        $stmt = $conexao->prepare($consulta);

        // Associa os valores dos critérios ao prepared statement
        foreach ($criterios as $coluna => $valor) {
            $stmt->bindValue(":$coluna", $valor);
        }
        //$criterios = ['nome' => 'João', 'idade' => 25];
        //Exemplo de resultado FINAL: SELECT * FROM usuarios WHERE nome = 'João' AND idade = 25

        if (!$stmt->execute()) {
            throw new Exception("Erro ao executar a consulta");
        }

        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($dados)) {
            error_log("Nenhum registro encontrado na tabela $nomeTabela");
            return [];
        }

        return $dados;
    } catch (PDOException $e) {
        error_log("Erro PDO ao obter dados da tabela $nomeTabela: " . $e->getMessage());
        throw new Exception("Erro ao buscar os dados no banco de dados");
    } catch (Exception $e) {
        error_log("Erro ao obter dados da tabela $nomeTabela: " . $e->getMessage());
        throw new Exception("Erro ao processar os dados");
    } finally {
        // Fecha a conexão
        $conexao = null;
    }
}

if (isset($_GET['desativar']) && isset($_GET['tabela'])) {
            
    try {

        $id = (int)$_GET['desativar'];
        $tabela = $_GET['tabela'];
        
        $conexao = conectarBD();

        if (!$conexao) {
            throw new Exception("Falha na conexão com o banco de dados");
        }

        $query = "UPDATE $tabela
                     SET status = NOT STATUS
                   WHERE id = :id";
                   
        $stmt = $conexao->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        $stmt->execute();

        $conexao = null;
        header('Location: ../public/admin.php');
        exit();

    } catch (PDOException $e) {

        echo 'Erro na conexão: ' . $e->getMessage();
    }
}

    

    
    

    