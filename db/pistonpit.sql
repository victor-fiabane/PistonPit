-- MySQL dump 10.13  Distrib 8.0.26, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: pistonpit
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bloqueio`
--

DROP TABLE IF EXISTS `bloqueio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bloqueio` (
  `id_bloq` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bloq_data` date NOT NULL,
  `bloq_desc` varchar(150) NOT NULL,
  `fk_id_func` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_bloq`),
  UNIQUE KEY `uq_bloq_data` (`bloq_data`),
  KEY `fk_bloq_func` (`fk_id_func`),
  CONSTRAINT `fk_bloq_func` FOREIGN KEY (`fk_id_func`) REFERENCES `funcionario` (`id_func`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bloqueio`
--

LOCK TABLES `bloqueio` WRITE;
/*!40000 ALTER TABLE `bloqueio` DISABLE KEYS */;
INSERT INTO `bloqueio` VALUES (1,'2026-11-18','Feriado',1),(2,'2026-11-16','Meu Aniversario',1);
/*!40000 ALTER TABLE `bloqueio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cancelamento`
--

DROP TABLE IF EXISTS `cancelamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cancelamento` (
  `id_canc` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `canc_just` varchar(255) NOT NULL,
  `canc_data` datetime NOT NULL DEFAULT current_timestamp(),
  `fk_id_corrida` int(10) unsigned NOT NULL,
  `fk_id_cli` int(10) unsigned DEFAULT NULL,
  `fk_id_func` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_canc`),
  UNIQUE KEY `uq_canc_corrida` (`fk_id_corrida`),
  KEY `fk_canc_cli` (`fk_id_cli`),
  KEY `fk_canc_func` (`fk_id_func`),
  CONSTRAINT `fk_canc_cli` FOREIGN KEY (`fk_id_cli`) REFERENCES `cliente` (`id_cli`),
  CONSTRAINT `fk_canc_corrida` FOREIGN KEY (`fk_id_corrida`) REFERENCES `corrida` (`id_corrida`),
  CONSTRAINT `fk_canc_func` FOREIGN KEY (`fk_id_func`) REFERENCES `funcionario` (`id_func`),
  CONSTRAINT `ck_canc_quem` CHECK (`fk_id_cli` is not null or `fk_id_func` is not null),
  CONSTRAINT `ck_canc_just` CHECK (char_length(trim(`canc_just`)) > 0)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cancelamento`
--

LOCK TABLES `cancelamento` WRITE;
/*!40000 ALTER TABLE `cancelamento` DISABLE KEYS */;
INSERT INTO `cancelamento` VALUES (1,'Teste','2026-11-15 18:21:09',2,NULL,1);
/*!40000 ALTER TABLE `cancelamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cliente`
--

DROP TABLE IF EXISTS `cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cliente` (
  `id_cli` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cli_nome` varchar(100) NOT NULL,
  `cli_cpf` char(11) NOT NULL,
  `cli_email` varchar(120) NOT NULL,
  `cli_telefone` varchar(20) NOT NULL,
  `cli_senha` varchar(255) NOT NULL,
  `cli_ativo` tinyint(1) NOT NULL DEFAULT 1,
  `cli_termo_data` datetime NOT NULL,
  PRIMARY KEY (`id_cli`),
  UNIQUE KEY `uq_cli_cpf` (`cli_cpf`),
  UNIQUE KEY `uq_cli_email` (`cli_email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cliente`
--

LOCK TABLES `cliente` WRITE;
/*!40000 ALTER TABLE `cliente` DISABLE KEYS */;
INSERT INTO `cliente` VALUES (1,'Cliente','952.419.140','CliTeste@pistonpit.com','51 99999999','$2y$10$YnMxvDAoF4xsRv85gCC7Ru7QWYy0UciV7.fKldxU9N2.YfHqCCM06',1,'2026-11-12 23:27:02');
/*!40000 ALTER TABLE `cliente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `corrida`
--

DROP TABLE IF EXISTS `corrida`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `corrida` (
  `id_corrida` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cor_data` date NOT NULL,
  `cor_horario` time NOT NULL,
  `cor_tipo` enum('TREINO','CORRIDA','TOMADA_TEMPO') NOT NULL,
  `cor_visibilidade` enum('PUBLICA','PRIVADA') NOT NULL,
  `cor_senha` varchar(255) DEFAULT NULL,
  `cor_desc` varchar(150) DEFAULT NULL,
  `cor_limite_pilot` tinyint(3) unsigned NOT NULL,
  `cor_status` enum('AGENDADA','CANCELADA','REALIZADA') NOT NULL DEFAULT 'AGENDADA',
  `fk_id_cli` int(10) unsigned DEFAULT NULL,
  `fk_id_func` int(10) unsigned DEFAULT NULL,
  `cor_horario_ativo` varchar(30) GENERATED ALWAYS AS (if(`cor_status` = 'CANCELADA',NULL,concat(`cor_data`,' ',`cor_horario`))) VIRTUAL,
  PRIMARY KEY (`id_corrida`),
  UNIQUE KEY `uq_cor_horario_ativo` (`cor_horario_ativo`),
  KEY `idx_cor_data` (`cor_data`),
  KEY `fk_cor_cli` (`fk_id_cli`),
  KEY `fk_cor_func` (`fk_id_func`),
  CONSTRAINT `fk_cor_cli` FOREIGN KEY (`fk_id_cli`) REFERENCES `cliente` (`id_cli`),
  CONSTRAINT `fk_cor_func` FOREIGN KEY (`fk_id_func`) REFERENCES `funcionario` (`id_func`),
  CONSTRAINT `ck_cor_limite` CHECK (`cor_limite_pilot` > 0),
  CONSTRAINT `ck_cor_quem` CHECK (`fk_id_cli` is not null or `fk_id_func` is not null),
  CONSTRAINT `ck_cor_publica` CHECK (`cor_visibilidade` = 'PRIVADA' or `fk_id_cli` is null),
  CONSTRAINT `ck_cor_senha` CHECK (`cor_visibilidade` = 'PRIVADA' and `cor_senha` is not null or `cor_visibilidade` = 'PUBLICA' and `cor_senha` is null)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `corrida`
--

LOCK TABLES `corrida` WRITE;
/*!40000 ALTER TABLE `corrida` DISABLE KEYS */;
INSERT INTO `corrida` (`id_corrida`, `cor_data`, `cor_horario`, `cor_tipo`, `cor_visibilidade`, `cor_senha`, `cor_desc`, `cor_limite_pilot`, `cor_status`, `fk_id_cli`, `fk_id_func`) VALUES (1,'2026-11-17','10:00:00','CORRIDA','PRIVADA','$2y$10$by7GX.H0.2kS92rFfFtZ0.LsR/HUdofxBiOHNZ3EodxC6D3F2TRxq',NULL,12,'AGENDADA',1,NULL),(2,'2026-11-17','10:30:00','CORRIDA','PUBLICA',NULL,'Bateria Teste',12,'CANCELADA',NULL,1),(3,'2026-11-17','13:30:00','TREINO','PUBLICA',NULL,'Etapa Teste',12,'AGENDADA',NULL,1);
/*!40000 ALTER TABLE `corrida` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `despesa`
--

DROP TABLE IF EXISTS `despesa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `despesa` (
  `id_desp` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `desp_desc` varchar(150) NOT NULL,
  `desp_valor` decimal(10,2) NOT NULL,
  `desp_data` date NOT NULL,
  `fk_id_func` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_desp`),
  KEY `idx_desp_data` (`desp_data`),
  KEY `fk_desp_func` (`fk_id_func`),
  CONSTRAINT `fk_desp_func` FOREIGN KEY (`fk_id_func`) REFERENCES `funcionario` (`id_func`),
  CONSTRAINT `ck_desp_valor` CHECK (`desp_valor` > 0)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `despesa`
--

LOCK TABLES `despesa` WRITE;
/*!40000 ALTER TABLE `despesa` DISABLE KEYS */;
INSERT INTO `despesa` VALUES (1,'Teste 2',100.00,'2026-11-16',1);
/*!40000 ALTER TABLE `despesa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `funcionario`
--

DROP TABLE IF EXISTS `funcionario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `funcionario` (
  `id_func` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `func_nome` varchar(100) NOT NULL,
  `func_email` varchar(120) NOT NULL,
  `func_senha` varchar(255) NOT NULL,
  `func_perfil` enum('ADMIN','FINANCEIRO','MECANICO') NOT NULL,
  `func_ativo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_func`),
  UNIQUE KEY `uq_func_email` (`func_email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `funcionario`
--

LOCK TABLES `funcionario` WRITE;
/*!40000 ALTER TABLE `funcionario` DISABLE KEYS */;
INSERT INTO `funcionario` VALUES
(1,'Administrador','admin@pistonpit.com','$2y$10$Eq93mJzhYSfSopSy4c233exwDgBqC8mCY9AaHTpBSha9YojhkbOCS','ADMIN',1),
(34776,'Funcionário Kart','kartfunc@admin.com','$2y$10$V62fb8.CWZ42jjwYOYjGaOhK1vFVCrCV3iPFHXQlUsM/xuSvymP5q','FINANCEIRO',1);
/*!40000 ALTER TABLE `funcionario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `participa`
--

DROP TABLE IF EXISTS `participa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `participa` (
  `id_part` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_id_cli` int(10) unsigned NOT NULL,
  `fk_id_corrida` int(10) unsigned NOT NULL,
  `part_data` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_part`),
  UNIQUE KEY `uq_part_cli_corrida` (`fk_id_cli`,`fk_id_corrida`),
  KEY `idx_part_corrida` (`fk_id_corrida`),
  CONSTRAINT `fk_part_cli` FOREIGN KEY (`fk_id_cli`) REFERENCES `cliente` (`id_cli`),
  CONSTRAINT `fk_part_corrida` FOREIGN KEY (`fk_id_corrida`) REFERENCES `corrida` (`id_corrida`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `participa`
--

LOCK TABLES `participa` WRITE;
/*!40000 ALTER TABLE `participa` DISABLE KEYS */;
INSERT INTO `participa` VALUES (1,1,1,'2026-11-15 18:07:28'),(2,1,3,'2026-11-15 18:21:39');
/*!40000 ALTER TABLE `participa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `receita`
--

DROP TABLE IF EXISTS `receita`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `receita` (
  `id_rece` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rece_desc` varchar(150) NOT NULL,
  `rece_valor` decimal(10,2) NOT NULL,
  `rece_data` date NOT NULL,
  `fk_id_func` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_rece`),
  KEY `idx_rece_data` (`rece_data`),
  KEY `fk_rece_func` (`fk_id_func`),
  CONSTRAINT `fk_rece_func` FOREIGN KEY (`fk_id_func`) REFERENCES `funcionario` (`id_func`),
  CONSTRAINT `ck_rece_valor` CHECK (`rece_valor` > 0)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `receita`
--

LOCK TABLES `receita` WRITE;
/*!40000 ALTER TABLE `receita` DISABLE KEYS */;
INSERT INTO `receita` VALUES (1,'Teste',120.00,'2026-11-16',1);
/*!40000 ALTER TABLE `receita` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'pistonpit'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-16  2:36:13
