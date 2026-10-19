<?php
/* ==========================================================================
   Contato — Cadastro Inicial (o "C" do CRUD: Create)
   ========================================================================== */

require_once '../includes/auth.php';
require_once '../config/conexao.php';
require_once '../includes/contato-funcoes.php';

exigirAdmin();

// 1. Verifica se os dados já existem no banco
$dadosAtuais = buscarConfiguracoesContato($pdo);

// Se já tem dados, não deixa cadastrar de novo
if (!empty($dadosAtuais['telefones']) || !empty($dadosAtuais['emails']) || !empty($dadosAtuais['horario_semana'])) {
    definirMensagem('As informações de contato já estão cadastradas. Altere-as abaixo.', 'info');
    redirecionar('contatos-editar.php');
}

// Valores iniciais para o formulário vazio
$dados = [
    'telefones' => [], 'emails' => [],
    'horario_semana' => '', 'horario_sabado' => '', 'horario_domingo' => '',
    'link_instagram' => '', 'link_facebook' => '', 'link_tiktok' => ''
];
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidar();

    // 1) Lê os dados
    $dados = lerFormularioContato();

    // 2) Valida
    $erros = validarContato($dados);

    // 3) Grava no banco
    if (!$erros) {
        salvarConfiguracoesContato($pdo, $dados);

        // Redirecionar depois do POST evita cadastrar de novo ao apertar F5
        definirMensagem('Informações de contato cadastradas com sucesso!');
        redirecionar('contatos.php');
    }
}

// Variáveis usadas pelo formulário (includes/contato-form.php)
$acao       = 'contatos-cadastrar.php';
$voltarPara = 'contatos.php';

$titulo    = 'Cadastrar Contato';
$menuAtivo = 'contatos';
require_once '../includes/header.php';
?>

      <main class="content">

        <div class="page-header">
          <div>
            <ol class="breadcrumb">
              <li><a href="dashboard.php">Dashboard</a></li>
              <li><a href="contatos.php">Contato</a></li>
              <li>Cadastrar</li>
            </ol>
            <h1>Cadastrar Contato</h1>
            <p>Adicione as primeiras informações de contato do site.</p>
          </div>
          <div class="page-header__actions">
            <a href="contatos.php" class="btn btn-light"><i class="bi bi-arrow-left"></i> Voltar para a lista</a>
          </div>
        </div>

        <?php exibirMensagem(); ?>

        <!-- Formulário compartilhado (igual ao da edição) -->
        <?php require '../includes/contato-form.php'; ?>

      </main>

<?php
$scripts = ['contato-form.js']; 
require_once '../includes/footer.php';
?>