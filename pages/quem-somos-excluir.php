<?php
/* ==========================================================================
   Quem Somos — Exclusão de Foto (o "D" do CRUD: Delete)
   --------------------------------------------------------------------------
   Recebe o campo (foto1 ou foto2) via POST, remove do banco e apaga o arquivo.
   ========================================================================== */

require_once '../includes/auth.php';
require_once '../config/conexao.php';
require_once '../includes/quem-somos-funcoes.php';

exigirAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar('quem-somos-editar.php');
}

csrfValidar('quem-somos-editar.php');

$campo = post('campo_foto');

// Valida se o campo é realmente uma das fotos
if (!in_array($campo, ['foto1', 'foto2'])) {
    definirMensagem('Requisição inválida.', 'danger');
    redirecionar('quem-somos-editar.php');
}

// 1. Busca os dados atuais
$dados = buscarConfiguracoesQuemSomos($pdo);

if (!$dados || empty($dados[$campo])) {
    definirMensagem('Foto não encontrada.', 'danger');
    redirecionar('quem-somos-editar.php');
}

$nomeArquivoAntigo = $dados[$campo];

// 2. Remove a referência no banco (define como NULL)
$dados[$campo] = null;
salvarConfiguracoesQuemSomos($pdo, $dados, $dados['foto1'], $dados['foto2']);

// 3. Apaga o arquivo físico do disco
// (Supondo que a função apagarImagem() exista no seu projeto, como no de usuários)
$caminhoArquivo = '../uploads/quem-somos/' . $nomeArquivoAntigo;
if (file_exists($caminhoArquivo)) {
    unlink($caminhoArquivo);
}

$nomeAmigavel = $campo === 'foto1' ? 'Foto 1' : 'Foto 2';
definirMensagem($nomeAmigavel . ' removida com sucesso!');
redirecionar('quem-somos-editar.php');