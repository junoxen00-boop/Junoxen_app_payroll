-- MySQL dump 10.13  Distrib 8.4.11, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: junoxen
-- ------------------------------------------------------
-- Server version	8.4.11

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
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,'Marketing','Digital marketing and growth',1,'2026-09-30 00:52:46','2026-09-30 00:52:46'),(2,'Operations','Business operations',1,'2026-09-30 00:52:46','2026-09-30 00:52:46'),(3,'Human Resources','People and culture',1,'2026-09-30 00:52:46','2026-09-30 00:52:46');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_salary_structures`
--

DROP TABLE IF EXISTS `employee_salary_structures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_salary_structures` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned NOT NULL,
  `basic_salary` decimal(12,2) NOT NULL DEFAULT '0.00',
  `hra` decimal(12,2) NOT NULL DEFAULT '0.00',
  `conveyance_allowance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `medical_allowance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `special_allowance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `other_allowance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `overtime_rate` decimal(12,2) NOT NULL DEFAULT '0.00',
  `pf_deduction` decimal(12,2) NOT NULL DEFAULT '0.00',
  `esi_deduction` decimal(12,2) NOT NULL DEFAULT '0.00',
  `professional_tax` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tds` decimal(12,2) NOT NULL DEFAULT '0.00',
  `loan_deduction` decimal(12,2) NOT NULL DEFAULT '0.00',
  `other_deduction` decimal(12,2) NOT NULL DEFAULT '0.00',
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `status` enum('Active','Inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_salary_structures_created_by_foreign` (`created_by`),
  KEY `employee_salary_structures_updated_by_foreign` (`updated_by`),
  KEY `ess_emp_status_effective_idx` (`employee_id`,`status`,`effective_from`),
  KEY `employee_salary_structures_effective_from_effective_to_index` (`effective_from`,`effective_to`),
  CONSTRAINT `employee_salary_structures_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_salary_structures_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `employee_salary_structures_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_salary_structures`
--

LOCK TABLES `employee_salary_structures` WRITE;
/*!40000 ALTER TABLE `employee_salary_structures` DISABLE KEYS */;
INSERT INTO `employee_salary_structures` VALUES (7,7,500000.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,'2026-10-08','2026-10-31','Active',NULL,1,1,'2026-09-30 23:32:12','2026-09-30 23:32:12');
/*!40000 ALTER TABLE `employee_salary_structures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `employee_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department_id` bigint unsigned NOT NULL,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `joining_date` date NOT NULL,
  `status` enum('Active','Inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_email_unique` (`email`),
  UNIQUE KEY `employees_employee_id_unique` (`employee_id`),
  KEY `employees_department_id_foreign` (`department_id`),
  KEY `employees_user_id_foreign` (`user_id`),
  CONSTRAINT `employees_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (4,6,'EMP-00004','Irri Hema','mehaz@lyfit.com.au','8790728467',2,'Virtual Admin Assistant','2026-04-22','Active','2026-09-30 23:02:49','2026-09-30 23:02:49'),(5,7,'EMP-00005','Durgapathi Sudharani','sudharani@junoxen.com','7893473096',2,'Virtual Admin Assistant','2026-09-15','Active','2026-09-30 23:10:46','2026-09-30 23:10:46'),(6,8,'EMP-00006','Pilla Bhargav Reddy','bhargav@junoxen.com','+91 6303360163',1,'Digital Marketing Excutive','2026-08-03','Active','2026-09-30 23:14:35','2026-09-30 23:14:35'),(7,9,'EMP-00007','Mohammed Ateeq Ur Rahman','ateeq@junoxen.com','+917416917851',2,'System Admin','2026-08-03','Active','2026-09-30 23:27:02','2026-09-30 23:27:02'),(8,10,'EMP-00008','Mohamme Abdul Jaleel','jaleel@junoxen.com','+919502935249',2,'Software Engineer','2026-08-06','Active','2026-09-30 23:28:02','2026-09-30 23:28:02');
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_07_09_060819_create_departments_table',1),(5,'2026_07_09_060836_create_employees_table',1),(6,'2026_07_09_060851_create_tasks_table',1),(7,'2026_07_16_050649_create_roles_table',1),(8,'2026_07_16_051135_add_role_id_to_users_table',1),(9,'2026_07_17_000002_create_task_assignments_table',1),(10,'2026_07_20_011648_add_review_fields_to_task_assignments_table',1),(11,'2026_07_20_013525_add_user_id_to_employees_table',1),(12,'2026_07_20_033719_add_must_change_password_to_users_table',1),(13,'2026_07_22_042107_add_import_fields_to_tasks_table',1),(14,'2026_07_24_005751_create_settings_table',1),(15,'2026_07_29_051619_create_notifications_table',1),(16,'2026_07_31_033454_add_manager_advice_to_task_assignments_table',1),(17,'2026_09_30_000100_create_employee_salary_structures_table',1),(18,'2026_09_30_000200_create_payrolls_table',1),(19,'2026_09_30_000300_create_payroll_payments_table',1),(20,'2026_09_30_000400_create_payroll_audit_logs_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_foreign` (`user_id`),
  CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_audit_logs`
--

DROP TABLE IF EXISTS `payroll_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payroll_audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payroll_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `action` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `payroll_audit_logs_user_id_foreign` (`user_id`),
  KEY `payroll_audit_logs_payroll_id_action_index` (`payroll_id`,`action`),
  CONSTRAINT `payroll_audit_logs_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payroll_audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_audit_logs`
--

LOCK TABLES `payroll_audit_logs` WRITE;
/*!40000 ALTER TABLE `payroll_audit_logs` DISABLE KEYS */;
INSERT INTO `payroll_audit_logs` VALUES (19,7,1,'payroll_created',NULL,'{\"id\": 7, \"hra\": \"0.00\", \"tds\": \"0.00\", \"bonus\": \"0.00\", \"status\": \"Draft\", \"incentive\": \"0.00\", \"created_at\": \"2026-10-01T05:02:35.000000Z\", \"net_salary\": \"500000.00\", \"period_end\": \"2026-10-31T00:00:00.000000Z\", \"updated_at\": \"2026-10-01T05:02:35.000000Z\", \"employee_id\": 7, \"basic_salary\": \"500000.00\", \"generated_at\": \"2026-10-01T05:02:35.000000Z\", \"generated_by\": 1, \"gross_salary\": \"500000.00\", \"payroll_year\": 2026, \"period_start\": \"2026-10-01T00:00:00.000000Z\", \"pf_deduction\": \"0.00\", \"esi_deduction\": \"0.00\", \"payroll_month\": 10, \"reimbursement\": \"0.00\", \"loan_deduction\": \"0.00\", \"other_earnings\": \"0.00\", \"overtime_hours\": \"0.00\", \"payroll_number\": \"JNX-PAY-2026-10-0001\", \"leave_deduction\": \"0.00\", \"other_allowance\": \"0.00\", \"other_deduction\": \"0.00\", \"overtime_amount\": \"0.00\", \"professional_tax\": \"0.00\", \"total_deductions\": \"0.00\", \"medical_allowance\": \"0.00\", \"special_allowance\": \"0.00\", \"conveyance_allowance\": \"0.00\"}','127.0.0.1','2026-09-30 23:32:35'),(20,7,1,'payroll_updated','{\"id\": 7, \"hra\": \"0.00\", \"tds\": \"0.00\", \"bonus\": \"0.00\", \"status\": \"Draft\", \"paid_by\": null, \"incentive\": \"0.00\", \"created_at\": \"2026-10-01T05:02:35.000000Z\", \"net_salary\": \"500000.00\", \"period_end\": \"2026-10-31T00:00:00.000000Z\", \"updated_at\": \"2026-10-01T05:02:35.000000Z\", \"employee_id\": 7, \"basic_salary\": \"500000.00\", \"generated_at\": \"2026-10-01T05:02:35.000000Z\", \"generated_by\": 1, \"gross_salary\": \"500000.00\", \"payment_date\": null, \"payroll_year\": 2026, \"period_start\": \"2026-10-01T00:00:00.000000Z\", \"pf_deduction\": \"0.00\", \"esi_deduction\": \"0.00\", \"payment_notes\": null, \"payroll_month\": 10, \"reimbursement\": \"0.00\", \"loan_deduction\": \"0.00\", \"other_earnings\": \"0.00\", \"overtime_hours\": \"0.00\", \"payment_method\": null, \"payroll_number\": \"JNX-PAY-2026-10-0001\", \"leave_deduction\": \"0.00\", \"other_allowance\": \"0.00\", \"other_deduction\": \"0.00\", \"overtime_amount\": \"0.00\", \"professional_tax\": \"0.00\", \"total_deductions\": \"0.00\", \"medical_allowance\": \"0.00\", \"payment_reference\": null, \"special_allowance\": \"0.00\", \"conveyance_allowance\": \"0.00\"}','{\"id\": 7, \"hra\": \"0.00\", \"tds\": \"0.00\", \"bonus\": \"0.00\", \"status\": \"Paid\", \"paid_by\": null, \"incentive\": \"0.00\", \"created_at\": \"2026-10-01T05:02:35.000000Z\", \"net_salary\": \"500000.00\", \"period_end\": \"2026-10-31T00:00:00.000000Z\", \"updated_at\": \"2026-10-01T05:02:49.000000Z\", \"employee_id\": 7, \"basic_salary\": \"500000.00\", \"generated_at\": \"2026-10-01T05:02:35.000000Z\", \"generated_by\": 1, \"gross_salary\": \"500000.00\", \"payment_date\": null, \"payroll_year\": 2026, \"period_start\": \"2026-10-01T00:00:00.000000Z\", \"pf_deduction\": \"0.00\", \"esi_deduction\": \"0.00\", \"payment_notes\": null, \"payroll_month\": 10, \"reimbursement\": \"0.00\", \"loan_deduction\": \"0.00\", \"other_earnings\": \"0.00\", \"overtime_hours\": \"0.00\", \"payment_method\": null, \"payroll_number\": \"JNX-PAY-2026-10-0001\", \"leave_deduction\": \"0.00\", \"other_allowance\": \"0.00\", \"other_deduction\": \"0.00\", \"overtime_amount\": \"0.00\", \"professional_tax\": \"0.00\", \"total_deductions\": \"0.00\", \"medical_allowance\": \"0.00\", \"payment_reference\": null, \"special_allowance\": \"0.00\", \"conveyance_allowance\": \"0.00\"}','127.0.0.1','2026-09-30 23:32:49');
/*!40000 ALTER TABLE `payroll_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_payments`
--

DROP TABLE IF EXISTS `payroll_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payroll_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payroll_id` bigint unsigned NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_reference` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `recorded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payroll_payments_recorded_by_foreign` (`recorded_by`),
  KEY `payroll_payments_payroll_id_payment_date_index` (`payroll_id`,`payment_date`),
  CONSTRAINT `payroll_payments_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payroll_payments_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_payments`
--

LOCK TABLES `payroll_payments` WRITE;
/*!40000 ALTER TABLE `payroll_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payrolls`
--

DROP TABLE IF EXISTS `payrolls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payrolls` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payroll_number` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `payroll_month` tinyint unsigned NOT NULL,
  `payroll_year` smallint unsigned NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `basic_salary` decimal(12,2) NOT NULL DEFAULT '0.00',
  `hra` decimal(12,2) NOT NULL DEFAULT '0.00',
  `conveyance_allowance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `medical_allowance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `special_allowance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `other_allowance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `overtime_hours` decimal(8,2) NOT NULL DEFAULT '0.00',
  `overtime_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `bonus` decimal(12,2) NOT NULL DEFAULT '0.00',
  `incentive` decimal(12,2) NOT NULL DEFAULT '0.00',
  `reimbursement` decimal(12,2) NOT NULL DEFAULT '0.00',
  `other_earnings` decimal(12,2) NOT NULL DEFAULT '0.00',
  `gross_salary` decimal(12,2) NOT NULL DEFAULT '0.00',
  `pf_deduction` decimal(12,2) NOT NULL DEFAULT '0.00',
  `esi_deduction` decimal(12,2) NOT NULL DEFAULT '0.00',
  `professional_tax` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tds` decimal(12,2) NOT NULL DEFAULT '0.00',
  `loan_deduction` decimal(12,2) NOT NULL DEFAULT '0.00',
  `leave_deduction` decimal(12,2) NOT NULL DEFAULT '0.00',
  `other_deduction` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_deductions` decimal(12,2) NOT NULL DEFAULT '0.00',
  `net_salary` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` enum('Draft','Generated','Paid','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Generated',
  `generated_at` timestamp NULL DEFAULT NULL,
  `generated_by` bigint unsigned DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_reference` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_notes` text COLLATE utf8mb4_unicode_ci,
  `paid_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payroll_employee_period_unique` (`employee_id`,`payroll_month`,`payroll_year`),
  UNIQUE KEY `payrolls_payroll_number_unique` (`payroll_number`),
  KEY `payrolls_generated_by_foreign` (`generated_by`),
  KEY `payrolls_paid_by_foreign` (`paid_by`),
  KEY `payrolls_payroll_year_payroll_month_status_index` (`payroll_year`,`payroll_month`,`status`),
  CONSTRAINT `payrolls_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `payrolls_generated_by_foreign` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payrolls_paid_by_foreign` FOREIGN KEY (`paid_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payrolls`
--

LOCK TABLES `payrolls` WRITE;
/*!40000 ALTER TABLE `payrolls` DISABLE KEYS */;
INSERT INTO `payrolls` VALUES (7,'JNX-PAY-2026-10-0001',7,10,2026,'2026-10-01','2026-10-31',500000.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,500000.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,500000.00,'Paid','2026-09-30 23:32:35',1,NULL,NULL,NULL,NULL,NULL,'2026-09-30 23:32:35','2026-09-30 23:32:49');
/*!40000 ALTER TABLE `payrolls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin','System Administrator','2026-09-30 00:52:46','2026-09-30 00:52:46'),(2,'Manager','Department Manager','2026-09-30 00:52:46','2026-09-30 00:52:46'),(3,'Employee','Regular Employee','2026-09-30 00:52:46','2026-09-30 00:52:46');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('mdDVR4lyDywov2LrOJGKXJKDttuKJoRQjGGw3H6A',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.140.0 Chrome/150.0.7871.250 Electron/43.7.3 Safari/537.36','eyJfdG9rZW4iOiJ1c0V1OXViSU1FZXREb2pYb1dSUmE0Y0s5Y2tDTThuS0pNUEVzcHZBIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9kYXNoYm9hcmQiLCJyb3V0ZSI6ImRhc2hib2FyZCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxfQ==',1790830527),('sHkilb1LUCKs5f3YU4mJJKcFu6IaXtX6H9CNDpXV',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0','eyJfdG9rZW4iOiJTbFdnZ1VxcDRGMWtmM0MxcGpSWERUZFZ5Qjl3TkhXeFR2eFV6OW0wIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2FkbWluXC9wYXlyb2xsXC81In0sIl9wcmV2aW91cyI6eyJ1cmwiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYWRtaW5cL3NhbGFyeS1zdHJ1Y3R1cmVzXC83XC9lZGl0Iiwicm91dGUiOiJhZG1pbi5zYWxhcnktc3RydWN0dXJlcy5lZGl0In0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjF9',1790831341),('uFJKOtVEIoBbjLXDOy8JvCv8cat3hw7Xy7P3n32a',9,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','eyJfdG9rZW4iOiIzQWZUdGN0bG5QZXNnbU84NG8wa1FwdlpUU2VnMTRUSUtWaFhUbVdQIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2VtcGxveWVlXC9wYXlyb2xsXC83Iiwicm91dGUiOiJlbXBsb3llZS5wYXlyb2xsLnNob3cifSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjl9',1790831038);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_address` text COLLATE utf8mb4_unicode_ci,
  `timezone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Asia/Kolkata',
  `date_format` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'd-m-Y',
  `sender_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sender_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reply_to_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_notifications` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'Junoxen PVT LTD',NULL,'admin@junoxen.com',NULL,NULL,NULL,'Asia/Kolkata','d-m-Y','Junoxen PVT LTD','admin@junoxen.com',NULL,1,'2026-09-30 00:52:46','2026-09-30 00:52:46');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_assignments`
--

DROP TABLE IF EXISTS `task_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_assignments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint unsigned NOT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `status` enum('Pending','In Progress','Completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `progress` tinyint unsigned NOT NULL DEFAULT '0',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `review_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `review_comment` text COLLATE utf8mb4_unicode_ci,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `manager_advice` text COLLATE utf8mb4_unicode_ci,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `task_assignments_task_id_employee_id_unique` (`task_id`,`employee_id`),
  KEY `task_assignments_task_id_index` (`task_id`),
  KEY `task_assignments_employee_id_index` (`employee_id`),
  KEY `task_assignments_status_index` (`status`),
  KEY `task_assignments_progress_index` (`progress`),
  KEY `task_assignments_completed_at_index` (`completed_at`),
  KEY `task_assignments_reviewed_by_foreign` (`reviewed_by`),
  CONSTRAINT `task_assignments_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `task_assignments_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `task_assignments_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_assignments`
--

LOCK TABLES `task_assignments` WRITE;
/*!40000 ALTER TABLE `task_assignments` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `task_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `week` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `month` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` enum('Low','Medium','High','Critical') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Medium',
  `start_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tasks_task_code_unique` (`task_code`),
  KEY `tasks_priority_index` (`priority`),
  KEY `tasks_start_date_index` (`start_date`),
  KEY `tasks_due_date_index` (`due_date`),
  KEY `tasks_created_by_index` (`created_by`),
  CONSTRAINT `tasks_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasks`
--

LOCK TABLES `tasks` WRITE;
/*!40000 ALTER TABLE `tasks` DISABLE KEYS */;
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Junoxen Admin','admin@junoxen.com',NULL,'$2y$12$/UKeMGJIC4Fj28ZOFRsHUeCAlE3UjlscN7iAvSkOJuLlJCpU00kqi',0,NULL,'2026-09-30 00:52:47','2026-09-30 01:41:06',1),(2,'Junoxen Manager','manager@junoxen.com',NULL,'$2y$12$7XBja6ARDdBsfqfr4sJR8OTPcqoInC5Fvok1qT02TAskH1HBwG6Xa',1,NULL,'2026-09-30 00:52:47','2026-09-30 00:52:47',2),(3,'Junoxen Employee','employee@junoxen.com',NULL,'$2y$12$vyodRPcVvMhH5hSPWyaZwuctOJwnQfFtogGQjX3TLKv2RL9FtEUmS',1,NULL,'2026-09-30 00:52:47','2026-09-30 00:52:47',3),(6,'Irri Hema','mehaz@lyfit.com.au',NULL,'$2y$12$q3j383Q2evu6zRIuxlLS.OhHA7d0rTa32xMv41c8Ll43BHwBI.hNi',1,NULL,'2026-09-30 23:02:49','2026-09-30 23:02:49',3),(7,'Durgapathi Sudharani','sudharani@junoxen.com',NULL,'$2y$12$FshmIy3yiRTbmpwcIGIUruUPIGt0.bmG/gVLb84fyk/hK7wuD.5uG',1,NULL,'2026-09-30 23:10:46','2026-09-30 23:10:46',3),(8,'Pilla Bhargav Reddy','bhargav@junoxen.com',NULL,'$2y$12$qiWD/ynIppPZ3Tv/LrQcHugefx7R7MMRQpdxVXJMTGr7s3U6sZOL2',1,NULL,'2026-09-30 23:14:35','2026-09-30 23:14:35',3),(9,'Mohammed Ateeq Ur Rahman','ateeq@junoxen.com',NULL,'$2y$12$INp37HQABD4dhSTu9MKQROF7n.IGKUeHMMmz.H2hzyeBGZwJviX8y',0,NULL,'2026-09-30 23:27:02','2026-09-30 23:30:04',3),(10,'Mohamme Abdul Jaleel','jaleel@junoxen.com',NULL,'$2y$12$h9RTUXZ7deRrbTym8HLeV.xsDuDjdcEHg48f9vtF5jDjDWB5CyKmy',1,NULL,'2026-09-30 23:28:02','2026-09-30 23:28:02',3);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'junoxen'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-01 11:09:58
