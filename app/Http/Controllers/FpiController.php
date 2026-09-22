<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FpiController extends Controller
{
    /** Map the form's title option values to m_titles codes (and back). */
    private array $titleToCode = ['M/S' => 'MS_CORP', 'MR' => 'MR', 'MRS' => 'MRS', 'MS' => 'MS'];
    private array $codeToTitle = ['MS_CORP' => 'M/S', 'MR' => 'MR', 'MRS' => 'MRS', 'MS' => 'MS'];

    /**
     * Default (demo) data used to pre-populate the FPI registration form.
     */
    private function defaultForm(): array
    {
        // Blank form: text/date/number fields cleared; dropdowns keep their
        // normal default selection (unchanged).
        return [
            // Individual applicant (Step 1) — persisted via caf_extra_json['ind_applicant']
            'indTitle' => '',
            'indFirstName' => '',
            'indMiddleName' => '',
            'indLastName' => '',
            'indOtherName' => '',
            'indOtherTitle' => '',
            'indOtherFirstName' => '',
            'indOtherMiddleName' => '',
            'indOtherLastName' => '',
            'indDob' => '',
            'indPlaceOfBirth' => '',
            'indCountryOfBirth' => '',
            'indBirthIsd' => '',
            'indNationality' => '',
            'indNationalityIsd' => '',
            'indPassport' => '',
            'indGender' => '',
            'indMaritalStatus' => '',
            'indCitizenshipStatus' => '',
            'indCountryOfCitizenship' => '',
            'indFatherFirstName' => '',
            'indFatherMiddleName' => '',
            'indFatherLastName' => '',
            'indMotherFirstName' => '',
            'indMotherMiddleName' => '',
            'indMotherLastName' => '',
            'indSpouseFirstName' => '',
            'indSpouseMiddleName' => '',
            'indSpouseLastName' => '',
            'nameTitle' => '',
            'entityName' => '',
            'applicantType' => '',
            'knownByAnotherName' => '',
            'otherTitle' => '',
            'otherEntityName' => '',
            'dateOfIncorporation' => '',
            'dateOfCommencementOfBusiness' => '',
            'placeOfIncorporation' => '',
            'countryOfIncorporation' => '',
            'incorporationIsdCode' => '',
            'lei' => '',
            'leiExpiryDate' => '',
            'regAddressLine1' => '',
            'regAddressLine2' => '',
            'regAddressLine3' => '',
            'regAddressLine4' => '',
            'regCity' => '',
            'regState' => '',
            'regCountry' => '',
            'regZip' => '',
            'sameAddress' => false,
            'commAddressLine1' => '',
            'commAddressLine2' => '',
            'commAddressLine3' => '',
            'commAddressLine4' => '',
            'commCity' => '',
            'commState' => '',
            'commCountry' => '',
            'commZip' => '',
            'offAddressLine1' => '',
            'offAddressLine2' => '',
            'offAddressLine3' => '',
            'offAddressLine4' => '',
            'offCity' => '',
            'offState' => '',
            'offCountry' => '',
            'offZip' => '',
            'telIsdCode' => '',
            'telAreaCode' => '',
            'telNumber' => '',
            'offTelIsdCode' => '',
            'offTelAreaCode' => '',
            'offTelNumber' => '',
            'mobileNumber' => '',
            'faxNumber' => '',
            'website' => '',
            'email' => '',
            'hasSubFunds' => '',
            'subFundsData' => [],
            'intermediatesData' => [],
            'controllersData' => [],
            'hasUbos' => '',
            'uboName' => '',
            'uboDob' => '',
            'uboNationality' => '',
            'uboPassport' => '',
            'uboOwnership' => '',
            'uboAddress' => '',
            'incomeRange' => '',
            'incomeSources' => [],
            'professionCode' => '',
            'grossIncome' => '',
            'occupation' => '',
            'netWorth' => '',
            'netWorthDate' => '',
            'taxCountry' => '',
            'tin' => '',
            'tinReason' => '',
            'tinExplanation' => '',
            'trcNumber' => '',
            'taxResidenciesData' => [],
            'fatcaCrs' => '',
            'isPep' => '',
            'relatedToPep' => '',
            'fpiCategory' => '',
            'subCategory' => '',
            'mimStructure' => '',
            'investmentManagersData' => [],
            'regulatoryStatus' => '',
            'regulatorName' => '',
            'licenseNumber' => '',
            'regulatorJurisdiction' => '',
            'regulatorCountry' => '',
            'regulatorWebsite' => '',
            'regulatorCapacity' => '',
            'complianceName' => '',
            'complianceTitle' => '',
            'complianceEmail' => '',
            'compliancePhone' => '',
            'complianceFax' => '',
            'hasCustodian' => '',
            'custodianNameCat' => '',
            'custodianReg' => '',
            'custRegCode' => '',
            'custodianAddress' => '',
            'disciplinaryHistory' => '',
            'disciplinaryDetails' => '',
            'clubbingDeclaration' => '',
            'fpiGroupNumber' => '',
            'groupNumber' => '',
            'fpiGroupData' => [],
            'publicRetailData' => [],
            'priorAssociation' => '',
            'priorAssociationsData' => [],
            'pan' => '',
            'bankName' => '',
            'bankAccount' => '',
            'bankAccountType' => '',
            'bankSwift' => '',
            'custodianName' => '',
            'dpId' => '',
            'clientId' => '',
            'hasPan' => '',
            'existingpanName' => '',
            'statusApplicant' => '',
            'panName' => '',
            'aoAreaCode' => '',
            'aoType' => '',
            'aoRangeCode' => '',
            'aoNo' => '',
            'registrationNumber' => '',
            'repTitle' => '',
            'repLastName' => '',
            'repFirstName' => '',
            'repMiddleName' => '',
            'repAddress' => '',
            'listed' => '',
            'exchangeName' => '',
            'poiType' => '',
            'poiNumber' => '',
            'poaType' => '',
            'poaNumber' => '',
            'sensitiveActivities' => [],
            'depositoryAuth' => '',
            'modeOfOperation' => '',
            'otherMode' => '',
            'bankAuth' => '',
            'primaryContactName' => '',
            'primaryContactDesignation' => '',
            'investmentManagerName' => '',
            'indiaPlaceOfBusiness' => '',
            'odiDerivatives' => '',
            'shareClassesData' => [],
            'categoryOneEntitiesData' => [],
            'bankDeclaration' => '',
            'bankEntityName' => '',
            'nriControl1' => '',
            'nriControl2' => '',
            'imTypes' => [],
            'directlyControlled' => '',
            'nriControlEntityName' => '',
            'offshoreFund' => '',
            'nriEntitlement' => '',
            'reg5b7' => '',
            'clientEligI' => false,
            'clientEligII' => false,
            'clientEligIII' => false,
            'clientsData' => [],
            'kraConsent' => '',
            'kraRepName' => '',
            'kraEmail1' => '',
            'kraEmail2' => '',
            'kraEmail3' => '',
            'kraMobile' => '',
            'signatoriesData' => [],
            'declarationAgreed' => false,
            'signatureName' => '',
            'declarationPlace' => '',
            'declarationDate' => '',
            'applicantName' => '',
            'applicantDesignation' => '',
            'authorizedName' => '',
            'authDesignation' => '',
            'authDate' => '',
            'declaration1' => false,
            'declaration2' => false,
            'declaration3' => false,
            'uploadedPoi' => '',
            'uploadedPoi_uri' => '',
            'uploadedPoa' => '',
            'uploadedPoa_uri' => '',
            'uploadedFatca' => '',
            'uploadedFatca_uri' => '',
            'uploadedSignature' => '',
            'uploadedSignature_uri' => '',
            'otherDocs' => [],
            'uploadedIncorpCert' => '',
            'uploadedLeiProof' => '',
            'uploadedPanCopy' => '',
            'uploadedUboDecl' => '',
            'uploadedIncorpCert_uri' => '',
            'uploadedLeiProof_uri' => '',
            'uploadedPanCopy_uri' => '',
            'uploadedUboDecl_uri' => '',
            'uboStructure' => '',
        ];
    }

    /** Landing page: choose the applicant kind (Individual / Non-Individual) before filling the form. */
    public function start()
    {
        return view('fpi.start', [
            'entityType' => session('caf_entity_type'),
        ]);
    }

    /** Handle the start page: record the chosen applicant kind, then open a fresh form. */
    public function begin(Request $request)
    {
        $request->validate(
            ['entityType' => ['required', Rule::in(['Individual', 'Non-Individual'])]],
            [
                'entityType.required' => 'Please select an applicant type to continue.',
                'entityType.in'       => 'Please select a valid applicant type.'
            ]
        );

        // Start a fresh application for the chosen kind.
        session()->forget('caf_applicant_id');
        session(['caf_entity_type' => $request->input('entityType')]);

        return redirect()->route($this->formRoute());
    }

    public function index()
    {
        // The form is only reachable after a kind has been chosen on the start page
        // (or when re-opening an existing application).
        if (!session('caf_entity_type') && !session('caf_applicant_id')) {
            return redirect()->route('fpi.start');
        }
        // Keep the URL consistent with the chosen applicant kind.
        if (\Illuminate\Support\Facades\Route::currentRouteName() !== $this->formRoute()) {
            return redirect()->route($this->formRoute());
        }

        $form = $this->defaultForm();

        // Pre-fetch the current draft applicant (if any) from the DB.
        $applicantId = session('caf_applicant_id');
        if ($applicantId) {
            $form = array_merge($form, $this->loadForm($applicantId));
        }

        // Submitted values take precedence so the form re-populates after a validation error.
        $form = array_merge($form, session()->getOldInput() ?: []);

        $countries = DB::table('m_countries')->where('is_active', true)
            ->orderBy('display_order')->get(['country_id', 'label_en']);

        $isdCodes = DB::table('m_countries')->where('is_active', true)->pluck('isd_code')->toArray();

        $activeSection = session('active_section', 'applicant');
        $savedSections = $this->savedSections($applicantId);

        $isSubmitted = false;
        if ($applicantId) {
            $status = DB::table('applicants')->where('applicant_id', $applicantId)->value('application_status');
            $isSubmitted = in_array($status, ['SUBMITTED', 'UNDER_REVIEW', 'APPROVED'], true);
        }

        // All applications, for the switcher (reopen any earlier one).
        $applications = DB::table('applicants')
            ->leftJoin('corporate_applicant_details', 'applicants.applicant_id', '=', 'corporate_applicant_details.applicant_id')
            ->orderByDesc('applicants.applicant_id')
            ->get(['applicants.applicant_id', 'applicants.application_status', 'corporate_applicant_details.company_name']);
        $currentApplicantId = $applicantId;

        // Beneficial-ownership rows for the current applicant (drives Tab 4 rows).
        $uboList = [];
        if ($applicantId) {
            $uboList = DB::table('ubo')->where('applicant_id', $applicantId)->orderBy('ubo_id')->get()->map(fn($u) => [
                'name'           => $u->full_name,
                'dob'            => $u->date_of_birth,
                'taxJurisdiction' => (string) ($u->tax_residency_country_id ?? ''),
                'nationality'    => (string) ($u->nationality_country_id ?? ''),
                'actingGroup'    => $u->acting_group_details ?? '',
                'passport'       => $u->id_document_number,
                'ownership'      => $u->shareholding_capital_pct !== null ? (string) $u->shareholding_capital_pct : '',
                'address'        => $u->residential_address,
            ])->values()->toArray();
        }

        $entityType = session('caf_entity_type', 'Non-Individual');

        return view('fpi.index', compact('form', 'countries', 'isdCodes', 'activeSection', 'savedSections', 'isSubmitted', 'applications', 'currentApplicantId', 'uboList', 'entityType'));
    }

    /** Start a fresh application (keeps the submitted record; just detaches the session draft). */
    public function newApplication()
    {
        session()->forget('caf_applicant_id');
        session()->forget('caf_entity_type');
        return redirect()->route('fpi.start');
    }

    /** Re-open an existing application (draft or submitted) by id. */
    public function load($applicant)
    {
        $row = DB::table('applicants')->where('applicant_id', $applicant)->first();
        if ($row) {
            session(['caf_applicant_id' => (int) $applicant]);
            // Sync the entity kind from the record so the URL + form match on reopen.
            session(['caf_entity_type' => $row->entity_type === 'Individual' ? 'Individual' : 'Non-Individual']);
            return redirect()->route($this->formRoute())
                ->with('status', 'Opened application FPI-' . str_pad($applicant, 6, '0', STR_PAD_LEFT) . '.');
        }
        return redirect()->route($this->formRoute())->withErrors(['submit' => 'Application not found.']);
    }

    /** Professional printable / PDF preview of the whole application. */
    public function preview()
    {
        $id = session('caf_applicant_id');
        if (!$id || !DB::table('applicants')->where('applicant_id', $id)->exists()) {
            return redirect()->route($this->formRoute())->withErrors(['submit' => 'No application to preview yet.']);
        }

        $countries = DB::table('m_countries')->pluck('label_en', 'country_id')->toArray();
        $catLabels = DB::table('m_fpi_categories')->pluck('label_en', 'category_code')->toArray();

        $p = [
            'app'         => DB::table('applicants')->where('applicant_id', $id)->first(),
            'corp'        => DB::table('corporate_applicant_details')->where('applicant_id', $id)->first(),
            'aliases'     => DB::table('applicant_aliases')->where('applicant_id', $id)->get(),
            'addresses'   => DB::table('applicant_addresses')->where('applicant_id', $id)->get(),
            'contacts'    => DB::table('applicant_contacts')->where('applicant_id', $id)->get(),
            'ubos'        => DB::table('ubo')->where('applicant_id', $id)->get(),
            'tax'         => DB::table('tax_residencies')->where('applicant_id', $id)->get(),
            'regulator'   => DB::table('applicant_foreign_regulators')->where('applicant_id', $id)->first(),
            'pan'         => DB::table('pan_additional_details')->where('applicant_id', $id)->first(),
            'bank'        => DB::table('depository_bank_accounts')->where('applicant_id', $id)->first(),
            'office'      => DB::table('office_verification')->where('applicant_id', $id)->first(),
            'custodian'   => DB::table('applicant_custodian_details')->where('applicant_id', $id)->first(),
            'ims'         => DB::table('investment_managers')->where('applicant_id', $id)->get(),
            'declaration' => DB::table('application_declaration')->where('applicant_id', $id)->first(),
            'docs'        => DB::table('kyc_documents')->join('m_document_types', 'kyc_documents.doc_type_id', '=', 'm_document_types.doc_type_id')
                ->where('kyc_documents.applicant_id', $id)->get(['m_document_types.label_en', 'kyc_documents.file_storage_uri']),
            'countries'   => $countries,
            'catLabels'   => $catLabels,
            'titleMap'    => $this->codeToTitle,
            'generatedAt' => now()->format('d M Y, H:i'),
        ];

        // UBO Determination: identified natural persons with effective ownership %.
        $tree = $p['app']->ubo_structure_json ? json_decode($p['app']->ubo_structure_json, true) : null;
        $p['uboPersons'] = $this->computeUboPersons($tree['entities'] ?? []);
        $p['uboThreshold'] = 10;

        // Everything captured in the flexible JSON stores (Steps 4–8 extras + BO tables).
        $extra = json_decode($p['app']->caf_extra_json ?? '{}', true) ?: [];
        $p['extra']       = $extra;
        $p['financialX']  = $extra['financial'] ?? [];
        $p['categoryX']   = $extra['category'] ?? [];
        $p['depositoryX'] = $extra['depository'] ?? [];
        $p['additionalX'] = $extra['additional'] ?? [];
        $p['declX']       = $extra['declarations'] ?? [];
        $p['indX']        = $extra['ind_applicant'] ?? [];
        $p['bo']          = json_decode($p['app']->beneficial_ownership_json ?? '{}', true) ?: [];
        $p['offContact']  = DB::table('applicant_contacts')->where('applicant_id', $id)->where('contact_type', 'Office')->first();

        return view('fpi.preview', $p);
    }

    /** Aggregate all natural persons in the ownership tree with their effective % (server mirror of the JS tool). */
    private function computeUboPersons(array $entities): array
    {
        $byId = [];
        foreach ($entities as $e) {
            $byId[$e['id'] ?? ''] = $e;
        }
        $results = [];
        $visited = [];
        $traverse = function ($entityId, $multiplier) use (&$traverse, $byId, &$results, &$visited) {
            if (!isset($byId[$entityId]) || !empty($visited[$entityId])) {
                return;
            }
            $visited[$entityId] = true;
            foreach ($byId[$entityId]['owners'] ?? [] as $owner) {
                $eff = ((float) ($owner['pct'] ?? 0) / 100) * $multiplier;
                if (($owner['type'] ?? '') === 'Individual') {
                    $nm = trim($owner['name'] ?? '');
                    if ($nm === '') {
                        continue;
                    }
                    $key = strtolower($nm);
                    if (isset($results[$key])) {
                        $results[$key]['effectivePct'] += $eff;
                    } else {
                        $results[$key] = ['name' => $nm, 'effectivePct' => $eff];
                    }
                } elseif (($owner['type'] ?? '') === 'Entity') {
                    $traverse($owner['targetId'] ?? '', $eff);
                }
            }
            unset($visited[$entityId]);
        };
        $traverse('applicant', 100);

        return array_values($results);
    }

    /** Final submission: validate completeness, mark SUBMITTED, log status history. */
    public function submit(Request $request)
    {
        $id = session('caf_applicant_id');
        if (!$id || !DB::table('applicants')->where('applicant_id', $id)->exists()) {
            $m = 'Please fill and save the form before submitting.';
            return $request->wantsJson()
                ? response()->json(['ok' => false, 'message' => $m], 422)
                : redirect()->route($this->formRoute())->withErrors(['submit' => $m]);
        }

        // Required sections must be saved before an application can be submitted.
        // Beneficial Ownership (ubo) applies to Non-Individual applicants only.
        $required = $this->isIndividual()
            ? ['applicant', 'contact', 'financial', 'category', 'depository', 'declarations']
            : ['applicant', 'contact', 'ubo', 'financial', 'category', 'depository', 'declarations'];
        $done = DB::table('application_section_progress')->where('applicant_id', $id)
            ->where('is_complete', 1)->pluck('section_code')->toArray();
        $missing = array_diff($required, $done);
        if ($missing) {
            $labels = [
                'applicant' => 'Applicant Profile',
                'contact' => 'Contact & Address',
                'ubo' => 'Beneficial Ownership',
                'financial' => 'Financial & Tax',
                'category' => 'Category & Regulatory',
                'depository' => 'PAN, Bank & Depository',
                'declarations' => 'Final Declarations',
            ];
            $names = implode(', ', array_map(fn($s) => $labels[$s] ?? $s, $missing));
            $m = "Please complete & save these sections before submitting: {$names}.";
            return $request->wantsJson()
                ? response()->json(['ok' => false, 'message' => $m], 422)
                : redirect()->route($this->formRoute())->with('active_section', reset($missing))->withErrors(['submit' => $m]);
        }

        $current = DB::table('applicants')->where('applicant_id', $id)->value('application_status');
        DB::table('applicants')->where('applicant_id', $id)->update(['application_status' => 'SUBMITTED']);
        DB::table('application_status_history')->insert([
            'applicant_id' => $id,
            'from_status'  => $current,
            'to_status'    => 'SUBMITTED',
            'remarks'      => 'Application submitted by applicant.',
        ]);

        $m = 'Application submitted successfully. You can now Print / Preview your application.';
        if ($request->wantsJson()) {
            session()->flash('active_section', 'declarations'); // reopen final tab on the client's reload
            return response()->json(['ok' => true, 'message' => $m]);
        }
        return redirect()->route($this->formRoute())->with('status', $m)->with('active_section', 'declarations');
    }

    /** Section order for the wizard flow (Non-Individual). */
    private array $order = ['applicant', 'contact', 'ubo_tool', 'ubo', 'financial', 'category', 'depository', 'additional', 'declarations'];

    /** Individual applicants skip the UBO determination + beneficial-ownership steps. */
    private array $orderIndividual = ['applicant', 'contact', 'financial', 'category', 'depository', 'additional', 'declarations'];

    /** Is the current draft an Individual applicant? */
    private function isIndividual(): bool
    {
        return session('caf_entity_type') === 'Individual';
    }

    /** The type-specific route name for the form (URL reflects applicant kind). */
    private function formRoute(): string
    {
        return $this->isIndividual() ? 'fpi.individual' : 'fpi.non-individual';
    }

    /** The active section order for the current applicant kind. */
    private function order(): array
    {
        return $this->isIndividual() ? $this->orderIndividual : $this->order;
    }

    private function isdCodes(): array
    {
        return DB::table('m_countries')->where('is_active', true)->pluck('isd_code')->map(fn($c) => (string) $c)->toArray();
    }

    /** Sections marked complete in application_section_progress (drives the green tab check). */
    private function savedSections($applicantId): array
    {
        if (!$applicantId) {
            return [];
        }
        return DB::table('application_section_progress')
            ->where('applicant_id', $applicantId)->where('is_complete', 1)
            ->pluck('section_code')->toArray();
    }

    /** Read the section-keyed extra-fields JSON document for an applicant. */
    private function loadExtra($applicantId): array
    {
        $raw = DB::table('applicants')->where('applicant_id', $applicantId)->value('caf_extra_json');
        $data = $raw ? json_decode($raw, true) : [];
        return is_array($data) ? $data : [];
    }

    /** Merge one section's slice into the extra-fields JSON document (read-modify-write). */
    private function saveExtra(int $id, string $section, array $data): void
    {
        $all = $this->loadExtra($id);
        $all[$section] = $data;
        DB::table('applicants')->where('applicant_id', $id)->update(['caf_extra_json' => json_encode($all)]);
    }

    /** Decode a JSON array field from the request (returns [] on anything invalid). */
    private function jsonArray(Request $request, string $field): array
    {
        $v = json_decode($request->input($field) ?? '[]', true);
        return is_array($v) ? array_values($v) : [];
    }

    /** Validation closure: every row present in a JSON table must have all the given columns filled. */
    private function allRowsFilled(array $keys, string $label): \Closure
    {
        return function ($attr, $value, $fail) use ($keys, $label) {
            foreach ((json_decode($value ?? '[]', true) ?: []) as $i => $row) {
                foreach ($keys as $k) {
                    if (trim((string) ($row[$k] ?? '')) === '') {
                        $fail("Please complete all columns for every {$label} row (row " . ($i + 1) . ').');
                        return;
                    }
                }
            }
        };
    }

    /** Load a draft applicant's stored values back into form-field keys. */
    private function loadForm($applicantId): array
    {
        $out = [];
        $app = DB::table('applicants')->where('applicant_id', $applicantId)->first();

        // Tab 1 — Applicant Profile
        $corp = DB::table('corporate_applicant_details')->where('applicant_id', $applicantId)->first();
        if ($corp) {
            $out['nameTitle'] = $this->codeToTitle[$corp->name_title_code] ?? '';
            $out['entityName'] = $corp->company_name;
            $out['applicantType'] = $corp->applicant_legal_type ?? '';
            $out['dateOfIncorporation'] = $corp->date_of_incorporation;
            $out['dateOfCommencementOfBusiness'] = $corp->date_commence_business;
            $out['placeOfIncorporation'] = $corp->place_of_incorporation;
            $out['countryOfIncorporation'] = (string) $corp->incorporation_country_id;
            $out['incorporationIsdCode'] = $corp->incorporation_isd_code ?? '';
            $out['lei'] = $corp->lei_number;
            $out['leiExpiryDate'] = $corp->lei_expiry_date;
            $out['indiaPlaceOfBusiness'] = $corp->india_place_of_business ?? '';
        }
        $alias = DB::table('applicant_aliases')->where('applicant_id', $applicantId)->first();
        if ($alias) {
            $out['knownByAnotherName'] = 'YES';
            $out['otherTitle'] = $this->codeToTitle[$alias->alias_title_code] ?? '';
            $out['otherEntityName'] = $alias->alias_last_name_or_company;
        } elseif ($corp) {
            $out['knownByAnotherName'] = 'NO'; // applicant saved, no alias -> keep the "No" choice
        }

        // Tab 2 — Contact & Address
        $addrs = DB::table('applicant_addresses')->where('applicant_id', $applicantId)
            ->where('address_type', 'Registered_Residence')->orderBy('address_id')->get();
        if ($addrs->count()) {
            $reg = $addrs[0];
            $out['regAddressLine1'] = $reg->flat_room_block;
            $out['regAddressLine2'] = $reg->premises_building;
            $out['regAddressLine3'] = $reg->road_street_lane;
            $out['regAddressLine4'] = $reg->area_locality_taluka;
            $out['regCity'] = $reg->town_city_district;
            $out['regState'] = $reg->state_union_territory;
            $out['regZip'] = $reg->pin_zip_code;
            $out['regCountry'] = (string) $reg->country_id;
            if ($addrs->count() > 1) {
                $comm = $addrs[1];
                $out['sameAddress'] = false;
                $out['commAddressLine1'] = $comm->flat_room_block;
                $out['commAddressLine2'] = $comm->premises_building;
                $out['commAddressLine3'] = $comm->road_street_lane;
                $out['commAddressLine4'] = $comm->area_locality_taluka;
                $out['commCity'] = $comm->town_city_district;
                $out['commState'] = $comm->state_union_territory;
                $out['commZip'] = $comm->pin_zip_code;
                $out['commCountry'] = (string) $comm->country_id;
            } else {
                $out['sameAddress'] = (bool) $reg->is_communication_dest;
            }
        }
        $office = DB::table('applicant_addresses')->where('applicant_id', $applicantId)->where('address_type', 'Office')->orderBy('address_id')->first();
        if ($office) {
            $out['offAddressLine1'] = $office->flat_room_block;
            $out['offAddressLine2'] = $office->premises_building;
            $out['offAddressLine3'] = $office->road_street_lane;
            $out['offAddressLine4'] = $office->area_locality_taluka;
            $out['offCity'] = $office->town_city_district;
            $out['offState'] = $office->state_union_territory;
            $out['offZip'] = $office->pin_zip_code;
            $out['offCountry'] = (string) $office->country_id;
        }
        $resContact = DB::table('applicant_contacts')->where('applicant_id', $applicantId)->where('contact_type', 'Residence')->first();
        if ($resContact) {
            $out['telIsdCode'] = $resContact->tel_isd_code;
            $out['telAreaCode'] = $resContact->tel_std_area_code;
            $out['telNumber'] = $resContact->telephone_number;
            $out['mobileNumber'] = $resContact->mobile_number;
            $out['faxNumber'] = $resContact->fax_number;
            $out['website'] = $resContact->website;
            $out['email'] = $resContact->email_id;
        }
        $offContact = DB::table('applicant_contacts')->where('applicant_id', $applicantId)->where('contact_type', 'Office')->first();
        if ($offContact) {
            $out['offTelIsdCode'] = $offContact->tel_isd_code;
            $out['offTelAreaCode'] = $offContact->tel_std_area_code;
            $out['offTelNumber'] = $offContact->telephone_number;
        }

        // Tab 4 — Beneficial Ownership (rows themselves are passed as $uboList)
        if (DB::table('ubo')->where('applicant_id', $applicantId)->exists()) {
            $out['hasUbos'] = 'YES';
        } elseif (DB::table('application_section_progress')->where('applicant_id', $applicantId)->where('section_code', 'ubo')->where('is_complete', 1)->exists()) {
            $out['hasUbos'] = 'NO'; // section saved with "No" -> keep the choice
        }

        // Tab 3 — UBO Determination tool (ownership tree JSON)
        if ($app && $app->ubo_structure_json) {
            $out['uboStructure'] = $app->ubo_structure_json;
        }

        // Tab 4 — Sub-funds, intermediate & controlling entities (Step 3 JSON doc)
        if ($app && $app->beneficial_ownership_json) {
            $bo = json_decode($app->beneficial_ownership_json, true);
            if (is_array($bo)) {
                $out['hasSubFunds']       = $bo['hasSubFunds'] ?? '';
                $out['subFundsData']      = $bo['subFunds'] ?? [];
                $out['intermediatesData'] = $bo['intermediates'] ?? [];
                $out['controllersData']   = $bo['controllers'] ?? [];
            }
        }

        // Tab 5 — Financial & Tax
        if ($app) {
            $out['netWorth'] = $app->net_worth_inr;
            $out['netWorthDate'] = $app->net_worth_date;
            $out['incomeRange'] = $app->gross_annual_income_band ?? '';
            $out['fpiCategory'] = $app->fpi_category_code ?? '';
            if ($app->fpi_category_code !== null) {
                $out['regulatoryStatus'] = $app->is_regulated_fpi ? 'REGULATED' : 'UNREGULATED';
            }
        }
        $tax = DB::table('tax_residencies')->where('applicant_id', $applicantId)->first();
        if ($tax) {
            $out['taxCountry'] = (string) $tax->country_id;
            $out['tin'] = $tax->trc_number;
        }

        // Tab 6 — Category & Regulatory
        $reg = DB::table('applicant_foreign_regulators')->where('applicant_id', $applicantId)->first();
        if ($reg) {
            $out['regulatorName'] = $reg->regulatory_authority_name;
            $out['licenseNumber'] = $reg->regulatory_registration_no;
            $out['regulatorJurisdiction'] = (string) ($reg->regulatory_country_id ?? '');
        }

        // Tab 7 — PAN, Bank & Depository
        $pan = DB::table('pan_additional_details')->where('applicant_id', $applicantId)->first();
        if ($pan) {
            $out['pan'] = $pan->existing_pan;
        }
        $bank = DB::table('depository_bank_accounts')->where('applicant_id', $applicantId)->first();
        if ($bank) {
            $out['bankName'] = $bank->ad_category_1_bank_name;
            $out['bankSwift'] = $bank->bank_swift_ifsc;
        }
        $office = DB::table('office_verification')->where('applicant_id', $applicantId)->first();
        if ($office) {
            $out['bankAccount'] = $office->bank_account_number;
            $out['bankAccountType'] = $office->bank_account_type;
            $out['dpId'] = $office->dp_id;
            $out['clientId'] = $office->client_id;
        }
        $cust = DB::table('applicant_custodian_details')->where('applicant_id', $applicantId)->first();
        if ($cust) {
            $out['custodianName'] = $cust->global_custodian_name;
        }

        // Tab 8 — Additional Info
        $compContact = DB::table('applicant_contacts')->where('applicant_id', $applicantId)->where('contact_type', 'Compliance')->first();
        if ($compContact) {
            $out['primaryContactName'] = $compContact->officer_name;
            $out['primaryContactDesignation'] = $compContact->job_title;
        }
        $im = DB::table('investment_managers')->where('applicant_id', $applicantId)->first();
        if ($im) {
            $out['investmentManagerName'] = $im->manager_name;
        }

        // Tab 9 — Declarations
        $decl = DB::table('application_declaration')->where('applicant_id', $applicantId)->first();
        if ($decl) {
            $out['signatureName'] = $decl->authorized_signatory_name;
            $out['declarationAgreed'] = true; // saved once -> keep the checkbox ticked
        }
        // Show previously-uploaded document filenames in the tiles.
        $docFieldByCode = ['INCORP' => 'uploadedIncorpCert', 'LEIPROOF' => 'uploadedLeiProof', 'PANCOPY' => 'uploadedPanCopy', 'UBODECL' => 'uploadedUboDecl'];
        $docs = DB::table('kyc_documents')
            ->join('m_document_types', 'kyc_documents.doc_type_id', '=', 'm_document_types.doc_type_id')
            ->where('kyc_documents.applicant_id', $applicantId)
            ->get(['m_document_types.code', 'kyc_documents.file_storage_uri']);
        foreach ($docs as $d) {
            if (isset($docFieldByCode[$d->code])) {
                $out[$docFieldByCode[$d->code]] = basename($d->file_storage_uri);
                $out[$docFieldByCode[$d->code] . '_uri'] = $d->file_storage_uri; // already public-relative
            }
        }

        // Merge the section-keyed extra-fields document (Steps 4–8) back into form keys.
        foreach ($this->loadExtra($applicantId) as $slice) {
            if (is_array($slice)) {
                foreach ($slice as $k => $v) {
                    $out[$k] = $v;
                }
            }
        }

        // Drop nulls so form defaults ('') apply cleanly.
        return array_filter($out, fn($v) => $v !== null);
    }

    /** Validation rules grouped per section (tab). */
    private function sectionRules(): array
    {
        $rules = [
            'applicant' => [
                'nameTitle'                     => ['required', Rule::in(['M/S', 'MR', 'MRS', 'MS'])],
                'entityName'                    => ['required', 'string', 'max:200'],
                'applicantType'                 => ['required', Rule::in(['Partnership', 'Company', 'Trust', 'Unincorporated Association / Body of Individuals'])],
                'knownByAnotherName'            => ['required', Rule::in(['YES', 'NO'])],
                'otherTitle'                    => ['nullable', Rule::in(['M/S', 'MR', 'MRS', ''])],
                'otherEntityName'               => ['nullable', 'required_if:knownByAnotherName,YES', 'string', 'max:200'],
                'dateOfIncorporation'           => ['required', 'date', 'before_or_equal:today'],
                'dateOfCommencementOfBusiness'  => ['nullable', 'date', 'before_or_equal:today'],
                'placeOfIncorporation'          => ['required', 'string', 'max:100'],
                'countryOfIncorporation'        => ['required', 'integer', Rule::exists('m_countries', 'country_id')],
                'incorporationIsdCode'          => ['nullable', 'string', 'max:5'],
                'lei'                           => ['nullable', 'string', 'size:20', $this->leiRule()],
                'leiExpiryDate'                 => ['nullable', 'date'],
            ],
            'contact' => [
                // Registered address — all required
                'regAddressLine1' => ['required', 'string', 'max:150'],
                'regAddressLine2' => ['required', 'string', 'max:150'],
                'regAddressLine3' => ['required', 'string', 'max:150'],
                'regAddressLine4' => ['nullable', 'string', 'max:100'],
                'regCity'         => ['required', 'string', 'max:100'],
                'regState'        => ['required', 'string', 'max:100'],
                'regCountry'      => ['required', 'integer', Rule::exists('m_countries', 'country_id')],
                'regZip'          => ['required', 'string', 'max:20'],
                'sameAddress'     => ['nullable'],
                // Correspondence address — required only when NOT "same as registered"
                'commAddressLine1' => ['required_without:sameAddress', 'nullable', 'string', 'max:150'],
                'commAddressLine2' => ['required_without:sameAddress', 'nullable', 'string', 'max:150'],
                'commAddressLine3' => ['required_without:sameAddress', 'nullable', 'string', 'max:150'],
                'commAddressLine4' => ['nullable', 'string', 'max:100'],
                'commCity'        => ['required_without:sameAddress', 'nullable', 'string', 'max:100'],
                'commState'       => ['required_without:sameAddress', 'nullable', 'string', 'max:100'],
                'commCountry'     => ['required_without:sameAddress', 'nullable', 'integer', Rule::exists('m_countries', 'country_id')],
                'commZip'         => ['required_without:sameAddress', 'nullable', 'string', 'max:20'],
                // Office address — mandatory except lines 3 & 4
                'offAddressLine1' => ['required', 'string', 'max:150'],
                'offAddressLine2' => ['required', 'string', 'max:150'],
                'offAddressLine3' => ['nullable', 'string', 'max:150'],
                'offAddressLine4' => ['nullable', 'string', 'max:100'],
                'offCity'         => ['required', 'string', 'max:100'],
                'offState'        => ['required', 'string', 'max:100'],
                'offCountry'      => ['required', 'integer', Rule::exists('m_countries', 'country_id')],
                'offZip'          => ['required', 'string', 'max:20'],
                // Contact — registered phone (all 3) + mobile + email required
                'telIsdCode'      => ['required', 'string', 'max:5'],
                'telAreaCode'     => ['required', 'string', 'max:10'],
                'telNumber'       => ['required', 'string', 'max:20'],
                'offTelIsdCode'   => ['nullable', 'string', 'max:5'],
                'offTelAreaCode'  => ['nullable', 'string', 'max:10'],
                'offTelNumber'    => ['nullable', 'string', 'max:20'],
                'mobileNumber'    => ['required', 'string', 'max:20', $this->mobileRule()],
                'faxNumber'       => ['nullable', 'string', 'max:20'],
                'website'         => ['nullable', 'string', 'max:150'],
                'email'           => ['required', 'email', 'max:100'],
            ],
            'ubo' => [
                'hasSubFunds'      => ['required', Rule::in(['YES', 'NO'])],
                'subFundsJson'     => ['nullable', 'string'],
                // Any Intermediate/Controlling entity row added must have all its columns filled.
                'intermediatesJson' => ['nullable', 'string', $this->allRowsFilled(['name', 'stakeType', 'chain', 'country', 'pct', 'type'], 'Intermediate Entity')],
                'controllersJson'  => ['nullable', 'string', $this->allRowsFilled(['name', 'method', 'country', 'pct', 'type'], 'Controlling Entity')],
                'hasUbos'          => ['required', Rule::in(['YES', 'NO'])],
                'uboRowsJson'      => ['nullable', 'string'],
            ],
            'financial' => [
                // Everything on this tab is mandatory except the Net Worth Date.
                'incomeRange'  => ['required', Rule::in(['UNDER_50K', '50K_250K', '250K_1M', 'ABOVE_1M'])],
                'incomeSourcesJson' => ['nullable', 'string'],
                'grossIncome'  => ['nullable', 'numeric', 'min:0'],
                'occupation'   => ['nullable', 'string', 'max:40'],
                'professionCode' => ['nullable', 'string', 'max:2'],
                'netWorth'     => ['required', 'numeric', 'min:0'],
                'netWorthDate' => ['nullable', 'date'],
                'taxCountry'   => ['required', 'string', 'max:100'],
                'tin'          => ['required', 'string', 'max:50'],
                'tinReason'    => ['nullable', 'string', 'max:40'],
                'tinExplanation' => ['nullable', 'string', 'max:200'],
                'trcNumber'    => ['nullable', 'string', 'max:50'],
                'fatcaCrs'     => ['nullable', 'string', 'max:20'],
                'isPep'        => ['nullable', 'string', 'max:5'],
                'relatedToPep' => ['nullable', 'string', 'max:5'],
                // Any additional tax-residency row added must have Country + TIN.
                'taxResidenciesJson' => ['nullable', 'string', $this->allRowsFilled(['country', 'tin'], 'Tax Residency')],
            ],
            'category' => [
                'fpiCategory'           => ['required', Rule::in(['CAT_I', 'CAT_II'])],
                'regulatoryStatus'      => ['required', Rule::in(['REGULATED', 'UNREGULATED'])],
                'regulatorName'         => ['nullable', 'string', 'max:150'],
                'licenseNumber'         => ['nullable', 'string', 'max:100'],
                'regulatorJurisdiction' => ['nullable', 'string', 'max:100'],
                // Any row added to these tables must have its key columns filled.
                'investmentManagersJson' => ['nullable', 'string', $this->allRowsFilled(['name', 'sebiReg'], 'Investment Manager')],
                'publicRetailJson'       => ['nullable', 'string', $this->allRowsFilled(['fpiName', 'fpiRegNo'], 'Public Retail Fund')],
                'fpiGroupJson'           => ['nullable', 'string', $this->allRowsFilled(['fpiName', 'fpiRegNo'], 'Investor Group')],
                'priorAssociationsJson'  => ['nullable', 'string', $this->allRowsFilled(['entity', 'associationType'], 'Prior Association')],
            ],
            'depository' => [
                'pan'             => ['nullable', 'required_if:hasPan,yes', 'string', 'size:10', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/'],
                'existingpanName' => ['nullable', 'required_if:hasPan,yes', 'string', 'max:150'],
                'bankName'        => ['required', 'string', 'max:150'],
                'bankAccount'     => ['required', 'string', 'max:30'],
                'bankAccountType' => ['required', Rule::in(['NRE', 'NRO', 'ESCROW'])],
                'bankSwift'       => ['nullable', 'string', 'max:20'],
                'custodianName'   => ['nullable', 'string', 'max:200'],
                'dpId'            => ['nullable', 'string', 'max:20'],
                'clientId'        => ['nullable', 'string', 'max:20'],
            ],
            'additional' => [
                'primaryContactName'        => ['required', 'string', 'max:150'],
                'primaryContactDesignation' => ['required', 'string', 'max:100'],
                'investmentManagerName'     => ['nullable', 'string', 'max:150'],
                'indiaPlaceOfBusiness'      => ['nullable', 'string', 'max:200'],
                // Any row added to these tables must have every column filled.
                'shareClassesJson'        => ['nullable', 'string', $this->allRowsFilled(['name'], 'Sub-Fund / Share Class')],
                'categoryOneEntitiesJson' => ['nullable', 'string', $this->allRowsFilled(['name', 'country', 'entityType'], 'Eligible Category I Entity')],
                'clientsJson'             => ['nullable', 'string', $this->allRowsFilled(['name', 'country', 'address', 'type'], 'Client')],
                'signatoriesJson'         => ['nullable', 'string', $this->allRowsFilled(['name', 'relationship', 'pan', 'nationality', 'dob', 'address', 'govId'], 'Authorized Signatory')],
            ],
            'declarations' => [
                'uploadedIncorpCert' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
                'uploadedLeiProof'   => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
                'uploadedPanCopy'    => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
                'uploadedUboDecl'    => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
                'declarationAgreed'  => ['accepted'],
                'signatureName'      => ['required', 'string', 'max:200'],
                // Place / date / applicant + signatory details and all 3 declaration checkboxes.
                'declarationPlace'     => ['required', 'string', 'max:100'],
                'declarationDate'      => ['required', 'date'],
                'applicantName'        => ['required', 'string', 'max:200'],
                'applicantDesignation' => ['required', 'string', 'max:150'],
                'authDesignation'      => ['required', 'string', 'max:150'],
                'authDate'             => ['required', 'date'],
                'declaration1'         => ['accepted'],
                'declaration2'         => ['accepted'],
                'declaration3'         => ['accepted'],
            ],
        ];

        // Individual applicants use a personal-details Step 1 instead of the corporate one.
        if ($this->isIndividual()) {
            $rules['applicant'] = [
                'indTitle'        => ['required', Rule::in(['Mr', 'Mrs', 'Ms'])],
                'indFirstName'    => ['required', 'string', 'max:100'],
                'indMiddleName'   => ['nullable', 'string', 'max:100'],
                'indLastName'     => ['required', 'string', 'max:100'],
                'indOtherName'    => ['required', Rule::in(['yes', 'no'])],
                'indOtherTitle'     => ['nullable', 'required_if:indOtherName,yes', Rule::in(['Mr', 'Mrs', 'Ms', ''])],
                'indOtherFirstName' => ['nullable', 'required_if:indOtherName,yes', 'string', 'max:100'],
                'indOtherLastName'  => ['nullable', 'required_if:indOtherName,yes', 'string', 'max:100'],
                'indDob'          => ['required', 'date', 'before_or_equal:today'],
                'indPlaceOfBirth' => ['required', 'string', 'max:100'],
                'indCountryOfBirth' => ['required', 'string', 'max:100'],
                'indNationality'  => ['required', 'string', 'max:100'],
                'indPassport'     => ['nullable', 'string', 'max:50'],
                'indGender'       => ['required', 'string', 'max:20'],
                'indMaritalStatus' => ['required', 'string', 'max:20'],
                'indCitizenshipStatus' => ['required', 'string', 'max:40'],
                'indCountryOfCitizenship' => ['required', 'string', 'max:100'],
                'indFatherFirstName' => ['required', 'string', 'max:100'],
                'indFatherLastName'  => ['required', 'string', 'max:100'],
                'indMotherFirstName' => ['required', 'string', 'max:100'],
                'indMotherLastName'  => ['required', 'string', 'max:100'],
                'indSpouseFirstName' => ['required', 'string', 'max:100'],
                'indSpouseLastName'  => ['required', 'string', 'max:100'],
            ];

            // Individual category tab: regulator, compliance officer (except fax),
            // custodian / disciplinary / prior-association questions are mandatory;
            // the Investment-Manager and Public-Retail table rows must be complete
            // whenever a row has been added.
            $rules['category'] = array_merge($rules['category'], [
                'regulatorName'         => ['required', 'string', 'max:150'],
                'licenseNumber'         => ['required', 'string', 'max:100'],
                'regulatorJurisdiction' => ['required', 'string', 'max:100'],
                'complianceName'        => ['required', 'string', 'max:150'],
                'complianceTitle'       => ['required', 'string', 'max:100'],
                'complianceEmail'       => ['required', 'email', 'max:100'],
                'compliancePhone'       => ['required', 'string', 'max:30'],
                'hasCustodian'          => ['required', Rule::in(['yes', 'no'])],
                'disciplinaryHistory'   => ['required', Rule::in(['yes', 'no'])],
                'priorAssociation'      => ['required', Rule::in(['yes', 'no'])],
            ]);

            // Individual financial tab: gross income, occupation and at least one
            // source of income are mandatory.
            $rules['financial'] = array_merge($rules['financial'], [
                'grossIncome' => ['required', 'numeric', 'min:0'],
                'occupation'  => ['required', 'string', 'max:40'],
                'incomeSourcesJson' => ['required', 'string', function ($attr, $value, $fail) {
                    $arr = json_decode($value ?? '[]', true);
                    if (!is_array($arr) || count($arr) === 0) {
                        $fail('Please select at least one source of income.');
                    }
                }],
            ]);


            // Individual depository tab: SWIFT/IFSC, custodian & DP details, and the
            // PAN question are mandatory; when a PAN is held, both PAN fields too.
            $rules['depository'] = array_merge($rules['depository'], [
                'bankSwift'       => ['required', 'string', 'max:20'],
                'custodianName'   => ['required', 'string', 'max:200'],
                'dpId'            => ['required', 'string', 'max:20'],
                'clientId'        => ['required', 'string', 'max:20'],
                'hasPan'          => ['required', Rule::in(['yes', 'no'])],
            ]);
        }

        return $rules;
    }

    /** Mobile number must start with one of our countries' ISD codes and have a valid length. */
    private function mobileRule(): \Closure
    {
        return function ($attribute, $value, $fail) {
            $digits = preg_replace('/\D/', '', (string) $value);
            if ($digits === '') {
                return;
            }
            $codes = $this->isdCodes();
            usort($codes, fn($a, $b) => strlen($b) - strlen($a)); // longest prefix first
            $match = null;
            foreach ($codes as $c) {
                if (str_starts_with($digits, $c)) {
                    $match = $c;
                    break;
                }
            }
            if ($match === null) {
                $fail('Mobile number must start with a valid country dialing code (' . implode(', ', array_map(fn($c) => "+$c", $this->isdCodes())) . ').');
                return;
            }
            $rest = substr($digits, strlen($match));
            if (strlen($rest) < 6 || strlen($rest) > 12) {
                $fail('Mobile number has an invalid length for the selected country code.');
            }
        };
    }

    /** ISO 17442 LEI: 20 chars, positions 5-6 = "00", 19-20 numeric, ISO 7064 mod-97-10 checksum. */
    private function leiRule(): \Closure
    {
        return function ($attribute, $value, $fail) {
            $v = strtoupper(trim((string) $value));
            if ($v === '') {
                return;
            }
            if (!preg_match('/^[A-Z0-9]{18}[0-9]{2}$/', $v)) {
                $fail('LEI must be 20 characters: 18 alphanumeric + 2 numeric check digits.');
                return;
            }
            if (substr($v, 4, 2) !== '00') {
                $fail('LEI is invalid: characters 5-6 must be "00" (ISO 17442).');
                return;
            }
            if (!$this->leiChecksumValid($v)) {
                $fail('LEI checksum is invalid (failed the ISO 17442 / mod-97 check).');
            }
        };
    }

    /** ISO 7064 MOD 97-10: letters A-Z -> 10-35, whole number mod 97 must equal 1. */
    private function leiChecksumValid(string $lei): bool
    {
        $digits = '';
        for ($i = 0, $n = strlen($lei); $i < $n; $i++) {
            $c = $lei[$i];
            $digits .= ctype_alpha($c) ? (string) (ord($c) - 55) : $c;
        }
        $rem = 0;
        for ($i = 0, $n = strlen($digits); $i < $n; $i++) {
            $rem = ($rem * 10 + (int) $digits[$i]) % 97;
        }
        return $rem === 1;
    }

    private function messages(): array
    {
        return [
            'pan.regex'                   => 'PAN must be 5 letters, 4 digits, then 1 letter (e.g. AAACG1234F).',
            'pan.size'                    => 'PAN must be exactly 10 characters.',
            'lei.regex'                   => 'LEI must be 20 alphanumeric characters.',
            'lei.size'                    => 'LEI must be exactly 20 characters.',
            'countryOfIncorporation.required' => 'Please select the country of incorporation.',
            'countryOfIncorporation.exists'   => 'Please select a valid country.',
            'dateOfIncorporation.before_or_equal'          => 'Date of Incorporation cannot be in the future.',
            'dateOfCommencementOfBusiness.before_or_equal' => 'Date of Commencement of Business cannot be in the future.',
            'uboDob.before_or_equal'      => 'Date of Birth cannot be in the future.',
            'declarationAgreed.accepted'  => 'You must agree to the declaration before saving.',
            'declaration1.accepted'       => 'Please tick this declaration.',
            'declaration2.accepted'       => 'Please tick this declaration.',
            'declaration3.accepted'       => 'Please tick this declaration.',
            'uboOwnership.between'        => 'Ownership % must be between 0 and 100.',
            'uploadedIncorpCert.required' => 'Certificate of Incorporation is required.',
            'uploadedPanCopy.required'    => 'Copy of Indian PAN Card is required.',
            '*.required_if'               => 'This field is required based on your earlier selection.',
        ];
    }

    public function store(Request $request)
    {
        $section = $request->input('section', 'applicant');

        // Normalise PAN/LEI to uppercase before validating.
        $request->merge([
            'pan' => strtoupper((string) $request->input('pan')),
            'lei' => $request->filled('lei') ? strtoupper((string) $request->input('lei')) : $request->input('lei'),
        ]);

        $autofill = $request->boolean('_autofill');

        // Validate ONLY the submitted section's fields (tab-wise).
        $rules = $this->sectionRules()[$section] ?? [];
        if ($section === 'declarations') {
            $rules = $this->relaxUploadedDocRules($rules);
            if ($autofill) {
                // Auto-fill can't attach real files; documents are seeded below.
                foreach (['uploadedIncorpCert', 'uploadedPanCopy'] as $f) {
                    $rules[$f] = ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'];
                }
            }
        }
        $request->validate($rules, $this->messages());

        // Beneficial Ownership: validate each UBO row (when the entity has UBOs).
        if ($section === 'ubo' && $request->input('hasUbos') === 'YES') {
            $err = $this->validateUboRows($request->input('uboRowsJson'));
            if ($err) {
                if ($request->wantsJson()) {
                    return response()->json(['ok' => false, 'message' => $err, 'errors' => ['uboRowsJson' => [$err]]], 422);
                }
                return back()->withErrors(['uboRowsJson' => $err])->with('active_section', 'ubo');
            }
        }

        $labels = [
            'applicant' => 'Applicant Profile',
            'contact' => 'Contact & Address',
            'ubo_tool' => 'UBO Determination',
            'ubo' => 'Beneficial Ownership',
            'financial' => 'Financial & Tax',
            'category' => 'Category & Regulatory',
            'depository' => 'PAN, Bank & Depository',
            'additional' => 'Additional Info',
            'declarations' => 'Final Declarations',
        ];

        // Every section (incl. the client-side UBO tool) creates/uses the draft
        // applicant and is recorded in application_section_progress for the ✓.
        $applicantId = $this->getOrCreateApplicantId();
        $method = 'save' . ucfirst($section) . 'Section';
        if (method_exists($this, $method)) {
            $this->{$method}($request, $applicantId);
        }
        if ($section === 'declarations' && $autofill) {
            $this->seedPlaceholderDocs($applicantId);
        }
        DB::table('application_section_progress')->updateOrInsert(
            ['applicant_id' => $applicantId, 'section_code' => $section],
            ['is_complete' => 1]
        );

        // Advance to the next tab on a successful save.
        $order = $this->order();
        $idx = array_search($section, $order, true);
        $next = ($idx !== false && isset($order[$idx + 1])) ? $order[$idx + 1] : $section;
        $msg = ($labels[$section] ?? 'Section') . ' saved successfully.';

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'section' => $section, 'next' => $next, 'message' => $msg]);
        }

        return redirect()->route($this->formRoute())->with('status', $msg)->with('active_section', $next);
    }

    /** Create small placeholder PDF documents (used by auto-fill only). */
    private function seedPlaceholderDocs(int $id): void
    {
        $pdf = "%PDF-1.4\n1 0 obj<</Type/Catalog>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF";
        foreach (['INCORP' => 'sample-incorporation.pdf', 'PANCOPY' => 'sample-pan.pdf'] as $code => $fname) {
            $type = DB::table('m_document_types')->where('code', $code)->first();
            if (!$type || DB::table('kyc_documents')->where('applicant_id', $id)->where('doc_type_id', $type->doc_type_id)->exists()) {
                continue;
            }
            $dir = public_path("uploads/kyc/{$id}/{$code}");
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            file_put_contents("{$dir}/{$fname}", $pdf);
            $path = "uploads/kyc/{$id}/{$code}/{$fname}";
            DB::table('kyc_documents')->insert([
                'applicant_id' => $id,
                'doc_type_id' => $type->doc_type_id,
                'document_purpose' => $type->purpose,
                'file_storage_uri' => $path,
                'is_verified' => 0,
            ]);
        }
    }

    /** For an already-uploaded mandatory document, drop the "required" rule. */
    private function relaxUploadedDocRules(array $rules): array
    {
        $id = session('caf_applicant_id');
        if (!$id) {
            return $rules;
        }
        $have = DB::table('kyc_documents')
            ->join('m_document_types', 'kyc_documents.doc_type_id', '=', 'm_document_types.doc_type_id')
            ->where('kyc_documents.applicant_id', $id)->pluck('code')->toArray();

        $codeByField = ['uploadedIncorpCert' => 'INCORP', 'uploadedPanCopy' => 'PANCOPY'];
        foreach ($codeByField as $field => $code) {
            if (in_array($code, $have, true) && isset($rules[$field])) {
                $rules[$field] = array_values(array_filter($rules[$field], fn($r) => $r !== 'required'));
                array_unshift($rules[$field], 'nullable');
            }
        }
        return $rules;
    }

    /** Get the current draft applicant id, creating a bare row on first use. */
    private function getOrCreateApplicantId(): int
    {
        $id = session('caf_applicant_id');
        if ($id && DB::table('applicants')->where('applicant_id', $id)->exists()) {
            return (int) $id;
        }
        $id = DB::table('applicants')->insertGetId([
            'entity_type'          => session('caf_entity_type', 'Non-Individual'),
            'pan_card_name_abbrev' => '(draft)',
            'application_status'   => 'DRAFT',
        ]);
        session(['caf_applicant_id' => $id]);
        return (int) $id;
    }

    /** Tab 1 -> applicants + corporate/individual details + aliases. */
    private function saveApplicantSection(Request $request, int $id): void
    {
        if ($this->isIndividual()) {
            $this->saveIndividualApplicant($request, $id);
            return;
        }
        DB::table('applicants')->where('applicant_id', $id)->update([
            'entity_type'          => session('caf_entity_type', 'Non-Individual'),
            'pan_card_name_abbrev' => $request->input('entityName'),
        ]);

        DB::table('corporate_applicant_details')->updateOrInsert(
            ['applicant_id' => $id],
            [
                'name_title_code'          => $this->titleToCode[$request->input('nameTitle')] ?? null,
                'company_name'             => $request->input('entityName'),
                'applicant_legal_type'     => $request->input('applicantType') ?: null,
                'date_of_incorporation'    => $request->input('dateOfIncorporation'),
                'date_commence_business'   => $request->input('dateOfCommencementOfBusiness') ?: null,
                'place_of_incorporation'   => $request->input('placeOfIncorporation'),
                'incorporation_country_id' => $request->input('countryOfIncorporation'),
                'incorporation_isd_code'   => $request->input('incorporationIsdCode') ?: null,
                'lei_number'               => $request->input('lei') ?: null,
                'lei_expiry_date'          => $request->input('leiExpiryDate') ?: null,
            ]
        );

        DB::table('applicant_aliases')->where('applicant_id', $id)->delete();
        if ($request->input('knownByAnotherName') === 'YES' && $request->filled('otherEntityName')) {
            DB::table('applicant_aliases')->insert([
                'applicant_id'               => $id,
                'alias_title_code'           => $this->titleToCode[$request->input('otherTitle')] ?? null,
                'alias_last_name_or_company' => $request->input('otherEntityName'),
            ]);
        }
    }

    /** Individual Tab 1 -> applicants + all demographics into caf_extra_json['ind_applicant']. */
    private function saveIndividualApplicant(Request $request, int $id): void
    {
        $name = trim(implode(' ', array_filter([
            $request->input('indFirstName'),
            $request->input('indMiddleName'),
            $request->input('indLastName'),
        ])));
        DB::table('applicants')->where('applicant_id', $id)->update([
            'entity_type'          => 'Individual',
            'pan_card_name_abbrev' => $name !== '' ? $name : '(individual)',
        ]);

        $keys = [
            'indTitle',
            'indFirstName',
            'indMiddleName',
            'indLastName',
            'indOtherName',
            'indOtherTitle',
            'indOtherFirstName',
            'indOtherMiddleName',
            'indOtherLastName',
            'indDob',
            'indPlaceOfBirth',
            'indCountryOfBirth',
            'indBirthIsd',
            'indNationality',
            'indNationalityIsd',
            'indPassport',
            'indGender',
            'indMaritalStatus',
            'indCitizenshipStatus',
            'indCountryOfCitizenship',
            'indFatherFirstName',
            'indFatherMiddleName',
            'indFatherLastName',
            'indMotherFirstName',
            'indMotherMiddleName',
            'indMotherLastName',
            'indSpouseFirstName',
            'indSpouseMiddleName',
            'indSpouseLastName',
        ];
        $data = [];
        foreach ($keys as $k) {
            $data[$k] = $request->input($k) ?: '';
        }
        $this->saveExtra($id, 'ind_applicant', $data);
    }

    /** Tab 3 -> applicants.ubo_structure_json (the ownership tree from the tool). */
    private function saveUbo_toolSection(Request $request, int $id): void
    {
        $json = $request->input('uboStructure');
        // Store only if it parses as JSON; otherwise clear.
        $valid = $json && json_decode($json) !== null;
        DB::table('applicants')->where('applicant_id', $id)->update([
            'ubo_structure_json' => $valid ? $json : null,
        ]);
    }

    /** Tab 2 -> applicant_addresses (1:N) + applicant_contacts. */
    private function saveContactSection(Request $request, int $id): void
    {
        $same = filter_var($request->input('sameAddress'), FILTER_VALIDATE_BOOLEAN);

        DB::table('applicant_addresses')->where('applicant_id', $id)->delete();
        if ($request->filled('regCountry')) {
            DB::table('applicant_addresses')->insert([
                'applicant_id'          => $id,
                'address_type'          => 'Registered_Residence',
                'is_communication_dest' => $same ? 1 : 0,
                'flat_room_block'       => $request->input('regAddressLine1'),
                'premises_building'     => $request->input('regAddressLine2'),
                'road_street_lane'      => $request->input('regAddressLine3'),
                'area_locality_taluka'  => $request->input('regAddressLine4') ?: null,
                'town_city_district'    => (string) $request->input('regCity'),
                'state_union_territory' => (string) $request->input('regState'),
                'pin_zip_code'          => (string) $request->input('regZip'),
                'country_id'            => $request->input('regCountry'),
            ]);
        }
        if (!$same && $request->filled('commCountry')) {
            DB::table('applicant_addresses')->insert([
                'applicant_id'          => $id,
                'address_type'          => 'Registered_Residence',
                'is_communication_dest' => 1,
                'flat_room_block'       => $request->input('commAddressLine1'),
                'premises_building'     => $request->input('commAddressLine2'),
                'road_street_lane'      => $request->input('commAddressLine3'),
                'area_locality_taluka'  => $request->input('commAddressLine4') ?: null,
                'town_city_district'    => (string) $request->input('commCity'),
                'state_union_territory' => (string) $request->input('commState'),
                'pin_zip_code'          => (string) $request->input('commZip'),
                'country_id'            => $request->input('commCountry'),
            ]);
        }

        // Office address (reference Step 2 second address block).
        if ($request->filled('offCountry')) {
            DB::table('applicant_addresses')->insert([
                'applicant_id'          => $id,
                'address_type'          => 'Office',
                'is_communication_dest' => 0,
                'flat_room_block'       => $request->input('offAddressLine1'),
                'premises_building'     => $request->input('offAddressLine2'),
                'road_street_lane'      => $request->input('offAddressLine3'),
                'area_locality_taluka'  => $request->input('offAddressLine4') ?: null,
                'town_city_district'    => (string) $request->input('offCity'),
                'state_union_territory' => (string) $request->input('offState'),
                'pin_zip_code'          => (string) $request->input('offZip'),
                'country_id'            => $request->input('offCountry'),
            ]);
        }

        DB::table('applicant_contacts')->where('applicant_id', $id)->whereIn('contact_type', ['Residence', 'Office'])->delete();
        DB::table('applicant_contacts')->insert([
            'applicant_id'      => $id,
            'contact_type'      => 'Residence',
            'tel_isd_code'      => $request->input('telIsdCode') ? substr($request->input('telIsdCode'), 0, 5) : null,
            'tel_std_area_code' => $request->input('telAreaCode') ? substr($request->input('telAreaCode'), 0, 10) : null,
            'telephone_number'  => $request->input('telNumber') ? substr($request->input('telNumber'), 0, 20) : null,
            'mobile_number'     => $request->input('mobileNumber') ? substr($request->input('mobileNumber'), 0, 20) : null,
            'fax_number'        => $request->input('faxNumber') ? substr($request->input('faxNumber'), 0, 20) : null,
            'website'           => $request->input('website') ? substr($request->input('website'), 0, 150) : null,
            'email_id'          => $request->input('email') ?: null,
        ]);

        // Office phone (reference Step 2 second phone block).
        if ($request->filled('offTelNumber') || $request->filled('offTelIsdCode')) {
            DB::table('applicant_contacts')->insert([
                'applicant_id'      => $id,
                'contact_type'      => 'Office',
                'tel_isd_code'      => $request->input('offTelIsdCode') ? substr($request->input('offTelIsdCode'), 0, 5) : null,
                'tel_std_area_code' => $request->input('offTelAreaCode') ? substr($request->input('offTelAreaCode'), 0, 10) : null,
                'telephone_number'  => $request->input('offTelNumber') ? substr($request->input('offTelNumber'), 0, 20) : null,
            ]);
        }
    }

    /** Validate the JSON list of UBO rows; returns an error string or null. */
    private function validateUboRows($json): ?string
    {
        $rows = json_decode($json ?? '', true);
        if (!is_array($rows) || count($rows) === 0) {
            return 'Please add at least one beneficial owner.';
        }
        foreach ($rows as $i => $r) {
            $n = $i + 1;
            if (trim($r['name'] ?? '') === '') return "UBO {$n}: full name is required.";
            if (empty($r['dob'])) return "UBO {$n}: date of birth is required.";
            if (strtotime($r['dob']) > strtotime(date('Y-m-d'))) return "UBO {$n}: date of birth cannot be in the future.";
            if (empty($r['nationality'])) return "UBO {$n}: nationality is required.";
            if (trim($r['passport'] ?? '') === '') return "UBO {$n}: passport / national ID is required.";
            $pct = $r['ownership'] ?? '';
            if ($pct === '' || !is_numeric($pct) || $pct < 0 || $pct > 100) return "UBO {$n}: ownership % must be between 0 and 100.";
            if (trim($r['address'] ?? '') === '') return "UBO {$n}: residential address is required.";
        }
        return null;
    }

    /** Tab 4 -> ubo (1:N). Multiple UBO rows carried from the determination tool. */
    private function saveUboSection(Request $request, int $id): void
    {
        // Sub-funds, intermediate & controlling entities (variable-shape) as one JSON doc.
        $subFunds     = json_decode($request->input('subFundsJson') ?? '[]', true);
        $intermediates = json_decode($request->input('intermediatesJson') ?? '[]', true);
        $controllers   = json_decode($request->input('controllersJson') ?? '[]', true);
        DB::table('applicants')->where('applicant_id', $id)->update([
            'beneficial_ownership_json' => json_encode([
                'hasSubFunds'   => $request->input('hasSubFunds'),
                'subFunds'      => is_array($subFunds) ? array_values($subFunds) : [],
                'intermediates' => is_array($intermediates) ? array_values($intermediates) : [],
                'controllers'   => is_array($controllers) ? array_values($controllers) : [],
            ]),
        ]);

        DB::table('ubo')->where('applicant_id', $id)->delete();
        if ($request->input('hasUbos') !== 'YES') {
            return;
        }
        $rows = json_decode($request->input('uboRowsJson') ?? '', true);
        if (!is_array($rows)) {
            return;
        }
        foreach ($rows as $r) {
            if (trim($r['name'] ?? '') === '') {
                continue;
            }
            DB::table('ubo')->insert([
                'applicant_id'                => $id,
                'is_senior_managing_official' => 0,
                'full_name'                   => $r['name'],
                'residential_address'         => $r['address'] ?? '',
                'date_of_birth'               => !empty($r['dob']) ? $r['dob'] : null,
                'tax_residency_country_id'    => !empty($r['taxJurisdiction']) ? (int) $r['taxJurisdiction'] : null,
                'nationality_country_id'      => !empty($r['nationality']) ? (int) $r['nationality'] : null,
                'acting_group_details'        => $r['actingGroup'] ?? null,
                'shareholding_capital_pct'    => ($r['ownership'] ?? '') !== '' ? $r['ownership'] : null,
                'id_document_type'            => !empty($r['passport']) ? 'Passport' : null,
                'id_document_number'          => $r['passport'] ?? null,
            ]);
        }
    }

    /** Tab 5 -> applicants (financial) + tax_residencies. */
    private function saveFinancialSection(Request $request, int $id): void
    {
        DB::table('applicants')->where('applicant_id', $id)->update([
            'net_worth_inr'            => $request->input('netWorth') ?: null,
            'net_worth_date'           => $request->input('netWorthDate') ?: null,
            'gross_annual_income_band' => $request->input('incomeRange') ?: null,
            'gross_annual_income_inr'  => $request->input('grossIncome') ?: null,
        ]);

        // Reference extras with no clean normalized home / unseeded FK masters.
        $this->saveExtra($id, 'financial', [
            'incomeSources'  => $this->jsonArray($request, 'incomeSourcesJson'),
            'professionCode' => $request->input('professionCode') ?: '',
            'occupation'     => $request->input('occupation') ?: '',
            'tinReason'      => $request->input('tinReason') ?: '',
            'tinExplanation' => $request->input('tinExplanation') ?: '',
            'trcNumber'      => $request->input('trcNumber') ?: '',
            'fatcaCrs'       => $request->input('fatcaCrs') ?: '',
            'isPep'          => $request->input('isPep') ?: '',
            'relatedToPep'   => $request->input('relatedToPep') ?: '',
            'taxResidenciesData' => $this->jsonArray($request, 'taxResidenciesJson'),
        ]);

        $taxResidencies = $this->jsonArray($request, 'taxResidenciesJson');

        DB::table('tax_residencies')->where('applicant_id', $id)->delete();
        if ($request->filled('taxCountry')) {
            DB::table('tax_residencies')->insert([
                'applicant_id' => $id,
                'country_id'   => $request->input('taxCountry'),
                'trc_number'   => (string) $request->input('tin'),
            ]);
        }
        // Additional tax residencies (reference "Add More Tax Residency").
        foreach ($taxResidencies as $tr) {
            if (!empty($tr['country'])) {
                DB::table('tax_residencies')->insert([
                    'applicant_id' => $id,
                    'country_id'   => (int) $tr['country'],
                    'trc_number'   => (string) ($tr['trc'] ?? $tr['tin'] ?? ''),
                ]);
            }
        }
    }

    /** Tab 6 -> applicants (category) + applicant_foreign_regulators. */
    private function saveCategorySection(Request $request, int $id): void
    {
        DB::table('applicants')->where('applicant_id', $id)->update([
            'fpi_category_code' => $request->input('fpiCategory') ?: null,
            'is_regulated_fpi'  => $request->input('regulatoryStatus') === 'REGULATED' ? 1 : 0,
        ]);

        DB::table('applicant_foreign_regulators')->where('applicant_id', $id)->delete();
        if ($request->filled('regulatorName')) {
            DB::table('applicant_foreign_regulators')->insert([
                'applicant_id'               => $id,
                'regulatory_authority_name'  => $request->input('regulatorName'),
                'regulatory_registration_no' => $request->input('licenseNumber') ?: null,
                'regulatory_country_id'      => $request->input('regulatorJurisdiction') ?: null,
            ]);
        }

        $this->saveExtra($id, 'category', [
            'subCategory'            => $request->input('subCategory') ?: '',
            'mimStructure'           => $request->input('mimStructure') ?: '',
            'investmentManagersData' => $this->jsonArray($request, 'investmentManagersJson'),
            'regulatorWebsite'       => $request->input('regulatorWebsite') ?: '',
            'regulatorCapacity'      => $request->input('regulatorCapacity') ?: '',
            'complianceName'         => $request->input('complianceName') ?: '',
            'complianceTitle'        => $request->input('complianceTitle') ?: '',
            'complianceEmail'        => $request->input('complianceEmail') ?: '',
            'compliancePhone'        => $request->input('compliancePhone') ?: '',
            'complianceFax'          => $request->input('complianceFax') ?: '',
            'hasCustodian'           => $request->input('hasCustodian') ?: '',
            'custodianNameCat'       => $request->input('custodianNameCat') ?: '',
            'custodianReg'           => $request->input('custodianReg') ?: '',
            'custRegCode'            => $request->input('custRegCode') ?: '',
            'custodianAddress'       => $request->input('custodianAddress') ?: '',
            'disciplinaryHistory'    => $request->input('disciplinaryHistory') ?: '',
            'disciplinaryDetails'    => $request->input('disciplinaryDetails') ?: '',
            'clubbingDeclaration'    => $request->input('clubbingDeclaration') ?: '',
            'fpiGroupNumber'         => $request->input('fpiGroupNumber') ?: '',
            'groupNumber'            => $request->input('groupNumber') ?: '',
            'fpiGroupData'           => $this->jsonArray($request, 'fpiGroupJson'),
            'publicRetailData'       => $this->jsonArray($request, 'publicRetailJson'),
            'priorAssociation'       => $request->input('priorAssociation') ?: '',
            'priorAssociationsData'  => $this->jsonArray($request, 'priorAssociationsJson'),
        ]);
    }

    /** Tab 7 -> pan_additional_details + depository_bank_accounts + office_verification + custodian. */
    private function saveDepositorySection(Request $request, int $id): void
    {
        DB::table('pan_additional_details')->updateOrInsert(
            ['applicant_id' => $id],
            ['already_holds_pan' => $request->filled('pan') ? 1 : 0, 'existing_pan' => $request->input('pan') ?: null]
        );
        DB::table('depository_bank_accounts')->updateOrInsert(
            ['applicant_id' => $id],
            ['ad_category_1_bank_name' => $request->input('bankName') ?: null, 'bank_swift_ifsc' => $request->input('bankSwift') ?: null]
        );
        DB::table('office_verification')->updateOrInsert(
            ['applicant_id' => $id],
            [
                'bank_account_number' => $request->input('bankAccount') ?: null,
                'bank_account_type'   => $request->input('bankAccountType') ?: null,
                'dp_id'               => $request->input('dpId') ?: null,
                'client_id'           => $request->input('clientId') ?: null,
            ]
        );
        if ($request->filled('custodianName')) {
            DB::table('applicant_custodian_details')->updateOrInsert(
                ['applicant_id' => $id],
                ['global_custodian_name' => $request->input('custodianName')]
            );
        }

        $this->saveExtra($id, 'depository', [
            'hasPan'             => $request->input('hasPan') ?: '',
            'existingpanName'    => $request->input('existingpanName') ?: '',
            'statusApplicant'    => $request->input('statusApplicant') ?: '',
            'panName'            => $request->input('panName') ?: '',
            'aoAreaCode'         => $request->input('aoAreaCode') ?: '',
            'aoType'             => $request->input('aoType') ?: '',
            'aoRangeCode'        => $request->input('aoRangeCode') ?: '',
            'aoNo'               => $request->input('aoNo') ?: '',
            'registrationNumber' => $request->input('registrationNumber') ?: '',
            'repTitle'           => $request->input('repTitle') ?: '',
            'repLastName'        => $request->input('repLastName') ?: '',
            'repFirstName'       => $request->input('repFirstName') ?: '',
            'repMiddleName'      => $request->input('repMiddleName') ?: '',
            'repAddress'         => $request->input('repAddress') ?: '',
            'listed'             => $request->input('listed') ?: '',
            'exchangeName'       => $request->input('exchangeName') ?: '',
            'poiType'            => $request->input('poiType') ?: '',
            'poiNumber'          => $request->input('poiNumber') ?: '',
            'poaType'            => $request->input('poaType') ?: '',
            'poaNumber'          => $request->input('poaNumber') ?: '',
            'sensitiveActivities' => $this->jsonArray($request, 'sensitiveActivitiesJson'),
            'depositoryAuth'     => $request->input('depositoryAuth') ?: '',
            'modeOfOperation'    => $request->input('modeOfOperation') ?: '',
            'otherMode'          => $request->input('otherMode') ?: '',
            'bankAuth'           => $request->input('bankAuth') ?: '',
        ]);
    }

    /** Tab 8 -> applicant_contacts (Compliance) + investment_managers + corporate india place. */
    private function saveAdditionalSection(Request $request, int $id): void
    {
        DB::table('applicant_contacts')->where('applicant_id', $id)->where('contact_type', 'Compliance')->delete();
        if ($request->filled('primaryContactName')) {
            DB::table('applicant_contacts')->insert([
                'applicant_id' => $id,
                'contact_type' => 'Compliance',
                'officer_name' => $request->input('primaryContactName'),
                'job_title'    => $request->input('primaryContactDesignation') ?: null,
            ]);
        }

        DB::table('investment_managers')->where('applicant_id', $id)->delete();
        if ($request->filled('investmentManagerName')) {
            DB::table('investment_managers')->insert([
                'applicant_id' => $id,
                'manager_name' => $request->input('investmentManagerName'),
            ]);
        }

        // india_place_of_business lives on the corporate row (updated only if it exists).
        DB::table('corporate_applicant_details')->where('applicant_id', $id)
            ->update(['india_place_of_business' => $request->input('indiaPlaceOfBusiness') ?: null]);

        $this->saveExtra($id, 'additional', [
            'odiDerivatives'          => $request->input('odiDerivatives') ?: '',
            'shareClassesData'        => $this->jsonArray($request, 'shareClassesJson'),
            'categoryOneEntitiesData' => $this->jsonArray($request, 'categoryOneEntitiesJson'),
            'bankDeclaration'         => $request->input('bankDeclaration') ?: '',
            'bankEntityName'          => $request->input('bankEntityName') ?: '',
            'nriControl1'             => $request->input('nriControl1') ?: '',
            'nriControl2'             => $request->input('nriControl2') ?: '',
            'imTypes'                 => $this->jsonArray($request, 'imTypesJson'),
            'directlyControlled'      => $request->input('directlyControlled') ?: '',
            'nriControlEntityName'    => $request->input('nriControlEntityName') ?: '',
            'offshoreFund'            => $request->input('offshoreFund') ?: '',
            'nriEntitlement'          => $request->input('nriEntitlement') ?: '',
            'reg5b7'                  => $request->input('reg5b7') ?: '',
            'clientEligI'             => $request->boolean('clientEligI'),
            'clientEligII'            => $request->boolean('clientEligII'),
            'clientEligIII'           => $request->boolean('clientEligIII'),
            'clientsData'             => $this->jsonArray($request, 'clientsJson'),
            'kraConsent'              => $request->input('kraConsent') ?: '',
            'kraRepName'              => $request->input('kraRepName') ?: '',
            'kraEmail1'               => $request->input('kraEmail1') ?: '',
            'kraEmail2'               => $request->input('kraEmail2') ?: '',
            'kraEmail3'               => $request->input('kraEmail3') ?: '',
            'kraMobile'               => $request->input('kraMobile') ?: '',
            'signatoriesData'         => $this->jsonArray($request, 'signatoriesJson'),
        ]);
    }

    /** Tab 9 -> application_declaration + kyc_documents (files). */
    private function saveDeclarationsSection(Request $request, int $id): void
    {
        DB::table('application_declaration')->updateOrInsert(
            ['applicant_id' => $id],
            [
                'declarant_name'            => $request->input('signatureName') ?: null,
                'authorized_signatory_name' => $request->input('signatureName') ?: null,
                'declaration_date'          => now()->toDateString(),
            ]
        );

        $docMap = [
            'uploadedIncorpCert' => 'INCORP',
            'uploadedLeiProof' => 'LEIPROOF',
            'uploadedPanCopy' => 'PANCOPY',
            'uploadedUboDecl' => 'UBODECL',
        ];
        foreach ($docMap as $field => $code) {
            if (!$request->hasFile($field)) {
                continue;
            }
            $type = DB::table('m_document_types')->where('code', $code)->first();
            if (!$type) {
                continue;
            }
            // Store directly under public/uploads/kyc/{id}/{code}/<original name>.
            $original = $request->file($field)->getClientOriginalName();
            $dir = public_path("uploads/kyc/{$id}/{$code}");
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $request->file($field)->move($dir, $original);
            $path = "uploads/kyc/{$id}/{$code}/{$original}";
            DB::table('kyc_documents')->where('applicant_id', $id)->where('doc_type_id', $type->doc_type_id)->delete();
            DB::table('kyc_documents')->insert([
                'applicant_id'     => $id,
                'doc_type_id'      => $type->doc_type_id,
                'document_purpose' => $type->purpose,
                'file_storage_uri' => $path,
                'is_verified'      => 0,
            ]);
        }

        // Additional reference uploads with no dedicated document type — stored to disk,
        // filenames recorded in the extra document. Merge into any existing declarations slice.
        $extra = $this->loadExtra($id)['declarations'] ?? [];
        $extraDocs = ['uploadedPoi' => 'POI', 'uploadedPoa' => 'POA', 'uploadedFatca' => 'FATCA', 'uploadedSignature' => 'SIGNATURE'];
        foreach ($extraDocs as $field => $code) {
            if (!$request->hasFile($field)) {
                continue;
            }
            $original = $request->file($field)->getClientOriginalName();
            $dir = public_path("uploads/kyc/{$id}/{$code}");
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $request->file($field)->move($dir, $original);
            $extra[$field] = $original;
            $extra[$field . '_uri'] = "uploads/kyc/{$id}/{$code}/{$original}";
        }

        // Any other supporting documents (multiple).
        if ($request->hasFile('otherDocs')) {
            $names = (array) ($extra['otherDocs'] ?? []);
            $dir = public_path("uploads/kyc/{$id}/OTHER");
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            foreach ((array) $request->file('otherDocs') as $file) {
                $original = $file->getClientOriginalName();
                $file->move($dir, $original);
                $names[] = $original;
            }
            $extra['otherDocs'] = array_values(array_unique($names));
        }

        $this->saveExtra($id, 'declarations', array_merge($extra, [
            'declarationPlace'     => $request->input('declarationPlace') ?: '',
            'declarationDate'      => $request->input('declarationDate') ?: '',
            'applicantName'        => $request->input('applicantName') ?: '',
            'applicantDesignation' => $request->input('applicantDesignation') ?: '',
            'authDesignation'      => $request->input('authDesignation') ?: '',
            'authDate'             => $request->input('authDate') ?: '',
            'declaration1'         => $request->boolean('declaration1'),
            'declaration2'         => $request->boolean('declaration2'),
            'declaration3'         => $request->boolean('declaration3'),
        ]));
    }
}
