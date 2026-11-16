<?php
/* ==========================================================================
   Principal (início) — excluir (o "D" do CRUD: Delete). Só aceita POST.
   ========================================================================== */

require_once '../includes/auth.php';
require_once '../config/conexao.php';
require_once '../includes/usuarios-funcoes.php';
require_once '../includes/principal-funcoes.php';

exigirAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validarCsrfProduto()) {
    definirMensagemProduto('danger', 'Requisição inválida.');
    header('Location: principal.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

$sql = $pdo->prepare('SELECT imagem FROM principal WHERE id = :id');
$sql->execute([':id' => $id]);
$item = $sql->fetch();

if (!$item) {
    definirMensagemProduto('danger', 'Registro não encontrado.');
    header('Location: principal.php');
    exit;
}

$sql = $pdo->prepare('DELETE FROM principal WHERE id = :id');
$sql->execute([':id' => $id]);
removerImagemProduto($item['imagem']);

definirMensagemProduto('success', 'Excluído com sucesso!');
header('Location: principal.php');
exit;