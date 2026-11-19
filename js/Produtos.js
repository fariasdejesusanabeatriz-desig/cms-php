/* ==========================================================================
   CMS — Listagem de produtos (admin/produtos.php)
   Requer: jQuery, main.js
   --------------------------------------------------------------------------
   A busca, o filtro e a paginação são feitos no SERVIDOR (produtos.php),
   com SELECT ... WHERE ... LIMIT ... OFFSET, lendo os dados de $_GET.
   Aqui ficam apenas as facilidades de uso da página.
   ========================================================================== */

(function ($) {
  'use strict';

  $(function () {

    /* Filtro: ao escolher as parcelas, envia o formulário ---------------- */
    $('#filtroParcelas').on('change', function () {
      $('#formFiltros').trigger('submit');
    });

    /* Exclusão: preenche o modal com os dados da linha ---------------------
       O formulário do modal envia o id via POST para produtos-excluir.php,
       que executa o DELETE e redireciona de volta com uma mensagem. */
    $('#tabelaProdutos').on('click', '.btn-excluir', function () {
      $('#excluirNome').text($(this).data('nome'));
      $('#excluirId').val($(this).data('id'));
      CMS.abrirModal('#modalExcluir');
    });
  });

})(jQuery);