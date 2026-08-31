<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Builds the full CAF (Common Application Form) FPI schema — 41 tables
 * (18 Master + 19 Core + 4 Workflow) — a faithful reproduction of the
 * approved "CAF Data Model - Improved (2).xlsx".
 *
 * The DDL and master-data seed live as reviewable .sql files under
 * database/schema/ and are executed verbatim here.
 */
return new class extends Migration
{
    /**
     * Tables in reverse dependency order, for a clean rollback.
     */
    private array $tables = [
        // Workflow
        'office_verification',
        'application_declaration',
        'application_section_progress',
        'application_status_history',
        // Core
        'representative_assessee_details',
        'pan_additional_details',
        'depository_bank_accounts',
        'investment_managers',
        'applicant_custodian_details',
        'applicant_foreign_regulators',
        'applicant_prior_associations',
        'applicant_clubbing_details',
        'applicant_disciplinary_history',
        'kyc_documents',
        'ubo',
        'applicant_income_sources',
        'tax_residencies',
        'applicant_contacts',
        'applicant_addresses',
        'applicant_aliases',
        'corporate_applicant_details',
        'individual_applicant_details',
        'applicants',
        // Master
        'm_ao_codes',
        'm_applicant_status',
        'm_document_types',
        'm_fpi_sub_categories',
        'm_fpi_categories',
        'm_occupation_codes',
        'm_business_profession_codes',
        'm_risk_categories',
        'm_card_name_prefs',
        'm_application_statuses',
        'm_operation_modes',
        'm_relationship_types',
        'm_income_sources',
        'm_citizenship_statuses',
        'm_marital_statuses',
        'm_genders',
        'm_titles',
        'm_countries',
    ];

    public function up(): void
    {
        DB::unprepared(file_get_contents(database_path('schema/caf_schema.sql')));
        DB::unprepared(file_get_contents(database_path('schema/caf_master_data.sql')));
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        foreach ($this->tables as $table) {
            DB::statement("DROP TABLE IF EXISTS `{$table}`");
        }
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
