<?php
/* ==========================================================================
   Funções do CRUD de depoimentos
   --------------------------------------------------------------------------
   Usadas por pages/depoimentos*.php.
   ========================================================================== */

/** Etiqueta colorida do status (Ativo / Inativo). */
function badgeStatusDepoimento($status): string
{
    return (int) $status === 1
        ? '<span class="badge badge-success badge-dot">Ativo</span>'
        : '<span class="badge badge-gray badge-dot">Inativo</span>';
}

/**
 * Lê os campos do formulário de depoimento enviados por POST.
 */
function lerFormularioDepoimento(): array
{
    return [
        'autor'  => post('autor'),
        'texto'  => limparHtml(post('texto')), // HTML do editor, já sem código perigoso
        'status' => isset($_POST['status']) ? 1 : 0,   // checkbox desmarcado não é enviado
    ];
}

/**
 * Valida os dados do depoimento NO SERVIDOR.
 * (As mesmas regras do depoimento-form.js).
 * 
 * Retorna um array [campo => mensagem]. Array vazio = tudo certo.
 */
function validarDepoimento(array $dados): array
{
    $erros = [];

    // Autor
    if (mb_strlen($dados['autor']) < 3) {
        $erros['autor'] = 'Informe o nome do autor (mínimo 3 caracteres).';
    } elseif (mb_strlen($dados['autor']) > 100) {
        $erros['autor'] = 'O nome do autor deve ter no máximo 100 caracteres.';
    }

    // Texto
    if (htmlVazio($dados['texto'])) {
        $erros['texto'] = 'Escreva o texto do depoimento.';
    }

    return $erros;
}