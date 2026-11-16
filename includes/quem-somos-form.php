<?php if (!empty($erros)): ?>
    <div class="alert alert-danger mb-4">
        <ul class="mb-0">
            <?php foreach ($erros as $erro): ?>
                <li><?= e($erro) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?= $acao ?>" method="POST" enctype="multipart/form-data" class="form-padrao">
    <?php csrfCampo(); ?>

    <!-- Textos -->
    <div class="card mb-4">
        <div class="card-header"><h3>Textos Institucionais</h3></div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Nossa História:</label>
                <textarea name="texto_historia" class="form-control" rows="5" placeholder="Conte a história da empresa..."><?= e($dados['texto_historia']) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Missão:</label>
                <textarea name="texto_missao" class="form-control" rows="3" placeholder="Qual é a missão da empresa?"><?= e($dados['texto_missao']) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Valores:</label>
                <textarea name="texto_valores" class="form-control" rows="3" placeholder="Quais são os valores?"><?= e($dados['texto_valores']) ?></textarea>
            </div>
        </div>
    </div>

    <!-- Fotos -->
    <div class="card mb-4">
        <div class="card-header"><h3>Fotos da Página</h3></div>
        <div class="card-body row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Foto 1 (Principal):</label>
                <?php if (!empty($dados['foto1'])): ?>
                    <div class="mb-2">
                        <img src="../uploads/quem-somos/<?= e($dados['foto1']) ?>" alt="Foto 1" style="max-height: 150px; border-radius: 8px;">
                    </div>
                <?php endif; ?>
                <input type="file" name="foto1" class="form-control" accept="image/*">
                <small class="text-muted">Deixe em branco para manter a foto atual.</small>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Foto 2 (Secundária):</label>
                <?php if (!empty($dados['foto2'])): ?>
                    <div class="mb-2">
                        <img src="../uploads/quem-somos/<?= e($dados['foto2']) ?>" alt="Foto 2" style="max-height: 150px; border-radius: 8px;">
                    </div>
                <?php endif; ?>
                <input type="file" name="foto2" class="form-control" accept="image/*">
                <small class="text-muted">Deixe em branco para manter a foto atual.</small>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-5">
        <a href="<?= $voltarPara ?>" class="btn btn-light">Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Salvar Alterações</button>
    </div>
</form>