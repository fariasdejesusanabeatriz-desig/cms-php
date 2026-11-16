<?php
/* ==========================================================================
   Produtos — excluir (o "D" do CRUD: Delete). Só aceita POST.
   ========================================================================== */

require_once '../includes/auth.php';
require_once '../config/conexao.php';
require_once '../includes/usuarios-funcoes.php';
require_once '../includes/produtos-funcoes.php';

exigirAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validarCsrfProduto()) {
    definirMensagemProduto('danger', 'Requisição inválida.');
    header('Location: produtos.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

$sql = $pdo->prepare('SELECT imagem FROM produtos WHERE id = :id');
$sql->execute([':id' => $id]);
$produto = $sql->fetch();

if (!$produto) {
    definirMensagemProduto('danger', 'Produto não encontrado.');
    header('Location: produtos.php');
    exit;
}

$sql = $pdo->prepare('DELETE FROM produtos WHERE id = :id');
$sql->execute([':id' => $id]);
removerImagemProduto($produto['imagem']);

definirMensagemProduto('success', 'Produto excluído com sucesso!');
header('Location: produtos.php');
exit;