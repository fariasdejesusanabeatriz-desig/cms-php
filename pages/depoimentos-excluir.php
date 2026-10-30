<?php
/* ==========================================================================
   Depoimentos — exclusão (o "D" do CRUD: Delete)
   --------------------------------------------------------------------------
   Recebe o id por POST (formulário do modal de confirmação), executa o
   DELETE e volta para a listagem com uma mensagem.

   Por que POST e não um link (GET)? Porque páginas que ALTERAM dados não
   devem funcionar por link: um link pode ser aberto sem querer, pelo
   histórico do navegador ou por outro site.
   ========================================================================== */

require_once '../includes/auth.php';
require_once '../config/conexao.php';

exigirAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar('depoimentos.php');
}

csrfValidar('depoimentos.php');

$id = (int) post('id');

$sql = $pdo->prepare('SELECT id, autor FROM depoimentos WHERE id = ?');
$sql->execute([$id]);
$depoimento = $sql->fetch();

if (!$depoimento) {
    definirMensagem('Depoimento não encontrado.', 'danger');
    redirecionar('depoimentos.php');
}

try {
    $sql = $pdo->prepare('DELETE FROM depoimentos WHERE id = ?');
    $sql->execute([$id]);
} catch (PDOException $erro) {
    // Código 23000 = violação de integridade. Acontece quando outra tabela tem
    // uma chave estrangeira apontando para este depoimento.
    if ($erro->getCode() !== '23000') { throw $erro; }

    definirMensagem('Não é possível excluir o depoimento de "' . $depoimento['autor'] . '": existem registros vinculados a ele.', 'danger');
    redirecionar('depoimentos.php');
}

definirMensagem('Depoimento de "' . $depoimento['autor'] . '" excluído com sucesso!');
redirecionar('depoimentos.php');