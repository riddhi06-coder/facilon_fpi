-- ============================================================================
-- CAF (Common Application Form) - FPI Registration
-- Database schema — faithful reproduction of "CAF Data Model - Improved (2).xlsx"
-- Engine: MySQL 8.x / MariaDB 10.5+ (InnoDB, utf8mb4)
-- 41 tables = 18 Master (m_*) + 19 Core + 4 Workflow
-- Order: masters -> core -> workflow (FK-safe)
-- ============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================================
-- 1. MASTER / LOOKUP TABLES (18)
-- ============================================================================

-- 1. m_countries — country + ISD calling code + ISO + FATF flag
CREATE TABLE m_countries (
  country_id       SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  iso2             CHAR(2) NOT NULL,
  iso3             CHAR(3) NOT NULL,
  isd_code         VARCHAR(6) NOT NULL,
  label_en         VARCHAR(100) NOT NULL,
  label_hi         VARCHAR(100) NULL,
  is_fatf_member   BOOLEAN NOT NULL DEFAULT FALSE,
  is_active        BOOLEAN NOT NULL DEFAULT TRUE,
  display_order    SMALLINT NOT NULL DEFAULT 0,
  PRIMARY KEY (country_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='MASTER. Single source of truth for country + ISD calling code + ISO + FATF flag.';

-- 2. m_titles — Title prefix Shri/Smt/Ms/M/s (fields 1,3,28)
CREATE TABLE m_titles (
  code             VARCHAR(40) NOT NULL,
  label_en         VARCHAR(100) NOT NULL,
  label_hi         VARCHAR(100) NULL,
  is_active        BOOLEAN NOT NULL DEFAULT TRUE,
  display_order    SMALLINT NOT NULL DEFAULT 0,
  PRIMARY KEY (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='MASTER. Title prefix Shri/Smt/Ms/M/s (fields 1,3,28).';

-- 3. m_genders — Gender (field 32)
CREATE TABLE m_genders (
  code             VARCHAR(40) NOT NULL,
  label_en         VARCHAR(100) NOT NULL,
  label_hi         VARCHAR(100) NULL,
  is_active        BOOLEAN NOT NULL DEFAULT TRUE,
  display_order    SMALLINT NOT NULL DEFAULT 0,
  PRIMARY KEY (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='MASTER. Gender (field 32).';

-- 4. m_marital_statuses — Marital status (field 33)
CREATE TABLE m_marital_statuses (
  code             VARCHAR(40) NOT NULL,
  label_en         VARCHAR(100) NOT NULL,
  label_hi         VARCHAR(100) NULL,
  is_active        BOOLEAN NOT NULL DEFAULT TRUE,
  display_order    SMALLINT NOT NULL DEFAULT 0,
  PRIMARY KEY (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='MASTER. Marital status (field 33).';

-- 5. m_citizenship_statuses — Citizenship status (field 34)
CREATE TABLE m_citizenship_statuses (
  code             VARCHAR(40) NOT NULL,
  label_en         VARCHAR(100) NOT NULL,
  label_hi         VARCHAR(100) NULL,
  is_active        BOOLEAN NOT NULL DEFAULT TRUE,
  display_order    SMALLINT NOT NULL DEFAULT 0,
  PRIMARY KEY (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='MASTER. Citizenship status (field 34).';

-- 6. m_income_sources — Income source values (field 9)
CREATE TABLE m_income_sources (
  code             VARCHAR(40) NOT NULL,
  label_en         VARCHAR(100) NOT NULL,
  label_hi         VARCHAR(100) NULL,
  is_active        BOOLEAN NOT NULL DEFAULT TRUE,
  display_order    SMALLINT NOT NULL DEFAULT 0,
  PRIMARY KEY (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='MASTER. Income source values (field 9).';

-- 7. m_relationship_types — Clubbing relationship type (field 22)
CREATE TABLE m_relationship_types (
  code             VARCHAR(40) NOT NULL,
  label_en         VARCHAR(100) NOT NULL,
  label_hi         VARCHAR(100) NULL,
  is_active        BOOLEAN NOT NULL DEFAULT TRUE,
  display_order    SMALLINT NOT NULL DEFAULT 0,
  PRIMARY KEY (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='MASTER. Clubbing relationship type (field 22).';

-- 8. m_operation_modes — Depository operation mode (field 37b)
CREATE TABLE m_operation_modes (
  code             VARCHAR(40) NOT NULL,
  label_en         VARCHAR(100) NOT NULL,
  label_hi         VARCHAR(100) NULL,
  is_active        BOOLEAN NOT NULL DEFAULT TRUE,
  display_order    SMALLINT NOT NULL DEFAULT 0,
  PRIMARY KEY (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='MASTER. Depository operation mode (field 37b).';

-- 9. m_application_statuses — Workflow lifecycle values
CREATE TABLE m_application_statuses (
  code             VARCHAR(40) NOT NULL,
  label_en         VARCHAR(100) NOT NULL,
  label_hi         VARCHAR(100) NULL,
  is_active        BOOLEAN NOT NULL DEFAULT TRUE,
  display_order    SMALLINT NOT NULL DEFAULT 0,
  PRIMARY KEY (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='MASTER. Workflow lifecycle values.';

-- 10. m_card_name_prefs — Father/Mother name-on-card preference (field 35)
CREATE TABLE m_card_name_prefs (
  code             VARCHAR(40) NOT NULL,
  label_en         VARCHAR(100) NOT NULL,
  label_hi         VARCHAR(100) NULL,
  is_active        BOOLEAN NOT NULL DEFAULT TRUE,
  display_order    SMALLINT NOT NULL DEFAULT 0,
  PRIMARY KEY (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='MASTER. Father/Mother name-on-card preference (field 35).';

-- 11. m_risk_categories — KYC risk category (office use)
CREATE TABLE m_risk_categories (
  code             VARCHAR(40) NOT NULL,
  label_en         VARCHAR(100) NOT NULL,
  label_hi         VARCHAR(100) NULL,
  is_active        BOOLEAN NOT NULL DEFAULT TRUE,
  display_order    SMALLINT NOT NULL DEFAULT 0,
  PRIMARY KEY (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='MASTER. KYC risk category (office use).';

-- 12. m_business_profession_codes — Business/Profession codes 01-20 (field 9a)
CREATE TABLE m_business_profession_codes (
  code             CHAR(2) NOT NULL,
  label_en         VARCHAR(150) NOT NULL,
  label_hi         VARCHAR(150) NULL,
  is_active        BOOLEAN NOT NULL DEFAULT TRUE,
  PRIMARY KEY (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='MASTER. Business/Profession codes 01-20 (field 9a).';

-- 13. m_occupation_codes — Occupation list, split Individual vs Non-Individual (field 10)
CREATE TABLE m_occupation_codes (
  occupation_id    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  applies_to       ENUM('Individual','Non-Individual') NOT NULL,
  code             VARCHAR(10) NOT NULL,
  label_en         VARCHAR(150) NOT NULL,
  label_hi         VARCHAR(150) NULL,
  is_active        BOOLEAN NOT NULL DEFAULT TRUE,
  PRIMARY KEY (occupation_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='MASTER. Occupation list, split Individual vs Non-Individual (field 10).';

-- 14. m_fpi_categories — FPI Category I / II (field 13)
CREATE TABLE m_fpi_categories (
  category_code    VARCHAR(20) NOT NULL,
  label_en         VARCHAR(100) NOT NULL,
  label_hi         VARCHAR(100) NULL,
  PRIMARY KEY (category_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='MASTER. FPI Category I / II (field 13).';

-- 15. m_fpi_sub_categories — SEBI sub-category clauses + UBO-exemption flag (field 13)
CREATE TABLE m_fpi_sub_categories (
  sub_category_id  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  category_code    VARCHAR(20) NOT NULL,
  clause_ref       VARCHAR(30) NOT NULL,
  label_en         VARCHAR(255) NOT NULL,
  label_hi         VARCHAR(255) NULL,
  ubo_exempt       BOOLEAN NOT NULL DEFAULT FALSE,
  is_active        BOOLEAN NOT NULL DEFAULT TRUE,
  display_order    SMALLINT NOT NULL DEFAULT 0,
  PRIMARY KEY (sub_category_id),
  KEY idx_fpi_sub_category (category_code),
  CONSTRAINT fk_fpi_sub_category FOREIGN KEY (category_code) REFERENCES m_fpi_categories (category_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='MASTER. SEBI sub-category clauses + UBO-exemption flag (field 13).';

-- 16. m_document_types — POI/POA document types per applicant type (fields 11,31)
CREATE TABLE m_document_types (
  doc_type_id      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  applies_to       ENUM('Individual','Non-Individual','Both') NOT NULL,
  purpose          ENUM('POI','POA','POI_AND_POA') NOT NULL,
  code             VARCHAR(10) NOT NULL,
  label_en         VARCHAR(200) NOT NULL,
  label_hi         VARCHAR(200) NULL,
  requires_expiry  BOOLEAN NOT NULL DEFAULT FALSE,
  requires_number  BOOLEAN NOT NULL DEFAULT TRUE,
  is_active        BOOLEAN NOT NULL DEFAULT TRUE,
  PRIMARY KEY (doc_type_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='MASTER. POI/POA document types per applicant type (fields 11,31).';

-- 17. m_applicant_status — PAN 'Status of Applicant' list (field 25)
CREATE TABLE m_applicant_status (
  status_code      VARCHAR(30) NOT NULL,
  label_en         VARCHAR(100) NOT NULL,
  label_hi         VARCHAR(100) NULL,
  pan_status       VARCHAR(30) NULL,
  PRIMARY KEY (status_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT="MASTER. PAN 'Status of Applicant' list (field 25).";

-- 18. m_ao_codes — Assessing Officer code list (field 26) - load externally
CREATE TABLE m_ao_codes (
  ao_id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  area_code        VARCHAR(3) NOT NULL,
  ao_type          VARCHAR(2) NOT NULL,
  range_code       VARCHAR(3) NOT NULL,
  ao_number        VARCHAR(3) NOT NULL,
  description      VARCHAR(200) NULL,
  PRIMARY KEY (ao_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='MASTER. Assessing Officer code list (field 26) - load externally.';

-- ============================================================================
-- 2. CORE / APPLICANT TABLES (19)
-- ============================================================================

-- 19. applicants — Root polymorphic hub
CREATE TABLE applicants (
  applicant_id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  entity_type                   ENUM('Individual','Non-Individual') NOT NULL,
  pan_card_name_abbrev          VARCHAR(100) NOT NULL,
  fpi_category_code             VARCHAR(20) NULL,
  fpi_sub_category_id           INT UNSIGNED NULL,
  is_mim_structure              BOOLEAN NOT NULL DEFAULT FALSE,
  gross_annual_income_inr       DECIMAL(18,2) NULL,
  net_worth_inr                 DECIMAL(18,2) NULL,
  net_worth_date                DATE NULL,
  has_fatca_crs_declaration     BOOLEAN NOT NULL DEFAULT FALSE,
  has_disciplinary_history      BOOLEAN NOT NULL DEFAULT FALSE,
  disciplinary_history_details  TEXT NULL,
  has_investment_limit_clubbing BOOLEAN NOT NULL DEFAULT FALSE,
  has_prior_indian_market_assoc BOOLEAN NOT NULL DEFAULT FALSE,
  is_regulated_fpi              BOOLEAN NOT NULL DEFAULT FALSE,
  is_using_global_custodian     BOOLEAN NOT NULL DEFAULT FALSE,
  application_status            VARCHAR(40) NOT NULL DEFAULT 'DRAFT',
  created_at                    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at                    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created_by                    VARCHAR(100) NULL,
  updated_by                    VARCHAR(100) NULL,
  is_deleted                    BOOLEAN NOT NULL DEFAULT FALSE,
  PRIMARY KEY (applicant_id),
  KEY idx_applicants_fpi_category (fpi_category_code),
  KEY idx_applicants_fpi_sub_category (fpi_sub_category_id),
  KEY idx_applicants_application_status (application_status),
  CONSTRAINT fk_applicants_fpi_category FOREIGN KEY (fpi_category_code) REFERENCES m_fpi_categories (category_code),
  CONSTRAINT fk_applicants_fpi_sub_category FOREIGN KEY (fpi_sub_category_id) REFERENCES m_fpi_sub_categories (sub_category_id),
  CONSTRAINT fk_applicants_application_status FOREIGN KEY (application_status) REFERENCES m_application_statuses (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='CORE. Root polymorphic hub. Shared onboarding, regulatory tier, workflow state for Individuals and entities.';

-- 20. individual_applicant_details — 1:1 human-only demographic extension
CREATE TABLE individual_applicant_details (
  applicant_id             BIGINT UNSIGNED NOT NULL,
  title_code               VARCHAR(40) NULL,
  first_name               VARCHAR(100) NOT NULL,
  middle_name              VARCHAR(100) NULL,
  last_name                VARCHAR(100) NOT NULL,
  date_of_birth            DATE NOT NULL,
  place_of_birth           VARCHAR(100) NOT NULL,
  birth_country_id         SMALLINT UNSIGNED NOT NULL,
  gender_code              VARCHAR(40) NULL,
  marital_status_code      VARCHAR(40) NULL,
  citizenship_status_code  VARCHAR(40) NULL,
  citizenship_country_id   SMALLINT UNSIGNED NULL,
  passport_number          VARCHAR(50) NULL,
  business_profession_code CHAR(2) NULL,
  occupation_id            INT UNSIGNED NULL,
  is_pep                   BOOLEAN NOT NULL DEFAULT FALSE,
  is_pep_related           BOOLEAN NOT NULL DEFAULT FALSE,
  mother_single_parent     BOOLEAN NOT NULL DEFAULT FALSE,
  father_first_name        VARCHAR(100) NULL,
  father_middle_name       VARCHAR(100) NULL,
  father_last_name         VARCHAR(100) NULL,
  mother_first_name        VARCHAR(100) NULL,
  mother_middle_name       VARCHAR(100) NULL,
  mother_last_name         VARCHAR(100) NULL,
  card_name_print_pref     VARCHAR(40) NULL,
  spouse_name              VARCHAR(200) NULL,
  PRIMARY KEY (applicant_id),
  KEY idx_iad_title (title_code),
  KEY idx_iad_birth_country (birth_country_id),
  KEY idx_iad_gender (gender_code),
  KEY idx_iad_marital (marital_status_code),
  KEY idx_iad_citizenship (citizenship_status_code),
  KEY idx_iad_citizenship_country (citizenship_country_id),
  KEY idx_iad_business_prof (business_profession_code),
  KEY idx_iad_occupation (occupation_id),
  KEY idx_iad_card_name_pref (card_name_print_pref),
  CONSTRAINT fk_iad_applicant FOREIGN KEY (applicant_id) REFERENCES applicants (applicant_id) ON DELETE CASCADE,
  CONSTRAINT fk_iad_title FOREIGN KEY (title_code) REFERENCES m_titles (code),
  CONSTRAINT fk_iad_birth_country FOREIGN KEY (birth_country_id) REFERENCES m_countries (country_id),
  CONSTRAINT fk_iad_gender FOREIGN KEY (gender_code) REFERENCES m_genders (code),
  CONSTRAINT fk_iad_marital FOREIGN KEY (marital_status_code) REFERENCES m_marital_statuses (code),
  CONSTRAINT fk_iad_citizenship FOREIGN KEY (citizenship_status_code) REFERENCES m_citizenship_statuses (code),
  CONSTRAINT fk_iad_citizenship_country FOREIGN KEY (citizenship_country_id) REFERENCES m_countries (country_id),
  CONSTRAINT fk_iad_business_prof FOREIGN KEY (business_profession_code) REFERENCES m_business_profession_codes (code),
  CONSTRAINT fk_iad_occupation FOREIGN KEY (occupation_id) REFERENCES m_occupation_codes (occupation_id),
  CONSTRAINT fk_iad_card_name_pref FOREIGN KEY (card_name_print_pref) REFERENCES m_card_name_prefs (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='CORE. 1:1 extension. Human-only demographic data.';

-- 21. corporate_applicant_details — 1:1 entity-only legal identity
CREATE TABLE corporate_applicant_details (
  applicant_id                       BIGINT UNSIGNED NOT NULL,
  company_name                       VARCHAR(200) NOT NULL,
  date_of_incorporation              DATE NOT NULL,
  date_commence_business             DATE NULL,
  place_of_incorporation             VARCHAR(100) NOT NULL,
  incorporation_country_id           SMALLINT UNSIGNED NOT NULL,
  lei_number                         VARCHAR(20) NULL,
  business_profession_code           CHAR(2) NULL,
  foreign_registration_no            VARCHAR(50) NULL,
  is_public_company_listed           BOOLEAN NOT NULL DEFAULT FALSE,
  stock_exchange_name                VARCHAR(100) NULL,
  occupation_id                      INT UNSIGNED NULL,
  provides_forex_money_changing      BOOLEAN NOT NULL DEFAULT FALSE,
  provides_gaming_gambling_services  BOOLEAN NOT NULL DEFAULT FALSE,
  provides_money_lending_pawning     BOOLEAN NOT NULL DEFAULT FALSE,
  im_entity_type                     ENUM('Investing','Non-Investing') NULL,
  PRIMARY KEY (applicant_id),
  KEY idx_cad_incorporation_country (incorporation_country_id),
  KEY idx_cad_business_prof (business_profession_code),
  KEY idx_cad_occupation (occupation_id),
  CONSTRAINT fk_cad_applicant FOREIGN KEY (applicant_id) REFERENCES applicants (applicant_id) ON DELETE CASCADE,
  CONSTRAINT fk_cad_incorporation_country FOREIGN KEY (incorporation_country_id) REFERENCES m_countries (country_id),
  CONSTRAINT fk_cad_business_prof FOREIGN KEY (business_profession_code) REFERENCES m_business_profession_codes (code),
  CONSTRAINT fk_cad_occupation FOREIGN KEY (occupation_id) REFERENCES m_occupation_codes (occupation_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='CORE. 1:1 extension. Entity-only legal identity.';

-- 22. applicant_aliases — 1:N other/former names (field 3)
CREATE TABLE applicant_aliases (
  alias_id                   BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  applicant_id               BIGINT UNSIGNED NOT NULL,
  alias_title_code           VARCHAR(40) NULL,
  alias_last_name_or_company VARCHAR(200) NOT NULL,
  alias_first_name           VARCHAR(100) NULL,
  alias_middle_name          VARCHAR(100) NULL,
  PRIMARY KEY (alias_id),
  KEY idx_alias_applicant (applicant_id),
  KEY idx_alias_title (alias_title_code),
  CONSTRAINT fk_alias_applicant FOREIGN KEY (applicant_id) REFERENCES applicants (applicant_id) ON DELETE CASCADE,
  CONSTRAINT fk_alias_title FOREIGN KEY (alias_title_code) REFERENCES m_titles (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='CORE. 1:N. Other/former names (field 3) for AML screening.';

-- 23. applicant_addresses — 1:N registered/residence & office addresses (field 7)
CREATE TABLE applicant_addresses (
  address_id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  applicant_id           BIGINT UNSIGNED NOT NULL,
  address_type           ENUM('Registered_Residence','Office') NOT NULL,
  is_communication_dest  BOOLEAN NOT NULL DEFAULT FALSE,
  office_name            VARCHAR(150) NULL,
  flat_room_block        VARCHAR(50) NULL,
  premises_building      VARCHAR(100) NULL,
  road_street_lane       VARCHAR(100) NULL,
  area_locality_taluka   VARCHAR(100) NULL,
  town_city_district     VARCHAR(100) NOT NULL,
  state_union_territory  VARCHAR(100) NOT NULL,
  pin_zip_code           VARCHAR(20) NOT NULL,
  country_id             SMALLINT UNSIGNED NOT NULL,
  PRIMARY KEY (address_id),
  KEY idx_address_applicant (applicant_id),
  KEY idx_address_country (country_id),
  CONSTRAINT fk_address_applicant FOREIGN KEY (applicant_id) REFERENCES applicants (applicant_id) ON DELETE CASCADE,
  CONSTRAINT fk_address_country FOREIGN KEY (country_id) REFERENCES m_countries (country_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='CORE. 1:N. Registered/Residence & Office addresses (field 7).';

-- 24. applicant_contacts — 1:N phone/mobile/email/website + compliance officer (fields 7d,17)
CREATE TABLE applicant_contacts (
  contact_id        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  applicant_id      BIGINT UNSIGNED NOT NULL,
  contact_type      ENUM('Residence','Office','Compliance') NOT NULL,
  officer_name      VARCHAR(150) NULL,
  job_title         VARCHAR(100) NULL,
  tel_isd_code      VARCHAR(5) NULL,
  tel_std_area_code VARCHAR(10) NULL,
  telephone_number  VARCHAR(20) NULL,
  mobile_isd_code   VARCHAR(5) NULL,
  mobile_number     VARCHAR(20) NULL,
  fax_number        VARCHAR(20) NULL,
  email_id          VARCHAR(100) NULL,
  website           VARCHAR(150) NULL,
  PRIMARY KEY (contact_id),
  KEY idx_contact_applicant (applicant_id),
  CONSTRAINT fk_contact_applicant FOREIGN KEY (applicant_id) REFERENCES applicants (applicant_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='CORE. 1:N. Phone/mobile/email/website + compliance officer (fields 7d,17).';

-- 25. tax_residencies — 1:N multi-jurisdiction TRC numbers (field 6b)
CREATE TABLE tax_residencies (
  residency_id  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  applicant_id  BIGINT UNSIGNED NOT NULL,
  trc_number    VARCHAR(50) NOT NULL,
  country_id    SMALLINT UNSIGNED NOT NULL,
  PRIMARY KEY (residency_id),
  KEY idx_tax_res_applicant (applicant_id),
  KEY idx_tax_res_country (country_id),
  CONSTRAINT fk_tax_res_applicant FOREIGN KEY (applicant_id) REFERENCES applicants (applicant_id) ON DELETE CASCADE,
  CONSTRAINT fk_tax_res_country FOREIGN KEY (country_id) REFERENCES m_countries (country_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='CORE. 1:N. Multi-jurisdiction TRC numbers (field 6b).';

-- 26. applicant_income_sources — 1:N multi-select income sources (field 9)
CREATE TABLE applicant_income_sources (
  source_id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  applicant_id        BIGINT UNSIGNED NOT NULL,
  income_source_code  VARCHAR(40) NOT NULL,
  PRIMARY KEY (source_id),
  KEY idx_income_applicant (applicant_id),
  KEY idx_income_source (income_source_code),
  CONSTRAINT fk_income_applicant FOREIGN KEY (applicant_id) REFERENCES applicants (applicant_id) ON DELETE CASCADE,
  CONSTRAINT fk_income_source FOREIGN KEY (income_source_code) REFERENCES m_income_sources (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='CORE. 1:N. Multi-select income sources (field 9).';

-- 27. ubo — 1:N Ultimate Beneficial Owners / senior managing official (field 8)
CREATE TABLE ubo (
  ubo_id                       BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  applicant_id                 BIGINT UNSIGNED NOT NULL,
  is_senior_managing_official  BOOLEAN NOT NULL DEFAULT FALSE,
  full_name                    VARCHAR(150) NOT NULL,
  residential_address          TEXT NOT NULL,
  date_of_birth                DATE NULL,
  tax_residency_country_id     SMALLINT UNSIGNED NULL,
  nationality_country_id       SMALLINT UNSIGNED NULL,
  acting_group_details         TEXT NULL,
  shareholding_capital_pct     DECIMAL(5,2) NULL,
  id_document_type             VARCHAR(50) NULL,
  id_document_number           VARCHAR(50) NULL,
  PRIMARY KEY (ubo_id),
  KEY idx_ubo_applicant (applicant_id),
  KEY idx_ubo_tax_country (tax_residency_country_id),
  KEY idx_ubo_nationality_country (nationality_country_id),
  CONSTRAINT fk_ubo_applicant FOREIGN KEY (applicant_id) REFERENCES applicants (applicant_id) ON DELETE CASCADE,
  CONSTRAINT fk_ubo_tax_country FOREIGN KEY (tax_residency_country_id) REFERENCES m_countries (country_id),
  CONSTRAINT fk_ubo_nationality_country FOREIGN KEY (nationality_country_id) REFERENCES m_countries (country_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='CORE. 1:N. Ultimate Beneficial Owners / senior managing official (field 8).';

-- 28. kyc_documents — 1:N POI/POA document metadata + file URI + verification (fields 11,31)
CREATE TABLE kyc_documents (
  document_id       BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  applicant_id      BIGINT UNSIGNED NOT NULL,
  doc_type_id       INT UNSIGNED NOT NULL,
  document_purpose  ENUM('POI','POA','POI_AND_POA') NOT NULL,
  document_number   VARCHAR(50) NULL,
  expiry_date       DATE NULL,
  file_storage_uri  VARCHAR(255) NULL,
  is_verified       BOOLEAN NOT NULL DEFAULT FALSE,
  uploaded_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (document_id),
  KEY idx_kyc_applicant (applicant_id),
  KEY idx_kyc_doc_type (doc_type_id),
  CONSTRAINT fk_kyc_applicant FOREIGN KEY (applicant_id) REFERENCES applicants (applicant_id) ON DELETE CASCADE,
  CONSTRAINT fk_kyc_doc_type FOREIGN KEY (doc_type_id) REFERENCES m_document_types (doc_type_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='CORE. 1:N. POI/POA document metadata + file URI + verification (fields 11,31).';

-- 29. applicant_disciplinary_history — 1:N regulatory enforcement / penalty log (field 21)
CREATE TABLE applicant_disciplinary_history (
  disciplinary_id       BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  applicant_id          BIGINT UNSIGNED NOT NULL,
  regulatory_body_name  VARCHAR(150) NOT NULL,
  date_of_order         DATE NULL,
  case_reference_number VARCHAR(100) NULL,
  penalty_details       TEXT NULL,
  PRIMARY KEY (disciplinary_id),
  KEY idx_disc_applicant (applicant_id),
  CONSTRAINT fk_disc_applicant FOREIGN KEY (applicant_id) REFERENCES applicants (applicant_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='CORE. 1:N. Regulatory enforcement / penalty log (field 21).';

-- 30. applicant_clubbing_details — 1:N related FPIs sharing >50% ownership/control (field 22)
CREATE TABLE applicant_clubbing_details (
  clubbing_id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  applicant_id            BIGINT UNSIGNED NOT NULL,
  related_entity_name     VARCHAR(200) NOT NULL,
  related_entity_pan      VARCHAR(10) NULL,
  sebi_registration_no    VARCHAR(50) NULL,
  relationship_type_code  VARCHAR(40) NULL,
  PRIMARY KEY (clubbing_id),
  KEY idx_club_applicant (applicant_id),
  KEY idx_club_relationship (relationship_type_code),
  CONSTRAINT fk_club_applicant FOREIGN KEY (applicant_id) REFERENCES applicants (applicant_id) ON DELETE CASCADE,
  CONSTRAINT fk_club_relationship FOREIGN KEY (relationship_type_code) REFERENCES m_relationship_types (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='CORE. 1:N. Related FPIs sharing >50% ownership/control (field 22).';

-- 31. applicant_prior_associations — 1:N prior Indian-market registrations (field 23)
CREATE TABLE applicant_prior_associations (
  association_id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  applicant_id             BIGINT UNSIGNED NOT NULL,
  past_registered_name     VARCHAR(200) NOT NULL,
  past_sebi_reg_no         VARCHAR(50) NULL,
  registered_as            VARCHAR(50) NULL,
  association_period_start DATE NULL,
  association_period_end   DATE NULL,
  PRIMARY KEY (association_id),
  KEY idx_prior_applicant (applicant_id),
  CONSTRAINT fk_prior_applicant FOREIGN KEY (applicant_id) REFERENCES applicants (applicant_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='CORE. 1:N. Prior Indian-market registrations FPI/FII/QFI/FVCI (field 23).';

-- 32. applicant_foreign_regulators — 1:1 foreign regulator licensing details (field 18)
CREATE TABLE applicant_foreign_regulators (
  regulator_id                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  applicant_id                 BIGINT UNSIGNED NOT NULL,
  regulatory_authority_name    VARCHAR(150) NOT NULL,
  regulatory_country_id        SMALLINT UNSIGNED NULL,
  regulatory_website           VARCHAR(150) NULL,
  regulatory_registration_no   VARCHAR(100) NULL,
  regulatory_capacity_category VARCHAR(150) NULL,
  PRIMARY KEY (regulator_id),
  UNIQUE KEY uq_foreign_reg_applicant (applicant_id),
  KEY idx_foreign_reg_country (regulatory_country_id),
  CONSTRAINT fk_foreign_reg_applicant FOREIGN KEY (applicant_id) REFERENCES applicants (applicant_id) ON DELETE CASCADE,
  CONSTRAINT fk_foreign_reg_country FOREIGN KEY (regulatory_country_id) REFERENCES m_countries (country_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='CORE. 1:1. Foreign regulator licensing details (field 18).';

-- 33. applicant_custodian_details — 1:1 global custodian identity & address (field 19)
CREATE TABLE applicant_custodian_details (
  custodian_id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  applicant_id                  BIGINT UNSIGNED NOT NULL,
  global_custodian_name         VARCHAR(200) NOT NULL,
  custodian_regulator_name      VARCHAR(150) NULL,
  global_custodian_sebi_reg_no  VARCHAR(50) NULL,
  custodian_registered_address  TEXT NULL,
  PRIMARY KEY (custodian_id),
  UNIQUE KEY uq_custodian_applicant (applicant_id),
  CONSTRAINT fk_custodian_applicant FOREIGN KEY (applicant_id) REFERENCES applicants (applicant_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='CORE. 1:1. Global custodian identity & address (field 19).';

-- 34. investment_managers — 1:N investment managers for MIM structures (field 15)
CREATE TABLE investment_managers (
  manager_id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  applicant_id          BIGINT UNSIGNED NOT NULL,
  manager_name          VARCHAR(150) NOT NULL,
  sebi_registration_no  VARCHAR(50) NULL,
  PRIMARY KEY (manager_id),
  KEY idx_im_applicant (applicant_id),
  CONSTRAINT fk_im_applicant FOREIGN KEY (applicant_id) REFERENCES applicants (applicant_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='CORE. 1:N. Investment managers for MIM structures (field 15).';

-- 35. depository_bank_accounts — 1:1 DDP, SNRA & AD Cat-I bank configuration (fields 20,37,38)
CREATE TABLE depository_bank_accounts (
  account_config_id        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  applicant_id             BIGINT UNSIGNED NOT NULL,
  open_depository_acc      BOOLEAN NOT NULL DEFAULT TRUE,
  target_dp_name           VARCHAR(150) NULL,
  is_non_investing_fpi     BOOLEAN NOT NULL DEFAULT FALSE,
  mode_of_operation_code   VARCHAR(40) NULL,
  mode_of_operation_others VARCHAR(50) NULL,
  open_snra_bank_acc       BOOLEAN NOT NULL DEFAULT TRUE,
  appointed_ddp_name       VARCHAR(150) NULL,
  ddp_sebi_reg_number      VARCHAR(50) NULL,
  ad_category_1_bank_name  VARCHAR(150) NULL,
  ad_bank_branch_address   TEXT NULL,
  PRIMARY KEY (account_config_id),
  UNIQUE KEY uq_depo_applicant (applicant_id),
  KEY idx_depo_operation_mode (mode_of_operation_code),
  CONSTRAINT fk_depo_applicant FOREIGN KEY (applicant_id) REFERENCES applicants (applicant_id) ON DELETE CASCADE,
  CONSTRAINT fk_depo_operation_mode FOREIGN KEY (mode_of_operation_code) REFERENCES m_operation_modes (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='CORE. 1:1. DDP, SNRA & AD Cat-I bank configuration (fields 20,37,38).';

-- 36. pan_additional_details — 1:1 PAN status, applicant status, Assessing Officer code (fields 24-26)
CREATE TABLE pan_additional_details (
  pan_detail_id         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  applicant_id          BIGINT UNSIGNED NOT NULL,
  already_holds_pan     BOOLEAN NOT NULL DEFAULT FALSE,
  existing_pan          VARCHAR(10) NULL,
  applicant_status_code VARCHAR(30) NULL,
  ao_area_code          VARCHAR(3) NULL,
  ao_type               VARCHAR(2) NULL,
  ao_range_code         VARCHAR(3) NULL,
  ao_number             VARCHAR(3) NULL,
  PRIMARY KEY (pan_detail_id),
  UNIQUE KEY uq_pan_applicant (applicant_id),
  KEY idx_pan_applicant_status (applicant_status_code),
  CONSTRAINT fk_pan_applicant FOREIGN KEY (applicant_id) REFERENCES applicants (applicant_id) ON DELETE CASCADE,
  CONSTRAINT fk_pan_applicant_status FOREIGN KEY (applicant_status_code) REFERENCES m_applicant_status (status_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='CORE. 1:1. PAN status, applicant status, Assessing Officer code (fields 24-26).';

-- 37. representative_assessee_details — 1:1 local Indian representative under IT Act s.160 (field 28)
CREATE TABLE representative_assessee_details (
  representative_id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  applicant_id                BIGINT UNSIGNED NOT NULL,
  has_representative_in_india BOOLEAN NOT NULL DEFAULT FALSE,
  representative_name         VARCHAR(200) NULL,
  representative_pan          VARCHAR(10) NULL,
  representative_address      TEXT NULL,
  PRIMARY KEY (representative_id),
  UNIQUE KEY uq_rep_applicant (applicant_id),
  CONSTRAINT fk_rep_applicant FOREIGN KEY (applicant_id) REFERENCES applicants (applicant_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='CORE. 1:1. Local Indian representative under IT Act s.160 (field 28).';

-- ============================================================================
-- 3. WORKFLOW TABLES (4)
-- ============================================================================

-- 38. application_status_history — 1:N audit trail of status transitions
CREATE TABLE application_status_history (
  history_id    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  applicant_id  BIGINT UNSIGNED NOT NULL,
  from_status   VARCHAR(30) NULL,
  to_status     VARCHAR(30) NOT NULL,
  changed_by    VARCHAR(100) NULL,
  remarks       VARCHAR(255) NULL,
  changed_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (history_id),
  KEY idx_status_hist_applicant (applicant_id),
  CONSTRAINT fk_status_hist_applicant FOREIGN KEY (applicant_id) REFERENCES applicants (applicant_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='WORKFLOW. Audit trail of Draft->Submitted->Approved transitions.';

-- 39. application_section_progress — 1:N per-section completion for partial drafts
CREATE TABLE application_section_progress (
  progress_id   BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  applicant_id  BIGINT UNSIGNED NOT NULL,
  section_code  VARCHAR(30) NOT NULL,
  is_complete   BOOLEAN NOT NULL DEFAULT FALSE,
  updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (progress_id),
  KEY idx_section_progress_applicant (applicant_id),
  CONSTRAINT fk_section_progress_applicant FOREIGN KEY (applicant_id) REFERENCES applicants (applicant_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='WORKFLOW. Per-section completion so the wizard can save partial drafts.';

-- 40. application_declaration — 1:1 Declaration & Undertaking signatory block (Part F)
CREATE TABLE application_declaration (
  declaration_id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  applicant_id              BIGINT UNSIGNED NOT NULL,
  declarant_name            VARCHAR(200) NULL,
  capacity                  VARCHAR(150) NULL,
  place                     VARCHAR(100) NULL,
  declaration_date          DATE NULL,
  authorized_signatory_name VARCHAR(200) NULL,
  designation               VARCHAR(150) NULL,
  signature_uri             VARCHAR(255) NULL,
  PRIMARY KEY (declaration_id),
  UNIQUE KEY uq_declaration_applicant (applicant_id),
  CONSTRAINT fk_declaration_applicant FOREIGN KEY (applicant_id) REFERENCES applicants (applicant_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='WORKFLOW. Declaration & Undertaking signatory block (Part F).';

-- 41. office_verification — 1:1 'For Office Use Only' page 32
CREATE TABLE office_verification (
  office_id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  applicant_id         BIGINT UNSIGNED NOT NULL,
  dp_name              VARCHAR(150) NULL,
  dp_address           TEXT NULL,
  dp_id                VARCHAR(20) NULL,
  client_id            VARCHAR(20) NULL,
  bank_account_type    VARCHAR(20) NULL DEFAULT 'SNRA',
  bank_account_number  VARCHAR(30) NULL,
  bank_name            VARCHAR(150) NULL,
  branch_address       TEXT NULL,
  micr_code            VARCHAR(15) NULL,
  documents_received   VARCHAR(100) NULL,
  risk_category_code   VARCHAR(40) NULL,
  ipv_done             BOOLEAN NOT NULL DEFAULT FALSE,
  ipv_date             DATE NULL,
  emp_name             VARCHAR(150) NULL,
  emp_code             VARCHAR(50) NULL,
  emp_designation      VARCHAR(100) NULL,
  emp_branch           VARCHAR(100) NULL,
  institution_name     VARCHAR(150) NULL,
  institution_code     VARCHAR(50) NULL,
  PRIMARY KEY (office_id),
  UNIQUE KEY uq_office_applicant (applicant_id),
  KEY idx_office_risk_category (risk_category_code),
  CONSTRAINT fk_office_applicant FOREIGN KEY (applicant_id) REFERENCES applicants (applicant_id) ON DELETE CASCADE,
  CONSTRAINT fk_office_risk_category FOREIGN KEY (risk_category_code) REFERENCES m_risk_categories (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT="WORKFLOW. 'For Office Use Only' page 32 - DDP capture, IPV, risk category.";

SET FOREIGN_KEY_CHECKS = 1;
