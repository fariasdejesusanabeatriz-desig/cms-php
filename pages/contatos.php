<?php
/* ==========================================================================
   Contato — listagem e gerenciamento (o "R" do CRUD: Read)
   ========================================================================== */

require_once '../includes/auth.php';
require_once '../config/conexao.php';
require_once '../includes/contato-funcoes.php';

exigirAdmin(); // só administradores gerenciam o contato do site

// Busca as configurações (se não existir, retorna arrays vazios)
$dados = buscarConfiguracoesContato($pdo);

$titulo    = 'Contato';
$menuAtivo = 'contatos';
require_once '../includes/header.php';
?>

      <main class="content">

        <div class="page-header">
          <div>
            <ol class="breadcrumb">
              <li><a href="dashboard.php">Dashboard</a></li>
              <li>Contato</li>
            </ol>
            <h1>Fale Conosco</h1>
            <p>Gerencie as informações de contato exibidas no site.</p>
          </div>
          <div class="page-header__actions">
            <a href="contatos-editar.php" class="btn btn-primary"><i class="bi bi-pencil"></i> Editar contato</a>
          </div>
        </div>

        <!-- Mensagem de retorno (alterado, item excluído...) -->
        <?php exibirMensagem(); ?>

        <div class="grid-2">

          <!-- ================= TELEFONES ================= -->
          <section class="card">
            <div class="card__header">
              <div>
                <h2 class="card__title"><i class="bi bi-telephone-fill text-danger"></i> Telefones</h2>
                <p class="card__subtitle">Números exibidos no site</p>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table--stack">
                <tbody>
                  <?php if (empty($dados['telefones'])): ?>
                  <tr>
                    <td class="table-empty"><i class="bi bi-telephone"></i> Nenhum telefone cadastrado.</td>
                  </tr>
                  <?php else: ?>
                    <?php foreach ($dados['telefones'] as $index => $tel): ?>
                  <tr>
                    <td class="col-user">
                      <div class="user-cell">
                        <div><span class="user-cell__name"><?= e($tel) ?></span><span class="user-cell__email">WhatsApp</span></div>
                      </div>
                    </td>
                    <td class="col-actions">
                      <button type="button" class="action-btn action-btn--danger btn-excluir"
                              data-tipo="telefone" data-valor="<?= e($tel) ?>" data-indice="<?= $index ?>"
                              title="Excluir telefone" aria-label="Excluir <?= e($tel) ?>">
                        <i class="bi bi-trash3"></i>
                      </button>
                    </td>
                  </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </section>

          <!-- ================= E-MAILS ================= -->
          <section class="card">
            <div class="card__header">
              <div>
                <h2 class="card__title"><i class="bi bi-envelope-fill text-primary"></i> E-mails</h2>
                <p class="card__subtitle">Endereços exibidos no site</p>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table--stack">
                <tbody>
                  <?php if (empty($dados['emails'])): ?>
                  <tr>
                    <td class="table-empty"><i class="bi bi-envelope"></i> Nenhum e-mail cadastrado.</td>
                  </tr>
                  <?php else: ?>
                    <?php foreach ($dados['emails'] as $index => $email): ?>
                  <tr>
                    <td class="col-user">
                      <div class="user-cell">
                        <div><span class="user-cell__name"><?= e($email) ?></span></div>
                      </div>
                    </td>
                    <td class="col-actions">
                      <button type="button" class="action-btn action-btn--danger btn-excluir"
                              data-tipo="email" data-valor="<?= e($email) ?>" data-indice="<?= $index ?>"
                              title="Excluir e-mail" aria-label="Excluir <?= e($email) ?>">
                        <i class="bi bi-trash3"></i>
                      </button>
                    </td>
                  </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </section>

          <!-- ================= HORÁRIOS ================= -->
          <section class="card">
            <div class="card__header">
              <div>
                <h2 class="card__title"><i class="bi bi-alarm-fill text-warning"></i> Horário de atendimento</h2>
                <p class="card__subtitle">Dias e horários de funcionamento</p>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table--stack">
                <tbody>
                  <tr>
                    <td data-label="Segunda a Sexta" class="text-muted">Segunda a Sexta</td>
                    <td><strong><?= e($dados['horario_semana'] ?: 'Não informado') ?></strong></td>
                  </tr>
                  <tr>
                    <td data-label="Sábado" class="text-muted">Sábado</td>
                    <td><strong><?= e($dados['horario_sabado'] ?: 'Não informado') ?></strong></td>
                  </tr>
                  <tr>
                    <td data-label="Domingo" class="text-muted">Domingo</td>
                    <td><strong><?= e($dados['horario_domingo'] ?: 'Não informado') ?></strong></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>

          <!-- ================= REDES SOCIAIS ================= -->
          <section class="card">
            <div class="card__header">
              <div>
                <h2 class="card__title"><i class="bi bi-share-fill text-info"></i> Redes sociais</h2>
                <p class="card__subtitle">Links exibidos no site</p>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table--stack">
                <tbody>
                  <tr>
                    <td data-label="Instagram" class="text-muted"><i class="bi bi-instagram"></i> Instagram</td>
                    <td>
                      <?php if (!empty($dados['link_instagram'])): ?>
                        <a href="<?= e($dados['link_instagram']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-light">Acessar <i class="bi bi-box-arrow-up-right"></i></a>
                      <?php else: ?><span class="text-muted">Não configurado</span><?php endif; ?>
                    </td>
                  </tr>
                  <tr>
                    <td data-label="Facebook" class="text-muted"><i class="bi bi-facebook"></i> Facebook</td>
                    <td>
                      <?php if (!empty($dados['link_facebook'])): ?>
                        <a href="<?= e($dados['link_facebook']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-light">Acessar <i class="bi bi-box-arrow-up-right"></i></a>
                      <?php else: ?><span class="text-muted">Não configurado</span><?php endif; ?>
                    </td>
                  </tr>
                  <tr>
                    <td data-label="TikTok" class="text-muted"><i class="bi bi-tiktok"></i> TikTok</td>
                    <td>
                      <?php if (!empty($dados['link_tiktok'])): ?>
                        <a href="<?= e($dados['link_tiktok']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-light">Acessar <i class="bi bi-box-arrow-up-right"></i></a>
                      <?php else: ?><span class="text-muted">Não configurado</span><?php endif; ?>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>

        </div>

      </main>

      <!-- =====================================================================
           MODAL DE CONFIRMAÇÃO DE EXCLUSÃO
           Preenchido via JS ao clicar na lixeira de telefone/e-mail.
           ===================================================================== -->
      <div class="modal" id="modalExcluir" role="dialog" aria-modal="true" aria-labelledby="modalExcluirTitulo" aria-hidden="true">
        <div class="modal__dialog">
          <form id="formExcluir" action="contatos-excluir.php" method="post">
            <?= csrfCampo() ?>
            <input type="hidden" name="tipo" id="excluirTipo" value="">
            <input type="hidden" name="indice" id="excluirIndice" value="">
            <div class="modal__body">
              <div class="modal__icon modal__icon--danger"><i class="bi bi-trash3"></i></div>
              <h3 class="modal__title" id="modalExcluirTitulo">Excluir informação?</h3>
              <p class="modal__text">Você está prestes a excluir <strong id="excluirValor"></strong>. Esta ação não poderá ser desfeita.</p>
            </div>
            <div class="modal__footer">
              <button type="button" class="btn btn-light" data-modal-close>Cancelar</button>
              <button type="submit" class="btn btn-danger"><i class="bi bi-trash3"></i> Sim, excluir</button>
            </div>
          </form>
        </div>
      </div>

<?php
$scripts = ['contatos.js'];
require_once '../includes/footer.php';
?>