<?php
// Criar a pasta vendor se não existir
if (!file_exists('vendor')) {
    mkdir('vendor', 0777, true);
}

// URL da biblioteca SimpleXLSX
$url = 'https://github.com/shuchkin/simplexlsx/archive/refs/heads/master.zip';
$zipFile = 'vendor/simplexlsx.zip';
$extractPath = 'vendor/';

// Baixar o arquivo
file_put_contents($zipFile, file_get_contents($url));

// Extrair o arquivo
$zip = new ZipArchive;
if ($zip->open($zipFile) === TRUE) {
    $zip->extractTo($extractPath);
    $zip->close();
    unlink($zipFile); // Remover o arquivo zip após extrair
    
    // Renomear a pasta para o nome correto
    rename('vendor/simplexlsx-master', 'vendor/simplexlsx');
    
    echo "Biblioteca SimpleXLSX instalada com sucesso!";
} else {
    echo "Erro ao extrair o arquivo.";
}
?> 