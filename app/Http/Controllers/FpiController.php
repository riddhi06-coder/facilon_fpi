<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FpiController extends Controller
{
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
        ];
    }

    public function index()
    {
        $form = $this->defaultForm();

        // Any submitted values take precedence so the form re-populates after validation.
        $form = array_merge($form, session()->getOldInput() ?: []);

        return view('fpi.index', compact('form'));
    }

    public function store(Request $request)
    {
        // Normalise PAN/LEI to uppercase before validating so the user isn't
        // penalised for lowercase input.
        $request->merge([
            'pan' => strtoupper((string) $request->input('pan')),
            'lei' => $request->filled('lei') ? strtoupper((string) $request->input('lei')) : $request->input('lei'),
        ]);

        // Rules aligned to the approved CAF DB column constraints.
        $rules = [
            // Step 1 — Applicant Profile
            'nameTitle'                     => ['required', Rule::in(['M/S', 'MR', 'MRS', 'MS'])],
            'entityName'                    => ['required', 'string', 'max:200'],
            'knownByAnotherName'            => ['required', Rule::in(['YES', 'NO'])],
            'otherTitle'                    => ['nullable', Rule::in(['M/S', 'MR', 'MRS', ''])],
            'otherEntityName'               => ['nullable', 'required_if:knownByAnotherName,YES', 'string', 'max:200'],
            'dateOfIncorporation'           => ['nullable', 'date'],
            'dateOfCommencementOfBusiness'  => ['nullable', 'date'],
            'placeOfIncorporation'          => ['required', 'string', 'max:100'],
            'countryOfIncorporation'        => ['required', 'string', 'max:100'],
            'lei'                           => ['nullable', 'string', 'size:20', 'regex:/^[A-Z0-9]{20}$/'],
            'leiExpiryDate'                 => ['nullable', 'date'],

            // Step 2 — Contact & Address
            'regAddressLine1'               => ['nullable', 'string', 'max:150'],
            'regAddressLine2'               => ['nullable', 'string', 'max:150'],
            'regAddressLine3'               => ['nullable', 'string', 'max:150'],
            'regCity'                       => ['nullable', 'string', 'max:100'],
            'regState'                      => ['nullable', 'string', 'max:100'],
            'regCountry'                    => ['nullable', 'string', 'max:100'],
            'regZip'                        => ['nullable', 'string', 'max:20'],
            'sameAddress'                   => ['nullable'],
            'commAddressLine1'              => ['nullable', 'string', 'max:150'],
            'commAddressLine2'              => ['nullable', 'string', 'max:150'],
            'commAddressLine3'              => ['nullable', 'string', 'max:150'],
            'commCity'                      => ['nullable', 'string', 'max:100'],
            'commState'                     => ['nullable', 'string', 'max:100'],
            'commCountry'                   => ['nullable', 'string', 'max:100'],
            'commZip'                       => ['nullable', 'string', 'max:20'],
            'telNumber'                     => ['nullable', 'string', 'max:20'],
            'mobileNumber'                  => ['nullable', 'string', 'max:20'],
            'email'                         => ['nullable', 'email', 'max:100'],

            // Step 4 — Beneficial Ownership (Step 3 UBO tool is client-side only)
            'hasUbos'                       => ['required', Rule::in(['YES', 'NO'])],
            'uboName'                       => ['nullable', 'required_if:hasUbos,YES', 'string', 'max:150'],
            'uboDob'                        => ['nullable', 'required_if:hasUbos,YES', 'date'],
            'uboNationality'                => ['nullable', 'required_if:hasUbos,YES', 'string', 'max:100'],
            'uboPassport'                   => ['nullable', 'required_if:hasUbos,YES', 'string', 'max:50'],
            'uboOwnership'                  => ['nullable', 'required_if:hasUbos,YES', 'numeric', 'between:0,100'],
            'uboAddress'                    => ['nullable', 'required_if:hasUbos,YES', 'string', 'max:1000'],

            // Step 5 — Financial & Tax
            'incomeRange'                   => ['nullable', Rule::in(['UNDER_50K', '50K_250K', '250K_1M', 'ABOVE_1M'])],
            'netWorth'                      => ['nullable', 'numeric', 'min:0'],
            'netWorthDate'                  => ['nullable', 'date'],
            'taxCountry'                    => ['nullable', 'string', 'max:100'],
            'tin'                           => ['nullable', 'string', 'max:50'],

            // Step 6 — Category & Regulatory
            'fpiCategory'                   => ['required', Rule::in(['CAT_I', 'CAT_II'])],
            'regulatoryStatus'              => ['required', Rule::in(['REGULATED', 'UNREGULATED'])],
            'regulatorName'                 => ['nullable', 'string', 'max:150'],
            'licenseNumber'                 => ['nullable', 'string', 'max:100'],
            'regulatorJurisdiction'         => ['nullable', 'string', 'max:100'],

            // Step 7 — PAN, Bank & Depository
            'pan'                           => ['required', 'string', 'size:10', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/'],
            'bankName'                      => ['nullable', 'string', 'max:150'],
            'bankAccount'                   => ['nullable', 'string', 'max:30'],
            'bankAccountType'               => ['required', Rule::in(['NRE', 'NRO', 'ESCROW'])],
            'bankSwift'                     => ['nullable', 'string', 'max:20'],
            'custodianName'                 => ['nullable', 'string', 'max:200'],
            'dpId'                          => ['nullable', 'string', 'max:20'],
            'clientId'                      => ['nullable', 'string', 'max:20'],

            // Step 8 — Additional Info
            'primaryContactName'            => ['nullable', 'string', 'max:150'],
            'primaryContactDesignation'     => ['nullable', 'string', 'max:100'],
            'investmentManagerName'         => ['nullable', 'string', 'max:150'],
            'indiaPlaceOfBusiness'          => ['nullable', 'string', 'max:200'],

            // Step 9 — Final Declarations
            'declarationAgreed'             => ['accepted'],
            'signatureName'                 => ['required', 'string', 'max:200'],
        ];

        $messages = [
            'pan.regex'          => 'PAN must be 5 letters, 4 digits, then 1 letter (e.g. AAACG1234F).',
            'pan.size'           => 'PAN must be exactly 10 characters.',
            'lei.regex'          => 'LEI must be 20 alphanumeric characters.',
            'lei.size'           => 'LEI must be exactly 20 characters.',
            'declarationAgreed.accepted' => 'You must agree to the declaration before saving.',
            'uboOwnership.between'       => 'Ownership % must be between 0 and 100.',
            '*.required_if'      => 'This field is required based on your earlier selection.',
        ];

        $request->validate($rules, $messages);

        // Demo mode: nothing is persisted yet. Persistence (form -> CAF tables)
        // is the next phase; this method currently only validates.

        return redirect()
            ->route('fpi.index')
            ->with('status', 'FPI Registration details validated & saved successfully (Demo Mode)!');
    }
}
