<?php
/* ==========================================================================
   Principal (início) — editar (o "U" do CRUD: Update)
   ========================================================================== */

require_once '../includes/auth.php';
require_once '../config/conexao.php';
require_once '../includes/usuarios-funcoes.php';
require_once '../includes/principal-funcoes.php';

exigirAdmin();

$id = (int) get('id');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? $id);
}

$sql = $pdo->prepare('SELECT * FROM principal WHERE id = :id');
$sql->execute([':id' => $id]);
$atual = $sql->fetch();

if (!$atual) {
    definirMensagemProduto('danger', 'Registro não encontrado.');
    header('Location: principal.php');
    exit;
}

$item = [
    'titulo'    => $atual['titulo'],
    'descricao' => $atual['descricao'],
    'imagem'    => $atual['imagem'],
];
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarCsrfProduto()) {
        $erros['geral'] = 'Sessão expirada. Recarregue a página e tente novamente.';
    }

    [$dados, $errosCampos] = validarPrincipal($_POST);
    $erros = array_merge($erros, $errosCampos);
    $item  = array_merge($item, $dados);

    // Na edição a imagem é opcional: se não enviar, mantém a atual
    $erroImagem = null;
    $caminho    = salvarImagemProduto($_FILES['imagem'] ?? null, $erroImagem);
    if ($caminho === false) {
        $erros['imagem'] = $erroImagem;
    }

    if (!$erros) {
        $novaImagem = $caminho ?: $atual['imagem'];

        $sql = $pdo->prepare(
            'UPDATE principal
                SET titulo = :titulo, descricao = :descricao, imagem = :imagem
              WHERE id = :id'
        );
        $sql->execute([
            ':titulo'    => $dados['titulo'],
            ':descricao' => $dados['descricao'],
            ':imagem'    => $novaImagem,
            ':id'        => $id,
        ]);

        if ($caminho) {
            removerImagemProduto($atual['imagem']); // apaga a imagem antiga
        }
        definirMensagemProduto('success', 'Alterado com sucesso!');
        header('Location: principal.php');
        exit;
    }

    // Deu erro em outro campo: descarta a imagem nova que acabou de subir
    if (is_string($caminho)) {
        removerImagemProduto($caminho);
    }
    $item['imagem'] = $atual['imagem'];
}

$titulo     = 'Editar (principal)';
$menuAtivo  = 'principal';
$acao       = 'principal-editar.php?id=' . $id;
$textoBotao = 'Salvar alterações';
require_once '../includes/header.php';
?>

      <main class="content">
        <div class="page-header">
          <div>
            <ol class="breadcrumb">
              <li><a href="dashboard.php">Dashboard</a></li>
              <li><a href="principal.php">Principal</a></li>
              <li>Editar</li>
            </ol>
            <h1>Editar banner principal</h1>
            <p>Altere os dados de <strong><?= e($atual['titulo']) ?></strong>.</p>
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

<?php require_once '../includes/footer.php'; ?>