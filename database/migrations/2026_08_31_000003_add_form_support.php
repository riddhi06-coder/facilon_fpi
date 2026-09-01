<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Support for wiring the remaining form tabs — all additive:
 *  1) One nullable column for a genuine orphan field (India place of business).
 *  2) Seed the two masters the form's dropdowns/uploads need so their FKs
 *     resolve (m_fpi_categories, m_document_types). These were empty because
 *     the approved sheet supplied no values; seeding is data, not a schema change.
 * Nothing existing is altered or removed.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('corporate_applicant_details', function (Blueprint $table) {
            $table->string('india_place_of_business', 200)->nullable()->after('im_entity_type');
        });

        // FPI categories (form sends CAT_I / CAT_II -> applicants.fpi_category_code FK)
        DB::table('m_fpi_categories')->insertOrIgnore([
            ['category_code' => 'CAT_I',  'label_en' => 'Category I',  'label_hi' => 'श्रेणी I'],
            ['category_code' => 'CAT_II', 'label_en' => 'Category II', 'label_hi' => 'श्रेणी II'],
        ]);

        // Document types backing the 4 upload slots (kyc_documents.doc_type_id FK)
        DB::table('m_document_types')->insertOrIgnore([
            ['applies_to' => 'Both', 'purpose' => 'POI_AND_POA', 'code' => 'INCORP',  'label_en' => 'Certificate of Incorporation'],
            ['applies_to' => 'Both', 'purpose' => 'POI_AND_POA', 'code' => 'LEIPROOF', 'label_en' => 'Proof of LEI Registration'],
            ['applies_to' => 'Both', 'purpose' => 'POI',         'code' => 'PANCOPY',  'label_en' => 'Copy of Indian PAN Card'],
            ['applies_to' => 'Both', 'purpose' => 'POI_AND_POA', 'code' => 'UBODECL',  'label_en' => 'UBO List & Declaration'],
        ]);
    }

    public function down(): void
    {
        Schema::table('corporate_applicant_details', function (Blueprint $table) {
            $table->dropColumn('india_place_of_business');
        });
        DB::table('m_document_types')->whereIn('code', ['INCORP', 'LEIPROOF', 'PANCOPY', 'UBODECL'])->delete();
        DB::table('m_fpi_categories')->whereIn('category_code', ['CAT_I', 'CAT_II'])->delete();
    }
};
