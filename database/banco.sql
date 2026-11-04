-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geraÃ§Ã£o: 19/09/2026 Ã s 19:58
-- VersÃ£o do servidor: 10.11.19-MariaDB-cll-lve-log
-- VersÃ£o do PHP: 8.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `cyberco1_cyber`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `config`
--

CREATE TABLE `config` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `version` varchar(50) DEFAULT '2',
  `update_url` text DEFAULT NULL,
  `udp_ports` varchar(255) DEFAULT '7300',
  `contato` varchar(255) DEFAULT '',
  `site` varchar(255) DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `config`
--

INSERT INTO `config` (`id`, `user_id`, `version`, `update_url`, `udp_ports`, `contato`, `site`, `created_at`, `updated_at`) VALUES
(1, 1, '10', 'https://painel.cybercoari.com.br/api/config.php?token=xMmiMJBoS8Bdz9PP3uipGsbz', '7300', '', 'https://cybercoari.com.br', '2026-09-10 20:54:01', '2026-09-15 00:43:48');

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs`
--

CREATE TABLE `logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `logs`
--

INSERT INTO `logs` (`id`, `user_id`, `action`, `module`, `description`, `created_at`) VALUES
(75, 1, 'Limpou logs', 'Logs', 'Todos os logs anteriores foram removidos.', '2026-09-14 23:53:52'),
(76, 1, 'Excluiu usuÃ¡rio', 'UsuÃ¡rios', 'UsuÃ¡rio ID  excluÃ­do.', '2026-09-14 23:54:02'),
(77, 1, 'Excluiu usuÃ¡rio', 'UsuÃ¡rios', 'UsuÃ¡rio ID  excluÃ­do.', '2026-09-14 23:54:13'),
(78, 1, 'Excluiu backup', 'Backups', 'Backup backup_2026-09-14_23-51-37.sql excluÃ­do.', '2026-09-14 23:54:49'),
(79, 1, 'Criou backup', 'Backups', 'Backup backup_2026-09-14_23-54-53.sql criado.', '2026-09-14 23:54:53'),
(80, 1, 'Alterou senha', 'UsuÃ¡rios', 'Senha alterada.', '2026-09-14 23:56:42'),
(81, 1, 'Criou backup', 'Backups', 'Backup backup_2026-09-14_23-56-55.sql criado.', '2026-09-14 23:56:55'),
(82, 1, 'Excluiu backup', 'Backups', 'Backup backup_2026-09-14_23-54-53.sql excluÃ­do.', '2026-09-14 23:57:00'),
(83, 1, 'Excluiu backup', 'Backups', 'Backup backup_2026-09-14_23-56-55.sql excluÃ­do.', '2026-09-14 23:57:03'),
(84, 1, 'Criou backup', 'Backups', 'Backup backup_2026-09-14_23-57-09.sql criado.', '2026-09-14 23:57:09'),
(85, 1, 'Logout', 'AutenticaÃ§Ã£o', 'Logout realizado.', '2026-09-15 00:09:30'),
(86, 1, 'Login', 'AutenticaÃ§Ã£o', 'Login realizado.', '2026-09-15 00:09:39'),
(87, 1, 'Excluiu backup', 'Backups', 'Backup backup_2026-09-14_23-57-09.sql excluÃ­do.', '2026-09-15 00:09:50'),
(88, 1, 'Criou backup', 'Backups', 'Backup backup_2026-09-15_00-09-54.sql criado.', '2026-09-15 00:09:54'),
(89, 1, 'Atualizou configuraÃ§Ã£o', 'ConfiguraÃ§Ãµes', 'ConfiguraÃ§Ã£o do cliente atualizada.', '2026-09-15 00:43:40'),
(90, 1, 'Atualizou configuraÃ§Ã£o', 'ConfiguraÃ§Ãµes', 'ConfiguraÃ§Ã£o do cliente atualizada.', '2026-09-15 00:43:48'),
(91, 1, 'Atualizou configuraÃ§Ã£o', 'ConfiguraÃ§Ãµes', 'ConfiguraÃ§Ã£o do cliente atualizada.', '2026-09-15 00:43:52'),
(92, 1, 'Login', 'AutenticaÃ§Ã£o', 'Login realizado.', '2026-09-15 06:40:18'),
(93, 1, 'Login', 'AutenticaÃ§Ã£o', 'Login realizado.', '2026-09-15 07:17:04'),
(94, 1, 'Login', 'AutenticaÃ§Ã£o', 'Login realizado.', '2026-09-15 12:09:40'),
(95, 1, 'Login', 'AutenticaÃ§Ã£o', 'Login realizado.', '2026-09-15 19:26:14'),
(96, 1, 'Criou servidor', 'Servidores', 'Servidor criado.', '2026-09-15 19:27:03'),
(97, 1, 'Criou rede', 'Redes', 'Rede criada.', '2026-09-15 19:28:52'),
(98, 1, 'Login', 'AutenticaÃ§Ã£o', 'Login realizado.', '2026-09-16 01:43:04'),
(99, 1, 'Login', 'AutenticaÃ§Ã£o', 'Login realizado.', '2026-09-16 06:36:50'),
(100, 1, 'Login', 'AutenticaÃ§Ã£o', 'Login realizado.', '2026-09-16 11:56:54'),
(101, 1, 'Login', 'AutenticaÃ§Ã£o', 'Login realizado.', '2026-09-17 11:03:57');

-- --------------------------------------------------------

--
-- Estrutura para tabela `messages`
--

CREATE TABLE `messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `version` varchar(50) NOT NULL DEFAULT '1',
  `notes` text DEFAULT NULL,
  `update_url` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `networks`
--

CREATE TABLE `networks` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `color_name` varchar(30) DEFAULT '#ffffff',
  `description` varchar(255) DEFAULT '',
  `color_description` varchar(30) DEFAULT '#ffffff',
  `flag_url` text DEFAULT NULL,
  `payload_file` varchar(255) DEFAULT '',
  `proxy_ip` varchar(255) DEFAULT '',
  `proxy_port` varchar(20) DEFAULT '',
  `sni` varchar(255) DEFAULT '',
  `tls_ip` varchar(255) DEFAULT '',
  `color` varchar(30) DEFAULT '#0000ff',
  `info` varchar(255) DEFAULT 'Tlsws',
  `operator` varchar(100) DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `networks`
--

INSERT INTO `networks` (`id`, `user_id`, `name`, `color_name`, `description`, `color_description`, `flag_url`, `payload_file`, `proxy_ip`, `proxy_port`, `sni`, `tls_ip`, `color`, `info`, `operator`, `created_at`) VALUES
(2, 1, 'Vivo Novo Servidor', '#ffffff', '', '#ffffff', '', '', '162.247.241.4#179.191.168.33#179.191.168.41#179.191.168.17#179.191.168.57#179.191.168.73#179.191.168.65#179.191.168.49#179.191.169.57', '443', '', '', '#0000ff', 'Tlsws', 'TIM', '2026-09-15 19:28:52');

-- --------------------------------------------------------

--
-- Estrutura para tabela `payloads`
--

CREATE TABLE `payloads` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `content` text DEFAULT NULL,
  `type` varchar(50) DEFAULT 'sni',
  `network_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `servers`
--

CREATE TABLE `servers` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `color_name` varchar(30) DEFAULT '#ffffff',
  `description` varchar(255) DEFAULT '',
  `color_description` varchar(30) DEFAULT '#ffffff',
  `type` varchar(50) DEFAULT '',
  `flag_url` text DEFAULT NULL,
  `server_ip` varchar(255) DEFAULT '',
  `check_user` varchar(255) DEFAULT '',
  `server_port` varchar(20) DEFAULT '',
  `ssl_port` varchar(20) DEFAULT '',
  `username` varchar(255) DEFAULT '',
  `password` varchar(255) DEFAULT '',
  `color` varchar(30) DEFAULT '#ffffff',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `servers`
--

INSERT INTO `servers` (`id`, `user_id`, `name`, `color_name`, `description`, `color_description`, `type`, `flag_url`, `server_ip`, `check_user`, `server_port`, `ssl_port`, `username`, `password`, `color`, `created_at`) VALUES
(3, 1, 'Novo Servidor', '#ffffff', '', '#ffffff', 'premium', '', '1.2.3.41', '', '22', '443', '', '', '#ffffff', '2026-09-15 19:27:03');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `update_token_hash` char(64) DEFAULT NULL,
  `update_token` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `status`, `update_token_hash`, `update_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$s3nQLzsWZ5XarSL74iHVme1SvZPgudJQQ881I8uuZ46CehRkISPEW', 'admin', 'active', 'b14f9489d05ac370aff15b1fc8635c02ea35ca09a35b5da44c9062de9a4583bc', NULL, '2026-09-10 20:54:01', '2026-09-14 23:56:42');

-- --------------------------------------------------------

--
-- Estrutura para tabela `user_theme`
--

CREATE TABLE `user_theme` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `version` varchar(20) DEFAULT '1',
  `img_fundo` varchar(255) DEFAULT '',
  `img_logo` varchar(255) DEFAULT '',
  `img_banner` varchar(255) DEFAULT '',
  `img_menu` varchar(255) DEFAULT '',
  `img_logs` varchar(255) DEFAULT '',
  `img_check` varchar(255) DEFAULT '',
  `img_user` varchar(255) DEFAULT '',
  `img_pass` varchar(255) DEFAULT '',
  `img_update` varchar(255) DEFAULT '',
  `img_roteador` varchar(255) DEFAULT '',
  `img_download` varchar(255) DEFAULT '',
  `img_upload` varchar(255) DEFAULT '',
  `color_one` varchar(20) DEFAULT '#333333',
  `color_two` varchar(20) DEFAULT '#333333',
  `color_starter` varchar(20) DEFAULT '#333333',
  `color_dialogs` varchar(20) DEFAULT '#ffffff',
  `color_buttons` varchar(20) DEFAULT '#667eea',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `app_name` varchar(100) DEFAULT 'ConnectPlus',
  `button_primary` varchar(30) DEFAULT '#667eea',
  `button_primary_hover` varchar(30) DEFAULT '#5b6ee1',
  `button_secondary` varchar(30) DEFAULT '#6b7280',
  `button_secondary_hover` varchar(30) DEFAULT '#4b5563',
  `button_success` varchar(30) DEFAULT '#16a34a',
  `button_success_hover` varchar(30) DEFAULT '#15803d',
  `button_danger` varchar(30) DEFAULT '#dc2626',
  `button_danger_hover` varchar(30) DEFAULT '#b91c1c',
  `button_warning` varchar(30) DEFAULT '#f59e0b',
  `button_warning_hover` varchar(30) DEFAULT '#d97706',
  `button_info` varchar(30) DEFAULT '#0ea5e9',
  `button_info_hover` varchar(30) DEFAULT '#0284c7',
  `button_text` varchar(30) DEFAULT '#ffffff'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `user_theme`
--

INSERT INTO `user_theme` (`id`, `user_id`, `version`, `img_fundo`, `img_logo`, `img_banner`, `img_menu`, `img_logs`, `img_check`, `img_user`, `img_pass`, `img_update`, `color_one`, `color_two`, `color_starter`, `color_dialogs`, `color_buttons`, `created_at`, `updated_at`, `app_name`, `button_primary`, `button_primary_hover`, `button_secondary`, `button_secondary_hover`, `button_success`, `button_success_hover`, `button_danger`, `button_danger_hover`, `button_warning`, `button_warning_hover`, `button_info`, `button_info_hover`, `button_text`) VALUES
(1, 1, '10', 'https://cybercoari.com.br/cyber/imagens/fundo.jpg', 'https://cybercoari.com.br/cyber/imagens/atualizar.png', 'https://cybercoari.com.br/cyber/imagens/banner.png', 'https://cybercoari.com.br/cyber/imagens/menu.png', 'https://cybercoari.com.br/cyber/imagens/logs.png', 'https://cybercoari.com.br/cyber/imagens/check.png', 'https://cybercoari.com.br/cyber/imagens/user.png', 'https://cybercoari.com.br/cyber/imagens/pass.png', 'https://cybercoari.com.br/cyber/imagens/update.png', '#1AFFFFFF', '#333333', '#333333', '#1AFFFFFF', '#667EEA', '2026-09-10 20:54:01', '2026-09-16 02:09:39', 'ConnectPlus V2', '#667eea', '#5b6ee1', '#6b7280', '#4b5563', '#16a34a', '#15803d', '#dc2626', '#b91c1c', '#f59e0b', '#d97706', '#0ea5e9', '#0284c7', '#ffffff');

--
-- Ãndices para tabelas despejadas
--

--
-- Ãndices de tabela `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_config_user` (`user_id`);

--
-- Ãndices de tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_logs_user` (`user_id`),
  ADD KEY `idx_logs_created` (`created_at`);

--
-- Ãndices de tabela `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_messages_user` (`user_id`),
  ADD KEY `idx_messages_updated` (`updated_at`);

--
-- Ãndices de tabela `networks`
--
ALTER TABLE `networks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_networks_user` (`user_id`);

--
-- Ãndices de tabela `payloads`
--
ALTER TABLE `payloads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_payloads_user` (`user_id`);

--
-- Ãndices de tabela `servers`
--
ALTER TABLE `servers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_servers_user` (`user_id`);

--
-- Ãndices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_username` (`username`),
  ADD UNIQUE KEY `uq_users_token_hash` (`update_token_hash`);

--
-- Ãndices de tabela `user_theme`
--
ALTER TABLE `user_theme`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_theme` (`user_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `config`
--
ALTER TABLE `config`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT de tabela `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `networks`
--
ALTER TABLE `networks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `payloads`
--
ALTER TABLE `payloads`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `servers`
--
ALTER TABLE `servers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `user_theme`
--
ALTER TABLE `user_theme`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- RestriÃ§Ãµes para tabelas despejadas
--

--
-- RestriÃ§Ãµes para tabelas `config`
--
ALTER TABLE `config`
  ADD CONSTRAINT `fk_config_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- RestriÃ§Ãµes para tabelas `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `fk_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- RestriÃ§Ãµes para tabelas `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_messages_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- RestriÃ§Ãµes para tabelas `networks`
--
ALTER TABLE `networks`
  ADD CONSTRAINT `fk_networks_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- RestriÃ§Ãµes para tabelas `payloads`
--
ALTER TABLE `payloads`
  ADD CONSTRAINT `fk_payloads_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- RestriÃ§Ãµes para tabelas `servers`
--
ALTER TABLE `servers`
  ADD CONSTRAINT `fk_servers_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- RestriÃ§Ãµes para tabelas `user_theme`
--
ALTER TABLE `user_theme`
  ADD CONSTRAINT `fk_theme_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;-- ATX separado do VIP 2.1
-- Pode ser executado manualmente, embora o painel/API também criem as tabelas automaticamente.

CREATE TABLE IF NOT EXISTS `atx_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `version` varchar(50) NOT NULL DEFAULT '0',
  `release_notes` text DEFAULT NULL,
  `update_url` text DEFAULT NULL,
  `contato` varchar(255) NOT NULL DEFAULT '',
  `logo` text DEFAULT NULL,
  `fundo` text DEFAULT NULL,
  `check_pop` varchar(10) NOT NULL DEFAULT 'true',
  `cor_menu` varchar(20) NOT NULL DEFAULT '#000000',
  `cor_btcu` varchar(20) NOT NULL DEFAULT '#ff0000',
  `webview` text DEFAULT NULL,
  `roteador` varchar(10) NOT NULL DEFAULT 'true',
  `status_if` varchar(10) NOT NULL DEFAULT 'false',
  `icon_cor` varchar(20) NOT NULL DEFAULT '#ffffff',
  `html_webview` text DEFAULT NULL,
  `html_use` varchar(10) NOT NULL DEFAULT 'false',
  `html_app` text DEFAULT NULL,
  `background_cor` varchar(20) NOT NULL DEFAULT '#000000',
  `background_borda` int NOT NULL DEFAULT 35,
  `border_cor` varchar(20) NOT NULL DEFAULT '#ff0000',
  `border_borda` int NOT NULL DEFAULT 14,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`), UNIQUE KEY `uq_atx_config_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `atx_servers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'ssh',
  `flag_url` text DEFAULT NULL,
  `server_ip` varchar(255) NOT NULL DEFAULT '',
  `check_user` text DEFAULT NULL,
  `server_port` varchar(20) NOT NULL DEFAULT '',
  `ssl_port` varchar(20) NOT NULL DEFAULT '',
  `username` varchar(150) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `payload` text DEFAULT NULL,
  `sni` varchar(255) NOT NULL DEFAULT '',
  `tls_ip` varchar(255) NOT NULL DEFAULT '',
  `proxy_ip` varchar(255) NOT NULL DEFAULT '',
  `proxy_port` varchar(20) NOT NULL DEFAULT '',
  `info` varchar(255) NOT NULL DEFAULT 'Tlsws',
  `color` varchar(20) NOT NULL DEFAULT '#ffffff',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`), KEY `idx_atx_servers_user` (`user_id`), KEY `idx_atx_servers_status` (`user_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS connectplus_config (id INT UNSIGNED NOT NULL AUTO_INCREMENT,user_id INT UNSIGNED NOT NULL,version VARCHAR(50) NOT NULL DEFAULT '0',update_url TEXT DEFAULT NULL,udp_port VARCHAR(255) DEFAULT '7300',contato VARCHAR(255) DEFAULT '',site VARCHAR(255) DEFAULT '',created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,PRIMARY KEY(id),UNIQUE KEY uq_connectplus_user(user_id),KEY idx_connectplus_user(user_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS connectplus_servers (id INT UNSIGNED NOT NULL AUTO_INCREMENT,user_id INT UNSIGNED NOT NULL,name VARCHAR(150) NOT NULL,color_name VARCHAR(30) DEFAULT '#ffffff',description VARCHAR(255) DEFAULT '',color_description VARCHAR(30) DEFAULT '#ffffff',type VARCHAR(50) DEFAULT 'PROXY',flag_url TEXT DEFAULT NULL,server_ip VARCHAR(255) DEFAULT '',check_user VARCHAR(255) DEFAULT '',server_port VARCHAR(20) DEFAULT '',ssl_port VARCHAR(20) DEFAULT '',username VARCHAR(255) DEFAULT '',password VARCHAR(255) DEFAULT '',payload TEXT DEFAULT NULL,proxy_ip VARCHAR(255) DEFAULT '',proxy_port VARCHAR(20) DEFAULT '',sni VARCHAR(255) DEFAULT '',path VARCHAR(255) DEFAULT '',color VARCHAR(30) DEFAULT '#0000ff',info VARCHAR(255) DEFAULT 'Proxy',created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY(id),KEY idx_cp_servers_user(user_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- C4GMOD: tabelas isoladas do módulo (não compartilham tabelas)
-- ============================================================
CREATE TABLE IF NOT EXISTS c4gmod_app_config (
 id INT UNSIGNED NOT NULL AUTO_INCREMENT, app_name VARCHAR(120) NOT NULL DEFAULT 'CYBERCOARI', version VARCHAR(30) NOT NULL DEFAULT '3', release_notes TEXT NULL, url_update VARCHAR(500) NULL, sms VARCHAR(500) NULL, email_feedback VARCHAR(190) NULL, url_contato VARCHAR(500) NULL, url_termos VARCHAR(500) NULL, logoonline VARCHAR(500) NULL, fundoonline VARCHAR(500) NULL, fundo_do_log_online VARCHAR(500) NULL, check_user VARCHAR(500) NULL, cor_quadro VARCHAR(20) DEFAULT '#1AFFFFFF', cor_borda_quadro VARCHAR(20) DEFAULT '#FFFF0000', espessura_borda_quadro VARCHAR(20) DEFAULT '5.00', raio_quadro VARCHAR(20) DEFAULT '20.00', cor_spinner VARCHAR(20) DEFAULT '#00000000', cor_borda_spinner VARCHAR(20) DEFAULT '#FF0000', espessura_borda_spinner VARCHAR(20) DEFAULT '3.00', raio_spinner VARCHAR(20) DEFAULT '25.00', cor_texto_spinner VARCHAR(20) DEFAULT '#FF00FF00', cor_campo VARCHAR(20) DEFAULT '#00000000', cor_borda_campo VARCHAR(20) DEFAULT '#FF00FF00', espessura_borda_campo VARCHAR(20) DEFAULT '3.00', raio_campo VARCHAR(20) DEFAULT '25.00', cor_texto_campo VARCHAR(20) DEFAULT '#FFFFFFFF', cor_botao_conectar VARCHAR(20) DEFAULT '#CC202020', cor_borda_botao_conectar VARCHAR(20) DEFAULT '#FF00FF00', espessura_borda_botao_conectar VARCHAR(20) DEFAULT '3.00', raio_botao_conectar VARCHAR(20) DEFAULT '25.00', cor_texto_botao_conectar VARCHAR(20) DEFAULT '#FFFFFFFF', cor_status_conectado VARCHAR(20) DEFAULT '#FF00D26A', cor_status_desconectado VARCHAR(20) DEFAULT '#FFFF3030', updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT INTO c4gmod_app_config (id,app_name,version) SELECT 1,'CYBERCOARI','3' WHERE NOT EXISTS (SELECT 1 FROM c4gmod_app_config WHERE id=1);
CREATE TABLE IF NOT EXISTS c4gmod_servers (id INT UNSIGNED NOT NULL AUTO_INCREMENT,name VARCHAR(150) NOT NULL,type VARCHAR(50) DEFAULT 'Premium',flag VARCHAR(500) DEFAULT '',server_ip VARCHAR(255) DEFAULT '',check_user VARCHAR(500) DEFAULT '',server_port VARCHAR(20) DEFAULT '',ssl_port VARCHAR(20) DEFAULT '',username VARCHAR(255) DEFAULT '',password VARCHAR(255) DEFAULT '',sort_order INT DEFAULT 0,active TINYINT(1) DEFAULT 1,PRIMARY KEY(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS c4gmod_networks (id INT UNSIGNED NOT NULL AUTO_INCREMENT,name VARCHAR(150) NOT NULL,flag VARCHAR(500) DEFAULT '',payload TEXT,sni VARCHAR(255) DEFAULT '',tls_ip VARCHAR(255) DEFAULT '',proxy_ip TEXT,proxy_port VARCHAR(20) DEFAULT '',info VARCHAR(100) DEFAULT '',sort_order INT DEFAULT 0,active TINYINT(1) DEFAULT 1,PRIMARY KEY(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS c4gmod_udp (id INT UNSIGNED NOT NULL AUTO_INCREMENT,porta VARCHAR(20) NOT NULL,sort_order INT DEFAULT 0,active TINYINT(1) DEFAULT 1,PRIMARY KEY(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS c4gmod_users (id INT UNSIGNED NOT NULL AUTO_INCREMENT,username VARCHAR(100) NOT NULL,password_hash VARCHAR(255) DEFAULT '',expiration_date DATE NULL,connection_limit INT DEFAULT 1,active TINYINT(1) DEFAULT 1,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY(id),UNIQUE KEY uq_c4gmod_username(username)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS c4gmod_api_tokens (id INT UNSIGNED NOT NULL AUTO_INCREMENT,name VARCHAR(100) DEFAULT 'C4GMOD',token_hash CHAR(64) NOT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,last_used_at TIMESTAMP NULL,active TINYINT(1) DEFAULT 1,PRIMARY KEY(id),UNIQUE KEY uq_c4gmod_token(token_hash)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT INTO c4gmod_servers(name,type,flag,server_ip,server_port,ssl_port) SELECT 'BR BRASIL','Premium','br.png','app.cybercoari.app.br','22','443' WHERE NOT EXISTS (SELECT 1 FROM c4gmod_servers);
INSERT INTO c4gmod_networks(name,flag,proxy_ip,proxy_port,info) SELECT 'Tim','https://cybercoari.com.br/cyber/imagens/tim.png','1.2.3.4','80','Tlsws' WHERE NOT EXISTS (SELECT 1 FROM c4gmod_networks);
INSERT INTO c4gmod_udp(porta) SELECT '7300' WHERE NOT EXISTS (SELECT 1 FROM c4gmod_udp);
