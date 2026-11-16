document.addEventListener('DOMContentLoaded', function() {
    
    // Função para adicionar novo campo
    function adicionarCampo(containerId, inputType, inputName, placeholder) {
        const container = document.getElementById(containerId);
        const div = document.createElement('div');
        div.className = 'input-group mb-2';
        div.innerHTML = `
            <input type="${inputType}" name="${inputName}[]" class="form-control" placeholder="${placeholder}">
            <button type="button" class="btn btn-danger remover-item"><i class="bi bi-trash"></i></button>
        `;
        container.appendChild(div);
    }

    // Botão adicionar telefone
    document.getElementById('add-telefone')?.addEventListener('click', function() {
        adicionarCampo('container-telefones', 'text', 'telefones', '(00) 00000-0000');
    });

    // Botão adicionar e-mail
    document.getElementById('add-email')?.addEventListener('click', function() {
        adicionarCampo('container-emails', 'email', 'emails', 'email@exemplo.com');
    });

    // Remover campos dinamicamente
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remover-item')) {
            const inputGroup = e.target.closest('.input-group');
            // Se for o único campo, apenas limpa. Se tiver mais, remove a linha.
            if (inputGroup.parentNode.children.length > 1) {
                inputGroup.remove();
            } else {
                const input = inputGroup.querySelector('input');
                if(input) input.value = '';
            }
        }
    });
});