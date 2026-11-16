<?php
/* ==========================================================================
   Formulário de produto — usado por produtos-cadastrar.php e produtos-editar.php
   Variáveis esperadas: $produto (array), $erros (array), $acao (string), $textoBotao (string)
   ========================================================================== */
?>
<section class="card">
  <form action="<?= e($acao) ?>" method="post" enctype="multipart/form-data" novalidate>
    <?= csrfCampoProduto() ?>

    <div class="form-group">
      <label class="form-label" for="nome">Nome *</label>
      <input type="text" class="form-control" id="nome" name="nome" maxlength="120" required value="<?= e($produto['nome']) ?>">
      <?php if (isset($erros['nome'])): ?><small class="form-error"><?= e($erros['nome']) ?></small><?php endif; ?>
    </div>

    <div class="form-group">
      <label class="form-label" for="descricao">Descrição *</label>
      <textarea class="form-control" id="descricao" name="descricao" rows="4" required><?= e($produto['descricao']) ?></textarea>
      <?php if (isset($erros['descricao'])): ?><small class="form-error"><?= e($erros['descricao']) ?></small><?php endif; ?>
    </div>

    <div class="form-group">
      <label class="form-label" for="preco">Preço (R$) *</label>
      <input type="text" class="form-control" id="preco" name="preco" inputmode="decimal" placeholder="49,90" required value="<?= e($produto['preco']) ?>">
      <?php if (isset($erros['preco'])): ?><small class="form-error"><?= e($erros['preco']) ?></small><?php endif; ?>
    </div>

    <div class="form-group">
      <label class="form-label" for="parcelas">Parcelas (sem juros) *</label>
      <select class="form-select" id="parcelas" name="parcelas">
        <?php for ($i = 1; $i <= 6; $i++): ?>
          <option value="<?= $i ?>" <?= (int) $produto['parcelas'] === $i ? 'selected' : '' ?>><?= $i === 1 ? '1x (à vista)' : $i . 'x sem juros' ?></option>
        <?php endfor; ?>
      </select>
      <?php if (isset($erros['parcelas'])): ?><small class="form-error"><?= e($erros['parcelas']) ?></small><?php endif; ?>
    </div>

    <div class="form-group">
      <label class="form-label" for="imagem">Imagem <?= $produto['imagem'] ? '' : '*' ?></label>
      <?php if ($produto['imagem']): ?>
        <div style="margin-bottom:.6rem;">
          <img src="../<?= e($produto['imagem']) ?>" alt="Imagem atual" width="96" height="96" style="object-fit:cover;border-radius:12px;background:#fef2ec;">
          <small class="text-muted" style="display:block;">Imagem atual. Envie outra só se quiser trocá-la.</small>
        </div>
      <?php endif; ?>
      <input type="file" class="form-control" id="imagem" name="imagem" accept="image/jpeg,image/png,image/webp,image/gif">
      <small class="text-muted">JPG, PNG, WEBP ou GIF, até 2 MB.</small>
      <?php if (isset($erros['imagem'])): ?><small class="form-error"><?= e($erros['imagem']) ?></small><?php endif; ?>
    </div>

    <div class="form-actions">
      <a href="produtos.php" class="btn btn-light">Cancelar</a>
      <button type="submit" class="btn btn-primary"><?= e($textoBotao) ?></button>
    </div>
  </form>
</section>