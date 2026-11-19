<?php
/* ==========================================================================
   Funções auxiliares do CRUD de configurações (logo)
   Reaproveita o upload, o CSRF e as mensagens de produtos-funcoes.php
   ========================================================================== */

require_once __DIR__ . '/produtos-funcoes.php';

/** Miniatura do logo para a listagem (a imagem fica na raiz do site, por isso o "../"). */
function imagemLogo(array $c): string
{
    $src = '../' . $c['logo'];
    return '<img src="' . e($src) . '" alt="Logo" height="48" style="max-width:140px;height:48px;object-fit:contain;border-radius:8px;background:#fef2ec;padding:4px;">';
}