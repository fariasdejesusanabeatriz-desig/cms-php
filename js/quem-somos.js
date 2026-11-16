/* ==========================================================================
   CMS — Listagem de Quem Somos (pages/quem-somos.php)
   Requer: jQuery, main.js
   --------------------------------------------------------------------------
   A exclusão de fotos é feita via POST para quem-somos-excluir.php.
   Aqui ficam apenas as facilidades de uso da página.
   ========================================================================== */

(function ($) {
  'use strict';

  $(function () {

    /* Exclusão de Foto: preenche o modal com o campo (foto1 ou foto2) ------
       O formulário do modal envia o campo_foto via POST para 
       quem-somos-excluir.php, que remove a referência no banco e apaga 
       o arquivo físico. */
    $('.btn-excluir-foto').on('click', function () {
      var campo = $(this).data('campo'); // 'foto1' ou 'foto2'
      
      $('#excluirCampoFoto').val(campo);
      CMS.abrirModal('#modalExcluirFoto');
    });

  });

})(jQuery);