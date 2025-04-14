document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const senhaInput = document.getElementById('senha');
    
    togglePassword.addEventListener('click', function() {
        const type = senhaInput.getAttribute('type') === 'password' ? 'text' : 'password';
        senhaInput.setAttribute('type', type);
        
        // Toggle eye icon
        const eyeIcon = this.querySelector('svg');
        if (type === 'text') {
            eyeIcon.innerHTML = `
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
            `;
        } else {
            eyeIcon.innerHTML = `
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            `;
        }
    });
    
    // Form submission
    const loginForm = document.getElementById('loginForm');
loginForm.addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent form from submitting normally
    
    const email = document.getElementById('email').value;
    const senha = document.getElementById('senha').value;
    
    console.log('Tentativa de login:', email, senha); // Debug log
    
    if (email === 'admin' && senha === 'admin') {
        console.log('Credenciais corretas, redirecionando para: main.html');
        try {
            window.location.href = 'main.html'; // Modificado para caminho relativo simples
        } catch (error) {
            console.error('Erro ao redirecionar:', error);
            alert('Erro ao redirecionar para a página principal.');
        }
    } else {
        console.log('Credenciais inválidas');
        alert('Credenciais inválidas. Use email: admin, senha: admin');
    }
});
});