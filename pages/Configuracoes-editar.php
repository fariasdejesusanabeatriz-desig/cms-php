<?php
/* ==========================================================================
   Configurações (logo) — editar (o "U" do CRUD: Update)
   ========================================================================== */

require_once '../includes/auth.php';
require_once '../config/conexao.php';
require_once '../includes/usuarios-funcoes.php';
require_once '../includes/configuracoes-funcoes.php';

exigirAdmin();

$id = (int) get('id');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? $id);
}

$sql = $pdo->prepare('SELECT * FROM configuracoes WHERE id = :id');
$sql->execute([':id' => $id]);
$atual = $sql->fetch();

if (!$atual) {
    definirMensagemProduto('danger', 'Registro não encontrado.');
    header('Location: configuracoes.php');
    exit;
}

$item  = ['logo' => $atual['logo']];
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarCsrfProduto()) {
        $erros['geral'] = 'Sessão expirada. Recarregue a página e tente novamente.';
    }

    // Na edição o logo é opcional: se não enviar, mantém o atual
    $erroImagem = null;
    $caminho    = salvarImagemProduto($_FILES['logo'] ?? null, $erroImagem);
    if ($caminho === false) {
        $erros['logo'] = $erroImagem;
    }

    if (!$erros) {
        if ($caminho) {
            $sql = $pdo->prepare('UPDATE configuracoes SET logo = :logo WHERE id = :id');
            $sql->execute([':logo' => $caminho, ':id' => $id]);
            removerImagemProduto($atual['logo']); // apaga o logo antigo
        }
        definirMensagemProduto('success', 'Logo alterado com sucesso!');
        header('Location: configuracoes.php');
        exit;
    }

    // Deu erro em outro ponto: descarta o logo novo que acabou de subir
    if (is_string($caminho)) {
        removerImagemProduto($caminho);
    }
}

$titulo     = 'Editar logo';
$menuAtivo  = 'configuracoes';
$acao       = 'configuracoes-editar.php?id=' . $id;
$textoBotao = 'Salvar alterações';
require_once '../includes/header.php';
?>

      <main class="content">
        <div class="page-header">
          <div>
            <ol class="breadcrumb">
              <li><a href="dashboard.php">Dashboard</a></li>
              <li><a href="configuracoes.php">Configurações</a></li>
              <li>Editar</li>
            </ol>
            <h1>Editar logo</h1>
            <p>Troque a imagem do logo #<?= (int) $atual['id'] ?>.</p>
          </div>
        </div>

        <?php if (isset($erros['geral'])): ?>
          <div class="alert alert-danger" role="alert"><?= e($erros['geral']) ?></div>
        <?php endif; ?>

        <?php require 'configuracoes-form.php'; ?>
      </main>

<?php require_once '../includes/footer.php'; ?>