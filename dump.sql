-- =========================================
-- ÉHistória - Portal de Notícias
-- Dump do Banco de Dados
-- =========================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Cria e seleciona o banco
CREATE DATABASE IF NOT EXISTS `portalnoticia` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `portalnoticia`;

-- Remove tabelas se existirem (ordem importa por causa do FK)
DROP TABLE IF EXISTS `noticias`;
DROP TABLE IF EXISTS `usuarios`;

-- -------------------------
-- Tabela: usuarios
-- -------------------------
CREATE TABLE `usuarios` (
  `id`    INT(11) NOT NULL AUTO_INCREMENT,
  `nome`  VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `senha` VARCHAR(255) NOT NULL,
  `tipo`  ENUM('admin','usuario') NOT NULL DEFAULT 'usuario',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------
-- Tabela: noticias
-- -------------------------
CREATE TABLE `noticias` (
  `id`      INT(11) NOT NULL AUTO_INCREMENT,
  `titulo`  VARCHAR(255) NOT NULL,
  `noticia` TEXT NOT NULL,
  `data`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `autor`   INT(11) NOT NULL,
  `imagem`  VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_autor_idx` (`autor`),
  CONSTRAINT `fk_autor` FOREIGN KEY (`autor`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------
-- Dados de exemplo
-- Senha de todos os usuarios: password
-- -------------------------
INSERT INTO `usuarios` (`nome`, `email`, `senha`, `tipo`) VALUES
('Administrador', 'admin@ehistoria.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Redator EHistoria', 'redator@ehistoria.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'usuario'),
('Maria Silva', 'maria@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'usuario');

INSERT INTO `noticias` (`titulo`, `noticia`, `data`, `autor`, `imagem`) VALUES
('Descoberta arqueologica revoluciona historia do Brasil colonial',
 'Pesquisadores da Universidade Federal encontraram vestigios de uma antiga civilizacao no interior de Minas Gerais que pode reescrever os livros de historia sobre o periodo colonial brasileiro.\r\nOs artefatos datam de aproximadamente 400 anos e indicam a existencia de uma comunidade organizada muito antes dos registros oficiais conhecidos.\r\nA equipe, liderada pela professora Dra. Ana Carvalho, trabalha no sitio arqueologico ha tres anos e ja coletou mais de 500 pecas.',
 '2026-04-06 10:00:00', 1, NULL),
('Festival de historia medieval reune milhares em Sao Paulo',
 'O maior festival de reconstituicao historica da America Latina aconteceu neste final de semana no Parque Ibirapuera, reunindo cerca de 50 mil visitantes nos dois dias de evento.\r\nO festival Tempos Medievais trouxe grupos de reconstituicao de 15 paises diferentes com trajes autenticos da epoca medieval europeia.\r\nHouve apresentacoes de musica medieval ao vivo, torneios de arquaria e demonstracoes de artes marciais historicas.',
 '2026-04-07 14:30:00', 1, NULL),
('Novo museu de historia natural abre as portas em Curitiba',
 'A cidade de Curitiba ganhou mais um espaco cultural com a inauguracao do Museu de Historia Natural do Parana. O local esta instalado em um casarao historico do seculo XIX totalmente restaurado.\r\nO museu conta com mais de 3.000 pecas em seu acervo inaugural dividido em tres grandes alas: Pre-historia Regional, Fauna e Flora do Parana e Historia Indigena.\r\nA entrada e gratuita as tercas-feiras. Nos demais dias o ingresso custa R$ 20,00 inteira e R$ 10,00 meia.',
 '2026-04-08 09:00:00', 2, NULL);
