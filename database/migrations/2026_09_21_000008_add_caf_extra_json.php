<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ADDITIVE ONLY — a single structured JSON store for the many reference-form
 * fields (Steps 4–8) that either have no column in the approved CAF schema or
 * whose master-data FK tables are unseeded (income sources, occupation codes,
 * profession codes, etc.). Keyed by section so each tab's save merges only its
 * own slice. Genuine scalar homes in the normalized schema are still used where
 * they exist; this column carries the remainder losslessly.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->text('caf_extra_json')->nullable()->after('beneficial_ownership_json');
        });
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn('caf_extra_json');
        });
    }
};
