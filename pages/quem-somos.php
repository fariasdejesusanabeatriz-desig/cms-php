<?php
/* ==========================================================================
   Quem Somos — listagem (o "R" do CRUD: Read)
   ========================================================================== */

require_once '../includes/auth.php';
require_once '../config/conexao.php';
require_once '../includes/quem-somos-funcoes.php';

exigirAdmin();

// Busca os dados atuais (se não existirem, retorna arrays vazios)
$dados = buscarConfiguracoesQuemSomos($pdo);

$titulo    = 'Quem Somos';
$menuAtivo = 'quem-somos';
require_once '../includes/header.php';
?>

      <main class="content">

        <div class="page-header">
          <div>
            <ol class="breadcrumb">
              <li><a href="dashboard.php">Dashboard</a></li>
              <li>Quem Somos</li>
            </ol>
            <h1>Quem Somos</h1>
            <p>Gerencie os textos institucionais e as fotos exibidas no site.</p>
          </div>
          <div class="page-header__actions">
            <!-- Botão para editar tudo de uma vez -->
            <a href="quem-somos-editar.php" class="btn btn-primary"><i class="bi bi-pencil"></i> Editar Página</a>
          </div>
        </div>

        <!-- Mensagem de retorno (cadastrado, alterado, excluído...) -->
        <?php exibirMensagem(); ?>

        <section class="card">
          <div class="card-header bg-transparent border-bottom-0 pb-0">
            <h2 class="h5 mb-0"><i class="bi bi-file-text text-primary me-2"></i> Textos Institucionais</h2>
          </div>
          <div class="table-responsive">
            <table class="table table--stack mb-0">
              <tbody>
                <tr>
                  <td data-label="História" style="width: 200px;" class="text-muted">Nossa História</td>
                  <td><?= e($dados['texto_historia']) ?: '<span class="text-muted">Não informado</span>' ?></td>
                </tr>
                <tr>
                  <td data-label="Missão" class="text-muted">Missão</td>
                  <td><?= e($dados['texto_missao']) ?: '<span class="text-muted">Não informado</span>' ?></td>
                </tr>
                <tr>
                  <td data-label="Valores" class="text-muted">Valores</td>
                  <td><?= e($dados['texto_valores']) ?: '<span class="text-muted">Não informado</span>' ?></td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="card-header bg-transparent border-bottom-0 pb-0 pt-4">
            <h2 class="h5 mb-0"><i class="bi bi-image text-success me-2"></i> Fotos da Página</h2>
          </div>
          <div class="table-responsive">
            <table class="table table--stack mb-0">
              <tbody>
                <!-- Foto 1 -->
                <tr>
                  <td data-label="Foto 1" style="width: 200px;" class="text-muted">Foto 1 (Principal)</td>
                  <td>
                    <?php if (!empty($dados['foto1'])): ?>
                      <div class="d-flex align-items-center gap-2">
                        <img src="../uploads/quem-somos/<?= e($dados['foto1']) ?>" alt="Foto 1" style="max-height: 80px; border-radius: 6px;">
                        <button type="button" class="action-btn action-btn--danger btn-excluir-foto" 
                                data-campo="foto1" data-valor="<?= e($dados['foto1']) ?>" title="Excluir Foto 1">
                          <i class="bi bi-trash3"></i>
                        </button>
                      </div>
                    <?php else: ?>
                      <span class="text-muted">Nenhuma foto cadastrada</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <!-- Foto 2 -->
                <tr>
                  <td data-label="Foto 2" class="text-muted">Foto 2 (Secundária)</td>
                  <td>
                    <?php if (!empty($dados['foto2'])): ?>
                      <div class="d-flex align-items-center gap-2">
                        <img src="../uploads/quem-somos/<?= e($dados['foto2']) ?>" alt="Foto 2" style="max-height: 80px; border-radius: 6px;">
                        <button type="button" class="action-btn action-btn--danger btn-excluir-foto" 
                                data-campo="foto2" data-valor="<?= e($dados['foto2']) ?>" title="Excluir Foto 2">
                          <i class="bi bi-trash3"></i>
                        </button>
                      </div>
                    <?php else: ?>
                      <span class="text-muted">Nenhuma foto cadastrada</span>
                    <?php endif; ?>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

      </main>

      <!-- =====================================================================
           MODAL DE CONFIRMAÇÃO DE EXCLUSÃO DE FOTO
           O quem-somos.js preenche os dados ao clicar na lixeira.
           ===================================================================== -->
      <div class="modal" id="modalExcluirFoto" role="dialog" aria-modal="true" aria-labelledby="modalExcluirFotoTitulo" aria-hidden="true">
        <div class="modal__dialog">
          <form id="formExcluirFoto" action="quem-somos-excluir.php" method="post">
            <?= csrfCampo() ?>
            <input type="hidden" name="campo_foto" id="excluirCampoFoto" value="">
            <div class="modal__body">
              <div class="modal__icon modal__icon--danger"><i class="bi bi-trash3"></i></div>
              <h3 class="modal__title" id="modalExcluirFotoTitulo">Excluir foto?</h3>
              <p class="modal__text">Você está prestes a excluir esta foto. Esta ação não poderá ser desfeita.</p>
            </div>
            <div class="modal__footer">
              <button type="button" class="btn btn-light" data-modal-close>Cancelar</button>
              <button type="submit" class="btn btn-danger"><i class="bi bi-trash3"></i> Sim, excluir</button>
            </div>
          </form>
        </div>
      </div>

<?php
$scripts = ['quem-somos.js'];
require_once '../includes/footer.php';
?>