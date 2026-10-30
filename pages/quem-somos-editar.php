<?php
/* ==========================================================================
   Quem Somos — Edição (o "U" do CRUD: Update / Create)
   ========================================================================== */

require_once '../includes/auth.php';
require_once '../config/conexao.php';
require_once '../includes/quem-somos-funcoes.php';
// Certifique-se de que a função salvarImagem() esteja disponível (geralmente está em um arquivo global de funções)

exigirAdmin();

$voltarPara = 'dashboard.php'; // Mude para a página de listagem se houver uma
$dados = buscarConfiguracoesQuemSomos($pdo);
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidar();
    $dados = lerFormularioQuemSomos();

    // Mantém as fotos atuais por padrão
    $foto1 = $dados['foto1'] ?? null; 
    $foto2 = $dados['foto2'] ?? null;
    $erroFoto = null;

    // Upload da Foto 1
    if (isset($_FILES['foto1']) && $_FILES['foto1']['error'] !== UPLOAD_ERR_NO_FILE) {
        $novaFoto1 = salvarImagem($_FILES['foto1'], 'quem-somos', $erroFoto);
        if ($erroFoto) { $erros[] = 'Foto 1: ' . $erroFoto; } else { $foto1 = $novaFoto1; }
    }
    
    // Upload da Foto 2
    if (isset($_FILES['foto2']) && $_FILES['foto2']['error'] !== UPLOAD_ERR_NO_FILE) {
        $novaFoto2 = salvarImagem($_FILES['foto2'], 'quem-somos', $erroFoto);
        if ($erroFoto) { $erros[] = 'Foto 2: ' . $erroFoto; } else { $foto2 = $novaFoto2; }
    }

    if (!$erros) {
        salvarConfiguracoesQuemSomos($pdo, $dados, $foto1, $foto2);
        definirMensagem('Página "Quem Somos" atualizada com sucesso!');
        redirecionar('quem-somos-editar.php');
    }
}

// Variáveis para o formulário
$acao = 'quem-somos-editar.php';
$titulo = 'Gerenciar Quem Somos';
$menuAtivo = 'quem-somos';
$usaEditor = true; // Ative se você usa TinyMCE/CKEditor nos textareas
require_once '../includes/header.php';
?>

      <main class="content">

        <div class="page-header">
          <div>
            <ol class="breadcrumb">
              <li><a href="dashboard.php">Dashboard</a></li>
              <li>Quem Somos</li>
              <li>Editar</li>
            </ol>
            <h1><?= e($titulo) ?></h1>
            <p>Edite os textos institucionais e as fotos exibidas no site.</p>
          </div>
        </div>

        <?php exibirMensagem(); ?>

        <?php require '../includes/quem-somos-form.php'; ?>

      </main>

<?php
$scripts = []; // Se precisar de JS para preview de imagem, adicione o arquivo aqui
require_once '../includes/footer.php';
?>