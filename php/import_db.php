<?php
try {
    $host = 'localhost:3307';
    $dbname = 'login';
    $username = 'root';
    $password = '';
    
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Criar tabela de usuários
    $sql = "CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        senha VARCHAR(255) NOT NULL,
        nivel_acesso ENUM('admin', 'usuario') NOT NULL DEFAULT 'usuario',
        status ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);
    echo "Tabela 'usuarios' criada com sucesso!<br>";
    
    // Criar tabela de produtos
    $sql = "CREATE TABLE IF NOT EXISTS produtos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        codigo_interno VARCHAR(50) NOT NULL UNIQUE,
        nome VARCHAR(100) NOT NULL,
        descricao TEXT,
        categoria_id INT,
        fornecedor_id INT,
        preco_custo DECIMAL(10,2) NOT NULL,
        preco_venda DECIMAL(10,2) NOT NULL,
        quantidade INT NOT NULL DEFAULT 0,
        unidade_medida VARCHAR(20) NOT NULL,
        status ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);
    echo "Tabela 'produtos' criada com sucesso!<br>";
    
    // Criar tabela de movimentações
    $sql = "CREATE TABLE IF NOT EXISTS movimentacoes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        produto_id INT NOT NULL,
        usuario_id INT NOT NULL,
        tipo ENUM('entrada', 'saida') NOT NULL,
        quantidade INT NOT NULL,
        valor_unitario DECIMAL(10,2) NOT NULL,
        motivo VARCHAR(100) NOT NULL,
        documento VARCHAR(50),
        data_movimentacao DATE NOT NULL,
        observacao TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (produto_id) REFERENCES produtos(id),
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    )";
    $conn->exec($sql);
    echo "Tabela 'movimentacoes' criada com sucesso!<br>";
    
    // Inserir usuário admin padrão
    $senha = password_hash('admin123', PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuarios (nome, email, senha, nivel_acesso) 
            VALUES ('Administrador', 'admin@papelariaibate.com', :senha, 'admin')
            ON DUPLICATE KEY UPDATE senha = :senha";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':senha' => $senha]);
    echo "Usuário admin criado com sucesso!<br>";
    
    echo "Banco de dados configurado com sucesso!";
    
} catch(PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?> 