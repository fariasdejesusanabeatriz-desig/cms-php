<?php
/* ==========================================================================
   Depoimentos — cadastro (o "C" do CRUD: Create)
   --------------------------------------------------------------------------
   A mesma página exibe o formulário (GET) e recebe o envio (POST):
     GET  → mostra o formulário vazio
     POST → valida; se estiver tudo certo, faz o INSERT e redireciona;
            se houver erro, mostra o formulário de novo com os dados digitados
   ========================================================================== */

require_once '../includes/auth.php';
require_once '../config/conexao.php';
require_once '../includes/depoimentos-funcoes.php';

exigirAdmin();

// Valores iniciais do formulário
$dados = [
    'autor'  => '', 
    'texto'  => '', 
    'status' => 1,
];
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidar();

    $dados = lerFormularioDepoimento();

    // 1) Valida os campos
    $erros = validarDepoimento($dados);

    // 2) Grava no banco
    if (!$erros) {
        $sql = $pdo->prepare(
            'INSERT INTO depoimentos (autor, texto, status)
             VALUES (:autor, :texto, :status)'
        );
        $sql->execute([
            ':autor'  => $dados['autor'],
            ':texto'  => $dados['texto'],
            ':status' => $dados['status'],
        ]);

        // Redirecionar depois do POST evita cadastrar de novo ao apertar F5
        definirMensagem('Depoimento cadastrado com sucesso!');
        redirecionar('depoimentos.php');
    }
}

// Variáveis usadas pelo formulário (includes/depoimento-form.php)
$acao       = 'depoimentos-cadastrar.php';
$voltarPara = 'depoimentos.php';

$titulo    = 'Novo Depoimento';
$menuAtivo = 'depoimentos';
require_once '../includes/header.php';
?>

      <main class="content">

        <div class="page-header">
          <div>
            <ol class="breadcrumb">
              <li><a href="dashboard.php">Dashboard</a></li>
              <li><a href="depoimentos.php">Depoimentos</a></li>
              <li>Cadastrar</li>
            </ol>
            <h1>Novo Depoimento</h1>
            <p>Adicione a opinião de um cliente para exibir no site.</p>
          </div>
          <div class="page-header__actions">
            <a href="<?= $voltarPara ?>" class="btn btn-light"><i class="bi bi-arrow-left"></i> Voltar para a lista</a>
          </div>
        </div>

        <?php exibirMensagem(); ?>

<?php require '../includes/depoimento-form.php'; ?>

      </main>

<?php
$scripts = [];
require_once '../includes/footer.php';
?>