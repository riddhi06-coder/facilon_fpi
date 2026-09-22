<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ADDITIVE ONLY — Non-Individual full-form build, Step 3
 * (Beneficial Ownership & Intermediate Shareholding).
 *
 * Beneficial-owner rows continue to live in the normalized `ubo` table.
 * The variable-shape supporting structures introduced by the reference form —
 * the sub-fund flag + sub-fund names, intermediate entities and controlling
 * entities — are stored as one structured JSON document on the applicant,
 * mirroring the existing `ubo_structure_json` approach.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->text('beneficial_ownership_json')->nullable()->after('ubo_structure_json');
        });
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn('beneficial_ownership_json');
        });
    }
};
