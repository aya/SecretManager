SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT=0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `secret_manager`
--
USE `secret_manager`;

--
-- Contenu de la table `cvl_civilities`
--

INSERT INTO `cvl_civilities` (`cvl_id`, `cvl_last_name`, `cvl_first_name`, `cvl_sex`, `cvl_birth_date`, `cvl_born_town`) VALUES
(1, 'de l\\''Outil', 'Administrateur', 0, '0000-00-00', '');

--
-- Contenu de la table `ent_entities`
--

INSERT INTO `ent_entities` (`ent_id`, `ent_code`, `ent_label`) VALUES
(1, 'ORA', 'Orasys');

--
-- Contenu de la table `env_environments`
--

INSERT INTO `env_environments` (`env_id`, `env_name`) VALUES
(1, 'L_Environment_1'),
(2, 'L_Environment_2'),
(3, 'L_Environment_3'),
(4, 'L_Environment_4');

--
-- Contenu de la table `idn_identities`
--

INSERT INTO `idn_identities` (`idn_id`, `ent_id`, `cvl_id`, `idn_login`, `idn_authenticator`, `idn_salt`, `idn_change_authenticator`, `idn_super_admin`, `idn_auditor`, `idn_attempt`, `idn_disable`, `idn_last_connection`, `idn_expiration_date`, `idn_updated_authentication`) VALUES
(1, 1, 1, 'root', '658be8288e1156eccbcf7c62a8d731d55fe81e7d', '0.X-n:A/9', 0, 1, 0, 0, 0, '0000-00-00 00:00:00', '2099-01-01 00:00:00', '0000-00-00 00:00:00');


--
-- Contenu de la table `rgh_rights`
--

INSERT INTO `rgh_rights` (`rgh_id`, `rgh_name`) VALUES
(1, 'L_Right_1'),
(2, 'L_Right_2'),
(3, 'L_Right_3'),
(4, 'L_Right_4');


--
-- Contenu de la table `spr_system_parameters`
--

INSERT INTO `spr_system_parameters` (`spr_name`, `spr_value`) VALUES
('alert_mail', '0'),
('alert_syslog', '1'),
('authentication_type', 'D'),
('expiration_time', '10'),
('mail_from', 'secret_manager@society.com'),
('mail_to', 'admin@society.com'),
('verbosity_alert', '2'),
('use_SecretServer', '1'),
('Min_Size_Password', '8'),
('Password_Complexity', '3'),
('Default_User_Lifetime', '6'),
('Max_Attempt', '3'),
('Default_Password', 'welcome'),
('Operator_Key_Size', '8'),
('Operator_Key_Complexity', '2'),
('Mother_Key_Size', '20'),
('Mother_Key_Complexity', '3'),
('Backup_Secrets_Date', ''),
('Backup_Total_Date', '');

--
-- Contenu de la table `stp_secret_types`
--

INSERT INTO `stp_secret_types` (`stp_id`, `stp_name`) VALUES
(1, 'L_Secret_Type_1'),
(2, 'L_Secret_Type_2');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
