<?php
/* ==========================================================================
   Depoimentos — listagem com busca, filtros e paginação (o "R" do CRUD: Read)
   ========================================================================== */

require_once '../includes/auth.php';
require_once '../config/conexao.php';
// Vamos criar este arquivo de funções abaixo
require_once '../includes/depoimentos-funcoes.php'; 

exigirAdmin(); // só administradores gerenciam depoimentos

$porPagina = 8; // registros exibidos em cada página

/* ---- 1. Filtros recebidos pela URL (?busca=...&status=...) ---- */
$busca  = get('busca');
$status = get('status');
$pagina = max(1, (int) get('pagina'));

/* ---- 2. Monta o WHERE conforme os filtros preenchidos -------------------- */
$condicoes  = [];
$parametros = [];

if ($busca !== '') {
    $condicoes[] = '(autor LIKE :busca1 OR texto LIKE :busca2)';
    $termo = '%' . addcslashes($busca, '%_\\') . '%';
    $parametros[':busca1'] = $termo;
    $parametros[':busca2'] = $termo;
}
if ($status === 'ativo' || $status === 'inativo') {
    $condicoes[] = 'status = :status';
    $parametros[':status'] = $status === 'ativo' ? 1 : 0;
}

$where = $condicoes ? 'WHERE ' . implode(' AND ', $condicoes) : '';

/* ---- 3. Total de registros (para calcular a quantidade de páginas) ------- */
$sql = $pdo->prepare("SELECT COUNT(*) FROM depoimentos $where");
$sql->execute($parametros);
$total = (int) $sql->fetchColumn();

$totalPaginas = max(1, (int) ceil($total / $porPagina));
$pagina       = min($pagina, $totalPaginas);
$offset       = ($pagina - 1) * $porPagina;

/* ---- 4. Busca só os registros da página atual ---------------------------- */
$sql = $pdo->prepare(
    "SELECT id, autor, texto, status, criado_em
       FROM depoimentos
       $where
      ORDER BY id DESC
      LIMIT :limite OFFSET :offset"
);
foreach ($parametros as $nome => $valor) {
    $sql->bindValue($nome, $valor);
}
$sql->bindValue(':limite', $porPagina, PDO::PARAM_INT);
$sql->bindValue(':offset', $offset, PDO::PARAM_INT);
$sql->execute();
$depoimentos = $sql->fetchAll();

$temFiltro = $busca !== '' || $status !== '';

/** Monta o link de uma página mantendo os filtros atuais. */
function linkPagina(int $numero): string
{
    $parametros = array_filter([
        'busca'  => get('busca'),
        'status' => get('status'),
    ], 'strlen');
    $parametros['pagina'] = $numero;
    return 'depoimentos.php?' . e(http_build_query($parametros));
}

$titulo    = 'Depoimentos';
$menuAtivo = 'depoimentos';
require_once '../includes/header.php';
?>

      <main class="content">

        <div class="page-header">
          <div>
            <ol class="breadcrumb">
              <li><a href="dashboard.php">Dashboard</a></li>
              <li>Depoimentos</li>
            </ol>
            <h1>Depoimentos</h1>
            <p>Gerencie os depoimentos de clientes exibidos no site.</p>
          </div>
          <div class="page-header__actions">
            <a href="depoimentos-cadastrar.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Novo depoimento</a>
          </div>
        </div>

        <!-- Mensagem de retorno -->
        <?php exibirMensagem(); ?>

        <section class="card">

          <!-- Filtros -->
          <form class="filters" id="formFiltros" action="depoimentos.php" method="get">
            <div class="input-icon">
              <i class="bi bi-search"></i>
              <input type="search" class="form-control" id="busca" name="busca" placeholder="Buscar por autor ou texto..." aria-label="Buscar" value="<?= e($busca) ?>">
            </div>
            <select class="form-select" id="filtroStatus" name="status" aria-label="Filtrar por status">
              <option value="">Todos os status</option>
              <option value="ativo"   <?= $status === 'ativo'   ? 'selected' : '' ?>>Ativo</option>
              <option value="inativo" <?= $status === 'inativo' ? 'selected' : '' ?>>Inativo</option>
            </select>
            <button type="submit" class="btn btn-light"><i class="bi bi-funnel"></i> Filtrar</button>
            <?php if ($temFiltro): ?>
              <a href="depoimentos.php" class="btn btn-ghost"><i class="bi bi-x-circle"></i> <span class="filters__clear-text">Limpar</span></a>
            <?php endif; ?>
          </form>

          <div class="table-responsive">
            <table class="table table--stack" id="tabelaDepoimentos">
              <thead>
                <tr>
                  <th>Autor</th>
                  <th>Depoimento (Resumo)</th>
                  <th>Status</th>
                  <th>Cadastro</th>
                  <th class="col-actions">Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($depoimentos as $d): ?>
                <tr>
                  <td class="col-user">
                    <div class="user-cell">
                      <div>
                        <span class="user-cell__name"><?= e($d['autor']) ?></span>
                      </div>
                    </div>
                  </td>
                  <td data-label="Depoimento" class="text-muted">
                    <?= e(mb_strimwidth($d['texto'], 0, 80, '...')) ?>
                  </td>
                  <td data-label="Status"><?= badgeStatusDepoimento($d['status']) ?></td>
                  <td data-label="Cadastro" class="text-muted"><?= formatarData($d['criado_em']) ?></td>
                  <td class="col-actions">
                    <a href="depoimentos-editar.php?id=<?= $d['id'] ?>" class="action-btn" title="Editar" aria-label="Editar depoimento de <?= e($d['autor']) ?>"><i class="bi bi-pencil"></i></a>
                    <button type="button" class="action-btn action-btn--danger btn-excluir" data-id="<?= $d['id'] ?>" data-nome="<?= e($d['autor']) ?>" title="Excluir" aria-label="Excluir depoimento de <?= e($d['autor']) ?>"><i class="bi bi-trash3"></i></button>
                  </td>
                </tr>
                <?php endforeach; ?>

                <?php if (!$depoimentos): ?>
                <tr>
                  <td colspan="5" class="table-empty">
                    <i class="bi bi-search"></i>
                    <?= $temFiltro ? 'Nenhum depoimento encontrado com os filtros informados.' : 'Nenhum depoimento cadastrado.' ?>
                  </td>
                </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <!-- Paginação -->
          <div class="card__footer">
            <span class="pagination-info">Mostrando <strong><?= count($depoimentos) ?></strong> de <strong><?= $total ?></strong> <?= $total === 1 ? 'depoimento' : 'depoimentos' ?></span>
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

      <!-- MODAL DE CONFIRMAÇÃO DE EXCLUSÃO -->
      <div class="modal" id="modalExcluir" role="dialog" aria-modal="true" aria-labelledby="modalExcluirTitulo" aria-hidden="true">
        <div class="modal__dialog">
          <form id="formExcluir" action="depoimentos-excluir.php" method="post">
            <?= csrfCampo() ?>
            <input type="hidden" name="id" id="excluirId" value="">
            <div class="modal__body">
              <div class="modal__icon modal__icon--danger"><i class="bi bi-trash3"></i></div>
              <h3 class="modal__title" id="modalExcluirTitulo">Excluir depoimento?</h3>
              <p class="modal__text">Você está prestes a excluir o depoimento de <strong id="excluirNome"></strong>. Esta ação não poderá ser desfeita.</p>
            </div>
            <div class="modal__footer">
              <button type="button" class="btn btn-light" data-modal-close>Cancelar</button>
              <button type="submit" class="btn btn-danger"><i class="bi bi-trash3"></i> Sim, excluir</button>
            </div>
          </form>
        </div>
      </div>

<?php
// Reutilizando o mesmo JS de usuários, pois a lógica do modal é idêntica
$scripts = ['usuarios.js'];
require_once '../includes/footer.php';
?>