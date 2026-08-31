<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FpiController extends Controller
{
    /**
     * Default (demo) data used to pre-populate the FPI registration form.
     */
    private function defaultForm(): array
    {
        return [
            'nameTitle' => 'M/S',
            'entityName' => 'GLOBAL ALPHAS FPI FUND',
            'knownByAnotherName' => 'NO',
            'otherTitle' => '',
            'otherEntityName' => '',
            'dateOfIncorporation' => '2015-06-12',
            'dateOfCommencementOfBusiness' => '2015-07-01',
            'placeOfIncorporation' => 'NEW YORK',
            'countryOfIncorporation' => 'UNITED STATES',
            'lei' => '549300INF823N7179062',
            'leiExpiryDate' => '2027-06-12',
            'regAddressLine1' => '120 BROADWAY',
            'regAddressLine2' => 'SUITE 3000',
            'regAddressLine3' => 'FINANCIAL DISTRICT',
            'regCity' => 'NEW YORK',
            'regState' => 'NEW YORK',
            'regCountry' => 'UNITED STATES',
            'regZip' => '10271',
            'sameAddress' => true,
            'commAddressLine1' => '120 BROADWAY',
            'commAddressLine2' => 'SUITE 3000',
            'commAddressLine3' => 'FINANCIAL DISTRICT',
            'commCity' => 'NEW YORK',
            'commState' => 'NEW YORK',
            'commCountry' => 'UNITED STATES',
            'commZip' => '10271',
            'telNumber' => '+1-212-555-0199',
            'mobileNumber' => '+1-917-555-0144',
            'email' => 'compliance@globalalphasfund.com',
            'hasUbos' => 'YES',
            'uboName' => 'JOHNATHAN DAVIS',
            'uboDob' => '1970-04-18',
            'uboNationality' => 'AMERICAN',
            'uboPassport' => 'USA839103982',
            'uboOwnership' => '35',
            'uboAddress' => '55 EAST 72ND ST, NEW YORK, NY 10021',
            'incomeRange' => 'ABOVE_1M',
            'netWorth' => '45000000',
            'netWorthDate' => '2026-03-31',
            'taxCountry' => 'UNITED STATES',
            'tin' => '13-3918239',
            'fpiCategory' => 'CAT_I',
            'regulatoryStatus' => 'REGULATED',
            'regulatorName' => 'SECURITIES AND EXCHANGE COMMISSION (SEC)',
            'licenseNumber' => 'SEC-FPI-9281A',
            'regulatorJurisdiction' => 'UNITED STATES',
            'pan' => 'AAACG1234F',
            'bankName' => 'CITIBANK N.A. MUMBAI',
            'bankAccount' => '98765432101',
            'bankAccountType' => 'NRO',
            'bankSwift' => 'CITIINBX',
            'custodianName' => 'DEUTSCHE BANK AG',
            'dpId' => 'IN300162',
            'clientId' => '10928374',
            'primaryContactName' => 'SARAH JENKINS',
            'primaryContactDesignation' => 'COMPLIANCE OFFICER',
            'investmentManagerName' => 'ALPHA ASSET MANAGEMENT LLC',
            'indiaPlaceOfBusiness' => 'NONE',
            'declarationAgreed' => true,
            'signatureName' => 'SARAH JENKINS',
            'uploadedIncorpCert' => 'incorp_cert.pdf',
            'uploadedLeiProof' => 'lei_verification.pdf',
            'uploadedPanCopy' => 'pan_card_copy.pdf',
            'uploadedUboDecl' => 'ubo_declaration.pdf',
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
        $validated = $request->validate([
            'nameTitle' => 'required|string',
            'entityName' => 'required|string|max:255',
            'placeOfIncorporation' => 'required|string|max:255',
            'countryOfIncorporation' => 'required|string|max:255',
            'pan' => 'required|string|max:20',
            'signatureName' => 'required|string|max:255',
        ]);

        // Demo mode: nothing is persisted. In production, save $request->all() here.

        return redirect()
            ->route('fpi.index')
            ->with('status', 'FPI Registration details saved successfully (Demo Mode)!');
    }
}
