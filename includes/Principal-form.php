<?php
/* ==========================================================================
   Formulário da seção principal — usado por principal-cadastrar.php e principal-editar.php
   Variáveis esperadas: $item (array), $erros (array), $acao (string), $textoBotao (string)
   ========================================================================== */
?>
<section class="card">
  <form action="<?= e($acao) ?>" method="post" enctype="multipart/form-data" novalidate>
    <?= csrfCampoProduto() ?>

    <div class="form-group">
      <label class="form-label" for="titulo">Título *</label>
      <input type="text" class="form-control" id="titulo" name="titulo" maxlength="120" required value="<?= e($item['titulo']) ?>">
      <?php if (isset($erros['titulo'])): ?><small class="form-error"><?= e($erros['titulo']) ?></small><?php endif; ?>
    </div>

    <div class="form-group">
      <label class="form-label" for="descricao">Descrição *</label>
      <textarea class="form-control" id="descricao" name="descricao" rows="4" required><?= e($item['descricao']) ?></textarea>
      <?php if (isset($erros['descricao'])): ?><small class="form-error"><?= e($erros['descricao']) ?></small><?php endif; ?>
    </div>

    <div class="form-group">
      <label class="form-label" for="imagem">Imagem <?= $item['imagem'] ? '' : '*' ?></label>
      <?php if ($item['imagem']): ?>
        <div style="margin-bottom:.6rem;">
          <img src="../<?= e($item['imagem']) ?>" alt="Imagem atual" width="96" height="96" style="object-fit:cover;border-radius:50%;background:#fef2ec;">
          <small class="text-muted" style="display:block;">Imagem atual. Envie outra só se quiser trocá-la.</small>
        </div>
      <?php endif; ?>
      <input type="file" class="form-control" id="imagem" name="imagem" accept="image/jpeg,image/png,image/webp,image/gif">
      <small class="text-muted">JPG, PNG, WEBP ou GIF, até 2 MB. Aparece em formato circular no site.</small>
      <?php if (isset($erros['imagem'])): ?><small class="form-error"><?= e($erros['imagem']) ?></small><?php endif; ?>
    </div>

    <div class="form-actions">
      <a href="principal.php" class="btn btn-light">Cancelar</a>
      <button type="submit" class="btn btn-primary"><?= e($textoBotao) ?></button>
    </div>
  </form>
</section>