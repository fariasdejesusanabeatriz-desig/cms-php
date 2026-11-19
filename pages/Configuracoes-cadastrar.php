<?php
/* ==========================================================================
   Configurações (logo) — cadastrar (o "C" do CRUD: Create)
   ========================================================================== */

require_once '../includes/auth.php';
require_once '../config/conexao.php';
require_once '../includes/usuarios-funcoes.php';
require_once '../includes/configuracoes-funcoes.php';

exigirAdmin();

$item  = ['logo' => ''];
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarCsrfProduto()) {
        $erros['geral'] = 'Sessão expirada. Recarregue a página e tente novamente.';
    }

    // Na criação o logo é obrigatório
    $erroImagem = null;
    $caminho    = salvarImagemProduto($_FILES['logo'] ?? null, $erroImagem);
    if ($caminho === false) {
        $erros['logo'] = $erroImagem;
    } elseif ($caminho === null) {
        $erros['logo'] = 'Envie a imagem do logo.';
    }

    if (!$erros) {
        $sql = $pdo->prepare('INSERT INTO configuracoes (logo) VALUES (:logo)');
        $sql->execute([':logo' => $caminho]);
        definirMensagemProduto('success', 'Logo cadastrado com sucesso! Ele já aparece no site.');
        header('Location: configuracoes.php');
        exit;
    }

    // Deu erro em outro ponto (ex.: sessão expirada): descarta a imagem que acabou de subir
    if (is_string($caminho)) {
        removerImagemProduto($caminho);
    }
}

$titulo     = 'Novo logo';
$menuAtivo  = 'configuracoes';
$acao       = 'configuracoes-cadastrar.php';
$textoBotao = 'Cadastrar logo';
require_once '../includes/header.php';
?>

      <main class="content">
        <div class="page-header">
          <div>
            <ol class="breadcrumb">
              <li><a href="dashboard.php">Dashboard</a></li>
              <li><a href="configuracoes.php">Configurações</a></li>
              <li>Novo</li>
            </ol>
            <h1>Novo logo</h1>
            <p>Envie a imagem do logo exibido no cabeçalho do site.</p>
          </div>
        </div>

        <?php if (isset($erros['geral'])): ?>
          <div class="alert alert-danger" role="alert"><?= e($erros['geral']) ?></div>
        <?php endif; ?>

        <?php require 'configuracoes-form.php'; ?>
      </main>

<?php require_once '../includes/footer.php'; ?>