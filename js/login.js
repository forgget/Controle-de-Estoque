document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const emailInput = document.querySelector('input[name="email"]');
    const passwordInput = document.querySelector('input[name="password"]');
    const errorMessage = document.querySelector('.error-message');

    // Função para validar o email
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Função para mostrar mensagem de erro
    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.style.display = 'block';
    }

    // Função para esconder mensagem de erro
    function hideError() {
        errorMessage.style.display = 'none';
    }

    // Validação do formulário
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        hideError();

        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();

        if (!email) {
            showError('Por favor, insira seu email.');
            return;
        }

        if (!validateEmail(email)) {
            showError('Por favor, insira um email válido.');
            return;
        }

        if (!password) {
            showError('Por favor, insira sua senha.');
            return;
        }

        // Se todas as validações passarem, envia o formulário
        form.submit();
    });

    // Limpar mensagem de erro quando o usuário começar a digitar
    emailInput.addEventListener('input', hideError);
    passwordInput.addEventListener('input', hideError);
}); 