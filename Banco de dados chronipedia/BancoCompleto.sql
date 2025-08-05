
CREATE DATABASE chronipedia;
USE chronipedia;

CREATE TABLE `usuarios` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `data_nascimento` date DEFAULT NULL,
  `genero` varchar(20) DEFAULT NULL,
  `imagem_perfil` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

CREATE TABLE `comunidades` (
  `id_comunidade` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `descricao` text DEFAULT NULL,
  `imagem_capa` varchar(255) DEFAULT NULL,
  `criador_id` int(11) NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp(),
  `privacidade` varchar(20) NOT NULL DEFAULT 'publica',
  `universo` varchar(255) DEFAULT NULL,
  `tags` text DEFAULT NULL,
  PRIMARY KEY (`id_comunidade`),
  KEY `criador_id` (`criador_id`),
  CONSTRAINT `comunidades_ibfk_1` FOREIGN KEY (`criador_id`) REFERENCES `usuarios` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `artigos` (
  `id_artigo` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(200) NOT NULL,
  `conteudo` longtext NOT NULL,
  `comunidade_id` int(11) NOT NULL,
  `criado_por` int(11) DEFAULT NULL,
  `data_publicacao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_artigo`),
  KEY `comunidade_id` (`comunidade_id`),
  KEY `criado_por` (`criado_por`),
  CONSTRAINT `artigos_ibfk_1` FOREIGN KEY (`comunidade_id`) REFERENCES `comunidades` (`id_comunidade`) ON DELETE CASCADE,
  CONSTRAINT `artigos_ibfk_2` FOREIGN KEY (`criado_por`) REFERENCES `usuarios` (`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `personagens` (
  `id_personagem` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `apelido` varchar(150) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `descricao` longtext DEFAULT NULL,
  `genero` varchar(50) DEFAULT NULL,
  `idade` varchar(50) DEFAULT NULL,
  `raca` varchar(100) DEFAULT NULL,
  `ocupacao` varchar(100) DEFAULT NULL,
  `afiliacao` varchar(100) DEFAULT NULL,
  `universo_id` int(11) DEFAULT NULL,
  `criado_por` int(11) DEFAULT NULL,
  `comunidade_id` int(11) NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_personagem`),
  KEY `universo_id` (`universo_id`),
  KEY `criado_por` (`criado_por`),
  KEY `fk_comunidade` (`comunidade_id`),
  CONSTRAINT `fk_comunidade` FOREIGN KEY (`comunidade_id`) REFERENCES `comunidades` (`id_comunidade`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `personagens_ibfk_1` FOREIGN KEY (`universo_id`) REFERENCES `comunidades` (`id_comunidade`) ON DELETE CASCADE,
  CONSTRAINT `personagens_ibfk_2` FOREIGN KEY (`criado_por`) REFERENCES `usuarios` (`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `mencoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mencoes` (
  `id_mencao` int(11) NOT NULL AUTO_INCREMENT,
  `artigo_id` int(11) NOT NULL,
  `personagem_mencionado_id` int(11) NOT NULL,
  `trecho` text DEFAULT NULL,
  PRIMARY KEY (`id_mencao`),
  KEY `artigo_id` (`artigo_id`),
  KEY `personagem_mencionado_id` (`personagem_mencionado_id`),
  CONSTRAINT `mencoes_ibfk_1` FOREIGN KEY (`artigo_id`) REFERENCES `artigos` (`id_artigo`) ON DELETE CASCADE,
  CONSTRAINT `mencoes_ibfk_2` FOREIGN KEY (`personagem_mencionado_id`) REFERENCES `personagens` (`id_personagem`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `relacionamentos_personagem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `relacionamentos_personagem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `personagem_origem_id` int(11) NOT NULL,
  `personagem_destino_id` int(11) NOT NULL,
  `tipo_relacao` varchar(100) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `personagem_origem_id` (`personagem_origem_id`),
  KEY `personagem_destino_id` (`personagem_destino_id`),
  CONSTRAINT `relacionamentos_personagem_ibfk_1` FOREIGN KEY (`personagem_origem_id`) REFERENCES `personagens` (`id_personagem`) ON DELETE CASCADE,
  CONSTRAINT `relacionamentos_personagem_ibfk_2` FOREIGN KEY (`personagem_destino_id`) REFERENCES `personagens` (`id_personagem`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

