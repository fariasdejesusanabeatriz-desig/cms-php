/* ==========================================================================
   CMS — Formulário de configurações / logo (cadastrar e editar)
   Páginas: admin/configuracoes-cadastrar.php e admin/configuracoes-editar.php
   Requer: jQuery, main.js
   --------------------------------------------------------------------------
   O <form> informa o modo em data-modo="cadastrar" ou data-modo="editar".
   No modo "editar" o logo é opcional (sem arquivo novo = manter o atual).

   LEMBRETE: estas validações só melhoram a experiência de quem usa o painel.
   TODAS são repetidas no servidor, em salvarImagemProduto()
   (includes/produtos-funcoes.php).
   ========================================================================== */

(function ($) {
  'use strict';

  var TAMANHO_MAX_IMAGEM = 2 * 1024 * 1024; // 2 MB

  /* Ajudantes de validação ------------------------------------------ */
  function marcarErro($campo, mensagem) {
    $campo.addClass('is-invalid');
    $campo.closest('.form-group').addClass('has-error').find('.invalid-feedback').text(mensagem);
  }

  function limparErro($campo) {
    $campo.removeClass('is-invalid');
    $campo.closest('.form-group').removeClass('has-error');
  }

  $(function () {
    var $form = $('#formConfiguracoes');
    var modo  = $form.data('modo'); // "cadastrar" ou "editar"
    var $logo = $('#logo');

    /* Logo: valida e mostra a pré-visualização ---------------------- */
    $logo.on('change', function () {
      var arquivo = this.files[0];
      if (!arquivo) { return; }

      if (!/^image\/(jpeg|png|webp|gif)$/.test(arquivo.type)) {
        CMS.toast('Escolha uma imagem JPG, PNG, WEBP ou GIF.', 'danger');
        this.value = '';
        return;
      }
      if (arquivo.size > TAMANHO_MAX_IMAGEM) {
        CMS.toast('A imagem deve ter no máximo 2 MB.', 'danger');
        this.value = '';
        return;
      }

      var leitor = new FileReader();
      leitor.onload = function (e) {
        $('#previewLogo').attr('src', e.target.result);
        $('#textoPreview').text('Pré-visualização do novo logo.');
        $('#areaPreview').removeClass('hidden');
      };
      leitor.readAsDataURL(arquivo);
    });

    /* Limpa o erro quando o usuário corrige o campo ----------------- */
    $form.on('input change', '.form-control, .form-select', function () {
      limparErro($(this));
    });

    /* ---------------------------------------------------------------
       Validação ao enviar
       --------------------------------------------------------------- */
    $form.on('submit', function (e) {
      var valido = true;

      // Logo: obrigatório no cadastro; opcional na edição
      if (modo === 'cadastrar' && !$logo[0].files.length) {
        marcarErro($logo, 'Envie a imagem do logo.'); valido = false;
      }

      if (!valido) {
        e.preventDefault();
        var $primeiro = $form.find('.has-error').first();
        $('html, body').animate({ scrollTop: $primeiro.offset().top - 110 }, 250);
        CMS.toast('Verifique os campos destacados.', 'danger');
        return;
      }

      // Tudo certo: o formulário segue para o PHP
      $('#btnSalvar').addClass('is-loading').html('<span class="spinner"></span> Salvando...');
    });
  });

})(jQuery);