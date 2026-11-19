<?php
/* ==========================================================================
   Formulário de Quem Somos (incluído pelo quem-somos-editar.php)
   Espera: $dados, $erros, $acao, $voltarPara
   ========================================================================== */
?>
<?php if (!empty($erros)): ?>
        <div class="alert alert-danger" style="margin-bottom: 16px;">
          <ul style="margin: 0; padding-left: 20px;">
            <?php foreach ($erros as $erro): ?>
              <li><?= e($erro) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
<?php endif; ?>

        <form action="<?= e($acao) ?>" method="post" enctype="multipart/form-data">
          <?= csrfCampo() ?>

          <section class="card">

            <!-- ================= TEXTOS ================= -->
            <div class="card__header">
              <div>
                <h2 class="card__title"><i class="bi bi-file-text text-primary"></i> Textos institucionais</h2>
                <p class="card__subtitle">Conteúdo exibido na página "Quem somos" do site</p>
              </div>
            </div>
            <div class="card__body">
              <div style="margin-bottom: 16px;">
                <label for="texto_historia" style="display: block; font-weight: 600; margin-bottom: 6px;">Nossa história</label>
                <textarea id="texto_historia" name="texto_historia" class="form-control" rows="5" placeholder="Conte a história da empresa..."><?= e($dados['texto_historia'] ?? '') ?></textarea>
              </div>
              <div style="margin-bottom: 16px;">
                <label for="texto_missao" style="display: block; font-weight: 600; margin-bottom: 6px;">Missão</label>
                <textarea id="texto_missao" name="texto_missao" class="form-control" rows="3" placeholder="Qual é a missão da empresa?"><?= e($dados['texto_missao'] ?? '') ?></textarea>
              </div>
              <div>
                <label for="texto_valores" style="display: block; font-weight: 600; margin-bottom: 6px;">Valores</label>
                <textarea id="texto_valores" name="texto_valores" class="form-control" rows="3" placeholder="Quais são os valores?"><?= e($dados['texto_valores'] ?? '') ?></textarea>
              </div>
            </div>

            <!-- ================= FOTOS ================= -->
            <div class="card__header">
              <div>
                <h2 class="card__title"><i class="bi bi-image text-success"></i> Fotos da página</h2>
                <p class="card__subtitle">Deixe o campo em branco para manter a foto atual</p>
              </div>
            </div>
            <div class="card__body">
              <div class="grid-2">
                <div>
                  <label for="foto1" style="display: block; font-weight: 600; margin-bottom: 6px;">Foto 1 (Principal)</label>
                  <?php if (!empty($dados['foto1'])): ?>
                    <div style="margin-bottom: 10px;">
                      <img src="../uploads/quem-somos/<?= e($dados['foto1']) ?>" alt="Foto 1 atual" style="max-height: 150px; border-radius: 8px;">
                    </div>
                  <?php endif; ?>
                  <input type="file" id="foto1" name="foto1" class="form-control" accept="image/*">
                </div>
                <div>
                  <label for="foto2" style="display: block; font-weight: 600; margin-bottom: 6px;">Foto 2 (Secundária)</label>
                  <?php if (!empty($dados['foto2'])): ?>
                    <div style="margin-bottom: 10px;">
                      <img src="../uploads/quem-somos/<?= e($dados['foto2']) ?>" alt="Foto 2 atual" style="max-height: 150px; border-radius: 8px;">
                    </div>
                  <?php endif; ?>
                  <input type="file" id="foto2" name="foto2" class="form-control" accept="image/*">
                </div>
              </div>
            </div>

            <!-- ================= AÇÕES ================= -->
            <div class="card__footer" style="justify-content: flex-end; gap: 8px;">
              <a href="<?= e($voltarPara) ?>" class="btn btn-light">Cancelar</a>
              <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Salvar alterações</button>
            </div>

          </section>
        </form>