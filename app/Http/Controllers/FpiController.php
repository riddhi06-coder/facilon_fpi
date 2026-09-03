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
            'nameTitle' => '',
            'entityName' => '',
            'knownByAnotherName' => '',
            'otherTitle' => '',
            'otherEntityName' => '',
            'dateOfIncorporation' => '',
            'dateOfCommencementOfBusiness' => '',
            'placeOfIncorporation' => '',
            'countryOfIncorporation' => '',
            'lei' => '',
            'leiExpiryDate' => '',
            'regAddressLine1' => '',
            'regAddressLine2' => '',
            'regAddressLine3' => '',
            'regCity' => '',
            'regState' => '',
            'regCountry' => '',
            'regZip' => '',
            'sameAddress' => false,
            'commAddressLine1' => '',
            'commAddressLine2' => '',
            'commAddressLine3' => '',
            'commCity' => '',
            'commState' => '',
            'commCountry' => '',
            'commZip' => '',
            'telNumber' => '',
            'mobileNumber' => '',
            'email' => '',
            'hasUbos' => '',
            'uboName' => '',
            'uboDob' => '',
            'uboNationality' => '',
            'uboPassport' => '',
            'uboOwnership' => '',
            'uboAddress' => '',
            'incomeRange' => '',
            'netWorth' => '',
            'netWorthDate' => '',
            'taxCountry' => '',
            'tin' => '',
            'fpiCategory' => '',
            'regulatoryStatus' => '',
            'regulatorName' => '',
            'licenseNumber' => '',
            'regulatorJurisdiction' => '',
            'pan' => '',
            'bankName' => '',
            'bankAccount' => '',
            'bankAccountType' => '',
            'bankSwift' => '',
            'custodianName' => '',
            'dpId' => '',
            'clientId' => '',
            'primaryContactName' => '',
            'primaryContactDesignation' => '',
            'investmentManagerName' => '',
            'indiaPlaceOfBusiness' => '',
            'declarationAgreed' => false,
            'signatureName' => '',
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

    public function index()
    {
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

        return view('fpi.index', compact('form', 'countries', 'isdCodes', 'activeSection', 'savedSections', 'isSubmitted'));
    }

    /** Professional printable / PDF preview of the whole application. */
    public function preview()
    {
        $id = session('caf_applicant_id');
        if (!$id || !DB::table('applicants')->where('applicant_id', $id)->exists()) {
            return redirect()->route('fpi.index')->withErrors(['submit' => 'No application to preview yet.']);
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

        return view('fpi.preview', $p);
    }

    /** Final submission: validate completeness, mark SUBMITTED, log status history. */
    public function submit(Request $request)
    {
        $id = session('caf_applicant_id');
        if (!$id || !DB::table('applicants')->where('applicant_id', $id)->exists()) {
            $m = 'Please fill and save the form before submitting.';
            return $request->wantsJson()
                ? response()->json(['ok' => false, 'message' => $m], 422)
                : redirect()->route('fpi.index')->withErrors(['submit' => $m]);
        }

        // Required sections must be saved before an application can be submitted.
        $required = ['applicant', 'contact', 'ubo', 'financial', 'category', 'depository', 'declarations'];
        $done = DB::table('application_section_progress')->where('applicant_id', $id)
            ->where('is_complete', 1)->pluck('section_code')->toArray();
        $missing = array_diff($required, $done);
        if ($missing) {
            $labels = [
                'applicant' => 'Applicant Profile', 'contact' => 'Contact & Address', 'ubo' => 'Beneficial Ownership',
                'financial' => 'Financial & Tax', 'category' => 'Category & Regulatory',
                'depository' => 'PAN, Bank & Depository', 'declarations' => 'Final Declarations',
            ];
            $names = implode(', ', array_map(fn ($s) => $labels[$s] ?? $s, $missing));
            $m = "Please complete & save these sections before submitting: {$names}.";
            return $request->wantsJson()
                ? response()->json(['ok' => false, 'message' => $m], 422)
                : redirect()->route('fpi.index')->with('active_section', reset($missing))->withErrors(['submit' => $m]);
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
        return redirect()->route('fpi.index')->with('status', $m)->with('active_section', 'declarations');
    }

    /** Section order for the wizard flow. */
    private array $order = ['applicant', 'contact', 'ubo_tool', 'ubo', 'financial', 'category', 'depository', 'additional', 'declarations'];

    private function isdCodes(): array
    {
        return DB::table('m_countries')->where('is_active', true)->pluck('isd_code')->map(fn ($c) => (string) $c)->toArray();
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
            $out['dateOfIncorporation'] = $corp->date_of_incorporation;
            $out['dateOfCommencementOfBusiness'] = $corp->date_commence_business;
            $out['placeOfIncorporation'] = $corp->place_of_incorporation;
            $out['countryOfIncorporation'] = (string) $corp->incorporation_country_id;
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
            $out['regAddressLine1'] = $reg->flat_room_block; $out['regAddressLine2'] = $reg->premises_building;
            $out['regAddressLine3'] = $reg->road_street_lane; $out['regCity'] = $reg->town_city_district;
            $out['regState'] = $reg->state_union_territory; $out['regZip'] = $reg->pin_zip_code;
            $out['regCountry'] = (string) $reg->country_id;
            if ($addrs->count() > 1) {
                $comm = $addrs[1]; $out['sameAddress'] = false;
                $out['commAddressLine1'] = $comm->flat_room_block; $out['commAddressLine2'] = $comm->premises_building;
                $out['commAddressLine3'] = $comm->road_street_lane; $out['commCity'] = $comm->town_city_district;
                $out['commState'] = $comm->state_union_territory; $out['commZip'] = $comm->pin_zip_code;
                $out['commCountry'] = (string) $comm->country_id;
            } else {
                $out['sameAddress'] = (bool) $reg->is_communication_dest;
            }
        }
        $resContact = DB::table('applicant_contacts')->where('applicant_id', $applicantId)->where('contact_type', 'Residence')->first();
        if ($resContact) {
            $out['telNumber'] = $resContact->telephone_number; $out['mobileNumber'] = $resContact->mobile_number;
            $out['email'] = $resContact->email_id;
        }

        // Tab 4 — Beneficial Ownership
        $ubo = DB::table('ubo')->where('applicant_id', $applicantId)->first();
        if ($ubo) {
            $out['hasUbos'] = 'YES'; $out['uboName'] = $ubo->full_name; $out['uboDob'] = $ubo->date_of_birth;
            $out['uboNationality'] = (string) $ubo->nationality_country_id;
            $out['uboPassport'] = $ubo->id_document_number; $out['uboOwnership'] = $ubo->shareholding_capital_pct;
            $out['uboAddress'] = $ubo->residential_address;
        } elseif (DB::table('application_section_progress')->where('applicant_id', $applicantId)->where('section_code', 'ubo')->where('is_complete', 1)->exists()) {
            $out['hasUbos'] = 'NO'; // section saved with "No" -> keep the choice
        }

        // Tab 3 — UBO Determination tool (ownership tree JSON)
        if ($app && $app->ubo_structure_json) {
            $out['uboStructure'] = $app->ubo_structure_json;
        }

        // Tab 5 — Financial & Tax
        if ($app) {
            $out['netWorth'] = $app->net_worth_inr; $out['netWorthDate'] = $app->net_worth_date;
            $out['incomeRange'] = $app->gross_annual_income_band ?? '';
            $out['fpiCategory'] = $app->fpi_category_code ?? '';
            if ($app->fpi_category_code !== null) {
                $out['regulatoryStatus'] = $app->is_regulated_fpi ? 'REGULATED' : 'UNREGULATED';
            }
        }
        $tax = DB::table('tax_residencies')->where('applicant_id', $applicantId)->first();
        if ($tax) { $out['taxCountry'] = (string) $tax->country_id; $out['tin'] = $tax->trc_number; }

        // Tab 6 — Category & Regulatory
        $reg = DB::table('applicant_foreign_regulators')->where('applicant_id', $applicantId)->first();
        if ($reg) {
            $out['regulatorName'] = $reg->regulatory_authority_name; $out['licenseNumber'] = $reg->regulatory_registration_no;
            $out['regulatorJurisdiction'] = (string) ($reg->regulatory_country_id ?? '');
        }

        // Tab 7 — PAN, Bank & Depository
        $pan = DB::table('pan_additional_details')->where('applicant_id', $applicantId)->first();
        if ($pan) { $out['pan'] = $pan->existing_pan; }
        $bank = DB::table('depository_bank_accounts')->where('applicant_id', $applicantId)->first();
        if ($bank) { $out['bankName'] = $bank->ad_category_1_bank_name; $out['bankSwift'] = $bank->bank_swift_ifsc; }
        $office = DB::table('office_verification')->where('applicant_id', $applicantId)->first();
        if ($office) {
            $out['bankAccount'] = $office->bank_account_number; $out['bankAccountType'] = $office->bank_account_type;
            $out['dpId'] = $office->dp_id; $out['clientId'] = $office->client_id;
        }
        $cust = DB::table('applicant_custodian_details')->where('applicant_id', $applicantId)->first();
        if ($cust) { $out['custodianName'] = $cust->global_custodian_name; }

        // Tab 8 — Additional Info
        $compContact = DB::table('applicant_contacts')->where('applicant_id', $applicantId)->where('contact_type', 'Compliance')->first();
        if ($compContact) { $out['primaryContactName'] = $compContact->officer_name; $out['primaryContactDesignation'] = $compContact->job_title; }
        $im = DB::table('investment_managers')->where('applicant_id', $applicantId)->first();
        if ($im) { $out['investmentManagerName'] = $im->manager_name; }

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

        // Drop nulls so form defaults ('') apply cleanly.
        return array_filter($out, fn ($v) => $v !== null);
    }

    /** Validation rules grouped per section (tab). */
    private function sectionRules(): array
    {
        return [
            'applicant' => [
                'nameTitle'                     => ['required', Rule::in(['M/S', 'MR', 'MRS', 'MS'])],
                'entityName'                    => ['required', 'string', 'max:200'],
                'knownByAnotherName'            => ['required', Rule::in(['YES', 'NO'])],
                'otherTitle'                    => ['nullable', Rule::in(['M/S', 'MR', 'MRS', ''])],
                'otherEntityName'               => ['nullable', 'required_if:knownByAnotherName,YES', 'string', 'max:200'],
                'dateOfIncorporation'           => ['required', 'date', 'before_or_equal:today'],
                'dateOfCommencementOfBusiness'  => ['nullable', 'date', 'before_or_equal:today'],
                'placeOfIncorporation'          => ['required', 'string', 'max:100'],
                'countryOfIncorporation'        => ['required', 'integer', Rule::exists('m_countries', 'country_id')],
                'lei'                           => ['nullable', 'string', 'size:20', 'regex:/^[A-Z0-9]{20}$/'],
                'leiExpiryDate'                 => ['nullable', 'date'],
            ],
            'contact' => [
                // Registered address — all required
                'regAddressLine1' => ['required', 'string', 'max:150'],
                'regAddressLine2' => ['required', 'string', 'max:150'],
                'regAddressLine3' => ['required', 'string', 'max:150'],
                'regCity'         => ['required', 'string', 'max:100'],
                'regState'        => ['required', 'string', 'max:100'],
                'regCountry'      => ['required', 'integer', Rule::exists('m_countries', 'country_id')],
                'regZip'          => ['required', 'string', 'max:20'],
                'sameAddress'     => ['nullable'],
                // Correspondence address — required only when NOT "same as registered"
                'commAddressLine1' => ['required_without:sameAddress', 'nullable', 'string', 'max:150'],
                'commAddressLine2' => ['required_without:sameAddress', 'nullable', 'string', 'max:150'],
                'commAddressLine3' => ['required_without:sameAddress', 'nullable', 'string', 'max:150'],
                'commCity'        => ['required_without:sameAddress', 'nullable', 'string', 'max:100'],
                'commState'       => ['required_without:sameAddress', 'nullable', 'string', 'max:100'],
                'commCountry'     => ['required_without:sameAddress', 'nullable', 'integer', Rule::exists('m_countries', 'country_id')],
                'commZip'         => ['required_without:sameAddress', 'nullable', 'string', 'max:20'],
                // Contact — telephone optional, mobile + email required
                'telNumber'       => ['nullable', 'string', 'max:20'],
                'mobileNumber'    => ['required', 'string', 'max:20', $this->mobileRule()],
                'email'           => ['required', 'email', 'max:100'],
            ],
            'ubo' => [
                'hasUbos'        => ['required', Rule::in(['YES', 'NO'])],
                'uboName'        => ['nullable', 'required_if:hasUbos,YES', 'string', 'max:150'],
                'uboDob'         => ['nullable', 'required_if:hasUbos,YES', 'date', 'before_or_equal:today'],
                'uboNationality' => ['nullable', 'required_if:hasUbos,YES', 'string', 'max:100'],
                'uboPassport'    => ['nullable', 'required_if:hasUbos,YES', 'string', 'max:50'],
                'uboOwnership'   => ['nullable', 'required_if:hasUbos,YES', 'numeric', 'between:0,100'],
                'uboAddress'     => ['nullable', 'required_if:hasUbos,YES', 'string', 'max:1000'],
            ],
            'financial' => [
                'incomeRange'  => ['nullable', Rule::in(['UNDER_50K', '50K_250K', '250K_1M', 'ABOVE_1M'])],
                'netWorth'     => ['nullable', 'numeric', 'min:0'],
                'netWorthDate' => ['nullable', 'date'],
                'taxCountry'   => ['nullable', 'string', 'max:100'],
                'tin'          => ['nullable', 'string', 'max:50'],
            ],
            'category' => [
                'fpiCategory'           => ['required', Rule::in(['CAT_I', 'CAT_II'])],
                'regulatoryStatus'      => ['required', Rule::in(['REGULATED', 'UNREGULATED'])],
                'regulatorName'         => ['nullable', 'string', 'max:150'],
                'licenseNumber'         => ['nullable', 'string', 'max:100'],
                'regulatorJurisdiction' => ['nullable', 'string', 'max:100'],
            ],
            'depository' => [
                'pan'             => ['required', 'string', 'size:10', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/'],
                'bankName'        => ['nullable', 'string', 'max:150'],
                'bankAccount'     => ['nullable', 'string', 'max:30'],
                'bankAccountType' => ['required', Rule::in(['NRE', 'NRO', 'ESCROW'])],
                'bankSwift'       => ['nullable', 'string', 'max:20'],
                'custodianName'   => ['nullable', 'string', 'max:200'],
                'dpId'            => ['nullable', 'string', 'max:20'],
                'clientId'        => ['nullable', 'string', 'max:20'],
            ],
            'additional' => [
                'primaryContactName'        => ['nullable', 'string', 'max:150'],
                'primaryContactDesignation' => ['nullable', 'string', 'max:100'],
                'investmentManagerName'     => ['nullable', 'string', 'max:150'],
                'indiaPlaceOfBusiness'      => ['nullable', 'string', 'max:200'],
            ],
            'declarations' => [
                'uploadedIncorpCert' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
                'uploadedLeiProof'   => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
                'uploadedPanCopy'    => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
                'uploadedUboDecl'    => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
                'declarationAgreed'  => ['accepted'],
                'signatureName'      => ['required', 'string', 'max:200'],
            ],
        ];
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
            usort($codes, fn ($a, $b) => strlen($b) - strlen($a)); // longest prefix first
            $match = null;
            foreach ($codes as $c) {
                if (str_starts_with($digits, $c)) { $match = $c; break; }
            }
            if ($match === null) {
                $fail('Mobile number must start with a valid country dialing code (' . implode(', ', array_map(fn ($c) => "+$c", $this->isdCodes())) . ').');
                return;
            }
            $rest = substr($digits, strlen($match));
            if (strlen($rest) < 6 || strlen($rest) > 12) {
                $fail('Mobile number has an invalid length for the selected country code.');
            }
        };
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

        $labels = [
            'applicant' => 'Applicant Profile', 'contact' => 'Contact & Address',
            'ubo_tool' => 'UBO Determination', 'ubo' => 'Beneficial Ownership',
            'financial' => 'Financial & Tax', 'category' => 'Category & Regulatory',
            'depository' => 'PAN, Bank & Depository', 'additional' => 'Additional Info',
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
        $idx = array_search($section, $this->order, true);
        $next = ($idx !== false && isset($this->order[$idx + 1])) ? $this->order[$idx + 1] : $section;
        $msg = ($labels[$section] ?? 'Section') . ' saved successfully.';

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'section' => $section, 'next' => $next, 'message' => $msg]);
        }

        return redirect()->route('fpi.index')->with('status', $msg)->with('active_section', $next);
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
                'applicant_id' => $id, 'doc_type_id' => $type->doc_type_id,
                'document_purpose' => $type->purpose, 'file_storage_uri' => $path, 'is_verified' => 0,
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
                $rules[$field] = array_values(array_filter($rules[$field], fn ($r) => $r !== 'required'));
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
            'entity_type'          => 'Non-Individual',
            'pan_card_name_abbrev' => '(draft)',
            'application_status'   => 'DRAFT',
        ]);
        session(['caf_applicant_id' => $id]);
        return (int) $id;
    }

    /** Tab 1 -> applicants + corporate_applicant_details + applicant_aliases. */
    private function saveApplicantSection(Request $request, int $id): void
    {
        DB::table('applicants')->where('applicant_id', $id)->update([
            'entity_type'          => 'Non-Individual',
            'pan_card_name_abbrev' => $request->input('entityName'),
        ]);

        DB::table('corporate_applicant_details')->updateOrInsert(
            ['applicant_id' => $id],
            [
                'name_title_code'          => $this->titleToCode[$request->input('nameTitle')] ?? null,
                'company_name'             => $request->input('entityName'),
                'date_of_incorporation'    => $request->input('dateOfIncorporation'),
                'date_commence_business'   => $request->input('dateOfCommencementOfBusiness') ?: null,
                'place_of_incorporation'   => $request->input('placeOfIncorporation'),
                'incorporation_country_id' => $request->input('countryOfIncorporation'),
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
                'town_city_district'    => (string) $request->input('commCity'),
                'state_union_territory' => (string) $request->input('commState'),
                'pin_zip_code'          => (string) $request->input('commZip'),
                'country_id'            => $request->input('commCountry'),
            ]);
        }

        DB::table('applicant_contacts')->where('applicant_id', $id)->where('contact_type', 'Residence')->delete();
        DB::table('applicant_contacts')->insert([
            'applicant_id'     => $id,
            'contact_type'     => 'Residence',
            'telephone_number' => $request->input('telNumber') ? substr($request->input('telNumber'), 0, 20) : null,
            'mobile_number'    => $request->input('mobileNumber') ? substr($request->input('mobileNumber'), 0, 20) : null,
            'email_id'         => $request->input('email') ?: null,
        ]);
    }

    /** Tab 4 -> ubo (1:N). */
    private function saveUboSection(Request $request, int $id): void
    {
        DB::table('ubo')->where('applicant_id', $id)->delete();
        if ($request->input('hasUbos') === 'YES') {
            DB::table('ubo')->insert([
                'applicant_id'             => $id,
                'is_senior_managing_official' => 0,
                'full_name'                => $request->input('uboName'),
                'residential_address'      => $request->input('uboAddress'),
                'date_of_birth'            => $request->input('uboDob') ?: null,
                'nationality_country_id'   => $request->input('uboNationality') ?: null,
                'shareholding_capital_pct' => $request->input('uboOwnership') ?: null,
                'id_document_type'         => $request->filled('uboPassport') ? 'Passport' : null,
                'id_document_number'       => $request->input('uboPassport') ?: null,
            ]);
        }
    }

    /** Tab 5 -> applicants (financial) + tax_residencies. */
    private function saveFinancialSection(Request $request, int $id): void
    {
        DB::table('applicants')->where('applicant_id', $id)->update([
            'net_worth_inr'           => $request->input('netWorth') ?: null,
            'net_worth_date'          => $request->input('netWorthDate') ?: null,
            'gross_annual_income_band' => $request->input('incomeRange') ?: null,
        ]);

        DB::table('tax_residencies')->where('applicant_id', $id)->delete();
        if ($request->filled('taxCountry')) {
            DB::table('tax_residencies')->insert([
                'applicant_id' => $id,
                'country_id'   => $request->input('taxCountry'),
                'trc_number'   => (string) $request->input('tin'),
            ]);
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
            'uploadedIncorpCert' => 'INCORP', 'uploadedLeiProof' => 'LEIPROOF',
            'uploadedPanCopy' => 'PANCOPY', 'uploadedUboDecl' => 'UBODECL',
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
    }
}
