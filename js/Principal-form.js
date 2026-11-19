/* ==========================================================================
   CMS — Formulário da seção principal (cadastrar e editar)
   Páginas: admin/principal-cadastrar.php e admin/principal-editar.php
   Requer: jQuery, main.js
   --------------------------------------------------------------------------
   O <form> informa o modo em data-modo="cadastrar" ou data-modo="editar".
   No modo "editar" a imagem é opcional (sem arquivo novo = manter a atual).

   LEMBRETE: estas validações só melhoram a experiência de quem usa o painel.
   TODAS são repetidas no servidor, em validarPrincipal() e
   salvarImagemProduto() (includes/principal-funcoes.php).
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
    var $form      = $('#formPrincipal');
    var modo       = $form.data('modo'); // "cadastrar" ou "editar"
    var $titulo    = $('#titulo');
    var $descricao = $('#descricao');
    var $imagem    = $('#imagem');

    /* Imagem: valida e mostra a pré-visualização -------------------- */
    $imagem.on('change', function () {
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
        $('#previewImagem').attr('src', e.target.result);
        $('#textoPreview').text('Pré-visualização da nova imagem.');
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
      var titulo = $.trim($titulo.val());
      var descr  = $.trim($descricao.val());

      if (titulo === '' || titulo.length > 120) {
        marcarErro($titulo, 'Informe o título (até 120 caracteres).'); valido = false;
      }

      if (descr === '') {
        marcarErro($descricao, 'Informe a descrição.'); valido = false;
      }

      // Imagem: obrigatória no cadastro; opcional na edição
      if (modo === 'cadastrar' && !$imagem[0].files.length) {
        marcarErro($imagem, 'Envie a imagem.'); valido = false;
      }

      if (!valido) {
        e.preventDefault();
        var $primeiro = $form.find('.has-error').first();
        $('html, body').animate({ scrollTop: $primeiro.offset().top - 110 }, 250);
        $primeiro.find('.form-control, .form-select').first().trigger('focus');
        CMS.toast('Verifique os campos destacados.', 'danger');
        return;
      }

      // Tudo certo: o formulário segue para o PHP
      $('#btnSalvar').addClass('is-loading').html('<span class="spinner"></span> Salvando...');
    });
  });

})(jQuery);