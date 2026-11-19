<?php
/* ==========================================================================
   Formulário de configurações (logo) — usado por configuracoes-cadastrar.php e configuracoes-editar.php
   Variáveis esperadas: $item (array), $erros (array), $acao (string), $textoBotao (string)
   ========================================================================== */
?>
<section class="card">
  <form action="<?= e($acao) ?>" method="post" enctype="multipart/form-data" novalidate>
    <?= csrfCampoProduto() ?>

    <div class="form-group">
      <label class="form-label" for="logo">Logo <?= $item['logo'] ? '' : '*' ?></label>
      <?php if ($item['logo']): ?>
        <div style="margin-bottom:.6rem;">
          <img src="../<?= e($item['logo']) ?>" alt="Logo atual" style="max-width:240px;max-height:96px;object-fit:contain;border-radius:12px;background:#fef2ec;padding:8px;">
          <small class="text-muted" style="display:block;">Logo atual. Envie outro só se quiser trocá-lo.</small>
        </div>
      <?php endif; ?>
      <input type="file" class="form-control" id="logo" name="logo" accept="image/jpeg,image/png,image/webp,image/gif">
      <small class="text-muted">JPG, PNG, WEBP ou GIF, até 2 MB. Prefira PNG com fundo transparente.</small>
      <?php if (isset($erros['logo'])): ?><small class="form-error"><?= e($erros['logo']) ?></small><?php endif; ?>
    </div>

    <div class="form-actions">
      <a href="configuracoes.php" class="btn btn-light">Cancelar</a>
      <button type="submit" class="btn btn-primary"><?= e($textoBotao) ?></button>
    </div>
  </form>
</section>