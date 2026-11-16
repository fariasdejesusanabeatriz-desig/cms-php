<?php
/* ==========================================================================
   Depoimentos — edição (o "U" do CRUD: Update)
   --------------------------------------------------------------------------
   Endereço: depoimentos-editar.php?id=5
     GET  → busca o registro (SELECT) e preenche o formulário
     POST → valida e grava as alterações (UPDATE)
   ========================================================================== */

require_once '../includes/auth.php';
require_once '../config/conexao.php';
require_once '../includes/depoimentos-funcoes.php';

exigirAdmin();

$id = (int) get('id');
$voltarPara = 'depoimentos.php';

/* ---- Busca o registro que será editado ---------------------------------- */
$sql = $pdo->prepare('SELECT * FROM depoimentos WHERE id = ?');
$sql->execute([$id]);
$registro = $sql->fetch();

if (!$registro) {
    definirMensagem('Depoimento não encontrado.', 'danger');
    redirecionar($voltarPara);
}

$dados = $registro; // na primeira exibição, o formulário mostra os dados do banco
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidar();

    // 1) Lê e valida os campos
    $dados = lerFormularioDepoimento();
    $erros = validarDepoimento($dados);

    // 2) Grava no banco
    if (!$erros) {
        $sql = $pdo->prepare(
            'UPDATE depoimentos
                SET autor = :autor, texto = :texto, status = :status
              WHERE id = :id'
        );
        $sql->execute([
            ':autor'  => $dados['autor'],
            ':texto'  => $dados['texto'],
            ':status' => $dados['status'],
            ':id'     => $id,
        ]);

        definirMensagem('Depoimento atualizado com sucesso!');
        redirecionar($voltarPara);
    }
}

// Variáveis usadas pelo formulário (includes/depoimento-form.php)
$acao       = 'depoimentos-editar.php?id=' . $id;
$titulo     = 'Editar Depoimento';
$menuAtivo  = 'depoimentos';
require_once '../includes/header.php';
?>

      <main class="content">

        <div class="page-header">
          <div>
            <ol class="breadcrumb">
              <li><a href="dashboard.php">Dashboard</a></li>
              <li><a href="depoimentos.php">Depoimentos</a></li>
              <li>Editar</li>
            </ol>
            <h1><?= e($titulo) ?></h1>
            <p>Atualize o texto ou o autor do depoimento.</p>
          </div>
          <div class="page-header__actions">
            <a href="<?= $voltarPara ?>" class="btn btn-light"><i class="bi bi-arrow-left"></i> Voltar para a lista</a>
          </div>
        </div>

        <?php exibirMensagem(); ?>

        <?php require '../includes/depoimento-form.php'; ?>

      </main>

<?php
$scripts = []; // Como não há foto nem editor complexo, não precisa de JS específico
require_once '../includes/footer.php';
?>