/* ==========================================================================
   CMS — Formulário de Quem Somos (cadastrar e editar)
   Páginas: pages/quem-somos-cadastrar.php e pages/quem-somos-editar.php
   Requer: jQuery, Summernote Lite (+ idioma pt-BR), main.js
   --------------------------------------------------------------------------
   O <form> informa o modo em data-modo="cadastrar" ou data-modo="editar".
   Este JS valida os campos no navegador para melhorar a experiência de uso.
   
   LEMBRETE: Estas validações são apenas para UX. O PHP também valida tudo no
   servidor (includes/quem-somos-funcoes.php) antes de salvar no banco.
   ========================================================================== */

(function ($) {
  'use strict';

  var TAMANHO_MAX_FOTO = 2 * 1024 * 1024; // 2 MB

  $(function () {
    var $form = $('#formQuemSomos');

    // Se o formulário não existir na página, encerra o script
    if (!$form.length) { return; }

    var modo = $form.data('modo'); // "cadastrar" ou "editar"

    /* ---------------------------------------------------------------
       Editor rich text (Summernote Lite) para os textareas
       --------------------------------------------------------------- */
    var $historia = $('#texto_historia');
    var $missao   = $('#texto_missao');
    var $valores  = $('#texto_valores');

    if ($historia.length) {
      $historia.summernote({
        lang: 'pt-BR',
        height: 200,
        placeholder: 'Escreva a história da empresa...',
        styleTags: ['p', 'h2', 'h3', 'h4', 'blockquote'],
        toolbar: [
          ['style',  ['style']],
          ['font',   ['bold', 'italic', 'underline', 'clear']],
          ['para',   ['ul', 'ol', 'paragraph']],
          ['insert', ['link']],
          ['view',   ['undo', 'redo', 'codeview']]
        ],
        callbacks: {
          onChange: function (conteudo) {
            $historia.val(conteudo);
          }
        }
      });
    }

    if ($missao.length) {
      $missao.summernote({
        lang: 'pt-BR',
        height: 150,
        placeholder: 'Qual é a missão da empresa?',
        toolbar: [
          ['font',   ['bold', 'italic', 'underline', 'clear']],
          ['para',   ['ul', 'ol', 'paragraph']]
        ]
      });
    }

    if ($valores.length) {
      $valores.summernote({
        lang: 'pt-BR',
        height: 150,
        placeholder: 'Quais são os valores da empresa?',
        toolbar: [
          ['font',   ['bold', 'italic', 'underline', 'clear']],
          ['para',   ['ul', 'ol', 'paragraph']]
        ]
      });
    }

    /* ---------------------------------------------------------------
       Pré-visualização das fotos (Foto 1 e Foto 2)
       --------------------------------------------------------------- */

    function configurarPreview(inputId, previewId, btnRemoverId, hiddenRemoverId) {
      var $input = $('#' + inputId);
      var $preview = $('#' + previewId);
      var $btnRemover = $('#' + btnRemoverId);
      var $hiddenRemover = $('#' + hiddenRemoverId);

      if (!$input.length) { return; }

      $input.on('change', function () {
        var arquivo = this.files[0];
        if (!arquivo) { return; }

        // Valida o tipo
        if (!/^image\/(jpeg|png|webp|gif)$/.test(arquivo.type)) {
          CMS.toast('Escolha uma imagem JPG, PNG, WEBP ou GIF.', 'danger');
          this.value = '';
          return;
        }

        // Valida o tamanho
        if (arquivo.size > TAMANHO_MAX_FOTO) {
          CMS.toast('A imagem deve ter no máximo 2 MB.', 'danger');
          this.value = '';
          return;
        }

        // Mostra a pré-visualização
        var leitor = new FileReader();
        leitor.onload = function (e) {
          $preview.attr('src', e.target.result).removeClass('hidden');
        };
        leitor.readAsDataURL(arquivo);

        // Se o usuário escolheu nova foto, cancela a remoção
        if ($hiddenRemover.length) {
          $hiddenRemover.val('0');
        }
        if ($btnRemover.length) {
          $btnRemover.addClass('hidden');
        }
      });

      // Botão "Remover foto"
      if ($btnRemover.length) {
        $btnRemover.on('click', function () {
          $input.val('');
          $preview.attr('src', '').addClass('hidden');
          if ($hiddenRemover.length) {
            $hiddenRemover.val('1'); // avisa o PHP que a foto atual deve ser apagada
          }
          $(this).addClass('hidden');
        });
      }
    }

    configurarPreview('foto1', 'previewFoto1', 'btnRemoverFoto1', 'removerFoto1');
    configurarPreview('foto2', 'previewFoto2', 'btnRemoverFoto2', 'removerFoto2');

    /* ---------------------------------------------------------------
       Validação ao enviar
       --------------------------------------------------------------- */
    $form.on('submit', function (e) {
      var valido = true;

      // Valida se a história foi preenchida (remova se for opcional)
      if ($historia.length && $historia.summernote('isEmpty')) {
        $historia.addClass('is-invalid');
        valido = false;
      } else if ($historia.length) {
        $historia.removeClass('is-invalid');
      }

      if (!valido) {
        e.preventDefault();
        $('html, body').animate({ scrollTop: $form.offset().top - 110 }, 250);
        CMS.toast('Preencha os campos obrigatórios.', 'danger');
        return;
      }

      // Tudo certo: o formulário segue para o PHP
      $('#btnSalvar').addClass('is-loading').html('<span class="spinner"></span> Salvando...');
    });

  });

})(jQuery);