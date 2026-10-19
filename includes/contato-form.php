<!-- Exibe os erros de validação, se houver -->
<?php if (!empty($erros)): ?>
    <div class="alert alert-danger mb-4">
        <ul class="mb-0">
            <?php foreach ($erros as $erro): ?>
                <li><?= e($erro) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?= $acao ?>" method="POST" class="form-padrao">
    <?php csrfCampo(); ?>

    <!-- Telefones -->
    <div class="card mb-4">
        <div class="card-header"><h3>Telefones (WhatsApp)</h3></div>
        <div class="card-body" id="container-telefones">
            <?php if (!empty($dados['telefones'])): ?>
                <?php foreach ($dados['telefones'] as $tel): ?>
                    <div class="input-group mb-2">
                        <input type="text" name="telefones[]" class="form-control" value="<?= e($tel) ?>" placeholder="(00) 00000-0000">
                        <button type="button" class="btn btn-danger remover-item"><i class="bi bi-trash"></i></button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="input-group mb-2">
                    <input type="text" name="telefones[]" class="form-control" placeholder="(00) 00000-0000">
                    <button type="button" class="btn btn-danger remover-item"><i class="bi bi-trash"></i></button>
                </div>
            <?php endif; ?>
        </div>
        <div class="card-footer">
            <button type="button" class="btn btn-secondary btn-sm" id="add-telefone"><i class="bi bi-plus"></i> Adicionar Telefone</button>
        </div>
    </div>

    <!-- E-mails -->
    <div class="card mb-4">
        <div class="card-header"><h3>E-mails</h3></div>
        <div class="card-body" id="container-emails">
            <?php if (!empty($dados['emails'])): ?>
                <?php foreach ($dados['emails'] as $email): ?>
                    <div class="input-group mb-2">
                        <input type="email" name="emails[]" class="form-control" value="<?= e($email) ?>" placeholder="email@exemplo.com">
                        <button type="button" class="btn btn-danger remover-item"><i class="bi bi-trash"></i></button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="input-group mb-2">
                    <input type="email" name="emails[]" class="form-control" placeholder="email@exemplo.com">
                    <button type="button" class="btn btn-danger remover-item"><i class="bi bi-trash"></i></button>
                </div>
            <?php endif; ?>
        </div>
        <div class="card-footer">
            <button type="button" class="btn btn-secondary btn-sm" id="add-email"><i class="bi bi-plus"></i> Adicionar E-mail</button>
        </div>
    </div>

    <!-- Horários -->
    <div class="card mb-4">
        <div class="card-header"><h3>Horário de Atendimento</h3></div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Segunda a Sexta:</label>
                <input type="text" name="horario_semana" class="form-control" value="<?= e($dados['horario_semana']) ?>" placeholder="Ex: 9h às 18h">
            </div>
            <div class="mb-3">
                <label class="form-label">Sábado:</label>
                <input type="text" name="horario_sabado" class="form-control" value="<?= e($dados['horario_sabado']) ?>" placeholder="Ex: 9h às 14h">
            </div>
            <div class="mb-3">
                <label class="form-label">Domingo:</label>
                <input type="text" name="horario_domingo" class="form-control" value="<?= e($dados['horario_domingo']) ?>" placeholder="Ex: Fechado">
            </div>
        </div>
    </div>

    <!-- Redes Sociais -->
    <div class="card mb-4">
        <div class="card-header"><h3>Redes Sociais</h3></div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Instagram:</label>
                <input type="url" name="link_instagram" class="form-control" value="<?= e($dados['link_instagram']) ?>" placeholder="https://instagram.com/...">
            </div>
            <div class="mb-3">
                <label class="form-label">Facebook:</label>
                <input type="url" name="link_facebook" class="form-control" value="<?= e($dados['link_facebook']) ?>" placeholder="https://facebook.com/...">
            </div>
            <div class="mb-3">
                <label class="form-label">TikTok:</label>
                <input type="url" name="link_tiktok" class="form-control" value="<?= e($dados['link_tiktok']) ?>" placeholder="https://tiktok.com/@...">
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-5">
        <a href="<?= $voltarPara ?>" class="btn btn-light">Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Salvar</button>
    </div>
</form>