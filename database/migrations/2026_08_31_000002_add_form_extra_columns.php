<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ADDITIVE ONLY — homes for 4 form fields that had no column in the approved
 * CAF schema. Nothing existing is altered or removed; these are 4 new NULLABLE
 * columns on the semantically-correct tables. The approved schema
 * (create_caf_schema) stays untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('corporate_applicant_details', function (Blueprint $table) {
            $table->string('name_title_code', 40)->nullable()->after('applicant_id');   // Title (M/s) for entity
            $table->date('lei_expiry_date')->nullable()->after('lei_number');            // LEI expiry
            $table->foreign('name_title_code')->references('code')->on('m_titles');
        });

        Schema::table('applicants', function (Blueprint $table) {
            $table->string('gross_annual_income_band', 20)->nullable()->after('gross_annual_income_inr'); // USD income bracket
        });

        Schema::table('depository_bank_accounts', function (Blueprint $table) {
            $table->string('bank_swift_ifsc', 20)->nullable()->after('ad_bank_branch_address'); // SWIFT / IFSC
        });
    }

    public function down(): void
    {
        Schema::table('corporate_applicant_details', function (Blueprint $table) {
            $table->dropForeign(['name_title_code']);
            $table->dropColumn(['name_title_code', 'lei_expiry_date']);
        });
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn('gross_annual_income_band');
        });
        Schema::table('depository_bank_accounts', function (Blueprint $table) {
            $table->dropColumn('bank_swift_ifsc');
        });
    }
};
