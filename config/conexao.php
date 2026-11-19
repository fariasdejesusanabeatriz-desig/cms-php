
<?php
/* ==========================================================================
   Conexão com o banco de dados (PDO)
   --------------------------------------------------------------------------
   Inclua este arquivo em qualquer página que precise do banco:
       require_once '../config/conexao.php';

   Depois disso, a variável $pdo estará disponível.
   ========================================================================== */

// Dados de conexão do XAMPP
define('DB_HOST',    'localhost');
define('DB_NOME',    'cms');
define('DB_USUARIO', 'root');
define('DB_SENHA',   '');

// Porta utilizada pelo MySQL no seu XAMPP
define('DB_PORTA',   '3307');

// Fuso horário utilizado pelo PHP
date_default_timezone_set('America/Bahia');

try {

    // Cria a conexão com o banco de dados utilizando PDO
    $pdo = new PDO(
        'mysql:host=' . DB_HOST .
        ';port=' . DB_PORTA .
        ';dbname=' . DB_NOME .
        ';charset=utf8mb4',

        DB_USUARIO,
        DB_SENHA,

        [
            // Faz com que os erros de SQL gerem exceções
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

            // Retorna os resultados como array associativo
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

            // Utiliza prepared statements nativos do MySQL
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    // Deixa o MySQL no mesmo fuso horário do PHP
    $pdo->exec("SET time_zone = '" . date('P') . "'");

} catch (PDOException $erro) {

    // Registra o erro no log do PHP
    error_log('Erro de conexão com o banco: ' . $erro->getMessage());

    // Define o código de resposta HTTP como erro interno do servidor
    http_response_code(500);

    // Exibe uma mensagem amigável caso a conexão falhe
    exit(
        'Não foi possível conectar ao banco de dados. ' .
        'Confira os dados em config/conexao.php ' .
        'e se o arquivo sql/banco.sql já foi importado.'
    );
}
