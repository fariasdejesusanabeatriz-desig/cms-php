-- ============================================================================
--  PAINEL CMS — Banco de dados
--  Projeto Prático: CMS com PHP e MySQL (Parte 2)
-- ----------------------------------------------------------------------------
--  Como importar:
--    • phpMyAdmin: aba "Importar" → escolha este arquivo → Executar
--    • Terminal:   mysql -u root -p < sql/banco.sql
--
--  Compatível com MySQL 5.7+ / 8.x e MariaDB 10.x (XAMPP, Laragon, WAMP).
--
--  ATENÇÃO: este script APAGA e recria as tabelas do painel.
-- ============================================================================

-- Este arquivo está em UTF-8: avisa o servidor para os acentos não serem trocados
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS cms
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE cms;

-- A tabela "filha" (que tem a chave estrangeira) precisa ser apagada primeiro
DROP TABLE IF EXISTS usuarios_tokens;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS produtos;
DROP TABLE IF EXISTS configuracoes;
DROP TABLE IF EXISTS principal;
DROP TABLE IF EXISTS quem_somos;
DROP TABLE IF EXISTS contato;
DROP TABLE IF EXISTS depoimentos;




-- ----------------------------------------------------------------------------
--  Tabela: usuarios
--  Pessoas que podem acessar o painel administrativo.
-- ----------------------------------------------------------------------------
CREATE TABLE usuarios (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome          VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL,
  usuario       VARCHAR(30)  NOT NULL,                  -- nome de usuário (ex.: maria.silva)
  senha         VARCHAR(255) NOT NULL,                  -- hash gerado por password_hash()
  telefone      VARCHAR(20)  NULL,
  foto          VARCHAR(255) NULL,                      -- caminho do arquivo (ex.: uploads/usuarios/abc.jpg)
  biografia     MEDIUMTEXT   NULL,                      -- HTML do editor rich text (pode conter imagens em Base64)
  perfil        ENUM('admin','editor') NOT NULL DEFAULT 'editor',
  status        TINYINT(1)   NOT NULL DEFAULT 1,        -- 1 = ativo, 0 = inativo
  criado_em     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em DATETIME     NULL ON UPDATE CURRENT_TIMESTAMP,
  ultimo_acesso DATETIME     NULL,

  PRIMARY KEY (id),
  UNIQUE KEY uk_usuarios_email   (email),
  UNIQUE KEY uk_usuarios_usuario (usuario),
  KEY idx_usuarios_perfil_status (perfil, status)       -- acelera os filtros da listagem
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
--  Tabela: usuarios_tokens
--  Guarda os tokens da opção "Manter conectado" da tela de login.
--
--  O navegador recebe um cookie no formato  seletor:token
--    • seletor     → serve para localizar a linha (é guardado como está)
--    • token_hash  → hash SHA-256 do token (o token "puro" nunca fica no banco,
--                    assim como acontece com a senha)
--
--  ON DELETE CASCADE: ao excluir um usuário, seus tokens são apagados juntos.
-- ----------------------------------------------------------------------------
CREATE TABLE usuarios_tokens (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  usuario_id INT UNSIGNED NOT NULL,
  seletor    CHAR(24)     NOT NULL,
  token_hash CHAR(64)     NOT NULL,
  expira_em  DATETIME     NOT NULL,
  criado_em  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uk_tokens_seletor (seletor),
  KEY idx_tokens_usuario (usuario_id),
  CONSTRAINT fk_tokens_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
--  Usuário administrador inicial
--    E-mail: admin@cms.com
--    Senha:  123456        (troque depois do primeiro acesso!)
--
--  O valor da coluna "senha" foi gerado no PHP com:
--    echo password_hash('123456', PASSWORD_DEFAULT);
-- ----------------------------------------------------------------------------
INSERT INTO usuarios (nome, email, usuario, senha, telefone, biografia, perfil, status)
VALUES (
  'Administrador do Sistema',
  'admin@cms.com',
  'admin',
  '$2y$10$d0QBjfVCxWsp0hbO6umz6.JHdFhXnZ1qjo8joywe4azH8szA9RECu',
  NULL,
  '<p>Conta de <strong>administrador</strong> criada na instalação do sistema.</p>',
  'admin',
  1
);


-- ============================================================================
--  MÓDULOS DO SITE — LIONS MAKEUP
--  Tabelas com os conteúdos que o painel edita e o site exibe.
--
--  Tabelas de registro ÚNICO (só existe 1 linha; o painel apenas edita):
--    configuracoes, principal, quem_somos, contato
--  Tabelas com vários registros (CRUD completo):
--    produtos, depoimentos, redes_sociais
-- ============================================================================

-- ----------------------------------------------------------------------------
--  Tabela: produtos  (CRUD de produtos da loja)
--  parcelas: 1 = à vista; de 2 a 12 = "em Nx sem juros".
--  destaque: 1 = aparece em "Destaques da coleção" na página inicial.
-- ----------------------------------------------------------------------------

CREATE TABLE produtos (
    id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome            VARCHAR(120) NOT NULL,
    descricao       TEXT NOT NULL,
    preco           DECIMAL(10,2) NOT NULL,
    parcelas        TINYINT UNSIGNED NOT NULL DEFAULT 1,
    imagem          VARCHAR(255) NOT NULL,
    destaque        TINYINT(1) NOT NULL DEFAULT 0,
    status          TINYINT(1) NOT NULL DEFAULT 1, -- 1 = à venda, 0 = oculto
    criado_em       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em   DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    KEY idx_produtos_status_destaque (status, destaque)

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
--  Tabela: configuracoes  (CRUD de configurações → logo da loja)
-- ----------------------------------------------------------------------------

CREATE TABLE configuracoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    logo VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ----------------------------------------------------------------------------
--  Tabela: principal  (CRUD da página inicial → título, descrição e imagem)
-- ----------------------------------------------------------------------------

USE cms;

CREATE TABLE IF NOT EXISTS principal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT,
    imagem VARCHAR(255) DEFAULT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ----------------------------------------------------------------------------
--  Tabela: quem_somos  (CRUD da página "Quem somos")
--  texto, valores e missao guardam HTML do editor rich text.
-- ----------------------------------------------------------------------------

CREATE TABLE quem_somos (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  texto         MEDIUMTEXT   NULL,
  valores       MEDIUMTEXT   NULL,
  missao        MEDIUMTEXT   NULL,
  foto1         VARCHAR(255) NULL,
  foto2         VARCHAR(255) NULL,
  atualizado_em DATETIME     NULL ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
--  Tabela: contato  (endereço, 2 telefones, 2 e-mails e horário de atendimento)
-- ----------------------------------------------------------------------------

CREATE TABLE contato (
    id INT AUTO_INCREMENT PRIMARY KEY,

    telefones JSON,
    emails JSON,

    horario_semana VARCHAR(100),
    horario_sabado VARCHAR(100),
    horario_domingo VARCHAR(100),

    link_instagram VARCHAR(255),
    link_facebook VARCHAR(255),
    link_tiktok VARCHAR(255),

    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP
);

-- ----------------------------------------------------------------------------
--  Tabela: depoimentos  (CRUD de depoimentos de clientes)
-- ----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS depoimentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    autor VARCHAR(150) NOT NULL,
    texto TEXT NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);