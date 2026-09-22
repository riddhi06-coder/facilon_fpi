<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ADDITIVE ONLY — Non-Individual full-form build, Steps 1 & 2.
 *
 * Step 2's extra fields (address line 4, phone ISD/area codes, fax, website)
 * already have homes in the approved schema (applicant_addresses.area_locality_taluka,
 * applicant_contacts.tel_isd_code / tel_std_area_code / mobile_isd_code / fax_number /
 * website), so no columns are needed there.
 *
 * Step 1 adds one new nullable column: the ISD dialing code for the country of
 * incorporation.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('corporate_applicant_details', function (Blueprint $table) {
            $table->string('incorporation_isd_code', 5)->nullable()->after('incorporation_country_id');
        });
    }

    public function down(): void
    {
        Schema::table('corporate_applicant_details', function (Blueprint $table) {
            $table->dropColumn('incorporation_isd_code');
        });
    }
};
