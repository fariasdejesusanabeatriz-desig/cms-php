/* ==========================================================================
   CMS — Listagem da seção principal (admin/principal.php)
   Requer: jQuery, main.js
   --------------------------------------------------------------------------
   A busca e a paginação são feitas no SERVIDOR (principal.php),
   com SELECT ... WHERE ... LIMIT ... OFFSET, lendo os dados de $_GET.
   Aqui ficam apenas as facilidades de uso da página.
   ========================================================================== */

(function ($) {
  'use strict';

  $(function () {

    /* Exclusão: preenche o modal com os dados da linha ---------------------
       O formulário do modal envia o id via POST para principal-excluir.php,
       que executa o DELETE e redireciona de volta com uma mensagem. */
    $('#tabelaPrincipal').on('click', '.btn-excluir', function () {
      $('#excluirNome').text($(this).data('nome'));
      $('#excluirId').val($(this).data('id'));
      CMS.abrirModal('#modalExcluir');
    });
  });

})(jQuery);