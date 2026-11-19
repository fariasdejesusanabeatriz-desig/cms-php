<?php
/* ==========================================================================
   Contato — edição (o "U" do CRUD: Update)
   --------------------------------------------------------------------------
   Endereço: contatos-editar.php
     GET  → busca o registro (SELECT) e preenche o formulário
     POST → valida e grava as alterações (UPDATE)
   
   Como a configuração de contato é única para o site, não há ID na URL.
   ========================================================================== */

require_once '../includes/auth.php';
require_once '../config/conexao.php';
require_once '../includes/contato-funcoes.php';

exigirAdmin(); // Apenas administradores gerenciam o contato

$voltarPara = 'contatos.php';

/* ---- Busca o registro que será editado ---------------------------------- */
$dados = buscarConfiguracoesContato($pdo);

// Se por algum motivo não existir (banco limpo), cria um array padrão
if (!$dados) {
    $dados = [
        'telefones' => [], 'emails' => [],
        'horario_semana' => '', 'horario_sabado' => '', 'horario_domingo' => '',
        'link_instagram' => '', 'link_facebook' => '', 'link_tiktok' => ''
    ];
}

$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidar();

    // 1) Lê os dados do formulário
    $dados = lerFormularioContato();

    // 2) Valida os campos
    $erros = validarContato($dados);

    // 3) Grava no banco
    if (!$erros) {
        // A função salvarConfiguracoesContato já faz INSERT se não existir ou UPDATE se já existir
        salvarConfiguracoesContato($pdo, $dados);

        definirMensagem('Informações de contato atualizadas com sucesso!');
        redirecionar($voltarPara);
    }
}

// Variáveis usadas pelo formulário (includes/contato-form.php)
$acao       = 'contatos-editar.php';
$voltarPara = 'contatos.php';

$titulo    = 'Editar Contato';
$menuAtivo = 'contato';
require_once '../includes/header.php';
?>

      <main class="content">

        <div class="page-header">
          <div>
            <ol class="breadcrumb">
              <li><a href="dashboard.php">Dashboard</a></li>
              <li><a href="contatos.php">Contato</a></li>
              <li>Editar</li>
            </ol>
            <h1><?= e($titulo) ?></h1>
            <p>Atualize os telefones, e-mails, horários e redes sociais exibidos no site.</p>
          </div>
          <div class="page-header__actions">
            <a href="<?= $voltarPara ?>" class="btn btn-light"><i class="bi bi-arrow-left"></i> Voltar para a lista</a>
          </div>
        </div>

        <?php exibirMensagem(); ?>

        <?php require '../includes/contato-form.php'; ?>

      </main>

<?php
$scripts = ['contato-form.js'];
require_once '../includes/footer.php';
?>