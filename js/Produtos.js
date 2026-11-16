/* Modal de confirmação de exclusão de produtos */
document.addEventListener('DOMContentLoaded', function () {
  var modal = document.getElementById('modalExcluir');
  if (!modal) return;

  var campoId   = document.getElementById('excluirId');
  var campoNome = document.getElementById('excluirNome');

  function abrir(id, nome) {
    campoId.value = id;
    campoNome.textContent = nome;
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
  }

  function fechar() {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
  }

  document.querySelectorAll('.btn-excluir').forEach(function (botao) {
    botao.addEventListener('click', function () {
      abrir(botao.dataset.id, botao.dataset.nome);
    });
  });

  modal.querySelectorAll('[data-modal-close]').forEach(function (el) {
    el.addEventListener('click', fechar);
  });

  // Fecha clicando no fundo escuro ou apertando Esc
  modal.addEventListener('click', function (e) { if (e.target === modal) fechar(); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') fechar(); });
});