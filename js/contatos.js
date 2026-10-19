/* ==========================================================================
   Contato — Scripts da listagem
   Controla o modal de exclusão de telefones e e-mails
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function () {

  const modal        = document.getElementById('modalExcluir');
  const formExcluir  = document.getElementById('formExcluir');
  const inputTipo    = document.getElementById('excluirTipo');
  const inputIndice  = document.getElementById('excluirIndice');
  const spanValor    = document.getElementById('excluirValor');
  const botoesExcluir = document.querySelectorAll('.btn-excluir');

  // 1. Abre o modal e preenche os dados ao clicar na lixeira
  botoesExcluir.forEach(function (btn) {
    btn.addEventListener('click', function () {
      const tipo   = this.dataset.tipo;
      const valor  = this.dataset.valor;
      const indice = this.dataset.indice;

      // Preenche os campos ocultos do formulário
      inputTipo.value   = tipo;
      inputIndice.value = indice;
      
      // Preenche o texto do modal (mostra o telefone ou e-mail)
      spanValor.textContent = valor;

      // Abre o modal (adiciona classe e atualiza acessibilidade)
      modal.classList.add('is-active');
      modal.setAttribute('aria-hidden', 'false');
    });
  });

  // 2. Fecha o modal ao clicar em "Cancelar" ou fora dele
  document.querySelectorAll('[data-modal-close]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      fecharModal();
    });
  });

  // Fecha se clicar no fundo escuro (fora da caixa do modal)
  modal.addEventListener('click', function (e) {
    if (e.target === modal) {
      fecharModal();
    }
  });

  // Fecha se apertar a tecla ESC
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && modal.classList.contains('is-active')) {
      fecharModal();
    }
  });

  function fecharModal() {
    modal.classList.remove('is-active');
    modal.setAttribute('aria-hidden', 'true');
    // Limpa os campos para não dar conflito no próximo clique
    inputTipo.value = '';
    inputIndice.value = '';
    spanValor.textContent = '';
  }

});