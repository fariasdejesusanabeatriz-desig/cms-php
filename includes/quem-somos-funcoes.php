<?php
/* ==========================================================================
   Funções para o CRUD de Quem Somos
   ========================================================================== */

/**
 * Busca as configurações no banco. Se não existir, retorna array padrão.
 */
function buscarConfiguracoesQuemSomos($pdo) {
    $stmt = $pdo->query("SELECT * FROM quem_somos LIMIT 1");
    $dados = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dados) {
        return [
            'texto_historia' => '', 
            'texto_missao'   => '', 
            'texto_valores'  => '',
            'foto1'          => null, 
            'foto2'          => null
        ];
    }
    return $dados;
}

/**
 * Lê os dados do POST e limpa.
 */
function lerFormularioQuemSomos() {
    return [
        'texto_historia' => trim($_POST['texto_historia'] ?? ''),
        'texto_missao'   => trim($_POST['texto_missao'] ?? ''),
        'texto_valores'  => trim($_POST['texto_valores'] ?? ''),
    ];
}

/**
 * Salva no banco (INSERT ou UPDATE).
 */
function salvarConfiguracoesQuemSomos($pdo, $dados, $foto1, $foto2) {
    $stmt = $pdo->query("SELECT id FROM quem_somos LIMIT 1");
    $existe = $stmt->fetchColumn();

    if ($existe) {
        $sql = "UPDATE quem_somos SET 
                    texto_historia = :historia, 
                    texto_missao = :missao, 
                    texto_valores = :valores,
                    foto1 = :foto1, 
                    foto2 = :foto2
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':historia' => $dados['texto_historia'], 
            ':missao'   => $dados['texto_missao'], 
            ':valores'  => $dados['texto_valores'],
            ':foto1'    => $foto1, 
            ':foto2'    => $foto2, 
            ':id'       => $existe
        ]);
    } else {
        $sql = "INSERT INTO quem_somos (texto_historia, texto_missao, texto_valores, foto1, foto2) 
                VALUES (:historia, :missao, :valores, :foto1, :foto2)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':historia' => $dados['texto_historia'], 
            ':missao'   => $dados['texto_missao'], 
            ':valores'  => $dados['texto_valores'],
            ':foto1'    => $foto1, 
            ':foto2'    => $foto2
        ]);
    }
}