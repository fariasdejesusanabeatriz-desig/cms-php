<?php
/* ==========================================================================
   Quem Somos — Cadastro Inicial (o "C" do CRUD: Create)
   --------------------------------------------------------------------------
   Como a configuração é única:
     GET  → mostra o formulário (se o banco estiver vazio)
     POST → valida e faz o INSERT
     Trava: se já existirem dados, manda para a tela de edição.
   ========================================================================== */

require_once '../includes/auth.php';
require_once '../config/conexao.php';
require_once '../includes/quem-somos-funcoes.php';

exigirAdmin();

// 1. Verifica se os dados já existem no banco
$dadosAtuais = buscarConfiguracoesQuemSomos($pdo);

// Se já tem textos preenchidos, não deixa cadastrar de novo
if (!empty($dadosAtuais['texto_historia']) || !empty($dadosAtuais['texto_missao'])) {
    definirMensagem('As informações da página "Quem Somos" já estão cadastradas. Altere-as abaixo.', 'info');
    redirecionar('quem-somos-editar.php');
}

// Valores iniciais para o formulário vazio
$dados = [
    'texto_historia' => '', 'texto_missao' => '', 'texto_valores' => '',
    'foto1' => null, 'foto2' => null
];
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidar();

    // 1) Lê os dados
    $dados = lerFormularioQuemSomos();

    // 2) Processa as fotos
    $foto1 = null; $foto2 = null; $erroFoto = null;
    
    if (isset($_FILES['foto1']) && $_FILES['foto1']['error'] !== UPLOAD_ERR_NO_FILE) {
        $foto1 = salvarImagem($_FILES['foto1'], 'quem-somos', $erroFoto);
        if ($erroFoto) { $erros[] = 'Foto 1: ' . $erroFoto; }
    }
    if (isset($_FILES['foto2']) && $_FILES['foto2']['error'] !== UPLOAD_ERR_NO_FILE) {
        $foto2 = salvarImagem($_FILES['foto2'], 'quem-somos', $erroFoto);
        if ($erroFoto) { $erros[] = 'Foto 2: ' . $erroFoto; }
    }

    // 3) Grava no banco
    if (!$erros) {
        salvarConfiguracoesQuemSomos($pdo, $dados, $foto1, $foto2);
        definirMensagem('Página "Quem Somos" cadastrada com sucesso!');
        redirecionar('quem-somos-editar.php');
    }
}

// Variáveis usadas pelo formulário
$acao       = 'quem-somos-cadastrar.php';
$voltarPara = 'dashboard.php';

$titulo    = 'Cadastrar Quem Somos';
$menuAtivo = 'quem-somos';
$usaEditor = true;
require_once '../includes/header.php';
?>

      <main class="content">

        <div class="page-header">
          <div>
            <ol class="breadcrumb">
              <li><a href="dashboard.php">Dashboard</a></li>
              <li>Quem Somos</li>
              <li>Cadastrar</li>
            </ol>
            <h1><?= e($titulo) ?></h1>
            <p>Adicione as primeiras informações da página institucional.</p>
          </div>
          <div class="page-header__actions">
            <a href="<?= $voltarPara ?>" class="btn btn-light"><i class="bi bi-arrow-left"></i> Voltar</a>
          </div>
        </div>

        <?php exibirMensagem(); ?>

        <?php require '../includes/quem-somos-form.php'; ?>

      </main>

<?php
$scripts = []; 
require_once '../includes/footer.php';
?>