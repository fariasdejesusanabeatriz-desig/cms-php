<?php
/* ==========================================================================
   Formulário de depoimento — usado no cadastro E na edição
   --------------------------------------------------------------------------
   A página que inclui este arquivo deve definir:
     $modo             → 'cadastrar' ou 'editar'
     $acao             → endereço que recebe o formulário (action)
     $dados            → valores dos campos (do banco ou do envio anterior)
     $erros            → erros da validação do PHP [campo => mensagem]
     $voltarPara       → página do botão "Cancelar"
     $registro         → (só na edição) linha original do banco
   ========================================================================== */
?>

        <?php if ($erros): ?>
        <div class="alert alert-danger" role="alert">
          <i class="bi bi-exclamation-octagon"></i>
          <div><strong>Não foi possível salvar.</strong><br>Verifique os campos destacados abaixo.</div>
          <button type="button" class="alert__close" aria-label="Fechar"><i class="bi bi-x-lg"></i></button>
        </div>
        <?php endif; ?>

        <form id="formDepoimento" data-modo="<?= $modo ?>" action="<?= e($acao) ?>" method="post" novalidate>
          <?= csrfCampo() ?>

          <div class="form-layout">

            <!-- ================= COLUNA PRINCIPAL ================= -->
            <section class="card">
              <div class="card__body">

                <!-- Seção 1: Dados do Depoimento -->
                <div class="form-section">
                  <h2 class="form-section__title">Informações do Depoimento</h2>
                  <p class="form-section__desc">Dados do cliente e o texto que será exibido no site.</p>

                  <div class="form-grid">
                    
                    <!-- Autor -->
                    <div class="form-group col-12<?= erroClasse($erros, 'autor') ?>">
                      <label class="form-label" for="autor">Nome do Autor <span class="req">*</span></label>
                      <div class="input-icon">
                        <i class="bi bi-person"></i>
                        <input type="text" class="form-control" id="autor" name="autor" placeholder="Ex.: Ana Silva" maxlength="100" value="<?= e($dados['autor']) ?>">
                      </div>
                      <div class="invalid-feedback"><?= erroTexto($erros, 'autor') ?></div>
                    </div>

                    <!-- Texto do Depoimento (Editor) -->
                    <div class="form-group col-12<?= erroClasse($erros, 'texto') ?>">
                      <label class="form-label" for="texto">Texto do Depoimento <span class="req">*</span></label>
                      <!-- O Summernote transforma este textarea em um editor visual -->
                      <textarea id="texto" name="texto" required><?= e($dados['texto']) ?></textarea>
                      <div class="invalid-feedback"><?= erroTexto($erros, 'texto') ?></div>
                    </div>

                  </div>
                </div>

              </div>

              <div class="card__footer">
                <span class="form-hint mt-0"><span class="req" style="color: var(--danger)">*</span> Campos obrigatórios</span>
                <div class="d-flex gap-1">
                  <a href="<?= e($voltarPara) ?>" class="btn btn-light">Cancelar</a>
                  <button type="submit" class="btn btn-primary" id="btnSalvar"><i class="bi bi-check-lg"></i> <?= $modo === 'cadastrar' ? 'Cadastrar depoimento' : 'Salvar alterações' ?></button>
                </div>
              </div>
            </section>

            <!-- ================= COLUNA LATERAL ================= -->
            <div class="form-layout__aside">

              <!-- Status (Ativo/Inativo) -->
              <section class="card">
                <div class="card__header"><h2 class="card__title">Publicação</h2></div>
                <div class="card__body">
                  <div class="form-group<?= erroClasse($erros, 'status') ?>">
                    <span class="form-label">Status no site</span>
                    <label class="switch">
                      <input type="checkbox" id="status" name="status" value="1" <?= $dados['status'] == 1 ? 'checked' : '' ?>>
                      <span class="switch__track"></span>
                      <span class="switch__label" id="statusTexto">Ativo</span>
                    </label>
                    <div class="invalid-feedback"><?= erroTexto($erros, 'status') ?></div>
                  </div>
                </div>
              </section>

              <?php if ($modo === 'editar' && isset($registro)): ?>
              <!-- Informações do registro (somente na edição) -->
              <section class="card">
                <div class="card__header"><h2 class="card__title">Informações do registro</h2></div>
                <div class="card__body">
                  <ul class="meta-list">
                    <li><span>ID</span><strong>#<?= $registro['id'] ?></strong></li>
                    <li><span>Cadastrado em</span><strong><?= formatarData($registro['criado_em']) ?></strong></li>
                    <?php if (!empty($registro['atualizado_em'])): ?>
                    <li><span>Última atualização</span><strong><?= formatarData($registro['atualizado_em'], true) ?></strong></li>
                    <?php endif; ?>
                  </ul>
                </div>
              </section>
              <?php else: ?>
              <!-- Dica (somente no cadastro) -->
              <section class="card">
                <div class="card__body">
                  <h2 class="card__title mb-0"><i class="bi bi-lightbulb" style="color: var(--warning)"></i> Dica</h2>
                  <p class="card__subtitle mb-0">Use o editor de texto para formatar o depoimento com negrito e itálico, se desejar.</p>
                </div>
              </section>
              <?php endif; ?>

            </div>

          </div>
        </form>