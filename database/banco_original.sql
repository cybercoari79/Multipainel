-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 06/10/2026 às 17:18
-- Versão do servidor: 10.11.19-MariaDB-cll-lve-log
-- Versão do PHP: 8.4.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `cyberco1_painel`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `4g_config`
--

CREATE TABLE `4g_config` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `version` varchar(50) NOT NULL DEFAULT '0',
  `app_name` varchar(100) NOT NULL DEFAULT 'CYBERCOARI',
  `release_notes` text DEFAULT NULL,
  `update_url` text DEFAULT NULL,
  `sms_url` text DEFAULT NULL,
  `logoonline` text DEFAULT NULL,
  `fundoonline` text DEFAULT NULL,
  `logonline` text DEFAULT NULL,
  `rodapeonline` text DEFAULT NULL,
  `email_feedback` varchar(255) DEFAULT '',
  `contato` text DEFAULT NULL,
  `termos` text DEFAULT NULL,
  `check_user` varchar(10) DEFAULT 'true',
  `cor_borda_campo` varchar(20) DEFAULT '#FF0000',
  `cor_icone_usuario` varchar(20) DEFAULT '#FF0000',
  `cor_icone_senha` varchar(20) DEFAULT '#0000FF',
  `cor_botao_start` varchar(20) DEFAULT '#0000FF',
  `cor_borda_botao_start` varchar(20) DEFAULT '#FF0000',
  `cor_fonte_botao_start` varchar(20) DEFAULT '#00FFFF',
  `cor_titulo_app` varchar(20) DEFAULT '#FF00BAFF',
  `cor_status` varchar(20) DEFAULT '#FFFFFFFF',
  `cor_texto_versao` varchar(20) DEFAULT '#FFFFFFFF',
  `cor_numero_versao` varchar(20) DEFAULT '#FF00FF00',
  `cor_ip` varchar(20) DEFAULT '#FF00FF00',
  `cor_operadora` varchar(20) DEFAULT '#FF00FF00',
  `cor_icone_atualizar` varchar(20) DEFAULT '#FF00DE0C',
  `espessura_borda_campo` int(11) DEFAULT 0,
  `udp_ports` varchar(255) DEFAULT '7300;7400;7500',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `4g_config`
--

INSERT INTO `4g_config` (`id`, `user_id`, `version`, `app_name`, `release_notes`, `update_url`, `sms_url`, `logoonline`, `fundoonline`, `logonline`, `rodapeonline`, `email_feedback`, `contato`, `termos`, `check_user`, `cor_borda_campo`, `cor_icone_usuario`, `cor_icone_senha`, `cor_botao_start`, `cor_borda_botao_start`, `cor_fonte_botao_start`, `cor_titulo_app`, `cor_status`, `cor_texto_versao`, `cor_numero_versao`, `cor_ip`, `cor_operadora`, `cor_icone_atualizar`, `espessura_borda_campo`, `udp_ports`, `created_at`, `updated_at`) VALUES
(1, 1, '2', 'CYBERCOARI', 'Ola novas credenciais do APK...', 'https://painel.cybercoari.com.br/api/4g.php?token=58fx7pCeBKVfgFEC7SKiUyx7', 'https://painel.cybercoari.com.br/api/sms.php', 'https://cybercoari.com.br/cyber/imagens/logoonline.png', 'https://cybercoari.com.br/cyber/imagens/fundoonline.png', 'https://cybercoari.com.br/cyber/imagens/logonline.png', 'https://cybercoari.com.br/cyber/imagens/rodapeonline.png', 'admin@cybercoari.com.br', '', '', 'true', '#FF0000', '#FF0000', '#0000FF', '#0000FF', '#FF0000', '#00FFFF', '#FF00BAFF', '#FFFFFFFF', '#FFFFFFFF', '#FF00FF00', '#FF00FF00', '#FF00FF00', '#FF00DE0C', 0, '7300;7400;7500', '2026-10-05 00:43:23', '2026-10-06 12:26:21');

-- --------------------------------------------------------

--
-- Estrutura para tabela `4g_networks`
--

CREATE TABLE `4g_networks` (
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
-- Despejando dados para a tabela `4g_networks`
--

INSERT INTO `4g_networks` (`id`, `user_id`, `name`, `color_name`, `description`, `color_description`, `flag_url`, `payload_file`, `proxy_ip`, `proxy_port`, `sni`, `tls_ip`, `color`, `info`, `operator`, `created_at`) VALUES
(1, 1, 'TIM', '#ffffff', '', '#ffffff', 'https://cybercoari.com.br/cyber/imagens/tim.png', '', '162.247.241.4#179.191.168.33#179.191.168.41#179.191.168.17#179.191.168.57#179.191.168.73#179.191.168.65#179.191.168.49#179.191.169.57#179.191.169.81#179.191.169.89#179.191.169.97#179.191.169.65#179.191.169.113#179.191.172.9#179.191.172.1#179.191.172.25#17', '443', 'app.tim.com.br', '', '#0263c4', 'Tlsws', 'TIM', '2026-10-06 12:16:48');

-- --------------------------------------------------------

--
-- Estrutura para tabela `4g_payloads`
--

CREATE TABLE `4g_payloads` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `content` text DEFAULT NULL,
  `type` varchar(50) DEFAULT 'sni',
  `network_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `4g_payloads`
--

INSERT INTO `4g_payloads` (`id`, `user_id`, `name`, `content`, `type`, `network_id`, `created_at`) VALUES
(1, 1, 'TIM Payload', 'GET / HTTP/1.1[lf]Host: ln.centraldacdn.com[lf]Upgrade: websocket[lf][lf]#GET / HTTP/1.1[lf]Host: ln.centraldacdn.com[lf]Upgrade: websocket[lf][lf]', 'sni', 1, '2026-10-06 12:18:24'),
(2, 1, 'TIM Payload', 'GET / HTTP/1.1[lf]Host: ln.centraldacdn.com[lf]Upgrade: websocket[lf][lf]#GET / HTTP/1.1[lf]Host: ln.centraldacdn.com[lf]Upgrade: websocket[lf][lf]', 'sni', 1, '2026-10-06 12:19:56'),
(3, 1, 'TIM Payload', 'GET / HTTP/1.1[lf]Host: ln.centraldacdn.com[lf]Upgrade: websocket[lf][lf]#GET / HTTP/1.1[lf]Host: ln.centraldacdn.com[lf]Upgrade: websocket[lf][lf]', 'sni', 1, '2026-10-06 12:38:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `4g_servers`
--

CREATE TABLE `4g_servers` (
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
-- Despejando dados para a tabela `4g_servers`
--

INSERT INTO `4g_servers` (`id`, `user_id`, `name`, `color_name`, `description`, `color_description`, `type`, `flag_url`, `server_ip`, `check_user`, `server_port`, `ssl_port`, `username`, `password`, `color`, `created_at`) VALUES
(1, 1, 'BR', '#ffffff', '', '#ffffff', 'premium', 'https://cybercoari.com.br/cyber/imagens/br.png', 'app.cybercoari.app.br', '', '22', '443', '', '', '#ffffff', '2026-10-06 12:11:42');

-- --------------------------------------------------------

--
-- Estrutura para tabela `atx_config`
--

CREATE TABLE `atx_config` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `version` varchar(50) NOT NULL DEFAULT '0',
  `app_name` varchar(100) NOT NULL DEFAULT 'ATX TUNNEL',
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
  `background_borda` int(11) NOT NULL DEFAULT 35,
  `border_cor` varchar(20) NOT NULL DEFAULT '#ff0000',
  `border_borda` int(11) NOT NULL DEFAULT 14,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `atx_config`
--

INSERT INTO `atx_config` (`id`, `user_id`, `version`, `app_name`, `release_notes`, `update_url`, `contato`, `logo`, `fundo`, `check_pop`, `cor_menu`, `cor_btcu`, `webview`, `roteador`, `status_if`, `icon_cor`, `html_webview`, `html_use`, `html_app`, `background_cor`, `background_borda`, `border_cor`, `border_borda`, `created_at`, `updated_at`) VALUES
(1, 1, '3', 'ATXMOD', 'Ola boa noite.', 'https://painel.cybercoari.com.br/api/atx.php', '', 'https://cybercoari.com.br/cyber/imagens/logoonline.png', 'https://cybercoari.com.br/cyber/imagens/fundoonline.png', 'true', '#0000FF', '#FF0000', '', 'true', 'false', '#FFFFFF', '', 'false', '', '#1AFFFFFF', 10, '#FF0000', 15, '2026-09-29 11:51:19', '2026-10-05 23:21:04');

-- --------------------------------------------------------

--
-- Estrutura para tabela `atx_servers`
--

CREATE TABLE `atx_servers` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
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
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `atx_servers`
--

INSERT INTO `atx_servers` (`id`, `user_id`, `name`, `type`, `flag_url`, `server_ip`, `check_user`, `server_port`, `ssl_port`, `username`, `password`, `payload`, `sni`, `tls_ip`, `proxy_ip`, `proxy_port`, `info`, `color`, `status`, `created_at`, `updated_at`) VALUES
(2, 1, 'Tim Direct', 'Direct', 'https://cybercoari.com.br/cyber/imagens/tim.png', 'app.cybercoari.app.br', '', '22', '443', '', '', '', '', '', '', '', 'Direct', '#FFFFFF', 'active', '2026-10-02 18:22:16', '2026-10-05 15:58:20'),
(3, 1, 'TIM SSL', 'Ssl', 'https://cybercoari.com.br/cyber/imagens/tim.png', 'app.cybercoari.app.br', '', '', '443', '', '', '', 'app.tim.com.br', '', '', '', 'Ssl', '#FFFFFF', 'active', '2026-10-02 20:13:06', '2026-10-02 20:16:07'),
(4, 1, 'TIM TLSWS', 'Tlsws', 'https://cybercoari.com.br/cyber/imagens/tim.png', 'app.cybercoari.app.br', '', '', '443', '', '', '', 'app.tim.com.br', '', '1.2.3.4', '443', 'Tlsws', '#FFFFFF', 'active', '2026-10-02 20:18:44', '2026-10-02 20:18:44'),
(5, 1, 'TIM PROXY 1', 'Proxy', 'https://cybercoari.com.br/cyber/imagens/tim.png', 'app.cybercoari.app.br', '', '22', '443', '', '', 'GET / HTTP/1.1[lf]Host: ln.centraldacdn.com[lf]Upgrade: websocket[lf][lf]#GET / HTTP/1.1[lf]Host: ln.centraldacdn.com[lf]Upgrade: websocket[lf][lf]', '', '', '162.247.241.4#179.191.168.33#179.191.168.41#179.191.168.17#179.191.168.57#179.191.168.73#179.191.168.65#179.191.168.49#179.191.169.57#179.191.169.81#179.191.169.89#179.191.169.97#179.191.169.65#179.191.169.113#179.191.172.9#179.191.172.1#179.191.172.25#17', '443', 'Proxy', '#FFFFFF', 'active', '2026-10-02 21:24:55', '2026-10-06 11:27:40');

-- --------------------------------------------------------

--
-- Estrutura para tabela `config`
--

CREATE TABLE `config` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `version` varchar(50) DEFAULT '2',
  `update_url` text DEFAULT NULL,
  `sms` text DEFAULT NULL,
  `email_feedback` varchar(255) DEFAULT '',
  `url_termos` text DEFAULT NULL,
  `check_user` varchar(20) DEFAULT 'true',
  `rodapeonline` text DEFAULT NULL,
  `udp_ports` varchar(255) DEFAULT '7300',
  `contato` varchar(255) DEFAULT '',
  `site` varchar(255) DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `sms_url` varchar(255) NOT NULL DEFAULT '',
  `logoonline` varchar(255) NOT NULL DEFAULT '',
  `fundoonline` varchar(255) NOT NULL DEFAULT '',
  `logonline` varchar(255) NOT NULL DEFAULT '',
  `cor_borda_campo` varchar(20) NOT NULL DEFAULT '#FF0000',
  `cor_icone_usuario` varchar(20) NOT NULL DEFAULT '#FF0000',
  `cor_icone_senha` varchar(20) NOT NULL DEFAULT '#0000FF',
  `cor_botao_start` varchar(20) NOT NULL DEFAULT '#0000FF',
  `cor_borda_botao_start` varchar(20) NOT NULL DEFAULT '#FF0000',
  `cor_fonte_botao_start` varchar(20) NOT NULL DEFAULT '#00FFFF',
  `cor_titulo_app` varchar(20) NOT NULL DEFAULT '#FF00BAFF',
  `cor_status` varchar(20) NOT NULL DEFAULT '#FFFFFFFF',
  `cor_texto_versao` varchar(20) NOT NULL DEFAULT '#FFFFFFFF',
  `cor_numero_versao` varchar(20) NOT NULL DEFAULT '#FF00FF00',
  `cor_ip` varchar(20) NOT NULL DEFAULT '#FF00FF00',
  `cor_operadora` varchar(20) NOT NULL DEFAULT '#FF00FF00',
  `cor_icone_atualizar` varchar(20) NOT NULL DEFAULT '#FF00DE0C',
  `espessura_borda_campo` tinyint(3) UNSIGNED NOT NULL DEFAULT 3
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `config`
--

INSERT INTO `config` (`id`, `user_id`, `version`, `update_url`, `sms`, `email_feedback`, `url_termos`, `check_user`, `rodapeonline`, `udp_ports`, `contato`, `site`, `created_at`, `updated_at`, `sms_url`, `logoonline`, `fundoonline`, `logonline`, `cor_borda_campo`, `cor_icone_usuario`, `cor_icone_senha`, `cor_botao_start`, `cor_borda_botao_start`, `cor_fonte_botao_start`, `cor_titulo_app`, `cor_status`, `cor_texto_versao`, `cor_numero_versao`, `cor_ip`, `cor_operadora`, `cor_icone_atualizar`, `espessura_borda_campo`) VALUES
(1, 1, '12', 'https://painel.cybercoari.com.br/api/config.php?token=cwUjukcJfg84pYL3ZX9Trtvc', NULL, 'admin@cybercoari.com.br', '', 'true', 'https://cybercoari.com.br/cyber/imagens/rodapeonline.png', '7300;7400;7500', '', 'https://cybercoari.com.br', '2026-09-10 20:54:01', '2026-10-04 12:14:15', 'https://painel.cybercoari.com.br/api/sms.php?token=EJXtz8hwGLpdUZftWzVyptoR', 'https://cybercoari.com.br/cyber/imagens/logoonline.png', 'https://cybercoari.com.br/cyber/imagens/fundoonline.png', 'https://cybercoari.com.br/cyber/imagens/logonline.png', '#FF0000', '#FF0000', '#0000FF', '#0000FF', '#FF0000', '#00FFFF', '#FF00BAFF', '#FFFFFFFF', '#FFFFFFFF', '#FF00FF00', '#FF00FF00', '#FF00FF00', '#FF00DE0C', 0),
(2, 2, '2', 'https://painel.cybercoari.com.br/api/config.php?token=e9fGPXMX9RKZT2YLvC69cWvv', NULL, '', NULL, 'true', NULL, '7300', '', '', '2026-10-05 14:55:56', '2026-10-05 14:55:56', '', '', '', '', '#FF0000', '#FF0000', '#0000FF', '#0000FF', '#FF0000', '#00FFFF', '#FF00BAFF', '#FFFFFFFF', '#FFFFFFFF', '#FF00FF00', '#FF00FF00', '#FF00FF00', '#FF00DE0C', 3),
(3, 3, '2', 'https://painel.cybercoari.com.br/api/config.php?token=DXZUy5XGK5NfsivxW5F5MNCN', NULL, '', NULL, 'true', NULL, '7300', '', '', '2026-10-05 22:13:58', '2026-10-05 22:13:58', '', '', '', '', '#FF0000', '#FF0000', '#0000FF', '#0000FF', '#FF0000', '#00FFFF', '#FF00BAFF', '#FFFFFFFF', '#FFFFFFFF', '#FF00FF00', '#FF00FF00', '#FF00FF00', '#FF00DE0C', 3);

-- --------------------------------------------------------

--
-- Estrutura para tabela `connectplus_config`
--

CREATE TABLE `connectplus_config` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `version` varchar(50) NOT NULL DEFAULT '0',
  `update_url` text DEFAULT NULL,
  `udp_port` varchar(255) DEFAULT '7300',
  `contato` varchar(255) DEFAULT '',
  `site` varchar(255) DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `connectplus_config`
--

INSERT INTO `connectplus_config` (`id`, `user_id`, `version`, `update_url`, `udp_port`, `contato`, `site`, `created_at`, `updated_at`) VALUES
(1, 1, '2', 'https://painel.cybercoari.com.br/update/admin/connectplus.json', '7300', 'https://wa.me/5597981187297', 'https://cybercoari.com.br', '2026-09-30 20:23:58', '2026-10-05 23:21:50');

-- --------------------------------------------------------

--
-- Estrutura para tabela `connectplus_servers`
--

CREATE TABLE `connectplus_servers` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `color_name` varchar(30) DEFAULT '#ffffff',
  `description` varchar(255) DEFAULT '',
  `color_description` varchar(30) DEFAULT '#ffffff',
  `type` varchar(50) DEFAULT 'PROXY',
  `flag_url` text DEFAULT NULL,
  `server_ip` varchar(255) DEFAULT '',
  `check_user` varchar(255) DEFAULT '',
  `server_port` varchar(20) DEFAULT '',
  `ssl_port` varchar(20) DEFAULT '',
  `username` varchar(255) DEFAULT '',
  `password` varchar(255) DEFAULT '',
  `payload` text DEFAULT NULL,
  `proxy_ip` varchar(255) DEFAULT '',
  `proxy_port` varchar(20) DEFAULT '',
  `sni` varchar(255) DEFAULT '',
  `path` varchar(255) DEFAULT '',
  `color` varchar(30) DEFAULT '#0000ff',
  `info` varchar(255) DEFAULT 'Proxy',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `connectplus_servers`
--

INSERT INTO `connectplus_servers` (`id`, `user_id`, `name`, `color_name`, `description`, `color_description`, `type`, `flag_url`, `server_ip`, `check_user`, `server_port`, `ssl_port`, `username`, `password`, `payload`, `proxy_ip`, `proxy_port`, `sni`, `path`, `color`, `info`, `created_at`) VALUES
(3, 1, 'TIM', '#ffffff', 'T1M 01', '#ffffff', 'DIRECT', 'https://cybercoari.com.br/cyber/imagens/tim.png', 'app.cybercoari.app.br', '', '443', '443', '', '', 'GET / HTTP/1.1[lf]Host: ln.centraldacdn.com[lf]Upgrade: websocket[lf][lf]#GET / HTTP/1.1[lf]Host: ln.centraldacdn.com[lf]Upgrade: websocket[lf][lf]', '', '', '', '', '#0000ff', 'Direct', '2026-10-05 22:55:20');

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
(1, NULL, 'Criou usuário', 'Usuários', 'Usuário admin criado.', '2026-10-05 14:55:56'),
(2, 2, 'Login', 'Autenticação', 'Login realizado.', '2026-10-05 14:57:51'),
(3, 1, 'Login', 'Autenticação', 'Login realizado.', '2026-10-05 15:52:59'),
(4, 1, 'Alterou senha', 'Usuários', 'Senha alterada.', '2026-10-05 15:53:25'),
(5, 1, 'Logout', 'Autenticação', 'Logout realizado.', '2026-10-05 15:53:34'),
(6, 1, 'Login', 'Autenticação', 'Login realizado.', '2026-10-05 15:53:42'),
(7, 1, 'Editou servidor ATX', 'ATX', 'Servidor ATX salvo.', '2026-10-05 15:58:20'),
(8, 1, 'Excluiu servidor ATX', 'ATX', 'Servidor ATX ID 1 excluído.', '2026-10-05 15:58:46'),
(9, 1, 'Login', 'Autenticação', 'Login realizado.', '2026-10-05 17:37:54'),
(10, 1, 'Criou backup', 'Backups', 'Backup backup_2026-10-05_17-38-53.sql criado.', '2026-10-05 17:38:53'),
(11, 1, 'Excluiu backup', 'Backups', 'Backup backup_2026-10-05_17-38-53.sql excluído.', '2026-10-05 17:39:10'),
(12, 1, 'Criou backup', 'Backups', 'Backup backup_05-10-2026_17-47-46.sql criado.', '2026-10-05 17:47:46'),
(13, 1, 'Criou backup', 'Backups', 'Backup backup_05-10-2026.sql criado.', '2026-10-05 17:49:23'),
(14, 1, 'Login', 'Autenticação', 'Login realizado.', '2026-10-05 19:53:49'),
(15, 1, 'Criou servidor', 'Servidores', 'Servidor criado.', '2026-10-05 20:41:44'),
(16, 1, 'Logout', 'Autenticação', 'Logout realizado.', '2026-10-05 21:50:25'),
(17, 1, 'Login', 'Autenticação', 'Login realizado.', '2026-10-05 21:50:35'),
(18, 1, 'Logout', 'Autenticação', 'Logout realizado.', '2026-10-05 22:13:44'),
(19, NULL, 'Criou usuário', 'Usuários', 'Usuário connan criado.', '2026-10-05 22:13:58'),
(20, 1, 'Login', 'Autenticação', 'Login realizado.', '2026-10-05 22:18:19'),
(21, 1, 'Criou servidor', 'Servidores', 'Servidor criado.', '2026-10-05 22:25:35'),
(22, 1, 'Editou servidor', 'Servidores', 'Servidor ID 2 atualizado.', '2026-10-05 22:27:43'),
(23, 1, 'Editou servidor', 'Servidores', 'Servidor ID 2 atualizado.', '2026-10-05 22:47:15'),
(24, 1, 'Editou servidor', 'Servidores', 'Servidor ID 2 atualizado.', '2026-10-05 22:54:22'),
(25, 1, 'Excluiu servidor', 'Servidores', 'Servidor ID 2 excluído.', '2026-10-05 22:54:29'),
(26, 1, 'Criou servidor', 'Servidores', 'Servidor criado.', '2026-10-05 22:55:20'),
(27, 1, 'Editou servidor', 'Servidores', 'Servidor ID 3 atualizado.', '2026-10-05 22:57:53'),
(28, 1, 'Atualizou configuração ATX', 'ATX', 'Configuração ATX atualizada.', '2026-10-05 23:21:04'),
(29, 1, 'Atualizou tema', 'Tema', 'Tema do aplicativo atualizado.', '2026-10-05 23:27:24'),
(30, 1, 'Atualizou tema', 'Tema', 'Tema do aplicativo atualizado.', '2026-10-05 23:35:26'),
(31, 1, 'Login', 'Autenticação', 'Login realizado.', '2026-10-06 00:09:41'),
(32, 1, 'Login', 'Autenticação', 'Login realizado.', '2026-10-06 09:29:16'),
(33, 1, 'Login', 'Autenticação', 'Login realizado.', '2026-10-06 10:13:27'),
(34, 1, 'Editou servidor ATX', 'ATX', 'Servidor ATX salvo.', '2026-10-06 10:14:22'),
(35, 1, 'Editou servidor ATX', 'ATX', 'Servidor ATX salvo.', '2026-10-06 10:14:54'),
(36, 1, 'Editou servidor ATX', 'ATX', 'Servidor ATX salvo.', '2026-10-06 10:16:57'),
(37, 1, 'Editou servidor', 'Servidores', 'Servidor ID 3 atualizado.', '2026-10-06 10:17:25'),
(38, 1, 'Login', 'Autenticação', 'Login realizado.', '2026-10-06 10:54:04'),
(39, 1, 'Editou servidor', 'Servidores', 'Servidor ID 3 atualizado.', '2026-10-06 10:54:46'),
(40, 1, 'Editou servidor', 'Servidores', 'Servidor ID 3 atualizado.', '2026-10-06 10:55:20'),
(41, 1, 'Editou servidor ATX', 'ATX', 'Servidor ATX salvo.', '2026-10-06 10:56:20'),
(42, 1, 'Login', 'Autenticação', 'Login realizado.', '2026-10-06 11:01:22'),
(43, 1, 'Criou servidor ATX', 'ATX', 'Servidor ATX salvo.', '2026-10-06 11:05:20'),
(44, 1, 'Editou servidor ATX', 'ATX', 'Servidor ATX salvo.', '2026-10-06 11:06:26'),
(45, 1, 'Editou servidor ATX', 'ATX', 'Servidor ATX salvo.', '2026-10-06 11:25:02'),
(46, 1, 'Excluiu servidor ATX', 'ATX', 'Servidor ATX ID 6 excluído.', '2026-10-06 11:27:05'),
(47, 1, 'Editou servidor ATX', 'ATX', 'Servidor ATX salvo.', '2026-10-06 11:27:40'),
(48, 1, 'Atualizou tema', 'Tema', 'Tema do aplicativo atualizado.', '2026-10-06 11:49:04'),
(49, 1, 'Atualizou tema', 'Tema', 'Tema do aplicativo atualizado.', '2026-10-06 11:49:35'),
(50, 1, 'Excluiu usuário', 'Usuários', 'Usuário ID 3 excluído.', '2026-10-06 11:52:05'),
(51, 1, 'Regenerou token', 'Usuários', 'Token de API regenerado para usuário ID 1.', '2026-10-06 11:55:33'),
(52, 1, 'Atualizou configuração', '4G', 'Configuração do 4G atualizada.', '2026-10-06 11:56:40'),
(53, 1, 'Atualizou configuração', '4G', 'Configuração do 4G atualizada.', '2026-10-06 11:59:34'),
(54, 1, 'Criou servidor', 'Servidores', 'Servidor criado.', '2026-10-06 12:11:42'),
(55, 1, 'Editou servidor', 'Servidores', 'Servidor ID atualizado.', '2026-10-06 12:12:36'),
(56, 1, 'Criou rede', 'Redes', 'Rede criada.', '2026-10-06 12:16:48'),
(57, 1, 'Editou rede', 'Redes', 'Rede ID atualizada.', '2026-10-06 12:18:24'),
(58, 1, 'Editou rede', 'Redes', 'Rede ID atualizada.', '2026-10-06 12:19:56'),
(59, 1, 'Editou servidor', 'Servidores', 'Servidor ID atualizado.', '2026-10-06 12:20:37'),
(60, 1, 'Atualizou configuração', '4G', 'Configuração do 4G atualizada.', '2026-10-06 12:22:29'),
(61, 1, 'Atualizou configuração', '4G', 'Configuração do 4G atualizada.', '2026-10-06 12:26:21'),
(62, 1, 'Editou rede', 'Redes', 'Rede ID atualizada.', '2026-10-06 12:38:00'),
(63, 1, 'Login', 'Autenticação', 'Login realizado.', '2026-10-06 17:11:58');

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

--
-- Despejando dados para a tabela `messages`
--

INSERT INTO `messages` (`id`, `user_id`, `version`, `notes`, `update_url`, `created_at`, `updated_at`) VALUES
(1, 1, '3', 'Ola novas credenciais do APK...', NULL, '2026-09-28 21:26:16', '2026-10-04 22:55:06');

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
(2, 1, 'Tim', '#ffffff', '', '#ffffff', 'https://cybercoari.com.br/cyber/imagens/tim.png', '', '162.247.241.4#179.191.168.33#179.191.168.41#179.191.168.17#179.191.168.57#179.191.168.73#179.191.168.65#179.191.168.49#179.191.169.57', '443', '', '', '#0000ff', 'Tlsws', 'TIM', '2026-09-15 19:28:52');

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
  `expiration_date` date DEFAULT NULL,
  `count_connection` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `limiter_user` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `update_token_hash` char(64) DEFAULT NULL,
  `update_token` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `status`, `expiration_date`, `count_connection`, `limiter_user`, `update_token_hash`, `update_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$89N7Qk3ytOK4nQ6NfghsuuFPPbL.zW78AZF.ABhWco98CytItbSmm', 'admin', 'active', NULL, 0, 1, 'aaac7e84645df024eff86a9b03c0a1db9836eb94f470b06c9cb1f1b47e44fedb', NULL, '2026-10-05 14:55:56', '2026-10-06 11:55:33');

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
(1, 1, '18', 'https://cybercoari.com.br/cyber/imagens/fundoonline.png', 'https://cybercoari.com.br/cyber/imagens/logoonline.png', 'https://cybercoari.com.br/cyber/imagens/banner.png', 'https://cybercoari.com.br/cyber/imagens/menu.png', 'https://cybercoari.com.br/cyber/imagens/logs.png', 'https://cybercoari.com.br/cyber/imagens/check.png', 'https://cybercoari.com.br/cyber/imagens/user.png', 'https://cybercoari.com.br/cyber/imagens/pass.png', 'https://cybercoari.com.br/cyber/imagens/update.png', '#1AFFFFFF', '#333333', '#333333', '#1AFFFFFF', '#667EEA', '2026-09-10 20:54:01', '2026-10-06 11:49:35', 'ConnectPlus', '#667eea', '#5b6ee1', '#6b7280', '#4b5563', '#16a34a', '#15803d', '#dc2626', '#b91c1c', '#f59e0b', '#d97706', '#0ea5e9', '#0284c7', '#ffffff'),
(2, 2, '1', '', '', '', '', '', '', '', '', '', '#333333', '#333333', '#333333', '#ffffff', '#667eea', '2026-10-05 14:55:56', '2026-10-05 14:55:56', 'ConnectPlus', '#667eea', '#5b6ee1', '#6b7280', '#4b5563', '#16a34a', '#15803d', '#dc2626', '#b91c1c', '#f59e0b', '#d97706', '#0ea5e9', '#0284c7', '#ffffff'),
(3, 3, '1', '', '', '', '', '', '', '', '', '', '#333333', '#333333', '#333333', '#ffffff', '#667eea', '2026-10-05 22:13:58', '2026-10-05 22:13:58', 'ConnectPlus', '#667eea', '#5b6ee1', '#6b7280', '#4b5563', '#16a34a', '#15803d', '#dc2626', '#b91c1c', '#f59e0b', '#d97706', '#0ea5e9', '#0284c7', '#ffffff');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `4g_config`
--
ALTER TABLE `4g_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_4g_user` (`user_id`);

--
-- Índices de tabela `4g_networks`
--
ALTER TABLE `4g_networks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_4g_networks_user` (`user_id`);

--
-- Índices de tabela `4g_payloads`
--
ALTER TABLE `4g_payloads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_4g_payloads_user` (`user_id`),
  ADD KEY `idx_4g_payloads_network` (`network_id`);

--
-- Índices de tabela `4g_servers`
--
ALTER TABLE `4g_servers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_4g_servers_user` (`user_id`);

--
-- Índices de tabela `atx_config`
--
ALTER TABLE `atx_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_atx_config_user` (`user_id`);

--
-- Índices de tabela `atx_servers`
--
ALTER TABLE `atx_servers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_atx_servers_user` (`user_id`),
  ADD KEY `idx_atx_servers_status` (`user_id`,`status`);

--
-- Índices de tabela `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_config_user` (`user_id`);

--
-- Índices de tabela `connectplus_config`
--
ALTER TABLE `connectplus_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_connectplus_user` (`user_id`),
  ADD KEY `idx_connectplus_user` (`user_id`);

--
-- Índices de tabela `connectplus_servers`
--
ALTER TABLE `connectplus_servers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cp_servers_user` (`user_id`);

--
-- Índices de tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_logs_user` (`user_id`),
  ADD KEY `idx_logs_created` (`created_at`);

--
-- Índices de tabela `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_messages_user` (`user_id`);

--
-- Índices de tabela `networks`
--
ALTER TABLE `networks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_networks_user` (`user_id`);

--
-- Índices de tabela `payloads`
--
ALTER TABLE `payloads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_payloads_user` (`user_id`),
  ADD KEY `idx_payloads_network` (`network_id`);

--
-- Índices de tabela `servers`
--
ALTER TABLE `servers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_servers_user` (`user_id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_username` (`username`);

--
-- Índices de tabela `user_theme`
--
ALTER TABLE `user_theme`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_theme_user` (`user_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `4g_config`
--
ALTER TABLE `4g_config`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `4g_networks`
--
ALTER TABLE `4g_networks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `4g_payloads`
--
ALTER TABLE `4g_payloads`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `4g_servers`
--
ALTER TABLE `4g_servers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `atx_config`
--
ALTER TABLE `atx_config`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `atx_servers`
--
ALTER TABLE `atx_servers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `config`
--
ALTER TABLE `config`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `connectplus_config`
--
ALTER TABLE `connectplus_config`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `connectplus_servers`
--
ALTER TABLE `connectplus_servers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT de tabela `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `networks`
--
ALTER TABLE `networks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `payloads`
--
ALTER TABLE `payloads`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `servers`
--
ALTER TABLE `servers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;