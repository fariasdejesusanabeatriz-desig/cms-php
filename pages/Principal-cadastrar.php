<?php
/* ==========================================================================
   Principal (início) — cadastrar (o "C" do CRUD: Create)
   ========================================================================== */

require_once '../includes/auth.php';
require_once '../config/conexao.php';
require_once '../includes/usuarios-funcoes.php';
require_once '../includes/principal-funcoes.php';

exigirAdmin();

$item  = ['titulo' => '', 'descricao' => '', 'imagem' => ''];
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarCsrfProduto()) {
        $erros['geral'] = 'Sessão expirada. Recarregue a página e tente novamente.';
    }

    [$dados, $errosCampos] = validarPrincipal($_POST);
    $erros = array_merge($erros, $errosCampos);
    $item  = array_merge($item, $dados);

    // Na criação a imagem é obrigatória
    $erroImagem = null;
    $caminho    = salvarImagemProduto($_FILES['imagem'] ?? null, $erroImagem);
    if ($caminho === false) {
        $erros['imagem'] = $erroImagem;
    } elseif ($caminho === null) {
        $erros['imagem'] = 'Envie a imagem.';
    }

    if (!$erros) {
        $sql = $pdo->prepare(
            'INSERT INTO principal (titulo, descricao, imagem)
             VALUES (:titulo, :descricao, :imagem)'
        );
        $sql->execute([
            ':titulo'    => $dados['titulo'],
            ':descricao' => $dados['descricao'],
            ':imagem'    => $caminho,
        ]);
        definirMensagemProduto('success', 'Cadastrado com sucesso! Ele já aparece na página inicial.');
        header('Location: principal.php');
        exit;
    }

    // Deu erro em outro campo: descarta a imagem que acabou de subir
    if (is_string($caminho)) {
        removerImagemProduto($caminho);
    }
}

$titulo     = 'Novo (principal)';
$menuAtivo  = 'principal';
$acao       = 'principal-cadastrar.php';
$textoBotao = 'Cadastrar';
require_once '../includes/header.php';
?>

      <main class="content">
        <div class="page-header">
          <div>
            <ol class="breadcrumb">
              <li><a href="dashboard.php">Dashboard</a></li>
              <li><a href="principal.php">Principal</a></li>
              <li>Novo</li>
            </ol>
            <h1>Novo banner principal</h1>
            <p>Título, descrição e imagem exibidos no início do site.</p>
          </div>
        </div>

        <?php if (isset($erros['geral'])): ?>
          <div class="alert alert-danger" role="alert"><?= e($erros['geral']) ?></div>
        <?php endif; ?>
        
        <style>
        .form-pagina .card > form { padding: 24px; }
        .form-pagina .form-group { margin-bottom: 20px; }
        .form-pagina .form-label { display: block; margin-bottom: 6px; font-weight: 600; }
        .form-pagina .form-control,
        .form-pagina .form-select { width: 100%; max-width: 100%; box-sizing: border-box; }
        .form-pagina small { display: block; margin-top: 6px; }
        .form-pagina .form-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 8px; }
        </style>
        
        <div class="form-pagina">
          <?php require '../includes/principal-form.php'; ?>
        </div>
      
      </main>

<?php require_once '../includes/footer.php'; ?>