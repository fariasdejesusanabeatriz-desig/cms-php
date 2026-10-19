<?php
/* ==========================================================================
   Funções para o CRUD de Contato
   ========================================================================== */

/**
 * Busca as configurações no banco. Se não existir, retorna array padrão.
 */
function buscarConfiguracoesContato($pdo) {
    $stmt = $pdo->query("SELECT * FROM contatos LIMIT 1");
    $dados = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dados) {
        // Valores padrão se o banco estiver vazio (evita erro no HTML)
        return [
            'telefones' => [],
            'emails' => [],
            'horario_semana' => '',
            'horario_sabado' => '',
            'horario_domingo' => '',
            'link_instagram' => '',
            'link_facebook' => '',
            'link_tiktok' => ''
        ];
    }

    // Descodifica os JSONs salvos no banco
    $dados['telefones'] = json_decode($dados['telefones_json'] ?? '[]', true) ?: [];
    $dados['emails'] = json_decode($dados['emails_json'] ?? '[]', true) ?: [];

    return $dados;
}

/**
 * Lê os dados do POST e limpa.
 */
function lerFormularioContato() {
    return [
        'telefones' => array_filter($_POST['telefones'] ?? []), 
        'emails' => array_filter($_POST['emails'] ?? []),
        'horario_semana' => trim($_POST['horario_semana'] ?? ''),
        'horario_sabado' => trim($_POST['horario_sabado'] ?? ''),
        'horario_domingo' => trim($_POST['horario_domingo'] ?? ''),
        'link_instagram' => trim($_POST['link_instagram'] ?? ''),
        'link_facebook' => trim($_POST['link_facebook'] ?? ''),
        'link_tiktok' => trim($_POST['link_tiktok'] ?? ''),
    ];
}

/**
 * Valida os dados.
 */
function validarContato($dados) {
    $erros = [];
    foreach ($dados['emails'] as $email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erros[] = "O e-mail '$email' é inválido.";
        }
    }
    return $erros;
}

/**
 * Salva no banco (INSERT ou UPDATE).
 */
function salvarConfiguracoesContato($pdo, $dados) {
    $stmt = $pdo->query("SELECT id FROM configuracoes_contato LIMIT 1");
    $existe = $stmt->fetchColumn();

    $telefonesJson = json_encode(array_values($dados['telefones']));
    $emailsJson = json_encode(array_values($dados['emails']));

    if ($existe) {
        $sql = "UPDATE configuracoes_contato SET 
                    telefones_json = :telefones, emails_json = :emails, 
                    horario_semana = :semana, horario_sabado = :sabado, horario_domingo = :domingo, 
                    link_instagram = :insta, link_facebook = :face, link_tiktok = :tiktok 
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':telefones' => $telefonesJson, ':emails' => $emailsJson,
            ':semana' => $dados['horario_semana'], ':sabado' => $dados['horario_sabado'], ':domingo' => $dados['horario_domingo'],
            ':insta' => $dados['link_instagram'], ':face' => $dados['link_facebook'], ':tiktok' => $dados['link_tiktok'],
            ':id' => $existe
        ]);
    } else {
        $sql = "INSERT INTO configuracoes_contato 
                (telefones_json, emails_json, horario_semana, horario_sabado, horario_domingo, link_instagram, link_facebook, link_tiktok) 
                VALUES (:telefones, :emails, :semana, :sabado, :domingo, :insta, :face, :tiktok)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':telefones' => $telefonesJson, ':emails' => $emailsJson,
            ':semana' => $dados['horario_semana'], ':sabado' => $dados['horario_sabado'], ':domingo' => $dados['horario_domingo'],
            ':insta' => $dados['link_instagram'], ':face' => $dados['link_facebook'], ':tiktok' => $dados['link_tiktok']
        ]);
    }
}