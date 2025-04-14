// Função para adicionar a classe 'active' ao item de menu clicado
document.addEventListener('DOMContentLoaded', function() {
    const navItems = document.querySelectorAll('.nav-item');
    
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove a classe 'active' de todos os itens
            navItems.forEach(i => i.classList.remove('active'));
            
            // Adiciona a classe 'active' ao item clicado
            this.classList.add('active');
        });
    });
});