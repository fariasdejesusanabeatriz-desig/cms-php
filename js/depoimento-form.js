/* ==========================================================================
   CMS — Formulário de depoimento (cadastrar e editar)
   Páginas: pages/depoimentos-cadastrar.php e pages/depoimentos-editar.php
   Requer: jQuery, Summernote Lite (+ idioma pt-BR), main.js
   --------------------------------------------------------------------------
   O <form> informa o modo em data-modo="cadastrar" ou data-modo="editar".

   LEMBRETE: estas validações só melhoram a experiência de quem usa o painel.
   TODAS são repetidas no servidor, em validarDepoimento()
   (includes/depoimentos-funcoes.php).
   ========================================================================== */

(function ($) {
  'use strict';

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
    var $form = $('#formDepoimento');
    
    // Se o formulário não existir na página, encerra o script
    if (!$form.length) { return; }

    var $autor = $('#autor');
    var $texto = $('#texto');

    /* ---------------------------------------------------------------
       Editor rich text (Summernote Lite)
       O conteúdo HTML é enviado no campo <textarea name="texto">.
       --------------------------------------------------------------- */
    if ($texto.length) {
      $texto.summernote({
        lang: 'pt-BR',
        height: 200,
        placeholder: 'Escreva aqui o depoimento... Você pode usar negrito e itálico se desejar.',
        styleTags: ['p', 'h3', 'h4', 'blockquote'],
        toolbar: [
          ['font',   ['bold', 'italic', 'underline', 'clear']],
          ['para',   ['ul', 'ol', 'paragraph']],
          ['view',   ['undo', 'redo', 'codeview']]
        ],
        callbacks: {
          onChange: function (conteudo) {
            // Mantém o <textarea> sempre sincronizado com o editor
            $texto.val(conteudo);
            if (!$texto.summernote('isEmpty')) { limparErro($texto); }
          }
        }
      });
    }

    /* Status: atualiza o texto do switch ---------------------------- */
    $('#status').on('change', function () {
      $('#statusTexto').text(this.checked ? 'Ativo — será exibido no site' : 'Inativo — oculto no site');
    }).trigger('change');

    /* Limpa o erro quando o usuário corrige o campo ----------------- */
    $form.on('input change', '.form-control, .form-check-input', function () {
      limparErro($(this));
    });

    /* ---------------------------------------------------------------
       Validação ao enviar
       --------------------------------------------------------------- */
    $form.on('submit', function (e) {
      var valido = true;
      var autor  = $.trim($autor.val());

      // Valida o Autor
      if (autor.length < 3) {
        marcarErro($autor, 'Informe o nome do autor (mínimo 3 caracteres).'); 
        valido = false;
      } else if (autor.length > 100) {
        marcarErro($autor, 'O nome deve ter no máximo 100 caracteres.'); 
        valido = false;
      }

      // Valida se o editor de texto está vazio
      if ($texto.length && $texto.summernote('isEmpty')) {
        marcarErro($texto, 'Escreva o texto do depoimento.'); 
        valido = false;
      }

      if (!valido) {
        e.preventDefault();
        var $primeiro = $form.find('.has-error').first();
        $('html, body').animate({ scrollTop: $primeiro.offset().top - 110 }, 250);
        $primeiro.find('.form-control').first().trigger('focus');
        CMS.toast('Verifique os campos destacados.', 'danger');
        return;
      }

      // Tudo certo: o formulário segue para o PHP
      $('#btnSalvar').addClass('is-loading').html('<span class="spinner"></span> Salvando...');
    });
  });

})(jQuery);