-- MySQL dump 10.13  Distrib 8.0.45, for Linux (x86_64)
--
-- Host: localhost    Database: proyectofinal
-- ------------------------------------------------------
-- Server version	8.0.45-0ubuntu0.24.04.1

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
-- Table structure for table `coleccion_usuario`
--

DROP TABLE IF EXISTS `coleccion_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coleccion_usuario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `edicion_id` int NOT NULL,
  `idioma_id` int DEFAULT NULL,
  `region` varchar(50) DEFAULT NULL,
  `estado` enum('pendiente','jugando','completado') DEFAULT 'pendiente',
  `estado_conservacion` enum('nuevo','como_nuevo','bueno','usado','sin_caja') DEFAULT NULL,
  `valoracion_personal` int DEFAULT NULL,
  `notas` text,
  `fecha_adicion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `edicion_id` (`edicion_id`),
  KEY `idx_coleccion_usuario` (`usuario_id`),
  KEY `fk_coleccion_idioma` (`idioma_id`),
  CONSTRAINT `coleccion_usuario_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `coleccion_usuario_ibfk_2` FOREIGN KEY (`edicion_id`) REFERENCES `ediciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_coleccion_idioma` FOREIGN KEY (`idioma_id`) REFERENCES `idiomas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `coleccion_usuario_chk_1` CHECK ((`valoracion_personal` between 1 and 10))
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coleccion_usuario`
--

LOCK TABLES `coleccion_usuario` WRITE;
/*!40000 ALTER TABLE `coleccion_usuario` DISABLE KEYS */;
INSERT INTO `coleccion_usuario` VALUES (32,2,31,1,'PAL','pendiente',NULL,NULL,NULL,'2026-05-26 13:15:04'),(33,2,32,13,NULL,'pendiente',NULL,NULL,NULL,'2026-05-26 13:15:17'),(34,2,33,13,NULL,'pendiente',NULL,NULL,NULL,'2026-05-26 13:30:25'),(35,2,35,1,'PAL','pendiente',NULL,NULL,NULL,'2026-05-27 13:08:02');
/*!40000 ALTER TABLE `coleccion_usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ediciones`
--

DROP TABLE IF EXISTS `ediciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ediciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `juego_id` int NOT NULL,
  `plataforma_id` int NOT NULL,
  `region` varchar(50) DEFAULT NULL,
  `bloqueo_regional` tinyint(1) NOT NULL DEFAULT '0',
  `anio` int DEFAULT NULL,
  `edicion_nombre` varchar(255) DEFAULT NULL,
  `codigo_barras` varchar(100) DEFAULT NULL,
  `imagen_portada` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_edicion_juego` (`juego_id`),
  KEY `idx_edicion_plataforma` (`plataforma_id`),
  CONSTRAINT `ediciones_ibfk_1` FOREIGN KEY (`juego_id`) REFERENCES `juegos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ediciones_ibfk_2` FOREIGN KEY (`plataforma_id`) REFERENCES `plataformas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ediciones`
--

LOCK TABLES `ediciones` WRITE;
/*!40000 ALTER TABLE `ediciones` DISABLE KEYS */;
INSERT INTO `ediciones` VALUES (31,23,17,'PAL',0,2004,'Edición Estándar',NULL,'Pokémon_Esmeralda_Game_Boy_Advance_PAL_31.jpg'),(32,24,15,NULL,0,NULL,'Edición Estándar',NULL,'Grand_Thef_Auto_V_PlayStation_4_32.jpg'),(33,24,6,NULL,0,NULL,'Edición Estándar',NULL,'Grand_Thef_Auto_V_PlayStation_5_33.jpg'),(34,24,14,NULL,0,NULL,'Edición Estándar',NULL,'Grand_Thef_Auto_V_PlayStation_3_34.jpg'),(35,25,17,NULL,1,NULL,'Edición Estándar',NULL,'Pokémon_Verde_Hoja_Game_Boy_Advance_35.png'),(49,33,15,NULL,0,2020,'Edición Estándar',NULL,NULL),(50,34,15,NULL,0,2015,'Edición Estándar',NULL,NULL),(51,34,21,NULL,0,2015,'Edición Estánddar',NULL,NULL),(52,35,6,NULL,0,2022,'Edición Estándar',NULL,'Elden_Ring_PlayStation_5_52.jpg'),(53,35,21,NULL,0,2022,'Edición Estándar',NULL,'Elden_Ring_Xbox_One_53.jpg'),(54,36,16,NULL,0,2017,'Edición Estándar',NULL,NULL),(55,37,14,NULL,0,2013,'Edición Estándar',NULL,NULL),(56,37,15,NULL,0,2014,'Edición Estándar',NULL,NULL),(57,37,6,NULL,0,2022,'Edición Next-Gen',NULL,NULL),(58,38,15,NULL,0,2019,'Edición Estándar',NULL,NULL),(59,38,14,NULL,0,2016,'Edición Estándar',NULL,NULL),(60,39,15,NULL,0,2018,'Edición Estándar',NULL,NULL),(61,39,21,NULL,0,2018,'Edición Estándar',NULL,NULL),(62,41,15,NULL,0,2015,'Edición Estándar',NULL,'Bloodborne_PlayStation_4_62.jpg'),(63,40,15,NULL,0,2018,'Edición Estándar',NULL,NULL),(64,42,15,NULL,0,2016,'Edición Estándar',NULL,NULL),(65,45,20,NULL,0,2007,'Edición Estándar',NULL,NULL),(66,44,20,NULL,0,2010,'Edición Estándar',NULL,NULL),(67,43,20,NULL,0,2011,'Edición Estándar',NULL,NULL),(68,46,21,NULL,0,2018,'Edición Estándar',NULL,NULL),(69,47,16,NULL,0,2017,'Edición Estándar',NULL,NULL),(70,49,16,NULL,0,2017,'Edición Estándar',NULL,NULL),(71,48,16,NULL,0,2023,'Edición Estándar',NULL,NULL),(72,51,2,NULL,0,2004,'Edición Estándar',NULL,NULL),(73,50,2,NULL,0,2005,'Edición Estándar',NULL,NULL),(75,58,42,NULL,0,1998,'Edición Estándar',NULL,'Crash_Bandicoot_3_Warped_PlayStation_75.webp'),(76,55,42,NULL,0,1997,'Edición Estándar',NULL,'Final_Fantasy_VII_PlayStation_76.jpg'),(77,56,42,NULL,0,1998,'Edición Estándar',NULL,NULL),(78,57,42,NULL,0,1998,'Edición Estándar',NULL,NULL),(82,61,43,NULL,0,1997,'Edición Estándar',NULL,NULL),(83,59,43,NULL,0,1996,'Edición Estándar',NULL,NULL),(84,60,43,NULL,0,1998,'Edición Estándar',NULL,NULL),(85,62,44,NULL,0,1994,'Edición Estándar',NULL,'Donkey_Kong_Country_Super_Nintendo_85.jpg'),(86,63,44,NULL,0,1994,'Edición Estándar',NULL,NULL),(88,64,45,NULL,0,1992,'Edición Estándar',NULL,NULL),(89,65,45,NULL,0,1992,'Edición Estándar',NULL,NULL),(91,66,46,NULL,0,1999,'Edición Estándar',NULL,NULL),(92,67,46,NULL,0,1999,'Edición Estándar',NULL,NULL),(94,68,47,NULL,0,2002,'Edición Estándar',NULL,NULL),(95,69,47,NULL,0,2002,'Edición Estándar',NULL,NULL),(97,70,48,NULL,0,1996,'Edición Estándar',NULL,NULL),(98,71,48,NULL,0,1993,'Edición Estándar',NULL,NULL),(100,73,17,NULL,0,2003,'Edición Estándar',NULL,'Castlevania_Aria_of_Sorrow_Game_Boy_Advance_100.webp'),(101,72,17,NULL,0,2001,'Edición Estándar',NULL,NULL),(103,74,10,NULL,0,2005,'Edición Estándar',NULL,'Mario_Kart_DS_Nintendo_DS_103.webp'),(104,75,10,NULL,0,2006,'Edición Estándar',NULL,NULL),(106,76,49,NULL,0,2008,'Edición Estándar',NULL,NULL),(107,86,48,NULL,0,2000,'Edición Estándar',NULL,NULL);
/*!40000 ALTER TABLE `ediciones` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `limpiar_juegos_huerfanos` AFTER DELETE ON `ediciones` FOR EACH ROW BEGIN
    
    DELETE FROM juegos 
    WHERE id = OLD.juego_id 
    AND NOT EXISTS (SELECT 1 FROM ediciones WHERE juego_id = OLD.juego_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `ediciones_pendientes`
--

DROP TABLE IF EXISTS `ediciones_pendientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ediciones_pendientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `juego_pendiente_id` int DEFAULT NULL,
  `juego_id_real` int DEFAULT NULL,
  `plataforma_id` int DEFAULT NULL,
  `idioma_id` int DEFAULT NULL,
  `region` varchar(50) DEFAULT NULL,
  `bloqueo_regional` tinyint(1) NOT NULL DEFAULT '0',
  `anio` int DEFAULT NULL,
  `edicion_nombre` varchar(255) DEFAULT NULL,
  `imagen_portada_sugerida` varchar(255) DEFAULT NULL,
  `plataforma_nombre_nueva` varchar(100) DEFAULT NULL,
  `fecha_plataforma_sugerida` date DEFAULT NULL,
  `idioma_nombre_nueva` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ediciones_pendientes_juego` (`juego_pendiente_id`),
  KEY `juego_id_real` (`juego_id_real`),
  KEY `fk_ediciones_pendientes_plataforma` (`plataforma_id`),
  KEY `fk_ediciones_pendientes_idioma` (`idioma_id`),
  CONSTRAINT `ediciones_pendientes_ibfk_1` FOREIGN KEY (`juego_pendiente_id`) REFERENCES `juegos_pendientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ediciones_pendientes_ibfk_3` FOREIGN KEY (`juego_id_real`) REFERENCES `juegos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ediciones_pendientes_idioma` FOREIGN KEY (`idioma_id`) REFERENCES `idiomas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_ediciones_pendientes_plataforma` FOREIGN KEY (`plataforma_id`) REFERENCES `plataformas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ediciones_pendientes`
--

LOCK TABLES `ediciones_pendientes` WRITE;
/*!40000 ALTER TABLE `ediciones_pendientes` DISABLE KEYS */;
INSERT INTO `ediciones_pendientes` VALUES (2,1,NULL,5,NULL,'PAL',0,NULL,'Edición Estándar',NULL,NULL,NULL,NULL),(4,2,NULL,5,NULL,'PAL',0,NULL,'Edición Estándar',NULL,NULL,NULL,NULL),(5,3,NULL,5,NULL,'PAL',0,NULL,'Edición Estándar',NULL,NULL,NULL,NULL),(6,4,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,'hey',NULL,NULL),(7,5,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,'lol',NULL,NULL),(8,6,NULL,NULL,NULL,'alaska',0,NULL,NULL,NULL,NULL,NULL,NULL),(9,7,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,'hey',NULL,NULL),(10,8,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,'w',NULL,NULL),(11,9,NULL,5,NULL,'PAL',0,NULL,'Edición Estándar',NULL,NULL,NULL,NULL),(12,10,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,'PlayStation 5',NULL,NULL),(16,14,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,'Nintendo Switch 2',NULL,NULL),(21,19,NULL,12,NULL,'PAL',0,NULL,'Edición Estándar',NULL,NULL,NULL,NULL),(22,20,NULL,9,NULL,NULL,0,NULL,'Edición Estándar',NULL,NULL,NULL,NULL),(24,22,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,'Catalán'),(25,23,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,'Wii',NULL,NULL),(26,24,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,'Wii U',NULL,NULL),(28,26,NULL,17,1,NULL,1,NULL,'Edición Estándar',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `ediciones_pendientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `idiomas`
--

DROP TABLE IF EXISTS `idiomas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `idiomas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_idioma_nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `idiomas`
--

LOCK TABLES `idiomas` WRITE;
/*!40000 ALTER TABLE `idiomas` DISABLE KEYS */;
INSERT INTO `idiomas` VALUES (4,'Alemán'),(1,'Español'),(3,'Francés'),(2,'Inglés'),(5,'Italiano'),(6,'Japonés'),(13,'Multiidioma');
/*!40000 ALTER TABLE `idiomas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `juego_idiomas`
--

DROP TABLE IF EXISTS `juego_idiomas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `juego_idiomas` (
  `juego_id` int NOT NULL,
  `idioma_id` int NOT NULL,
  PRIMARY KEY (`juego_id`,`idioma_id`),
  KEY `idioma_id` (`idioma_id`),
  CONSTRAINT `juego_idiomas_ibfk_1` FOREIGN KEY (`juego_id`) REFERENCES `juegos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `juego_idiomas_ibfk_2` FOREIGN KEY (`idioma_id`) REFERENCES `idiomas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `juego_idiomas`
--

LOCK TABLES `juego_idiomas` WRITE;
/*!40000 ALTER TABLE `juego_idiomas` DISABLE KEYS */;
INSERT INTO `juego_idiomas` VALUES (23,1),(33,1),(34,1),(35,1),(36,1),(37,1),(38,1),(39,1),(33,2),(34,2),(35,2),(36,2),(37,2),(38,2),(39,2),(33,3),(34,3),(35,3),(36,3),(37,3),(39,3),(33,4),(34,4),(35,4),(36,4),(37,4),(39,4),(33,5),(34,5),(35,5),(36,5),(37,5),(39,5),(34,6),(35,6),(36,6),(38,6),(24,13);
/*!40000 ALTER TABLE `juego_idiomas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `juegos`
--

DROP TABLE IF EXISTS `juegos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `juegos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text,
  `fecha_lanzamiento` date DEFAULT NULL,
  `desarrollador` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_juego_titulo` (`titulo`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `juegos`
--

LOCK TABLES `juegos` WRITE;
/*!40000 ALTER TABLE `juegos` DISABLE KEYS */;
INSERT INTO `juegos` VALUES (23,'Pokémon Esmeralda',NULL,'2004-09-16','Game Freak','2026-05-25 12:59:41'),(24,'Grand Thef Auto V',NULL,'2013-09-13','Rockstar Games','2026-05-25 13:05:52'),(25,'Pokémon Verde Hoja',NULL,'2004-01-29','Game Freak','2026-05-27 13:06:57'),(33,'The Last of Us Part II','Acción y aventura postapocalíptica.','2020-06-19','Naughty Dog','2026-05-28 13:04:35'),(34,'The Witcher 3: Wild Hunt','RPG de mundo abierto.','2015-05-19','CD Projekt RED','2026-05-28 13:04:35'),(35,'Elden Ring','RPG de acción de fantasía oscura.','2022-02-25','FromSoftware','2026-05-28 13:04:35'),(36,'The Legend of Zelda: Breath of the Wild','Aventura de mundo abierto.','2017-03-03','Nintendo','2026-05-28 13:04:35'),(37,'Grand Theft Auto V','Acción de mundo abierto contemporáneo.','2013-09-17','Rockstar North','2026-05-28 13:04:35'),(38,'Persona 5 Royal','JRPG por turnos.','2019-10-31','Atlus','2026-05-28 13:04:35'),(39,'Red Dead Redemption 2','Aventura de mundo abierto en el oeste.','2018-10-26','Rockstar Games','2026-05-28 13:04:35'),(40,'God of War','Acción y aventura mitológica.','2018-04-20','Santa Monica Studio','2026-05-28 13:12:48'),(41,'Bloodborne','Acción RPG gótico.','2015-03-24','FromSoftware','2026-05-28 13:12:48'),(42,'Uncharted 4: A Thief\'s End','Aventura de acción.','2016-05-10','Naughty Dog','2026-05-28 13:12:48'),(43,'The Elder Scrolls V: Skyrim','RPG de mundo abierto.','2011-11-11','Bethesda Game Studios','2026-05-28 13:12:48'),(44,'Red Dead Redemption','Acción en mundo abierto del oeste.','2010-05-18','Rockstar San Diego','2026-05-28 13:12:48'),(45,'Halo 3','Shooter en primera persona.','2007-09-25','Bungie','2026-05-28 13:12:48'),(46,'Forza Horizon 4','Conducción en mundo abierto.','2018-10-02','Playground Games','2026-05-28 13:12:48'),(47,'Mario Kart 8 Deluxe','Carreras arcade.','2017-04-28','Nintendo','2026-05-28 13:12:48'),(48,'The Legend of Zelda: Tears of the Kingdom','Aventura de mundo abierto.','2023-05-12','Nintendo','2026-05-28 13:12:48'),(49,'Super Mario Odyssey','Plataformas 3D.','2017-10-27','Nintendo','2026-05-28 13:12:48'),(50,'Resident Evil 4','Survival horror de acción.','2005-01-11','Capcom','2026-05-28 13:12:48'),(51,'Metal Gear Solid 3: Snake Eater','Sigilo y acción.','2004-11-17','Konami','2026-05-28 13:12:48'),(55,'Final Fantasy VII','JRPG clásico de PS1.','1997-01-31','Square','2026-05-28 13:14:02'),(56,'Metal Gear Solid','Acción y sigilo en PS1.','1998-09-03','Konami','2026-05-28 13:14:02'),(57,'Resident Evil 2','Survival horror clásico.','1998-01-21','Capcom','2026-05-28 13:14:02'),(58,'Crash Bandicoot 3: Warped','Plataformas clásico de PS1.','1998-10-31','Naughty Dog','2026-05-28 13:14:02'),(59,'Super Mario 64','Plataformas 3D pionero.','1996-06-23','Nintendo','2026-05-28 13:14:02'),(60,'The Legend of Zelda: Ocarina of Time','Aventura clásica N64.','1998-11-21','Nintendo','2026-05-28 13:14:02'),(61,'GoldenEye 007','Shooter clásico de Nintendo 64.','1997-08-25','Rare','2026-05-28 13:14:02'),(62,'Donkey Kong Country','Plataformas de SNES.','1994-11-21','Rare','2026-05-28 13:14:02'),(63,'Super Metroid','Acción-aventura en SNES.','1994-03-19','Nintendo','2026-05-28 13:14:02'),(64,'Sonic the Hedgehog 2','Plataformas clásico de Mega Drive.','1992-11-21','Sega','2026-05-28 13:14:02'),(65,'Streets of Rage 2','Beat\'em up clásico.','1992-12-20','Sega','2026-05-28 13:14:02'),(66,'Shenmue','Aventura de culto en Dreamcast.','1999-12-29','Sega AM2','2026-05-28 13:14:02'),(67,'Soulcalibur','Lucha 3D en Dreamcast.','1999-08-03','Namco','2026-05-28 13:14:02'),(68,'Metroid Prime','Acción-aventura en GameCube.','2002-11-18','Retro Studios','2026-05-28 13:14:02'),(69,'The Legend of Zelda: The Wind Waker','Aventura en GameCube.','2002-12-13','Nintendo','2026-05-28 13:14:02'),(70,'Pokémon Rojo','RPG portátil clásico.','1996-02-27','Game Freak','2026-05-28 13:14:02'),(71,'The Legend of Zelda: Link\'s Awakening','Aventura clásica portátil.','1993-06-06','Nintendo','2026-05-28 13:14:02'),(72,'Golden Sun','JRPG en Game Boy Advance.','2001-08-01','Camelot','2026-05-28 13:14:02'),(73,'Castlevania: Aria of Sorrow','Acción-aventura en GBA.','2003-05-08','Konami','2026-05-28 13:14:02'),(74,'Mario Kart DS','Carreras portátil.','2005-11-14','Nintendo','2026-05-28 13:14:02'),(75,'New Super Mario Bros.','Plataformas en Nintendo DS.','2006-05-15','Nintendo','2026-05-28 13:14:02'),(76,'God of War: Chains of Olympus','Acción en PSP.','2008-03-04','Ready at Dawn','2026-05-28 13:14:02'),(86,'Pokémon Amarillo','Edición especial inspirada en la serie, para Game Boy.','2000-06-16','Game Freak','2026-05-28 13:28:51');
/*!40000 ALTER TABLE `juegos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `juegos_pendientes`
--

DROP TABLE IF EXISTS `juegos_pendientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `juegos_pendientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text,
  `fecha_lanzamiento` date DEFAULT NULL,
  `desarrollador` varchar(255) DEFAULT NULL,
  `estado` enum('pendiente','aprobado','rechazado') DEFAULT 'pendiente',
  `revisado_por` int DEFAULT NULL,
  `fecha_revision` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_juegos_pendientes_estado` (`estado`),
  KEY `fk_jp_usuario` (`usuario_id`),
  KEY `fk_jp_revisado` (`revisado_por`),
  CONSTRAINT `fk_jp_revisado` FOREIGN KEY (`revisado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_jp_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `juegos_pendientes`
--

LOCK TABLES `juegos_pendientes` WRITE;
/*!40000 ALTER TABLE `juegos_pendientes` DISABLE KEYS */;
INSERT INTO `juegos_pendientes` VALUES (1,2,'hola',NULL,NULL,'','rechazado',1,'2026-03-22 11:27:54','2026-03-21 21:03:07'),(2,2,'adios',NULL,NULL,'','rechazado',1,'2026-03-22 11:27:52','2026-03-22 10:33:43'),(3,2,'pep',NULL,NULL,'','rechazado',1,'2026-03-22 11:27:51','2026-03-22 10:35:09'),(4,2,'SISTEMA: hey',NULL,NULL,NULL,'rechazado',1,'2026-03-22 11:27:49','2026-03-22 10:39:53'),(5,2,'SISTEMA: lol',NULL,NULL,NULL,'rechazado',1,'2026-03-22 11:27:47','2026-03-22 10:51:46'),(6,2,'REGIÓN: alaska',NULL,NULL,NULL,'rechazado',1,'2026-03-22 11:27:46','2026-03-22 10:54:27'),(7,2,'SISTEMA: hey',NULL,NULL,NULL,'rechazado',1,'2026-03-22 11:27:44','2026-03-22 10:56:46'),(8,2,'Plataforma: w',NULL,NULL,NULL,'rechazado',1,'2026-03-22 11:27:43','2026-03-22 11:08:35'),(9,2,'Juego: dede',NULL,NULL,'','rechazado',1,'2026-03-22 11:27:41','2026-03-22 11:08:44'),(10,2,'Plataforma: PlayStation 5',NULL,NULL,NULL,'aprobado',1,'2026-03-22 11:31:20','2026-03-22 11:30:57'),(11,2,'Elden Ring',NULL,NULL,NULL,'rechazado',1,'2026-03-22 11:33:56','2026-03-22 11:31:52'),(12,2,'Grand Theft Auto: San Andreas',NULL,NULL,NULL,'aprobado',1,'2026-03-22 11:37:22','2026-03-22 11:34:33'),(13,2,'Cyberpunk 2077',NULL,NULL,NULL,'aprobado',1,'2026-03-22 11:53:35','2026-03-22 11:52:10'),(14,2,'Plataforma: Nintendo Switch 2',NULL,NULL,NULL,'aprobado',1,'2026-03-22 13:05:13','2026-03-22 13:04:55'),(15,2,'The Last of Us Part II',NULL,NULL,NULL,'aprobado',1,'2026-03-22 17:09:35','2026-03-22 17:07:43'),(16,2,'Metroid Prime',NULL,NULL,NULL,'rechazado',1,'2026-03-25 11:57:23','2026-03-23 08:47:48'),(17,2,'Resident Evil 4',NULL,NULL,NULL,'aprobado',1,'2026-03-25 11:57:21','2026-03-23 09:20:22'),(18,5,'Grand Theft Auto: San Andreas',NULL,NULL,NULL,'aprobado',1,'2026-03-25 19:05:32','2026-03-25 19:04:00'),(19,2,'Juego: Zelda',NULL,NULL,'','aprobado',1,'2026-03-26 10:28:53','2026-03-26 10:28:15'),(20,2,'Juego: Mario 1',NULL,NULL,'','rechazado',1,'2026-05-24 19:52:10','2026-05-24 17:42:57'),(21,2,'Elden Ring',NULL,NULL,NULL,'aprobado',1,'2026-05-24 18:46:17','2026-05-24 18:45:42'),(22,2,'Idioma: Catalán',NULL,NULL,NULL,'aprobado',1,'2026-05-24 19:08:59','2026-05-24 19:08:37'),(23,2,'Plataforma: Wii',NULL,NULL,NULL,'aprobado',1,'2026-05-24 20:14:08','2026-05-24 20:13:34'),(24,2,'Plataforma: Wii U',NULL,NULL,NULL,'aprobado',1,'2026-05-24 20:14:06','2026-05-24 20:13:43'),(25,2,'New Super Mario Bros U',NULL,NULL,NULL,'aprobado',1,'2026-05-24 20:16:00','2026-05-24 20:15:21'),(26,2,'Juego: Pokémon Esmeralda',NULL,'2004-09-16','Game Freak','aprobado',1,'2026-05-25 12:59:41','2026-05-25 12:51:13');
/*!40000 ALTER TABLE `juegos_pendientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `juegos_pendientes_idiomas`
--

DROP TABLE IF EXISTS `juegos_pendientes_idiomas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `juegos_pendientes_idiomas` (
  `juego_pendiente_id` int NOT NULL,
  `idioma_id` int NOT NULL,
  PRIMARY KEY (`juego_pendiente_id`,`idioma_id`),
  KEY `idioma_id` (`idioma_id`),
  CONSTRAINT `juegos_pendientes_idiomas_ibfk_1` FOREIGN KEY (`juego_pendiente_id`) REFERENCES `juegos_pendientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `juegos_pendientes_idiomas_ibfk_2` FOREIGN KEY (`idioma_id`) REFERENCES `idiomas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `juegos_pendientes_idiomas`
--

LOCK TABLES `juegos_pendientes_idiomas` WRITE;
/*!40000 ALTER TABLE `juegos_pendientes_idiomas` DISABLE KEYS */;
/*!40000 ALTER TABLE `juegos_pendientes_idiomas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plataformas`
--

DROP TABLE IF EXISTS `plataformas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plataformas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `fecha_lanzamiento` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plataformas`
--

LOCK TABLES `plataformas` WRITE;
/*!40000 ALTER TABLE `plataformas` DISABLE KEYS */;
INSERT INTO `plataformas` VALUES (2,'PlayStation 2','2000-03-04'),(5,'GameCube','2001-09-14'),(6,'PlayStation 5','2020-11-12'),(8,'PC',NULL),(9,'Game Boy Color','1998-10-21'),(10,'Nintendo DS','2004-11-21'),(11,'Nintendo Switch 2','2025-06-05'),(12,'Nintendo 3ds','2011-02-26'),(14,'PlayStation 3','2006-11-11'),(15,'PlayStation 4','2013-11-15'),(16,'Nintendo Switch','2017-03-03'),(17,'Game Boy Advance','2001-03-21'),(18,'Wii U','2012-11-18'),(19,'Wii','2006-11-19'),(20,'Xbox 360','2005-11-22'),(21,'Xbox One','2013-11-22'),(42,'PlayStation','1994-12-03'),(43,'Nintendo 64','1996-06-23'),(44,'Super Nintendo','1990-11-21'),(45,'Mega Drive','1988-10-29'),(46,'Dreamcast','1998-11-27'),(47,'Nintendo GameCube','2001-09-14'),(48,'Game Boy','1989-04-21'),(49,'PSP','2004-12-12');
/*!40000 ALTER TABLE `plataformas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prestamos`
--

DROP TABLE IF EXISTS `prestamos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `prestamos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `coleccion_id` int NOT NULL,
  `nombre_persona` varchar(255) NOT NULL,
  `fecha_prestamo` date NOT NULL,
  `fecha_devolucion` date DEFAULT NULL,
  `devuelto` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_prestamos_coleccion` (`coleccion_id`),
  CONSTRAINT `prestamos_ibfk_1` FOREIGN KEY (`coleccion_id`) REFERENCES `coleccion_usuario` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prestamos`
--

LOCK TABLES `prestamos` WRITE;
/*!40000 ALTER TABLE `prestamos` DISABLE KEYS */;
/*!40000 ALTER TABLE `prestamos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `regiones`
--

DROP TABLE IF EXISTS `regiones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `regiones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_region_nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `regiones`
--

LOCK TABLES `regiones` WRITE;
/*!40000 ALTER TABLE `regiones` DISABLE KEYS */;
INSERT INTO `regiones` VALUES (3,'NTSC-J'),(2,'NTSC-U'),(1,'PAL');
/*!40000 ALTER TABLE `regiones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('usuario','admin','super_admin') NOT NULL DEFAULT 'usuario',
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_email` (`email`),
  KEY `idx_usuario_rol` (`rol`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Ana Sánchez Suárez','ssanitax@gmail.com','$2y$10$/g66Um3lpkqeji9yFaxbtue1JdiptOmE2ss/o9dBJ1PIGvib4KVbO','super_admin',1,'2026-02-26 21:50:23','2026-05-24 19:24:38'),(2,'Maria Sánchez','mariasanchez@gmail.com','$2y$10$w/VJ6d8/jFqU3UBVNBhE6eqo0p9jCV7M156wekD.maiJ7xPLZBQp.','usuario',1,'2026-03-21 19:46:03','2026-03-21 19:46:03'),(5,'Fatima','fatima@gmail.es','$2y$10$AahBNM1bCo39DVHh7KLrA.Ht95p6xYvfxFa00Cd8IGIcBQoXZzNg6','usuario',1,'2026-03-25 19:02:54','2026-03-25 19:02:54');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `valoraciones`
--

DROP TABLE IF EXISTS `valoraciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `valoraciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `juego_id` int NOT NULL,
  `puntuacion` int NOT NULL,
  `comentario` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_usuario_juego` (`usuario_id`,`juego_id`),
  KEY `idx_valoraciones_juego` (`juego_id`),
  CONSTRAINT `valoraciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `valoraciones_ibfk_2` FOREIGN KEY (`juego_id`) REFERENCES `juegos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `valoraciones_chk_1` CHECK ((`puntuacion` between 1 and 10))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `valoraciones`
--

LOCK TABLES `valoraciones` WRITE;
/*!40000 ALTER TABLE `valoraciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `valoraciones` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-29  6:12:51
