<?php
/* ==========================================================================
   Produtos — cadastrar (o "C" do CRUD: Create)
   ========================================================================== */

require_once '../includes/auth.php';
require_once '../config/conexao.php';
require_once '../includes/usuarios-funcoes.php';
require_once '../includes/produtos-funcoes.php';

exigirAdmin();

$produto = ['nome' => '', 'descricao' => '', 'preco' => '', 'parcelas' => 1, 'imagem' => ''];
$erros   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarCsrfProduto()) {
        $erros['geral'] = 'Sessão expirada. Recarregue a página e tente novamente.';
    }

    [$dados, $errosCampos] = validarProduto($_POST);
    $erros   = array_merge($erros, $errosCampos);
    $produto = array_merge($produto, $dados);

    // Na criação a imagem é obrigatória
    $erroImagem = null;
    $caminho    = salvarImagemProduto($_FILES['imagem'] ?? null, $erroImagem);
    if ($caminho === false) {
        $erros['imagem'] = $erroImagem;
    } elseif ($caminho === null) {
        $erros['imagem'] = 'Envie a imagem do produto.';
    }

    if (!$erros) {
        $sql = $pdo->prepare(
            'INSERT INTO produtos (nome, descricao, preco, parcelas, imagem)
             VALUES (:nome, :descricao, :preco, :parcelas, :imagem)'
        );
        $sql->execute([
            ':nome'      => $dados['nome'],
            ':descricao' => $dados['descricao'],
            ':preco'     => precoParaDecimal($dados['preco']),
            ':parcelas'  => $dados['parcelas'],
            ':imagem'    => $caminho,
        ]);
        definirMensagemProduto('success', 'Produto cadastrado com sucesso!');
        header('Location: produtos.php');
        exit;
    }

    // Deu erro em outro campo: descarta a imagem que acabou de subir
    if (is_string($caminho)) {
        removerImagemProduto($caminho);
    }
}

$titulo    = 'Novo produto';
$menuAtivo = 'produtos';
$acao      = 'produtos-cadastrar.php';
$textoBotao = 'Cadastrar produto';
require_once '../includes/header.php';
?>

      <main class="content">
        <div class="page-header">
          <div>
            <ol class="breadcrumb">
              <li><a href="dashboard.php">Dashboard</a></li>
              <li><a href="produtos.php">Produtos</a></li>
              <li>Novo</li>
            </ol>
            <h1>Novo produto</h1>
            <p>Preencha os dados do produto.</p>
          </div>
        </div>

        <?php if (isset($erros['geral'])): ?>
          <div class="alert alert-danger" role="alert"><?= e($erros['geral']) ?></div>
        <?php endif; ?>

        <?php require '../includes/produtos-form.php'; ?>
      </main>

<?php require_once '../includes/footer.php'; ?>