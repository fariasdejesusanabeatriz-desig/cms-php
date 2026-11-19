<?php
/* ==========================================================================
   Configurações (logo) — excluir (o "D" do CRUD: Delete). Só aceita POST.
   ========================================================================== */

require_once '../includes/auth.php';
require_once '../config/conexao.php';
require_once '../includes/usuarios-funcoes.php';
require_once '../includes/configuracoes-funcoes.php';

exigirAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validarCsrfProduto()) {
    definirMensagemProduto('danger', 'Requisição inválida.');
    header('Location: configuracoes.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

$sql = $pdo->prepare('SELECT logo FROM configuracoes WHERE id = :id');
$sql->execute([':id' => $id]);
$item = $sql->fetch();

if (!$item) {
    definirMensagemProduto('danger', 'Registro não encontrado.');
    header('Location: configuracoes.php');
    exit;
}

$sql = $pdo->prepare('DELETE FROM configuracoes WHERE id = :id');
$sql->execute([':id' => $id]);
removerImagemProduto($item['logo']);

definirMensagemProduto('success', 'Logo excluído com sucesso!');
header('Location: configuracoes.php');
exit;