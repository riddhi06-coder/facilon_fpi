<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ADDITIVE ONLY — home for the "Applicant Type" (Partnership/Company/Trust/BOI)
 * moved to the Applicant Profile tab. Nullable; nothing existing is altered.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('corporate_applicant_details', function (Blueprint $table) {
            $table->string('applicant_legal_type', 60)->nullable()->after('company_name');
        });
    }

    public function down(): void
    {
        Schema::table('corporate_applicant_details', function (Blueprint $table) {
            $table->dropColumn('applicant_legal_type');
        });
    }
};
