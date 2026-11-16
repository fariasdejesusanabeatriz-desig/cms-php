<?php
/* ==========================================================================
   Produtos — listagem com busca, filtro e paginação (o "R" do CRUD: Read)
   ========================================================================== */

require_once '../includes/auth.php';
require_once '../config/conexao.php';
require_once '../includes/usuarios-funcoes.php';
require_once '../includes/produtos-funcoes.php';

exigirAdmin(); // só administradores gerenciam produtos

$porPagina = 8; // registros exibidos em cada página

/* ---- 1. Filtros recebidos pela URL (?busca=...&parcelas=...) ---- */
$busca    = get('busca');
$parcelas = get('parcelas');
$pagina   = max(1, (int) get('pagina'));

/* ---- 2. Monta o WHERE conforme os filtros preenchidos --------------------
   As condições e os valores ficam em arrays separados: os valores NUNCA são
   concatenados no SQL, vão sempre pelos parâmetros do prepared statement. */
$condicoes  = [];
$parametros = [];

if ($busca !== '') {
    $condicoes[] = '(nome LIKE :busca1 OR descricao LIKE :busca2)';
    // addcslashes() faz % e _ digitados pelo usuário serem tratados como texto comum
    $termo = '%' . addcslashes($busca, '%_\\') . '%';
    $parametros[':busca1'] = $termo;
    $parametros[':busca2'] = $termo;
}
if (ctype_digit($parcelas) && (int) $parcelas >= 1 && (int) $parcelas <= 6) {
    $condicoes[] = 'parcelas = :parcelas';
    $parametros[':parcelas'] = (int) $parcelas;
}

$where = $condicoes ? 'WHERE ' . implode(' AND ', $condicoes) : '';

/* ---- 3. Total de registros (para calcular a quantidade de páginas) ------- */
$sql = $pdo->prepare("SELECT COUNT(*) FROM produtos $where");
$sql->execute($parametros);
$total = (int) $sql->fetchColumn();

$totalPaginas = max(1, (int) ceil($total / $porPagina));
$pagina       = min($pagina, $totalPaginas);
$offset       = ($pagina - 1) * $porPagina;

/* ---- 4. Busca só os registros da página atual ---------------------------- */
$sql = $pdo->prepare(
    "SELECT id, nome, descricao, preco, parcelas, imagem, criado_em
       FROM produtos
       $where
      ORDER BY nome
      LIMIT :limite OFFSET :offset"
);
foreach ($parametros as $nome => $valor) {
    $sql->bindValue($nome, $valor);
}
// LIMIT e OFFSET precisam ser enviados como números inteiros
$sql->bindValue(':limite', $porPagina, PDO::PARAM_INT);
$sql->bindValue(':offset', $offset, PDO::PARAM_INT);
$sql->execute();
$produtos = $sql->fetchAll();

$temFiltro = $busca !== '' || $parcelas !== '';

/** Monta o link de uma página mantendo os filtros atuais. */
function linkPagina(int $numero): string
{
    $parametros = array_filter([
        'busca'    => get('busca'),
        'parcelas' => get('parcelas'),
    ], 'strlen');
    $parametros['pagina'] = $numero;
    return 'produtos.php?' . e(http_build_query($parametros));
}

$titulo    = 'Produtos';
$menuAtivo = 'produtos';
require_once '../includes/header.php';
?>

      <main class="content">

        <div class="page-header">
          <div>
            <ol class="breadcrumb">
              <li><a href="dashboard.php">Dashboard</a></li>
              <li>Produtos</li>
            </ol>
            <h1>Produtos</h1>
            <p>Gerencie os produtos exibidos na loja.</p>
          </div>
          <div class="page-header__actions">
            <a href="produtos-cadastrar.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Novo produto</a>
          </div>
        </div>

        <!-- Mensagem de retorno (cadastrado, alterado, excluído...) -->
        <?php exibirMensagemProduto(); ?>

        <section class="card">

          <!-- Filtros: o formulário envia via GET (?busca=...&parcelas=...) -->
          <form class="filters" id="formFiltros" action="produtos.php" method="get">
            <div class="input-icon">
              <i class="bi bi-search"></i>
              <input type="search" class="form-control" id="busca" name="busca" placeholder="Buscar por nome ou descrição..." aria-label="Buscar" value="<?= e($busca) ?>">
            </div>
            <select class="form-select" id="filtroParcelas" name="parcelas" aria-label="Filtrar por parcelas">
              <option value="">Todas as parcelas</option>
              <?php for ($i = 1; $i <= 6; $i++): ?>
                <option value="<?= $i ?>" <?= $parcelas === (string) $i ? 'selected' : '' ?>><?= $i ?>x</option>
              <?php endfor; ?>
            </select>
            <button type="submit" class="btn btn-light"><i class="bi bi-funnel"></i> Filtrar</button>
            <?php if ($temFiltro): ?>
              <a href="produtos.php" class="btn btn-ghost"><i class="bi bi-x-circle"></i> <span class="filters__clear-text">Limpar</span></a>
            <?php endif; ?>
          </form>

          <div class="table-responsive">
            <table class="table table--stack" id="tabelaProdutos">
              <thead>
                <tr>
                  <th>Produto</th>
                  <th>Preço</th>
                  <th>Parcelas</th>
                  <th>Cadastro</th>
                  <th class="col-actions">Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($produtos as $p): ?>
                <tr>
                  <td class="col-user">
                    <div class="user-cell">
                      <?= imagemProduto($p) ?>
                      <div><span class="user-cell__name"><?= e($p['nome']) ?></span><span class="user-cell__email"><?= e(resumoTexto($p['descricao'])) ?></span></div>
                    </div>
                  </td>
                  <td data-label="Preço"><?= formatarPreco($p['preco']) ?></td>
                  <td data-label="Parcelas" class="text-muted"><?= e(textoParcelas((int) $p['parcelas'])) ?></td>
                  <td data-label="Cadastro" class="text-muted"><?= formatarData($p['criado_em']) ?></td>
                  <td class="col-actions">
                    <a href="produtos-editar.php?id=<?= $p['id'] ?>" class="action-btn" title="Editar" aria-label="Editar <?= e($p['nome']) ?>"><i class="bi bi-pencil"></i></a>
                    <button type="button" class="action-btn action-btn--danger btn-excluir" data-id="<?= $p['id'] ?>" data-nome="<?= e($p['nome']) ?>" title="Excluir" aria-label="Excluir <?= e($p['nome']) ?>"><i class="bi bi-trash3"></i></button>
                  </td>
                </tr>
                <?php endforeach; ?>

                <?php if (!$produtos): ?>
                <tr>
                  <td colspan="5" class="table-empty">
                    <i class="bi bi-search"></i>
                    <?= $temFiltro ? 'Nenhum produto encontrado com os filtros informados.' : 'Nenhum produto cadastrado.' ?>
                  </td>
                </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <!-- Paginação -->
          <div class="card__footer">
            <span class="pagination-info">Mostrando <strong><?= count($produtos) ?></strong> de <strong><?= $total ?></strong> <?= $total === 1 ? 'produto' : 'produtos' ?></span>
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
          <form id="formExcluir" action="produtos-excluir.php" method="post">
            <?= csrfCampoProduto() ?>
            <input type="hidden" name="id" id="excluirId" value="">
            <div class="modal__body">
              <div class="modal__icon modal__icon--danger"><i class="bi bi-trash3"></i></div>
              <h3 class="modal__title" id="modalExcluirTitulo">Excluir produto?</h3>
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
$scripts = ['produtos.js'];
require_once '../includes/footer.php';
?>