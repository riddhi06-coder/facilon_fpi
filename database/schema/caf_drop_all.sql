-- ============================================================
--  CAF — DROP ALL 41 TABLES (clean-rebuild helper)
--  Run this FIRST, then run caf_full.sql to recreate + seed.
--
--  ⚠  DESTRUCTIVE: permanently deletes these 41 tables AND any
--     data in them. Only run on an empty/test VAPT database that
--     you intend to rebuild. It does NOT touch any other tables.
-- ============================================================
USE facilon_fpi_vapt;
SET FOREIGN_KEY_CHECKS = 0;

-- Core / workflow (child) tables
DROP TABLE IF EXISTS `office_verification`;
DROP TABLE IF EXISTS `application_declaration`;
DROP TABLE IF EXISTS `application_section_progress`;
DROP TABLE IF EXISTS `application_status_history`;
DROP TABLE IF EXISTS `representative_assessee_details`;
DROP TABLE IF EXISTS `pan_additional_details`;
DROP TABLE IF EXISTS `depository_bank_accounts`;
DROP TABLE IF EXISTS `investment_managers`;
DROP TABLE IF EXISTS `applicant_custodian_details`;
DROP TABLE IF EXISTS `applicant_foreign_regulators`;
DROP TABLE IF EXISTS `applicant_prior_associations`;
DROP TABLE IF EXISTS `applicant_clubbing_details`;
DROP TABLE IF EXISTS `applicant_disciplinary_history`;
DROP TABLE IF EXISTS `kyc_documents`;
DROP TABLE IF EXISTS `ubo`;
DROP TABLE IF EXISTS `applicant_income_sources`;
DROP TABLE IF EXISTS `tax_residencies`;
DROP TABLE IF EXISTS `applicant_contacts`;
DROP TABLE IF EXISTS `applicant_addresses`;
DROP TABLE IF EXISTS `applicant_aliases`;
DROP TABLE IF EXISTS `corporate_applicant_details`;
DROP TABLE IF EXISTS `individual_applicant_details`;
DROP TABLE IF EXISTS `applicants`;

-- Master / lookup tables
DROP TABLE IF EXISTS `m_ao_codes`;
DROP TABLE IF EXISTS `m_applicant_status`;
DROP TABLE IF EXISTS `m_document_types`;
DROP TABLE IF EXISTS `m_fpi_sub_categories`;
DROP TABLE IF EXISTS `m_fpi_categories`;
DROP TABLE IF EXISTS `m_occupation_codes`;
DROP TABLE IF EXISTS `m_business_profession_codes`;
DROP TABLE IF EXISTS `m_risk_categories`;
DROP TABLE IF EXISTS `m_card_name_prefs`;
DROP TABLE IF EXISTS `m_application_statuses`;
DROP TABLE IF EXISTS `m_operation_modes`;
DROP TABLE IF EXISTS `m_relationship_types`;
DROP TABLE IF EXISTS `m_income_sources`;
DROP TABLE IF EXISTS `m_citizenship_statuses`;
DROP TABLE IF EXISTS `m_marital_statuses`;
DROP TABLE IF EXISTS `m_genders`;
DROP TABLE IF EXISTS `m_titles`;
DROP TABLE IF EXISTS `m_countries`;

SET FOREIGN_KEY_CHECKS = 1;
-- Done. Now run caf_full.sql to recreate the schema + seed master data.
