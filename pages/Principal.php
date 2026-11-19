<?php
/* ==========================================================================
   Principal (início) — listagem com busca e paginação (o "R" do CRUD: Read)
   ========================================================================== */

require_once '../includes/auth.php';
require_once '../config/conexao.php';
require_once '../includes/usuarios-funcoes.php';
require_once '../includes/principal-funcoes.php';

exigirAdmin(); // só administradores gerenciam a página inicial

$porPagina = 8; // registros exibidos em cada página

/* ---- 1. Filtros recebidos pela URL (?busca=...) ---- */
$busca  = get('busca');
$pagina = max(1, (int) get('pagina'));

/* ---- 2. Monta o WHERE conforme os filtros preenchidos --------------------
   As condições e os valores ficam em arrays separados: os valores NUNCA são
   concatenados no SQL, vão sempre pelos parâmetros do prepared statement. */
$condicoes  = [];
$parametros = [];

if ($busca !== '') {
    $condicoes[] = '(titulo LIKE :busca1 OR descricao LIKE :busca2)';
    // addcslashes() faz % e _ digitados pelo usuário serem tratados como texto comum
    $termo = '%' . addcslashes($busca, '%_\\') . '%';
    $parametros[':busca1'] = $termo;
    $parametros[':busca2'] = $termo;
}

$where = $condicoes ? 'WHERE ' . implode(' AND ', $condicoes) : '';

/* ---- 3. Total de registros (para calcular a quantidade de páginas) ------- */
$sql = $pdo->prepare("SELECT COUNT(*) FROM principal $where");
$sql->execute($parametros);
$total = (int) $sql->fetchColumn();

$totalPaginas = max(1, (int) ceil($total / $porPagina));
$pagina       = min($pagina, $totalPaginas);
$offset       = ($pagina - 1) * $porPagina;

/* ---- 4. Busca só os registros da página atual ---------------------------- */
$sql = $pdo->prepare(
    "SELECT id, titulo, descricao, imagem, criado_em
       FROM principal
       $where
      ORDER BY id DESC
      LIMIT :limite OFFSET :offset"
);
foreach ($parametros as $nome => $valor) {
    $sql->bindValue($nome, $valor);
}
// LIMIT e OFFSET precisam ser enviados como números inteiros
$sql->bindValue(':limite', $porPagina, PDO::PARAM_INT);
$sql->bindValue(':offset', $offset, PDO::PARAM_INT);
$sql->execute();
$itens = $sql->fetchAll();

// O site exibe o registro mais recente; guardamos o id dele para destacar na lista
$idNoSite = (int) $pdo->query('SELECT MAX(id) FROM principal')->fetchColumn();

$temFiltro = $busca !== '';

/** Monta o link de uma página mantendo os filtros atuais. */
function linkPagina(int $numero): string
{
    $parametros = array_filter(['busca' => get('busca')], 'strlen');
    $parametros['pagina'] = $numero;
    return 'principal.php?' . e(http_build_query($parametros));
}

$titulo    = 'Principal (início)';
$menuAtivo = 'principal';
require_once '../includes/header.php';
?>

      <main class="content">

        <div class="page-header">
          <div>
            <ol class="breadcrumb">
              <li><a href="dashboard.php">Dashboard</a></li>
              <li>Principal</li>
            </ol>
            <h1>Principal (início)</h1>
            <p>Gerencie o banner principal da página inicial. O site exibe o cadastro mais recente.</p>
          </div>
          <div class="page-header__actions">
            <a href="principal-cadastrar.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Novo</a>
          </div>
        </div>

        <!-- Mensagem de retorno (cadastrado, alterado, excluído...) -->
        <?php exibirMensagemProduto(); ?>

        <section class="card">

          <!-- Filtros: o formulário envia via GET (?busca=...) -->
          <form class="filters" id="formFiltros" action="principal.php" method="get">
            <div class="input-icon">
              <i class="bi bi-search"></i>
              <input type="search" class="form-control" id="busca" name="busca" placeholder="Buscar por título ou descrição..." aria-label="Buscar" value="<?= e($busca) ?>">
            </div>
            <button type="submit" class="btn btn-light"><i class="bi bi-funnel"></i> Filtrar</button>
            <?php if ($temFiltro): ?>
              <a href="principal.php" class="btn btn-ghost"><i class="bi bi-x-circle"></i> <span class="filters__clear-text">Limpar</span></a>
            <?php endif; ?>
          </form>

          <div class="table-responsive">
            <table class="table table--stack" id="tabelaPrincipal">
              <thead>
                <tr>
                  <th>Título</th>
                  <th>Cadastro</th>
                  <th class="col-actions">Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($itens as $p): ?>
                <tr>
                  <td class="col-user">
                    <div class="user-cell">
                      <?= imagemPrincipal($p) ?>
                      <div>
                        <span class="user-cell__name"><?= e($p['titulo']) ?><?= (int) $p['id'] === $idNoSite ? ' <small class="text-muted">(no site)</small>' : '' ?></span>
                        <span class="user-cell__email"><?= e(resumoTexto($p['descricao'])) ?></span>
                      </div>
                    </div>
                  </td>
                  <td data-label="Cadastro" class="text-muted"><?= formatarData($p['criado_em']) ?></td>
                  <td class="col-actions">
                    <a href="principal-editar.php?id=<?= $p['id'] ?>" class="action-btn" title="Editar" aria-label="Editar <?= e($p['titulo']) ?>"><i class="bi bi-pencil"></i></a>
                    <button type="button" class="action-btn action-btn--danger btn-excluir" data-id="<?= $p['id'] ?>" data-nome="<?= e($p['titulo']) ?>" title="Excluir" aria-label="Excluir <?= e($p['titulo']) ?>"><i class="bi bi-trash3"></i></button>
                  </td>
                </tr>
                <?php endforeach; ?>

                <?php if (!$itens): ?>
                <tr>
                  <td colspan="3" class="table-empty">
                    <i class="bi bi-search"></i>
                    <?= $temFiltro ? 'Nenhum registro encontrado com os filtros informados.' : 'Nenhum registro cadastrado. O site está usando o banner padrão.' ?>
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
           O principal.js preenche o nome e o id ao clicar na lixeira.
           O id vai no campo oculto e é enviado via POST.
           ===================================================================== -->
      <div class="modal" id="modalExcluir" role="dialog" aria-modal="true" aria-labelledby="modalExcluirTitulo" aria-hidden="true">
        <div class="modal__dialog">
          <form id="formExcluir" action="principal-excluir.php" method="post">
            <?= csrfCampoProduto() ?>
            <input type="hidden" name="id" id="excluirId" value="">
            <div class="modal__body">
              <div class="modal__icon modal__icon--danger"><i class="bi bi-trash3"></i></div>
              <h3 class="modal__title" id="modalExcluirTitulo">Excluir registro?</h3>
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
$scripts = ['principal.js']; // script da listagem Principal (abre o modal de exclusão)
require_once '../includes/footer.php';
?>