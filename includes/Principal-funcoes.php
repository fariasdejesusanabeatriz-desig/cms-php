<?php
/* ==========================================================================
   Funções auxiliares do CRUD da seção principal (início)
   Reaproveita o upload, o CSRF e as mensagens de produtos-funcoes.php
   ========================================================================== */

require_once __DIR__ . '/produtos-funcoes.php';

/** Retorna [dados, erros] a partir do $_POST. */
function validarPrincipal(array $post): array
{
    $dados = [
        'titulo'    => trim($post['titulo'] ?? ''),
        'descricao' => trim($post['descricao'] ?? ''),
    ];
    $erros = [];

    if ($dados['titulo'] === '' || mb_strlen($dados['titulo']) > 120) {
        $erros['titulo'] = 'Informe o título (até 120 caracteres).';
    }
    if ($dados['descricao'] === '') {
        $erros['descricao'] = 'Informe a descrição.';
    }

    return [$dados, $erros];
}

/** Miniatura para a listagem (a imagem fica na raiz do site, por isso o "../"). */
function imagemPrincipal(array $p): string
{
    $src = '../' . $p['imagem'];
    return '<img src="' . e($src) . '" alt="' . e($p['titulo']) . '" width="48" height="48" style="object-fit:cover;border-radius:50%;background:#fef2ec;">';
}