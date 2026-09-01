<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ADDITIVE ONLY — one nullable column to persist the UBO Determination tool's
 * ownership tree (entities, owners, percentages). The approved schema has no
 * table for an ownership graph, so this JSON column is the home for it.
 * Nothing existing is altered.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->longText('ubo_structure_json')->nullable()->after('is_using_global_custodian');
        });
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn('ubo_structure_json');
        });
    }
};
