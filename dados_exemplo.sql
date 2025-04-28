USE login;

-- Inserir categorias de exemplo
INSERT INTO categorias (nome, descricao) VALUES 
('Material Escolar', 'Cadernos, lápis, canetas, etc'),
('Papelaria', 'Papéis, envelopes, pastas, etc'),
('Escritório', 'Grampeadores, clipes, organizadores, etc'),
('Artes', 'Tintas, pincéis, telas, etc'),
('Informática', 'Cartuchos, toners, mídias, etc');

-- Inserir fornecedores de exemplo
INSERT INTO fornecedores (nome, cnpj, telefone, email, endereco) VALUES 
('Papelaria Central', '12345678000190', '(11) 9999-8888', 'contato@papelariacentral.com.br', 'Rua das Flores, 123 - Centro'),
('Distribuidora Escolar', '98765432000110', '(11) 7777-6666', 'vendas@distribuidoraescolar.com.br', 'Av. Principal, 456 - Vila Nova'),
('Arte & Cia', '45678912000134', '(11) 5555-4444', 'contato@arteecia.com.br', 'Rua dos Artistas, 789 - Bairro das Artes');

-- Inserir produtos de exemplo
INSERT INTO produtos (codigo_barras, codigo_interno, nome, descricao, categoria_id, fornecedor_id, quantidade, preco_custo, preco_venda, margem_lucro, estoque_minimo) VALUES 
('7891234567890', 'CAD001', 'Caderno 10 matérias', 'Caderno espiral 10 matérias 200 folhas', 1, 1, 50, 15.00, 25.00, 66.67, 10),
('7891234567891', 'LAP001', 'Lápis HB', 'Caixa com 12 lápis HB', 1, 2, 100, 5.00, 8.00, 60.00, 20),
('7891234567892', 'CAN001', 'Caneta Azul', 'Caneta esferográfica azul ponta fina', 1, 1, 200, 1.50, 3.00, 100.00, 50),
('7891234567893', 'PAP001', 'Papel A4', 'Resma com 500 folhas de papel A4', 2, 1, 30, 25.00, 35.00, 40.00, 5),
('7891234567894', 'TON001', 'Toner Preto', 'Toner para impressora HP', 5, 3, 20, 80.00, 120.00, 50.00, 5);

-- Inserir movimentações de exemplo
INSERT INTO movimentacoes (produto_id, usuario_id, tipo, quantidade, valor_unitario, motivo, documento, observacao) VALUES 
(1, 1, 'entrada', 100, 15.00, 'compra', 'NF123', 'Compra inicial de estoque'),
(2, 1, 'entrada', 200, 5.00, 'compra', 'NF124', 'Compra inicial de estoque'),
(3, 1, 'entrada', 300, 1.50, 'compra', 'NF125', 'Compra inicial de estoque'),
(1, 1, 'saida', 50, 25.00, 'venda', 'VEN001', 'Venda para cliente'),
(2, 1, 'saida', 100, 8.00, 'venda', 'VEN002', 'Venda para cliente'); 