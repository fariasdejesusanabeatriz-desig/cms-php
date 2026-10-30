<?php
/* ==========================================================================
   Contato — exclusão de item (o "D" do CRUD: Delete)
   --------------------------------------------------------------------------
   Recebe o tipo (telefone/email) e o indice via POST, remove o item do array,
   salva de volta no banco e retorna para a listagem.

   Por que POST e não um link (GET)? Porque páginas que ALTERAM dados não
   devem funcionar por link: um link pode ser aberto sem querer, pelo
   histórico do navegador ou por outro site.
   ========================================================================== */

require_once '../includes/auth.php';
require_once '../config/conexao.php';
require_once '../includes/contato-funcoes.php';

exigirAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar('contatos.php');
}

csrfValidar('contatos.php');

$tipo   = post('tipo');
$indice = (int) post('indice');

// Validação básica de segurança
if (!in_array($tipo, ['telefone', 'email']) || $indice < 0) {
    definirMensagem('Requisição inválida.', 'danger');
    redirecionar('contatos.php');
}

// 1. Busca os dados atuais
$dados = buscarConfiguracoesContato($pdo);

if (!$dados) {
    definirMensagem('Configurações de contato não encontradas.', 'danger');
    redirecionar('contatos.php');
}

// 2. Remove o item do array correspondente
if ($tipo === 'telefone') {
    if (!isset($dados['telefones'][$indice])) {
        definirMensagem('Telefone não encontrado.', 'danger');
        redirecionar('contatos.php');
    }
    $itemRemovido = $dados['telefones'][$indice];
    unset($dados['telefones'][$indice]);
    
    // Reorganiza os índices do array (para não ficar com buracos tipo 0, 2, 3)
    $dados['telefones'] = array_values($dados['telefones']);
    
} else { // email
    if (!isset($dados['emails'][$indice])) {
        definirMensagem('E-mail não encontrado.', 'danger');
        redirecionar('contatos.php');
    }
    $itemRemovido = $dados['emails'][$indice];
    unset($dados['emails'][$indice]);
    
    // Reorganiza os índices
    $dados['emails'] = array_values($dados['emails']);
}

// 3. Salva as alterações no banco
salvarConfiguracoesContato($pdo, $dados);

// 4. Mensagem de sucesso e volta para a listagem
$mensagem = $tipo === 'telefone' ? 'Telefone' : 'E-mail';
definirMensagem($mensagem . ' "' . $itemRemovido . '" excluído com sucesso!');
redirecionar('contatos.php');