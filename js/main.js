// Função para atualizar a saudação baseada no horário
function atualizarSaudacao() {
    const hora = new Date().getHours();
    const saudacao = document.querySelector('.welcome-section h1');
    
    if (!saudacao) return; // Só executa se existir a saudação

    if (hora >= 5 && hora < 12) {
        saudacao.textContent = `Bom dia, ${saudacao.textContent.split(', ')[1]}`;
    } else if (hora >= 12 && hora < 18) {
        saudacao.textContent = `Boa tarde, ${saudacao.textContent.split(', ')[1]}`;
    } else {
        saudacao.textContent = `Boa noite, ${saudacao.textContent.split(', ')[1]}`;
    }
}

// Atualizar a saudação quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    atualizarSaudacao();

    // --- Controle do modal de produto ---
    const btnNovoProduto = document.getElementById('btn-novo-produto');
    const modalProduto = document.getElementById('modal-produto');
    const closeModal = modalProduto ? modalProduto.querySelector('.close-modal') : null;
    const cancelBtn = modalProduto ? modalProduto.querySelector('.cancel-btn') : null;

    if (btnNovoProduto && modalProduto) {
        btnNovoProduto.addEventListener('click', function() {
            const idInput = document.getElementById('produto-id');
            if (idInput) idInput.value = '';
            document.getElementById('modal-titulo').textContent = 'Novo Produto';
            document.getElementById('form-modal-produto').reset();
            modalProduto.style.display = 'block';
        });
    }
    if (closeModal && modalProduto) {
        closeModal.addEventListener('click', function() {
            modalProduto.style.display = 'none';
        });
    }
    if (cancelBtn && modalProduto) {
        cancelBtn.addEventListener('click', function(e) {
            e.preventDefault();
            modalProduto.style.display = 'none';
        });
    }
    // Fechar modal ao clicar fora do conteúdo
    if (modalProduto) {
        modalProduto.addEventListener('click', function(e) {
            if (e.target === modalProduto) {
                modalProduto.style.display = 'none';
            }
        });
    }

    // Aplica máscara nos campos de preço do modal
    const precoCusto = document.getElementById('produto-preco-custo');
    const precoVenda = document.getElementById('produto-preco-venda');
    if (precoCusto) aplicarMascaraMoeda(precoCusto);
    if (precoVenda) aplicarMascaraMoeda(precoVenda);

    // Cadastro de produto via AJAX
    const formModalProduto = document.getElementById('form-modal-produto');
    if (formModalProduto) {
        formModalProduto.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(formModalProduto);
            fetch('api_produto.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                // Remover alertas, apenas recarregar a página
                window.location.reload();
            })
            .catch(() => window.location.reload());
        });
    }

    // --- Remover produtos ---
    const btnRemoverProduto = document.getElementById('btn-remover-produto');
    const checkboxes = document.querySelectorAll('.produto-checkbox');
    const selectAll = document.getElementById('select-all');
    const btnCancelarRemocao = document.getElementById('btn-cancelar-remocao');
    const btnApagarProdutos = document.getElementById('btn-apagar-produtos');
    const apagarContainer = document.getElementById('apagar-container');
    const removerActions = document.getElementById('remover-actions');
    let modoRemocao = false;

    if (btnRemoverProduto && checkboxes.length > 0 && selectAll && btnCancelarRemocao && btnApagarProdutos && apagarContainer) {
        btnRemoverProduto.addEventListener('click', function() {
            modoRemocao = true;
            checkboxes.forEach(cb => cb.style.display = '');
            selectAll.style.display = '';
            btnRemoverProduto.style.display = 'none';
            btnCancelarRemocao.style.display = '';
            apagarContainer.style.display = '';
        });
        btnCancelarRemocao.addEventListener('click', function() {
            modoRemocao = false;
            checkboxes.forEach(cb => cb.style.display = 'none');
            selectAll.style.display = 'none';
            btnRemoverProduto.style.display = '';
            btnCancelarRemocao.style.display = 'none';
            apagarContainer.style.display = 'none';
            checkboxes.forEach(cb => cb.checked = false);
            selectAll.checked = false;
        });
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        });
        btnApagarProdutos.addEventListener('click', function() {
            const selecionados = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
            if (selecionados.length === 0) {
                alert('Selecione pelo menos um produto para remover.');
                return;
            }
            if (!window.confirm('Tem certeza que deseja remover os produtos selecionados?')) return;
            fetch('api_produto.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ remover: true, ids: selecionados })
            })
            .then(res => res.json())
            .then(data => {
                window.location.reload();
            })
            .catch(() => window.location.reload());
        });
    }

    // --- Editar produto ---
    const editButtons = document.querySelectorAll('.edit-btn');
    if (editButtons.length > 0 && modalProduto) {
        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = btn.getAttribute('data-id');
                fetch('api_produto.php?id=' + id)
                    .then(res => res.json())
                    .then(produto => {
                        // Trocar título e botão do modal para modo edição
                        document.getElementById('modal-titulo').textContent = 'Editar Produto';
                        
                        // Adicionar campo oculto com ID do produto
                        let idInput = document.getElementById('produto-id');
                        if (!idInput) {
                            idInput = document.createElement('input');
                            idInput.type = 'hidden';
                            idInput.id = 'produto-id';
                            idInput.name = 'id';
                            document.getElementById('form-modal-produto').appendChild(idInput);
                        }
                        idInput.value = produto.id;
                        
                        // Preencher campos do modal
                        document.getElementById('produto-codigo').value = produto.codigo_interno || '';
                        document.getElementById('produto-nome').value = produto.nome || '';
                        document.getElementById('produto-categoria').value = produto.categoria_id || '';
                        document.getElementById('produto-quantidade').value = produto.quantidade || '';
                        document.getElementById('produto-estoque-minimo').value = produto.estoque_minimo || '';
                        document.getElementById('produto-preco-custo').value = 'R$ ' + (produto.preco_custo || '0,00');
                        document.getElementById('produto-preco-venda').value = 'R$ ' + (produto.preco_venda || '0,00');
                        document.getElementById('produto-descricao').value = produto.descricao || '';
                        
                        // Mostrar o modal
                        modalProduto.style.display = 'block';
                    });
            });
        });
    }
});

// Atualizar a saudação a cada hora
setInterval(atualizarSaudacao, 3600000);

// Máscara de moeda para campos de preço
function aplicarMascaraMoeda(input) {
    input.addEventListener('input', function(e) {
        let v = input.value.replace(/\D/g, '');
        v = v.padStart(3, '0');
        let reais = v.slice(0, v.length - 2);
        let centavos = v.slice(-2);
        reais = reais.replace(/^0+/, '') || '0';
        reais = reais.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
        input.value = 'R$ ' + reais + ',' + centavos;
    });

    input.addEventListener('focus', function() {
        if (input.value === '') input.value = 'R$ 0,00';
    });

    input.addEventListener('blur', function() {
        if (input.value === 'R$ 0,00' || input.value === '') input.value = '';
    });
}