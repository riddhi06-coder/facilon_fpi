-- CAF Data Model — schema + master data (NO CREATE DATABASE / USE)
-- Select your target schema in Workbench first (double-click it under SCHEMAS), then Execute.

-- Run this in MySQL Workbench on an EMPTY database named facilon_fpi_vapt.
SET FOREIGN_KEY_CHECKS=0;

-- ============ STRUCTURE (41 tables) ============
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `m_countries` (
  `country_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `iso2` char(2) NOT NULL,
  `iso3` char(3) NOT NULL,
  `isd_code` varchar(6) NOT NULL,
  `label_en` varchar(100) NOT NULL,
  `label_hi` varchar(100) DEFAULT NULL,
  `is_fatf_member` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` smallint(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`country_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='MASTER. Single source of truth for country + ISD calling code + ISO + FATF flag.';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `m_titles` (
  `code` varchar(40) NOT NULL,
  `label_en` varchar(100) NOT NULL,
  `label_hi` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` smallint(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='MASTER. Title prefix Shri/Smt/Ms/M/s (fields 1,3,28).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `m_genders` (
  `code` varchar(40) NOT NULL,
  `label_en` varchar(100) NOT NULL,
  `label_hi` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` smallint(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='MASTER. Gender (field 32).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `m_marital_statuses` (
  `code` varchar(40) NOT NULL,
  `label_en` varchar(100) NOT NULL,
  `label_hi` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` smallint(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='MASTER. Marital status (field 33).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `m_citizenship_statuses` (
  `code` varchar(40) NOT NULL,
  `label_en` varchar(100) NOT NULL,
  `label_hi` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` smallint(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='MASTER. Citizenship status (field 34).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `m_income_sources` (
  `code` varchar(40) NOT NULL,
  `label_en` varchar(100) NOT NULL,
  `label_hi` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` smallint(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='MASTER. Income source values (field 9).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `m_relationship_types` (
  `code` varchar(40) NOT NULL,
  `label_en` varchar(100) NOT NULL,
  `label_hi` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` smallint(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='MASTER. Clubbing relationship type (field 22).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `m_operation_modes` (
  `code` varchar(40) NOT NULL,
  `label_en` varchar(100) NOT NULL,
  `label_hi` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` smallint(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='MASTER. Depository operation mode (field 37b).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `m_application_statuses` (
  `code` varchar(40) NOT NULL,
  `label_en` varchar(100) NOT NULL,
  `label_hi` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` smallint(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='MASTER. Workflow lifecycle values.';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `m_card_name_prefs` (
  `code` varchar(40) NOT NULL,
  `label_en` varchar(100) NOT NULL,
  `label_hi` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` smallint(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='MASTER. Father/Mother name-on-card preference (field 35).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `m_risk_categories` (
  `code` varchar(40) NOT NULL,
  `label_en` varchar(100) NOT NULL,
  `label_hi` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` smallint(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='MASTER. KYC risk category (office use).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `m_business_profession_codes` (
  `code` char(2) NOT NULL,
  `label_en` varchar(150) NOT NULL,
  `label_hi` varchar(150) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='MASTER. Business/Profession codes 01-20 (field 9a).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `m_occupation_codes` (
  `occupation_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `applies_to` enum('Individual','Non-Individual') NOT NULL,
  `code` varchar(10) NOT NULL,
  `label_en` varchar(150) NOT NULL,
  `label_hi` varchar(150) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`occupation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='MASTER. Occupation list, split Individual vs Non-Individual (field 10).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `m_fpi_categories` (
  `category_code` varchar(20) NOT NULL,
  `label_en` varchar(100) NOT NULL,
  `label_hi` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`category_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='MASTER. FPI Category I / II (field 13).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `m_fpi_sub_categories` (
  `sub_category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_code` varchar(20) NOT NULL,
  `clause_ref` varchar(30) NOT NULL,
  `label_en` varchar(255) NOT NULL,
  `label_hi` varchar(255) DEFAULT NULL,
  `ubo_exempt` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` smallint(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`sub_category_id`),
  KEY `idx_fpi_sub_category` (`category_code`),
  CONSTRAINT `fk_fpi_sub_category` FOREIGN KEY (`category_code`) REFERENCES `m_fpi_categories` (`category_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='MASTER. SEBI sub-category clauses + UBO-exemption flag (field 13).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `m_document_types` (
  `doc_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `applies_to` enum('Individual','Non-Individual','Both') NOT NULL,
  `purpose` enum('POI','POA','POI_AND_POA') NOT NULL,
  `code` varchar(10) NOT NULL,
  `label_en` varchar(200) NOT NULL,
  `label_hi` varchar(200) DEFAULT NULL,
  `requires_expiry` tinyint(1) NOT NULL DEFAULT 0,
  `requires_number` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`doc_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='MASTER. POI/POA document types per applicant type (fields 11,31).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `m_applicant_status` (
  `status_code` varchar(30) NOT NULL,
  `label_en` varchar(100) NOT NULL,
  `label_hi` varchar(100) DEFAULT NULL,
  `pan_status` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`status_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='MASTER. PAN ''Status of Applicant'' list (field 25).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `m_ao_codes` (
  `ao_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `area_code` varchar(3) NOT NULL,
  `ao_type` varchar(2) NOT NULL,
  `range_code` varchar(3) NOT NULL,
  `ao_number` varchar(3) NOT NULL,
  `description` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`ao_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='MASTER. Assessing Officer code list (field 26) - load externally.';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `applicants` (
  `applicant_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entity_type` enum('Individual','Non-Individual') NOT NULL,
  `pan_card_name_abbrev` varchar(100) NOT NULL,
  `fpi_category_code` varchar(20) DEFAULT NULL,
  `fpi_sub_category_id` int(10) unsigned DEFAULT NULL,
  `is_mim_structure` tinyint(1) NOT NULL DEFAULT 0,
  `gross_annual_income_inr` decimal(18,2) DEFAULT NULL,
  `gross_annual_income_band` varchar(20) DEFAULT NULL,
  `net_worth_inr` decimal(18,2) DEFAULT NULL,
  `net_worth_date` date DEFAULT NULL,
  `has_fatca_crs_declaration` tinyint(1) NOT NULL DEFAULT 0,
  `has_disciplinary_history` tinyint(1) NOT NULL DEFAULT 0,
  `disciplinary_history_details` text DEFAULT NULL,
  `has_investment_limit_clubbing` tinyint(1) NOT NULL DEFAULT 0,
  `has_prior_indian_market_assoc` tinyint(1) NOT NULL DEFAULT 0,
  `is_regulated_fpi` tinyint(1) NOT NULL DEFAULT 0,
  `is_using_global_custodian` tinyint(1) NOT NULL DEFAULT 0,
  `ubo_structure_json` longtext DEFAULT NULL,
  `application_status` varchar(40) NOT NULL DEFAULT 'DRAFT',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` varchar(100) DEFAULT NULL,
  `updated_by` varchar(100) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`applicant_id`),
  KEY `idx_applicants_fpi_category` (`fpi_category_code`),
  KEY `idx_applicants_fpi_sub_category` (`fpi_sub_category_id`),
  KEY `idx_applicants_application_status` (`application_status`),
  CONSTRAINT `fk_applicants_application_status` FOREIGN KEY (`application_status`) REFERENCES `m_application_statuses` (`code`),
  CONSTRAINT `fk_applicants_fpi_category` FOREIGN KEY (`fpi_category_code`) REFERENCES `m_fpi_categories` (`category_code`),
  CONSTRAINT `fk_applicants_fpi_sub_category` FOREIGN KEY (`fpi_sub_category_id`) REFERENCES `m_fpi_sub_categories` (`sub_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='CORE. Root polymorphic hub. Shared onboarding, regulatory tier, workflow state for Individuals and entities.';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `individual_applicant_details` (
  `applicant_id` bigint(20) unsigned NOT NULL,
  `title_code` varchar(40) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `place_of_birth` varchar(100) NOT NULL,
  `birth_country_id` smallint(5) unsigned NOT NULL,
  `gender_code` varchar(40) DEFAULT NULL,
  `marital_status_code` varchar(40) DEFAULT NULL,
  `citizenship_status_code` varchar(40) DEFAULT NULL,
  `citizenship_country_id` smallint(5) unsigned DEFAULT NULL,
  `passport_number` varchar(50) DEFAULT NULL,
  `business_profession_code` char(2) DEFAULT NULL,
  `occupation_id` int(10) unsigned DEFAULT NULL,
  `is_pep` tinyint(1) NOT NULL DEFAULT 0,
  `is_pep_related` tinyint(1) NOT NULL DEFAULT 0,
  `mother_single_parent` tinyint(1) NOT NULL DEFAULT 0,
  `father_first_name` varchar(100) DEFAULT NULL,
  `father_middle_name` varchar(100) DEFAULT NULL,
  `father_last_name` varchar(100) DEFAULT NULL,
  `mother_first_name` varchar(100) DEFAULT NULL,
  `mother_middle_name` varchar(100) DEFAULT NULL,
  `mother_last_name` varchar(100) DEFAULT NULL,
  `card_name_print_pref` varchar(40) DEFAULT NULL,
  `spouse_name` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`applicant_id`),
  KEY `idx_iad_title` (`title_code`),
  KEY `idx_iad_birth_country` (`birth_country_id`),
  KEY `idx_iad_gender` (`gender_code`),
  KEY `idx_iad_marital` (`marital_status_code`),
  KEY `idx_iad_citizenship` (`citizenship_status_code`),
  KEY `idx_iad_citizenship_country` (`citizenship_country_id`),
  KEY `idx_iad_business_prof` (`business_profession_code`),
  KEY `idx_iad_occupation` (`occupation_id`),
  KEY `idx_iad_card_name_pref` (`card_name_print_pref`),
  CONSTRAINT `fk_iad_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_iad_birth_country` FOREIGN KEY (`birth_country_id`) REFERENCES `m_countries` (`country_id`),
  CONSTRAINT `fk_iad_business_prof` FOREIGN KEY (`business_profession_code`) REFERENCES `m_business_profession_codes` (`code`),
  CONSTRAINT `fk_iad_card_name_pref` FOREIGN KEY (`card_name_print_pref`) REFERENCES `m_card_name_prefs` (`code`),
  CONSTRAINT `fk_iad_citizenship` FOREIGN KEY (`citizenship_status_code`) REFERENCES `m_citizenship_statuses` (`code`),
  CONSTRAINT `fk_iad_citizenship_country` FOREIGN KEY (`citizenship_country_id`) REFERENCES `m_countries` (`country_id`),
  CONSTRAINT `fk_iad_gender` FOREIGN KEY (`gender_code`) REFERENCES `m_genders` (`code`),
  CONSTRAINT `fk_iad_marital` FOREIGN KEY (`marital_status_code`) REFERENCES `m_marital_statuses` (`code`),
  CONSTRAINT `fk_iad_occupation` FOREIGN KEY (`occupation_id`) REFERENCES `m_occupation_codes` (`occupation_id`),
  CONSTRAINT `fk_iad_title` FOREIGN KEY (`title_code`) REFERENCES `m_titles` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='CORE. 1:1 extension. Human-only demographic data.';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `corporate_applicant_details` (
  `applicant_id` bigint(20) unsigned NOT NULL,
  `name_title_code` varchar(40) DEFAULT NULL,
  `company_name` varchar(200) NOT NULL,
  `applicant_legal_type` varchar(60) DEFAULT NULL,
  `date_of_incorporation` date NOT NULL,
  `date_commence_business` date DEFAULT NULL,
  `place_of_incorporation` varchar(100) NOT NULL,
  `incorporation_country_id` smallint(5) unsigned NOT NULL,
  `lei_number` varchar(20) DEFAULT NULL,
  `lei_expiry_date` date DEFAULT NULL,
  `business_profession_code` char(2) DEFAULT NULL,
  `foreign_registration_no` varchar(50) DEFAULT NULL,
  `is_public_company_listed` tinyint(1) NOT NULL DEFAULT 0,
  `stock_exchange_name` varchar(100) DEFAULT NULL,
  `occupation_id` int(10) unsigned DEFAULT NULL,
  `provides_forex_money_changing` tinyint(1) NOT NULL DEFAULT 0,
  `provides_gaming_gambling_services` tinyint(1) NOT NULL DEFAULT 0,
  `provides_money_lending_pawning` tinyint(1) NOT NULL DEFAULT 0,
  `im_entity_type` enum('Investing','Non-Investing') DEFAULT NULL,
  `india_place_of_business` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`applicant_id`),
  KEY `idx_cad_incorporation_country` (`incorporation_country_id`),
  KEY `idx_cad_business_prof` (`business_profession_code`),
  KEY `idx_cad_occupation` (`occupation_id`),
  KEY `corporate_applicant_details_name_title_code_foreign` (`name_title_code`),
  CONSTRAINT `corporate_applicant_details_name_title_code_foreign` FOREIGN KEY (`name_title_code`) REFERENCES `m_titles` (`code`),
  CONSTRAINT `fk_cad_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cad_business_prof` FOREIGN KEY (`business_profession_code`) REFERENCES `m_business_profession_codes` (`code`),
  CONSTRAINT `fk_cad_incorporation_country` FOREIGN KEY (`incorporation_country_id`) REFERENCES `m_countries` (`country_id`),
  CONSTRAINT `fk_cad_occupation` FOREIGN KEY (`occupation_id`) REFERENCES `m_occupation_codes` (`occupation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='CORE. 1:1 extension. Entity-only legal identity.';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `applicant_aliases` (
  `alias_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `applicant_id` bigint(20) unsigned NOT NULL,
  `alias_title_code` varchar(40) DEFAULT NULL,
  `alias_last_name_or_company` varchar(200) NOT NULL,
  `alias_first_name` varchar(100) DEFAULT NULL,
  `alias_middle_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`alias_id`),
  KEY `idx_alias_applicant` (`applicant_id`),
  KEY `idx_alias_title` (`alias_title_code`),
  CONSTRAINT `fk_alias_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_alias_title` FOREIGN KEY (`alias_title_code`) REFERENCES `m_titles` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='CORE. 1:N. Other/former names (field 3) for AML screening.';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `applicant_addresses` (
  `address_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `applicant_id` bigint(20) unsigned NOT NULL,
  `address_type` enum('Registered_Residence','Office') NOT NULL,
  `is_communication_dest` tinyint(1) NOT NULL DEFAULT 0,
  `office_name` varchar(150) DEFAULT NULL,
  `flat_room_block` varchar(50) DEFAULT NULL,
  `premises_building` varchar(100) DEFAULT NULL,
  `road_street_lane` varchar(100) DEFAULT NULL,
  `area_locality_taluka` varchar(100) DEFAULT NULL,
  `town_city_district` varchar(100) NOT NULL,
  `state_union_territory` varchar(100) NOT NULL,
  `pin_zip_code` varchar(20) NOT NULL,
  `country_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`address_id`),
  KEY `idx_address_applicant` (`applicant_id`),
  KEY `idx_address_country` (`country_id`),
  CONSTRAINT `fk_address_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_address_country` FOREIGN KEY (`country_id`) REFERENCES `m_countries` (`country_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='CORE. 1:N. Registered/Residence & Office addresses (field 7).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `applicant_contacts` (
  `contact_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `applicant_id` bigint(20) unsigned NOT NULL,
  `contact_type` enum('Residence','Office','Compliance') NOT NULL,
  `officer_name` varchar(150) DEFAULT NULL,
  `job_title` varchar(100) DEFAULT NULL,
  `tel_isd_code` varchar(5) DEFAULT NULL,
  `tel_std_area_code` varchar(10) DEFAULT NULL,
  `telephone_number` varchar(20) DEFAULT NULL,
  `mobile_isd_code` varchar(5) DEFAULT NULL,
  `mobile_number` varchar(20) DEFAULT NULL,
  `fax_number` varchar(20) DEFAULT NULL,
  `email_id` varchar(100) DEFAULT NULL,
  `website` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`contact_id`),
  KEY `idx_contact_applicant` (`applicant_id`),
  CONSTRAINT `fk_contact_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='CORE. 1:N. Phone/mobile/email/website + compliance officer (fields 7d,17).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tax_residencies` (
  `residency_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `applicant_id` bigint(20) unsigned NOT NULL,
  `trc_number` varchar(50) NOT NULL,
  `country_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`residency_id`),
  KEY `idx_tax_res_applicant` (`applicant_id`),
  KEY `idx_tax_res_country` (`country_id`),
  CONSTRAINT `fk_tax_res_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_tax_res_country` FOREIGN KEY (`country_id`) REFERENCES `m_countries` (`country_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='CORE. 1:N. Multi-jurisdiction TRC numbers (field 6b).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `applicant_income_sources` (
  `source_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `applicant_id` bigint(20) unsigned NOT NULL,
  `income_source_code` varchar(40) NOT NULL,
  PRIMARY KEY (`source_id`),
  KEY `idx_income_applicant` (`applicant_id`),
  KEY `idx_income_source` (`income_source_code`),
  CONSTRAINT `fk_income_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_income_source` FOREIGN KEY (`income_source_code`) REFERENCES `m_income_sources` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='CORE. 1:N. Multi-select income sources (field 9).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubo` (
  `ubo_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `applicant_id` bigint(20) unsigned NOT NULL,
  `is_senior_managing_official` tinyint(1) NOT NULL DEFAULT 0,
  `full_name` varchar(150) NOT NULL,
  `residential_address` text NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `tax_residency_country_id` smallint(5) unsigned DEFAULT NULL,
  `nationality_country_id` smallint(5) unsigned DEFAULT NULL,
  `acting_group_details` text DEFAULT NULL,
  `shareholding_capital_pct` decimal(5,2) DEFAULT NULL,
  `id_document_type` varchar(50) DEFAULT NULL,
  `id_document_number` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ubo_id`),
  KEY `idx_ubo_applicant` (`applicant_id`),
  KEY `idx_ubo_tax_country` (`tax_residency_country_id`),
  KEY `idx_ubo_nationality_country` (`nationality_country_id`),
  CONSTRAINT `fk_ubo_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ubo_nationality_country` FOREIGN KEY (`nationality_country_id`) REFERENCES `m_countries` (`country_id`),
  CONSTRAINT `fk_ubo_tax_country` FOREIGN KEY (`tax_residency_country_id`) REFERENCES `m_countries` (`country_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='CORE. 1:N. Ultimate Beneficial Owners / senior managing official (field 8).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kyc_documents` (
  `document_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `applicant_id` bigint(20) unsigned NOT NULL,
  `doc_type_id` int(10) unsigned NOT NULL,
  `document_purpose` enum('POI','POA','POI_AND_POA') NOT NULL,
  `document_number` varchar(50) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `file_storage_uri` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `uploaded_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`document_id`),
  KEY `idx_kyc_applicant` (`applicant_id`),
  KEY `idx_kyc_doc_type` (`doc_type_id`),
  CONSTRAINT `fk_kyc_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_kyc_doc_type` FOREIGN KEY (`doc_type_id`) REFERENCES `m_document_types` (`doc_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='CORE. 1:N. POI/POA document metadata + file URI + verification (fields 11,31).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `applicant_disciplinary_history` (
  `disciplinary_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `applicant_id` bigint(20) unsigned NOT NULL,
  `regulatory_body_name` varchar(150) NOT NULL,
  `date_of_order` date DEFAULT NULL,
  `case_reference_number` varchar(100) DEFAULT NULL,
  `penalty_details` text DEFAULT NULL,
  PRIMARY KEY (`disciplinary_id`),
  KEY `idx_disc_applicant` (`applicant_id`),
  CONSTRAINT `fk_disc_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='CORE. 1:N. Regulatory enforcement / penalty log (field 21).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `applicant_clubbing_details` (
  `clubbing_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `applicant_id` bigint(20) unsigned NOT NULL,
  `related_entity_name` varchar(200) NOT NULL,
  `related_entity_pan` varchar(10) DEFAULT NULL,
  `sebi_registration_no` varchar(50) DEFAULT NULL,
  `relationship_type_code` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`clubbing_id`),
  KEY `idx_club_applicant` (`applicant_id`),
  KEY `idx_club_relationship` (`relationship_type_code`),
  CONSTRAINT `fk_club_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_club_relationship` FOREIGN KEY (`relationship_type_code`) REFERENCES `m_relationship_types` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='CORE. 1:N. Related FPIs sharing >50% ownership/control (field 22).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `applicant_prior_associations` (
  `association_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `applicant_id` bigint(20) unsigned NOT NULL,
  `past_registered_name` varchar(200) NOT NULL,
  `past_sebi_reg_no` varchar(50) DEFAULT NULL,
  `registered_as` varchar(50) DEFAULT NULL,
  `association_period_start` date DEFAULT NULL,
  `association_period_end` date DEFAULT NULL,
  PRIMARY KEY (`association_id`),
  KEY `idx_prior_applicant` (`applicant_id`),
  CONSTRAINT `fk_prior_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='CORE. 1:N. Prior Indian-market registrations FPI/FII/QFI/FVCI (field 23).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `applicant_foreign_regulators` (
  `regulator_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `applicant_id` bigint(20) unsigned NOT NULL,
  `regulatory_authority_name` varchar(150) NOT NULL,
  `regulatory_country_id` smallint(5) unsigned DEFAULT NULL,
  `regulatory_website` varchar(150) DEFAULT NULL,
  `regulatory_registration_no` varchar(100) DEFAULT NULL,
  `regulatory_capacity_category` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`regulator_id`),
  UNIQUE KEY `uq_foreign_reg_applicant` (`applicant_id`),
  KEY `idx_foreign_reg_country` (`regulatory_country_id`),
  CONSTRAINT `fk_foreign_reg_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_foreign_reg_country` FOREIGN KEY (`regulatory_country_id`) REFERENCES `m_countries` (`country_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='CORE. 1:1. Foreign regulator licensing details (field 18).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `applicant_custodian_details` (
  `custodian_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `applicant_id` bigint(20) unsigned NOT NULL,
  `global_custodian_name` varchar(200) NOT NULL,
  `custodian_regulator_name` varchar(150) DEFAULT NULL,
  `global_custodian_sebi_reg_no` varchar(50) DEFAULT NULL,
  `custodian_registered_address` text DEFAULT NULL,
  PRIMARY KEY (`custodian_id`),
  UNIQUE KEY `uq_custodian_applicant` (`applicant_id`),
  CONSTRAINT `fk_custodian_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='CORE. 1:1. Global custodian identity & address (field 19).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `investment_managers` (
  `manager_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `applicant_id` bigint(20) unsigned NOT NULL,
  `manager_name` varchar(150) NOT NULL,
  `sebi_registration_no` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`manager_id`),
  KEY `idx_im_applicant` (`applicant_id`),
  CONSTRAINT `fk_im_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='CORE. 1:N. Investment managers for MIM structures (field 15).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `depository_bank_accounts` (
  `account_config_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `applicant_id` bigint(20) unsigned NOT NULL,
  `open_depository_acc` tinyint(1) NOT NULL DEFAULT 1,
  `target_dp_name` varchar(150) DEFAULT NULL,
  `is_non_investing_fpi` tinyint(1) NOT NULL DEFAULT 0,
  `mode_of_operation_code` varchar(40) DEFAULT NULL,
  `mode_of_operation_others` varchar(50) DEFAULT NULL,
  `open_snra_bank_acc` tinyint(1) NOT NULL DEFAULT 1,
  `appointed_ddp_name` varchar(150) DEFAULT NULL,
  `ddp_sebi_reg_number` varchar(50) DEFAULT NULL,
  `ad_category_1_bank_name` varchar(150) DEFAULT NULL,
  `ad_bank_branch_address` text DEFAULT NULL,
  `bank_swift_ifsc` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`account_config_id`),
  UNIQUE KEY `uq_depo_applicant` (`applicant_id`),
  KEY `idx_depo_operation_mode` (`mode_of_operation_code`),
  CONSTRAINT `fk_depo_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_depo_operation_mode` FOREIGN KEY (`mode_of_operation_code`) REFERENCES `m_operation_modes` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='CORE. 1:1. DDP, SNRA & AD Cat-I bank configuration (fields 20,37,38).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pan_additional_details` (
  `pan_detail_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `applicant_id` bigint(20) unsigned NOT NULL,
  `already_holds_pan` tinyint(1) NOT NULL DEFAULT 0,
  `existing_pan` varchar(10) DEFAULT NULL,
  `applicant_status_code` varchar(30) DEFAULT NULL,
  `ao_area_code` varchar(3) DEFAULT NULL,
  `ao_type` varchar(2) DEFAULT NULL,
  `ao_range_code` varchar(3) DEFAULT NULL,
  `ao_number` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`pan_detail_id`),
  UNIQUE KEY `uq_pan_applicant` (`applicant_id`),
  KEY `idx_pan_applicant_status` (`applicant_status_code`),
  CONSTRAINT `fk_pan_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pan_applicant_status` FOREIGN KEY (`applicant_status_code`) REFERENCES `m_applicant_status` (`status_code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='CORE. 1:1. PAN status, applicant status, Assessing Officer code (fields 24-26).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `representative_assessee_details` (
  `representative_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `applicant_id` bigint(20) unsigned NOT NULL,
  `has_representative_in_india` tinyint(1) NOT NULL DEFAULT 0,
  `representative_name` varchar(200) DEFAULT NULL,
  `representative_pan` varchar(10) DEFAULT NULL,
  `representative_address` text DEFAULT NULL,
  PRIMARY KEY (`representative_id`),
  UNIQUE KEY `uq_rep_applicant` (`applicant_id`),
  CONSTRAINT `fk_rep_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='CORE. 1:1. Local Indian representative under IT Act s.160 (field 28).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `application_status_history` (
  `history_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `applicant_id` bigint(20) unsigned NOT NULL,
  `from_status` varchar(30) DEFAULT NULL,
  `to_status` varchar(30) NOT NULL,
  `changed_by` varchar(100) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `changed_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`history_id`),
  KEY `idx_status_hist_applicant` (`applicant_id`),
  CONSTRAINT `fk_status_hist_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='WORKFLOW. Audit trail of Draft->Submitted->Approved transitions.';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `application_section_progress` (
  `progress_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `applicant_id` bigint(20) unsigned NOT NULL,
  `section_code` varchar(30) NOT NULL,
  `is_complete` tinyint(1) NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`progress_id`),
  KEY `idx_section_progress_applicant` (`applicant_id`),
  CONSTRAINT `fk_section_progress_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='WORKFLOW. Per-section completion so the wizard can save partial drafts.';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `application_declaration` (
  `declaration_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `applicant_id` bigint(20) unsigned NOT NULL,
  `declarant_name` varchar(200) DEFAULT NULL,
  `capacity` varchar(150) DEFAULT NULL,
  `place` varchar(100) DEFAULT NULL,
  `declaration_date` date DEFAULT NULL,
  `authorized_signatory_name` varchar(200) DEFAULT NULL,
  `designation` varchar(150) DEFAULT NULL,
  `signature_uri` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`declaration_id`),
  UNIQUE KEY `uq_declaration_applicant` (`applicant_id`),
  CONSTRAINT `fk_declaration_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='WORKFLOW. Declaration & Undertaking signatory block (Part F).';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `office_verification` (
  `office_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `applicant_id` bigint(20) unsigned NOT NULL,
  `dp_name` varchar(150) DEFAULT NULL,
  `dp_address` text DEFAULT NULL,
  `dp_id` varchar(20) DEFAULT NULL,
  `client_id` varchar(20) DEFAULT NULL,
  `bank_account_type` varchar(20) DEFAULT 'SNRA',
  `bank_account_number` varchar(30) DEFAULT NULL,
  `bank_name` varchar(150) DEFAULT NULL,
  `branch_address` text DEFAULT NULL,
  `micr_code` varchar(15) DEFAULT NULL,
  `documents_received` varchar(100) DEFAULT NULL,
  `risk_category_code` varchar(40) DEFAULT NULL,
  `ipv_done` tinyint(1) NOT NULL DEFAULT 0,
  `ipv_date` date DEFAULT NULL,
  `emp_name` varchar(150) DEFAULT NULL,
  `emp_code` varchar(50) DEFAULT NULL,
  `emp_designation` varchar(100) DEFAULT NULL,
  `emp_branch` varchar(100) DEFAULT NULL,
  `institution_name` varchar(150) DEFAULT NULL,
  `institution_code` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`office_id`),
  UNIQUE KEY `uq_office_applicant` (`applicant_id`),
  KEY `idx_office_risk_category` (`risk_category_code`),
  CONSTRAINT `fk_office_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_office_risk_category` FOREIGN KEY (`risk_category_code`) REFERENCES `m_risk_categories` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='WORKFLOW. ''For Office Use Only'' page 32 - DDP capture, IPV, risk category.';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


-- ============ MASTER / LOOKUP DATA ============
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

LOCK TABLES `m_countries` WRITE;
/*!40000 ALTER TABLE `m_countries` DISABLE KEYS */;
INSERT INTO `m_countries` (`country_id`, `iso2`, `iso3`, `isd_code`, `label_en`, `label_hi`, `is_fatf_member`, `is_active`, `display_order`) VALUES (1,'IN','IND','91','India','भारत',0,1,1),(2,'US','USA','1','United States','संयुक्त राज्य अमेरिका',0,1,2),(3,'GB','GBR','44','United Kingdom','यूनाइटेड किंगडम',0,1,3),(4,'SG','SGP','65','Singapore','सिंगापुर',0,1,4),(5,'AE','ARE','971','United Arab Emirates','संयुक्त अरब अमीरात',0,1,5),(6,'MU','MUS','230','Mauritius','मॉरिशस',0,1,6),(7,'LU','LUX','352','Luxembourg','लक्ज़मबर्ग',0,1,7);
/*!40000 ALTER TABLE `m_countries` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `m_titles` WRITE;
/*!40000 ALTER TABLE `m_titles` DISABLE KEYS */;
INSERT INTO `m_titles` (`code`, `label_en`, `label_hi`, `is_active`, `display_order`) VALUES ('MR','Shri/Mr.','श्री',1,0),('MRS','Smt/Mrs.','श्रीमती',1,0),('MS','Kumari/Ms.','कुमारी/सुश्री',1,0),('MS_CORP','M/s','मैससर्',1,0);
/*!40000 ALTER TABLE `m_titles` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `m_genders` WRITE;
/*!40000 ALTER TABLE `m_genders` DISABLE KEYS */;
INSERT INTO `m_genders` (`code`, `label_en`, `label_hi`, `is_active`, `display_order`) VALUES ('FEMALE','Female','महिला',1,0),('MALE','Male','पुरुष',1,0),('TRANS','Transgender','ट्रांसजेंडर',1,0);
/*!40000 ALTER TABLE `m_genders` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `m_marital_statuses` WRITE;
/*!40000 ALTER TABLE `m_marital_statuses` DISABLE KEYS */;
INSERT INTO `m_marital_statuses` (`code`, `label_en`, `label_hi`, `is_active`, `display_order`) VALUES ('DIVORCED','Divorced','तलाकशुदा',1,0),('MARRIED','Married','विवाहित',1,0),('SINGLE','Single','अविवाहित',1,0),('WIDOW','Widow/Widower','विधवा/विधुर',1,0);
/*!40000 ALTER TABLE `m_marital_statuses` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `m_citizenship_statuses` WRITE;
/*!40000 ALTER TABLE `m_citizenship_statuses` DISABLE KEYS */;
INSERT INTO `m_citizenship_statuses` (`code`, `label_en`, `label_hi`, `is_active`, `display_order`) VALUES ('FOREIGNER','Foreigner','विदेशी',1,0),('OCI','Overseas citizen of India','भारत का विदेशी नागरिक',1,0),('PIO','Person of Indian origin','भारतीय मूल का व्यक्ति',1,0);
/*!40000 ALTER TABLE `m_citizenship_statuses` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `m_income_sources` WRITE;
/*!40000 ALTER TABLE `m_income_sources` DISABLE KEYS */;
INSERT INTO `m_income_sources` (`code`, `label_en`, `label_hi`, `is_active`, `display_order`) VALUES ('BUSINESS','Income from Business/Profession','कारबार/व्यवसाय से आय',1,0),('CAP_GAINS','Capital Gains','पूँजीगत अभिलाभ',1,0),('HOUSE_PROP','Income from House Property','गृह संपत्ति से आय',1,0),('NO_INCOME','No Income','कोई आय नहीं',1,0),('OTHER','Income from other Sources','अन्य स्रोतों से आय',1,0),('SALARY','Salary','वेतन',1,0);
/*!40000 ALTER TABLE `m_income_sources` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `m_relationship_types` WRITE;
/*!40000 ALTER TABLE `m_relationship_types` DISABLE KEYS */;
INSERT INTO `m_relationship_types` (`code`, `label_en`, `label_hi`, `is_active`, `display_order`) VALUES ('COMMON_CTRL','Common Control','समान नियंत्रण',1,0),('COMMON_OWN','Common Ownership','समान स्वामित्व',1,0),('OTHER','Other','अन्य',1,0);
/*!40000 ALTER TABLE `m_relationship_types` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `m_operation_modes` WRITE;
/*!40000 ALTER TABLE `m_operation_modes` DISABLE KEYS */;
INSERT INTO `m_operation_modes` (`code`, `label_en`, `label_hi`, `is_active`, `display_order`) VALUES ('ANY_ONE','Any one single','कोई भी एक',1,0),('JOINTLY','Jointly by','संयुक्त रूप से',1,0),('OTHERS','Others','अन्य',1,0),('RESOLUTION','As per resolution','संकल्प के अनुसार',1,0);
/*!40000 ALTER TABLE `m_operation_modes` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `m_application_statuses` WRITE;
/*!40000 ALTER TABLE `m_application_statuses` DISABLE KEYS */;
INSERT INTO `m_application_statuses` (`code`, `label_en`, `label_hi`, `is_active`, `display_order`) VALUES ('APPROVED','Approved','अनुमोदित',1,0),('DRAFT','Draft','प्रारूप',1,0),('REJECTED','Rejected','अस्वीकृत',1,0),('SUBMITTED','Submitted','प्रस्तुत',1,0),('UNDER_REVIEW','Under Review','समीक्षाधीन',1,0);
/*!40000 ALTER TABLE `m_application_statuses` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `m_card_name_prefs` WRITE;
/*!40000 ALTER TABLE `m_card_name_prefs` DISABLE KEYS */;
INSERT INTO `m_card_name_prefs` (`code`, `label_en`, `label_hi`, `is_active`, `display_order`) VALUES ('FATHER','Father\'s Name','पिता का नाम',1,0),('MOTHER','Mother\'s Name','माता का नाम',1,0);
/*!40000 ALTER TABLE `m_card_name_prefs` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `m_risk_categories` WRITE;
/*!40000 ALTER TABLE `m_risk_categories` DISABLE KEYS */;
INSERT INTO `m_risk_categories` (`code`, `label_en`, `label_hi`, `is_active`, `display_order`) VALUES ('HIGH','High','उच्च',1,0),('LOW','Low','निम्न',1,0),('MEDIUM','Medium','मध्यम',1,0);
/*!40000 ALTER TABLE `m_risk_categories` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `m_business_profession_codes` WRITE;
/*!40000 ALTER TABLE `m_business_profession_codes` DISABLE KEYS */;
INSERT INTO `m_business_profession_codes` (`code`, `label_en`, `label_hi`, `is_active`) VALUES ('01','Medical Profession and Business','चिकित्सा संबंधी व्यवसाय और कारबार',1),('02','Engineering','इंजीनियरिंग',1),('03','Architecture','वास्तुकला',1),('04','Chartered Accountant / Accountancy','चार्टर्ड अकाउंटेंट / अकाउंटेंसी',1),('05','Interior Decoration','इंटीरियर डेकोरेशन',1),('06','Technical Consultancy','तकनीकी परामर्श कार्य',1),('07','Company Secretary','कंपनी सचिव',1),('08','Legal Practitioner and Solicitors','विधि व्यवसायी और सालिसिटर्स',1),('09','Government Contractors','सरकारी ठेकेदार',1),('10','Insurance Agency','बीमा एजेंट',1),('11','Films, TV and such other entertainment','फिल्म, टी.वी. और अन्य मनोरंजन',1),('12','Information Technology','सूचना प्रौद्योगिकी',1),('13','Builders and Developers','बिल्डर्स एवं डेवलपर्स',1),('14','Members of Stock Exchange, Share Brokers and Sub-Brokers','स्टॉक एक्सचेंज के सदस्य, शेयर दलाल और उप-दलाल',1),('15','Performing Arts and Yatra','प्रदर्शन कलाएँ',1),('16','Operation of Ships, Hovercraft, Aircrafts or Helicopters','जहाजों, होवरक्राफ्ट, एयरक्राफ्ट या हेलिकॉप्टर का संचालन',1),('17','Plying Taxis, Lorries, Trucks, Buses or other Commercial Vehicles','टैक्सी, लॉरी, ट्रक, बसें या अन्य वाणिज्यिक वाहन',1),('18','Ownership of Horses or Jockeys','घोड़ों का स्वामित्व या जॉकी',1),('19','Cinema Halls and Other Theatres','सिनेमा हॉल या अन्य थियेटर',1),('20','Others','अन्य',1);
/*!40000 ALTER TABLE `m_business_profession_codes` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `m_occupation_codes` WRITE;
/*!40000 ALTER TABLE `m_occupation_codes` DISABLE KEYS */;
INSERT INTO `m_occupation_codes` (`occupation_id`, `applies_to`, `code`, `label_en`, `label_hi`, `is_active`) VALUES (1,'Individual','1','Service - Private sector','सेवा - निजी क्षेत्र',1),(2,'Individual','2','Service - Public Sector','सेवा - सार्वजनिक क्षेत्र',1),(3,'Individual','3','Service - Govt. service','सेवा - सरकारी सेवा',1),(4,'Individual','4','Business','कारबार',1),(5,'Individual','5','Professional','व्यवसायी',1),(6,'Individual','6','Agriculturist','कृषक',1),(7,'Individual','7','Retired','सेवानिवृत्त',1),(8,'Individual','8','Housewife','गृहिणी',1),(9,'Individual','9','Student','छात्र',1),(10,'Individual','10','Others','अन्य',1),(11,'Non-Individual','R','Private Company','निजी कंपनी',1),(12,'Non-Individual','U','Public Company','सार्वजनिक कंपनी',1),(13,'Non-Individual','D','Body Corporate','निगमित निकाय',1),(14,'Non-Individual','S','Financial Institution','वित्तीय संस्था',1),(15,'Non-Individual','N','Non-Government Organisation','गैर-सरकारी संगठन',1),(16,'Non-Individual','C','Charitable Organisation','धर्मार्थ संगठन',1);
/*!40000 ALTER TABLE `m_occupation_codes` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `m_fpi_categories` WRITE;
/*!40000 ALTER TABLE `m_fpi_categories` DISABLE KEYS */;
INSERT INTO `m_fpi_categories` (`category_code`, `label_en`, `label_hi`) VALUES ('CAT_I','Category I','श्रेणी I'),('CAT_II','Category II','श्रेणी II');
/*!40000 ALTER TABLE `m_fpi_categories` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `m_fpi_sub_categories` WRITE;
/*!40000 ALTER TABLE `m_fpi_sub_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `m_fpi_sub_categories` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `m_document_types` WRITE;
/*!40000 ALTER TABLE `m_document_types` DISABLE KEYS */;
INSERT INTO `m_document_types` (`doc_type_id`, `applies_to`, `purpose`, `code`, `label_en`, `label_hi`, `requires_expiry`, `requires_number`, `is_active`) VALUES (1,'Both','POI_AND_POA','INCORP','Certificate of Incorporation',NULL,0,1,1),(2,'Both','POI_AND_POA','LEIPROOF','Proof of LEI Registration',NULL,0,1,1),(3,'Both','POI','PANCOPY','Copy of Indian PAN Card',NULL,0,1,1),(4,'Both','POI_AND_POA','UBODECL','UBO List & Declaration',NULL,0,1,1);
/*!40000 ALTER TABLE `m_document_types` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `m_applicant_status` WRITE;
/*!40000 ALTER TABLE `m_applicant_status` DISABLE KEYS */;
/*!40000 ALTER TABLE `m_applicant_status` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `m_ao_codes` WRITE;
/*!40000 ALTER TABLE `m_ao_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `m_ao_codes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

SET FOREIGN_KEY_CHECKS=1;
