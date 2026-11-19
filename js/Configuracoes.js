/* ==========================================================================
   CMS — Listagem de configurações / logo (admin/configuracoes.php)
   Requer: jQuery, main.js
   --------------------------------------------------------------------------
   A paginação é feita no SERVIDOR (configuracoes.php), com
   SELECT ... LIMIT ... OFFSET, lendo os dados de $_GET.
   Aqui ficam apenas as facilidades de uso da página.
   ========================================================================== */

(function ($) {
  'use strict';

  $(function () {

    /* Exclusão: preenche o modal com os dados da linha ---------------------
       O formulário do modal envia o id via POST para configuracoes-excluir.php,
       que executa o DELETE e redireciona de volta com uma mensagem. */
    $('#tabelaConfiguracoes').on('click', '.btn-excluir', function () {
      $('#excluirNome').text($(this).data('nome'));
      $('#excluirId').val($(this).data('id'));
      CMS.abrirModal('#modalExcluir');
    });
  });

})(jQuery);