<?php
/* ==========================================================================
   Configurações (logo) — listagem com paginação (o "R" do CRUD: Read)
   ========================================================================== */

require_once '../includes/auth.php';
require_once '../config/conexao.php';
require_once '../includes/usuarios-funcoes.php';
require_once '../includes/configuracoes-funcoes.php';

exigirAdmin(); // só administradores gerenciam as configurações

$porPagina = 8; // registros exibidos em cada página
$pagina    = max(1, (int) get('pagina'));

/* ---- 1. Total de registros (para calcular a quantidade de páginas) ------- */
$total = (int) $pdo->query('SELECT COUNT(*) FROM configuracoes')->fetchColumn();

$totalPaginas = max(1, (int) ceil($total / $porPagina));
$pagina       = min($pagina, $totalPaginas);
$offset       = ($pagina - 1) * $porPagina;

/* ---- 2. Busca só os registros da página atual ---------------------------- */
$sql = $pdo->prepare(
    'SELECT id, logo, criado_em
       FROM configuracoes
      ORDER BY id DESC
      LIMIT :limite OFFSET :offset'
);
// LIMIT e OFFSET precisam ser enviados como números inteiros
$sql->bindValue(':limite', $porPagina, PDO::PARAM_INT);
$sql->bindValue(':offset', $offset, PDO::PARAM_INT);
$sql->execute();
$itens = $sql->fetchAll();

// O site exibe o logo mais recente; guardamos o id dele para destacar na lista
$idNoSite = (int) $pdo->query('SELECT MAX(id) FROM configuracoes')->fetchColumn();

/** Monta o link de uma página. */
function linkPagina(int $numero): string
{
    return 'configuracoes.php?' . e(http_build_query(['pagina' => $numero]));
}

$titulo    = 'Configurações';
$menuAtivo = 'configuracoes';
require_once '../includes/header.php';
?>

      <main class="content">

        <div class="page-header">
          <div>
            <ol class="breadcrumb">
              <li><a href="dashboard.php">Dashboard</a></li>
              <li>Configurações</li>
            </ol>
            <h1>Configurações</h1>
            <p>Gerencie o logo do site. O site exibe o cadastro mais recente.</p>
          </div>
          <div class="page-header__actions">
            <a href="configuracoes-cadastrar.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Novo logo</a>
          </div>
        </div>

        <!-- Mensagem de retorno (cadastrado, alterado, excluído...) -->
        <?php exibirMensagemProduto(); ?>

        <section class="card">

          <div class="table-responsive">
            <table class="table table--stack" id="tabelaConfiguracoes">
              <thead>
                <tr>
                  <th>Logo</th>
                  <th>Cadastro</th>
                  <th class="col-actions">Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($itens as $c): ?>
                <tr>
                  <td class="col-user">
                    <div class="user-cell">
                      <?= imagemLogo($c) ?>
                      <div><span class="user-cell__name"><?= (int) $c['id'] === $idNoSite ? 'Logo atual <small class="text-muted">(no site)</small>' : 'Logo #' . (int) $c['id'] ?></span></div>
                    </div>
                  </td>
                  <td data-label="Cadastro" class="text-muted"><?= formatarData($c['criado_em']) ?></td>
                  <td class="col-actions">
                    <a href="configuracoes-editar.php?id=<?= $c['id'] ?>" class="action-btn" title="Editar" aria-label="Editar logo #<?= (int) $c['id'] ?>"><i class="bi bi-pencil"></i></a>
                    <button type="button" class="action-btn action-btn--danger btn-excluir" data-id="<?= $c['id'] ?>" data-nome="o logo #<?= (int) $c['id'] ?>" title="Excluir" aria-label="Excluir logo #<?= (int) $c['id'] ?>"><i class="bi bi-trash3"></i></button>
                  </td>
                </tr>
                <?php endforeach; ?>

                <?php if (!$itens): ?>
                <tr>
                  <td colspan="3" class="table-empty">
                    <i class="bi bi-image"></i>
                    Nenhum logo cadastrado. O site está usando o logo em texto padrão.
                  </td>
                </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <!-- Paginação -->
          <div class="card__footer">
            <span class="pagination-info">Mostrando <strong><?= count($itens) ?></strong> de <strong><?= $total ?></strong> <?= $total === 1 ? 'registro' : 'registros' ?></span>
            <?php if ($totalPaginas > 1): ?>
            <nav aria-label="Paginação">
              <ul class="pagination">
                <?php if ($pagina > 1): ?>
                  <li><a href="<?= linkPagina($pagina - 1) ?>" aria-label="Anterior"><i class="bi bi-chevron-left"></i></a></li>
                <?php else: ?>
                  <li class="is-disabled"><span aria-label="Anterior"><i class="bi bi-chevron-left"></i></span></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                  <?php if ($i === $pagina): ?>
                    <li class="is-active"><span aria-current="page"><?= $i ?></span></li>
                  <?php else: ?>
                    <li><a href="<?= linkPagina($i) ?>"><?= $i ?></a></li>
                  <?php endif; ?>
                <?php endfor; ?>

                <?php if ($pagina < $totalPaginas): ?>
                  <li><a href="<?= linkPagina($pagina + 1) ?>" aria-label="Próxima"><i class="bi bi-chevron-right"></i></a></li>
                <?php else: ?>
                  <li class="is-disabled"><span aria-label="Próxima"><i class="bi bi-chevron-right"></i></span></li>
                <?php endif; ?>
              </ul>
            </nav>
            <?php endif; ?>
          </div>
        </section>

      </main>

      <!-- =====================================================================
           MODAL DE CONFIRMAÇÃO DE EXCLUSÃO
           O produtos.js preenche o nome e o id ao clicar na lixeira.
           O id vai no campo oculto e é enviado via POST.
           ===================================================================== -->
      <div class="modal" id="modalExcluir" role="dialog" aria-modal="true" aria-labelledby="modalExcluirTitulo" aria-hidden="true">
        <div class="modal__dialog">
          <form id="formExcluir" action="configuracoes-excluir.php" method="post">
            <?= csrfCampoProduto() ?>
            <input type="hidden" name="id" id="excluirId" value="">
            <div class="modal__body">
              <div class="modal__icon modal__icon--danger"><i class="bi bi-trash3"></i></div>
              <h3 class="modal__title" id="modalExcluirTitulo">Excluir logo?</h3>
              <p class="modal__text">Você está prestes a excluir <strong id="excluirNome"></strong>. Esta ação não poderá ser desfeita.</p>
            </div>
            <div class="modal__footer">
              <button type="button" class="btn btn-light" data-modal-close>Cancelar</button>
              <button type="submit" class="btn btn-danger"><i class="bi bi-trash3"></i> Sim, excluir</button>
            </div>
          </form>
        </div>
      </div>

<?php
$scripts = ['produtos.js']; // o mesmo script do modal de exclusão serve aqui
require_once '../includes/footer.php';
?>