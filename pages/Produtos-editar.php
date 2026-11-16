<?php
/* ==========================================================================
   Produtos — editar (o "U" do CRUD: Update)
   ========================================================================== */

require_once '../includes/auth.php';
require_once '../config/conexao.php';
require_once '../includes/usuarios-funcoes.php';
require_once '../includes/produtos-funcoes.php';

exigirAdmin();

$id = (int) get('id');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
}

$sql = $pdo->prepare('SELECT * FROM produtos WHERE id = :id');
$sql->execute([':id' => $id]);
$atual = $sql->fetch();

if (!$atual) {
    definirMensagemProduto('danger', 'Produto não encontrado.');
    header('Location: produtos.php');
    exit;
}

$produto = [
    'nome'      => $atual['nome'],
    'descricao' => $atual['descricao'],
    'preco'     => number_format((float) $atual['preco'], 2, ',', ''),
    'parcelas'  => (int) $atual['parcelas'],
    'imagem'    => $atual['imagem'],
];
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarCsrfProduto()) {
        $erros['geral'] = 'Sessão expirada. Recarregue a página e tente novamente.';
    }

    [$dados, $errosCampos] = validarProduto($_POST);
    $erros   = array_merge($erros, $errosCampos);
    $produto = array_merge($produto, $dados);

    // Na edição a imagem é opcional: se não enviar, mantém a atual
    $erroImagem = null;
    $caminho    = salvarImagemProduto($_FILES['imagem'] ?? null, $erroImagem);
    if ($caminho === false) {
        $erros['imagem'] = $erroImagem;
    }

    if (!$erros) {
        $novaImagem = $caminho ?: $atual['imagem'];

        $sql = $pdo->prepare(
            'UPDATE produtos
                SET nome = :nome, descricao = :descricao, preco = :preco,
                    parcelas = :parcelas, imagem = :imagem
              WHERE id = :id'
        );
        $sql->execute([
            ':nome'      => $dados['nome'],
            ':descricao' => $dados['descricao'],
            ':preco'     => precoParaDecimal($dados['preco']),
            ':parcelas'  => $dados['parcelas'],
            ':imagem'    => $novaImagem,
            ':id'        => $id,
        ]);

        if ($caminho) {
            removerImagemProduto($atual['imagem']); // apaga a imagem antiga
        }
        definirMensagemProduto('success', 'Produto alterado com sucesso!');
        header('Location: produtos.php');
        exit;
    }

    // Deu erro em outro campo: descarta a imagem nova que acabou de subir
    if (is_string($caminho)) {
        removerImagemProduto($caminho);
    }
    $produto['imagem'] = $atual['imagem'];
}

$titulo     = 'Editar produto';
$menuAtivo  = 'produtos';
$acao       = 'produtos-editar.php?id=' . $id;
$textoBotao = 'Salvar alterações';
require_once '../includes/header.php';
?>

      <main class="content">
        <div class="page-header">
          <div>
            <ol class="breadcrumb">
              <li><a href="dashboard.php">Dashboard</a></li>
              <li><a href="produtos.php">Produtos</a></li>
              <li>Editar</li>
            </ol>
            <h1>Editar produto</h1>
            <p>Altere os dados de <strong><?= e($atual['nome']) ?></strong>.</p>
          </div>
        </div>

        <?php if (isset($erros['geral'])): ?>
          <div class="alert alert-danger" role="alert"><?= e($erros['geral']) ?></div>
        <?php endif; ?>

        <?php require 'produtos-form.php'; ?>
      </main>

<?php require_once '../includes/footer.php'; ?>