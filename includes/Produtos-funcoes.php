<?php
/* ==========================================================================
   Funções auxiliares do CRUD de produtos
   (valida dados, trata upload da imagem, CSRF e mensagens de retorno)
   ========================================================================== */

// Pasta física das imagens (raiz do site/img/produtos) e caminho salvo no banco
const PRODUTOS_PASTA_IMG   = __DIR__ . '/../img/produtos/';
const PRODUTOS_CAMINHO_IMG = 'img/produtos/';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ---------- CSRF ---------- */
function csrfCampoProduto(): string
{
    if (empty($_SESSION['csrf_produtos'])) {
        $_SESSION['csrf_produtos'] = bin2hex(random_bytes(32));
    }
    return '<input type="hidden" name="csrf_produtos" value="' . $_SESSION['csrf_produtos'] . '">';
}

function validarCsrfProduto(): bool
{
    $enviado = $_POST['csrf_produtos'] ?? '';
    return !empty($_SESSION['csrf_produtos']) && hash_equals($_SESSION['csrf_produtos'], $enviado);
}

/* ---------- Mensagens de retorno (cadastrado, alterado, excluído...) ---------- */
function definirMensagemProduto(string $tipo, string $texto): void
{
    $_SESSION['msg_produtos'] = ['tipo' => $tipo, 'texto' => $texto]; // tipo: success | danger
}

function exibirMensagemProduto(): void
{
    if (empty($_SESSION['msg_produtos'])) {
        return;
    }
    $m = $_SESSION['msg_produtos'];
    unset($_SESSION['msg_produtos']);
    echo '<div class="alert alert-' . e($m['tipo']) . '" role="alert">' . e($m['texto']) . '</div>';
}

/* ---------- Formatação ---------- */
function formatarPreco($valor): string
{
    return 'R$ ' . number_format((float) $valor, 2, ',', '.');
}

function textoParcelas(int $parcelas): string
{
    return $parcelas <= 1 ? 'à vista' : 'ou ' . $parcelas . 'x sem juros';
}

function resumoTexto(string $texto, int $limite = 70): string
{
    return mb_strimwidth($texto, 0, $limite, '...', 'UTF-8');
}

/** Miniatura do produto para a listagem (a imagem fica na raiz do site, por isso o "../"). */
function imagemProduto(array $p): string
{
    $src = '../' . $p['imagem'];
    return '<img src="' . e($src) . '" alt="' . e($p['nome']) . '" width="48" height="48" style="object-fit:cover;border-radius:8px;background:#fef2ec;">';
}

/* ---------- Validação dos campos do formulário ---------- */
/** Converte "49,90" ou "1.049,90" ou "49.90" em float. */
function precoParaDecimal(string $valor): float
{
    $valor = trim($valor);
    if (strpos($valor, ',') !== false) {
        $valor = str_replace('.', '', $valor);   // remove separador de milhar
        $valor = str_replace(',', '.', $valor);  // vírgula vira ponto decimal
    }
    return (float) $valor;
}

/** Retorna [dados, erros] a partir do $_POST. */
function validarProduto(array $post): array
{
    $dados = [
        'nome'      => trim($post['nome'] ?? ''),
        'descricao' => trim($post['descricao'] ?? ''),
        'preco'     => trim($post['preco'] ?? ''),
        'parcelas'  => (int) ($post['parcelas'] ?? 1),
    ];
    $erros = [];

    if ($dados['nome'] === '' || mb_strlen($dados['nome']) > 120) {
        $erros['nome'] = 'Informe o nome do produto (até 120 caracteres).';
    }
    if ($dados['descricao'] === '') {
        $erros['descricao'] = 'Informe a descrição do produto.';
    }
    if ($dados['preco'] === '' || precoParaDecimal($dados['preco']) <= 0) {
        $erros['preco'] = 'Informe um preço válido, maior que zero.';
    }
    if ($dados['parcelas'] < 1 || $dados['parcelas'] > 6) {
        $erros['parcelas'] = 'Escolha de 1x a 6x.';
    }

    return [$dados, $erros];
}

/* ---------- Upload da imagem ---------- */
/**
 * Salva a imagem enviada. Retorna o caminho para gravar no banco,
 * null se nenhum arquivo foi enviado, ou false (e preenche $erro) se for inválido.
 */
function salvarImagemProduto(?array $arquivo, ?string &$erro)
{
    if (!$arquivo || $arquivo['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        $erro = 'Não foi possível enviar a imagem. Tente novamente.';
        return false;
    }
    if ($arquivo['size'] > 2 * 1024 * 1024) {
        $erro = 'A imagem deve ter no máximo 2 MB.';
        return false;
    }

    $tipos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $mime  = (new finfo(FILEINFO_MIME_TYPE))->file($arquivo['tmp_name']);
    if (!isset($tipos[$mime])) {
        $erro = 'Formato inválido. Use JPG, PNG, WEBP ou GIF.';
        return false;
    }

    if (!is_dir(PRODUTOS_PASTA_IMG)) {
        mkdir(PRODUTOS_PASTA_IMG, 0755, true);
    }
    $nomeArquivo = bin2hex(random_bytes(8)) . '.' . $tipos[$mime];
    if (!move_uploaded_file($arquivo['tmp_name'], PRODUTOS_PASTA_IMG . $nomeArquivo)) {
        $erro = 'Falha ao salvar a imagem no servidor.';
        return false;
    }
    return PRODUTOS_CAMINHO_IMG . $nomeArquivo;
}

/** Apaga o arquivo da imagem (só dentro de img/produtos, para não apagar imagens do site). */
function removerImagemProduto(?string $caminho): void
{
    if (!$caminho || strpos($caminho, PRODUTOS_CAMINHO_IMG) !== 0) {
        return;
    }
    $arquivo = PRODUTOS_PASTA_IMG . basename($caminho);
    if (is_file($arquivo)) {
        unlink($arquivo);
    }
}