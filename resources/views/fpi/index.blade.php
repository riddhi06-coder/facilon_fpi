@extends('layouts.app')

@section('title', 'FPI Registration')
@section('page_title', 'FPI Registration')
@section('page_desc', 'Complete your Foreign Portfolio Investor registration details.')
@section('page_icon')
<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.8">
    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
    <polyline points="14,2 14,8 20,8" />
    <line x1="16" y1="13" x2="8" y2="13" />
    <line x1="16" y1="17" x2="8" y2="17" />
</svg>
@endsection

@php
$isIndividual = ($entityType ?? 'Non-Individual') === 'Individual';
if ($isIndividual) {
$steps = [
['id' => 'applicant', 'num' => 'Profile', 'tab' => 'Applicant Profile', 'title' => 'Step 1: Basic Applicant Details'],
['id' => 'contact', 'num' => 'Contact', 'tab' => 'Address & Contact', 'title' => 'Step 2: Address & Contact Information'],
['id' => 'financial', 'num' => 'Financial', 'tab' => 'Financial & Tax', 'title' => 'Step 3: Financial & Tax Information'],
['id' => 'category', 'num' => 'Category', 'tab' => 'Regulatory', 'title' => 'Step 4: Regulatory & Business Classification'],
['id' => 'depository', 'num' => 'PAN & Bank', 'tab' => 'Bank, Depository & PAN', 'title' => 'Step 5: Bank, Depository & PAN Details'],
['id' => 'additional', 'num' => 'Additional', 'tab' => 'Additional Info', 'title' => 'Step 6: Additional Information'],
['id' => 'declarations', 'num' => 'Declarations', 'tab' => 'Declarations', 'title' => 'Step 7: Document Upload & Declaration'],
];
} else {
$steps = [
['id' => 'applicant', 'num' => 'Profile', 'tab' => 'Applicant Profile', 'title' => 'Step 1: Basic Applicant Details'],
['id' => 'contact', 'num' => 'Contact', 'tab' => 'Contact & Address', 'title' => 'Step 2: Contact & Address Details'],
['id' => 'ubo_tool', 'num' => 'UBO Tool', 'tab' => 'UBO Determination', 'title' => 'Step 3: UBO Determination Tool'],
['id' => 'ubo', 'num' => 'UBO', 'tab' => 'Beneficial Ownership', 'title' => 'Step 4: Beneficial Ownership Information'],
['id' => 'financial', 'num' => 'Financial', 'tab' => 'Financial & Tax', 'title' => 'Step 5: Financial & Tax Information'],
['id' => 'category', 'num' => 'Category', 'tab' => 'Category & Regulatory', 'title' => 'Step 6: Category & Regulatory Classification'],
['id' => 'depository', 'num' => 'PAN & Bank', 'tab' => 'PAN, Bank & Depository', 'title' => 'Step 7: PAN, Bank & Depository Details'],
['id' => 'additional', 'num' => 'Additional', 'tab' => 'Additional Info', 'title' => 'Step 8: Additional Information'],
['id' => 'declarations', 'num' => 'Declarations', 'tab' => 'Final Declarations', 'title' => 'Step 9: Final Declarations & Document Upload'],
];
}

$faqs = [
'applicant' => [
['q' => 'What is a Legal Entity Identifier (LEI)?', 'a' => 'A unique code used to identify legally distinct entities. Required under SEBI norms.'],
['q' => 'What if I have been known by another name?', 'a' => 'Include all known legal names with documentation.'],
],
'contact' => [
['q' => 'Can my registered address be different from correspondence address?', 'a' => 'Yes, you can specify different addresses. If they are the same, check the \'Same as Registered\' option.'],
['q' => 'Are ISD country codes mandatory for telephone numbers?', 'a' => 'Yes, please include the appropriate prefix (+1, +44, etc.) to ensure correct dialing formatting.'],
],
'ubo_tool' => [
['q' => 'How does this tool identify a UBO?', 'a' => 'It walks the ownership hierarchy, multiplies percentages across each layer, and flags any natural person whose effective ownership is 10% or more (SEBI FPI threshold).'],
['q' => 'What if no natural person crosses the threshold?', 'a' => 'If no individual is identified through ownership or control, the Senior Managing Official (SMO) must be designated as the beneficial owner.'],
],
'ubo' => [
['q' => 'Who qualifies as an Ultimate Beneficial Owner (UBO)?', 'a' => 'Any natural person who ultimately owns or controls a 10% or more interest in Category I, or 25% or more in Category II FPIs.'],
['q' => 'What if no natural person qualifies as a UBO?', 'a' => 'Under SEBI rules, you must identify the Senior Managing Official (SMO) of the FPI as the beneficial owner in this scenario.'],
],
'financial' => [
['q' => 'How frequently must net worth details be certified?', 'a' => 'Net worth must be certified by a public accountant or notary at least once a year.'],
['q' => 'What is TIN under FATCA?', 'a' => 'TIN stands for Tax Identification Number, required under Foreign Account Tax Compliance Act agreements.'],
],
'category' => [
['q' => 'Which category does my fund fall into?', 'a' => 'Category I includes sovereign wealth funds, central banks, and government agencies. Category II includes corporate bodies and private trusts.'],
],
'depository' => [
['q' => 'Is a physical Indian PAN card required?', 'a' => 'A scanned copy of the original PAN card must be uploaded. A digital PAN letter is also accepted during preliminary checks.'],
],
'additional' => [
['q' => 'Who is considered a primary contact person?', 'a' => 'A senior employee or compliance manager who can handle queries from local custodian banks or SEBI.'],
],
'declarations' => [
['q' => 'Can a digital signature be used for authorized sign-off?', 'a' => 'Yes, digital signatures containing verifiable certificates are accepted.'],
],
];

// Simple array for the JS layer (avoids @json parsing an inline arrow fn).
$stepsJs = array_map(fn ($s) => ['id' => $s['id'], 'title' => $s['title'], 'tab' => $s['tab']], $steps);
// Precomputed here (not inside <script>) so an editor JS-formatter can't mangle the `->` operators.
$uboCountriesJs = $countries->map(fn ($c) => ['id' => (string) $c->country_id, 'label' => $c->label_en])->values();
$serverErrorsJs = $errors->messages();
@endphp

@push('styles')
<link rel="stylesheet" href="{{ asset('css/fpi-form.css') }}">
@endpush

@section('content')
<div class="fpi-form-error-banner" id="fpiErrorBanner" style="display:none">
    Please correct the highlighted fields in this section.
</div>

{{-- Application switcher: reopen any earlier application, or start a new one --}}
<div class="fpi-appbar">
    <div class="fpi-appbar-left">
        <span class="fpi-appbar-icon">🗂</span>
        <div class="fpi-appbar-field">
            <label class="fpi-appbar-label" for="appSelect">Application</label>
            <select id="appSelect" class="fpi-appbar-select">
                <option value="">— New (unsaved) application —</option>
                @foreach ($applications as $a)
                <option value="{{ route('fpi.load', $a->applicant_id) }}" @selected($currentApplicantId==$a->applicant_id)>
                    FPI-{{ str_pad($a->applicant_id, 6, '0', STR_PAD_LEFT) }} · {{ $a->company_name ?: '(draft)' }} · {{ $a->application_status }}
                </option>
                @endforeach
            </select>
        </div>
        <button type="button" class="btn btn-outline btn-sm" style="height:34px" onclick="var v=document.getElementById('appSelect').value; if(v) window.location.href=v;">Open</button>
        <button type="submit" form="fpiNewForm" class="btn btn-primary btn-sm" style="height:34px">＋ New Application</button>
    </div>
    <button type="button" class="btn btn-outline btn-sm js-fpi-autofill" style="height:34px">⚡ Auto-fill &amp; Save All</button>
</div>

@if ($isSubmitted)
<div style="background:#eef3f5;border:1px solid var(--gray200);border-left:4px solid var(--primary);border-radius:6px;padding:12px 16px;margin-bottom:14px;font-size:13px;color:var(--gray700)">
    🔒 This application has been <strong>submitted</strong> and is now <strong>read-only</strong>. Use <strong>Print / Preview</strong> on the last tab, pick another from <strong>Application</strong> above, or start a new one.
</div>
@endif

<form id="fpiNewForm" method="POST" action="{{ route('fpi.new') }}" style="display:none">@csrf</form>

<form class="fpi-container" method="POST" action="{{ route('fpi.store') }}" id="fpiForm" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="section" id="fpiSection" value="applicant">
    <input type="hidden" name="uboStructure" id="uboStructureField" value="{{ $form['uboStructure'] }}">

    {{-- Progress stepper --}}
    <div class="fpi-header-banner">
        <div class="fpi-stepper">
            <div class="fpi-stepper-line">
                <div id="fpiProgressBar"></div>
            </div>
            @foreach ($steps as $i => $step)
            <div class="fpi-step" data-step="{{ $step['id'] }}" data-index="{{ $i }}">
                <div class="fpi-step-dot" data-num="{{ $i + 1 }}">{{ $i + 1 }}</div>
                <div class="fpi-step-label">{{ $step['num'] }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Tabs --}}
    <div class="fpi-tabs">
        @foreach ($steps as $step)
        <button type="button" class="fpi-tab-btn" data-step="{{ $step['id'] }}">{{ $step['tab'] }}</button>
        @endforeach
    </div>

    <div class="fpi-card">

        {{-- STEP 1: Applicant --}}
        <div class="fpi-step-panel" data-panel="applicant">
            <div class="fpi-card-heading">Step 1: Basic Applicant Details</div>
            @if ($isIndividual)
            <div class="fpi-grid">
                <div class="fpi-form-group">
                    <label class="fpi-label">Title <span class="fpi-req">*</span></label>
                    <select class="fpi-select" name="indTitle">
                        <option value="">Select</option>
                        @foreach (['Mr' => 'Mr', 'Mrs' => 'Mrs', 'Ms' => 'Ms'] as $v => $l)
                        <option value="{{ $v }}" @selected($form['indTitle']===$v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fpi-form-group"><label class="fpi-label">First Name <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="indFirstName" value="{{ $form['indFirstName'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Middle Name</label><input class="fpi-input" type="text" name="indMiddleName" value="{{ $form['indMiddleName'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Last Name / Surname <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="indLastName" value="{{ $form['indLastName'] }}"></div>
                <div class="fpi-form-group">
                    <label class="fpi-label">Ever known by another name? <span class="fpi-req">*</span></label>
                    <select class="fpi-select" name="indOtherName" id="indOtherName">
                        <option value="" @selected($form['indOtherName']==='' )>Select</option>
                        <option value="no" @selected($form['indOtherName']==='no' )>No</option>
                        <option value="yes" @selected($form['indOtherName']==='yes' )>Yes</option>
                    </select>
                </div>
            </div>
            <div id="indOtherNameGroup" style="{{ $form['indOtherName'] === 'yes' ? '' : 'display:none' }}">
                <div class="fpi-sub-heading">Other Name Details</div>
                <div class="fpi-grid">
                    <div class="fpi-form-group">
                        <label class="fpi-label">Title <span class="fpi-req">*</span></label>
                        <select class="fpi-select" name="indOtherTitle">
                            <option value="">Select</option>
                            @foreach (['Mr' => 'Mr', 'Mrs' => 'Mrs', 'Ms' => 'Ms'] as $v => $l)
                            <option value="{{ $v }}" @selected($form['indOtherTitle']===$v)>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fpi-form-group"><label class="fpi-label">Other First Name <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="indOtherFirstName" value="{{ $form['indOtherFirstName'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Other Middle Name</label><input class="fpi-input" type="text" name="indOtherMiddleName" value="{{ $form['indOtherMiddleName'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Other Last Name <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="indOtherLastName" value="{{ $form['indOtherLastName'] }}"></div>
                </div>
            </div>
            <div class="fpi-sub-heading">Birth &amp; Identity</div>
            <div class="fpi-grid">
                <div class="fpi-form-group"><label class="fpi-label">Date of Birth <span class="fpi-req">*</span></label><input class="fpi-input" type="date" name="indDob" max="{{ date('Y-m-d') }}" value="{{ $form['indDob'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Place of Birth <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="indPlaceOfBirth" value="{{ $form['indPlaceOfBirth'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Country of Birth <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="indCountryOfBirth" value="{{ $form['indCountryOfBirth'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">ISD Code (Country of Birth)</label><input class="fpi-input" type="text" name="indBirthIsd" value="{{ $form['indBirthIsd'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Nationality / Citizenship <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="indNationality" value="{{ $form['indNationality'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">ISD Code (Nationality)</label><input class="fpi-input" type="text" name="indNationalityIsd" value="{{ $form['indNationalityIsd'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Passport Number (if any)</label><input class="fpi-input" type="text" name="indPassport" value="{{ $form['indPassport'] }}"></div>
                <div class="fpi-form-group">
                    <label class="fpi-label">Gender <span class="fpi-req">*</span></label>
                    <select class="fpi-select" name="indGender">
                        <option value="">Select</option>
                        @foreach (['Male', 'Female', 'Transgender'] as $g)
                        <option value="{{ $g }}" @selected($form['indGender']===$g)>{{ $g }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fpi-form-group">
                    <label class="fpi-label">Marital Status <span class="fpi-req">*</span></label>
                    <select class="fpi-select" name="indMaritalStatus">
                        <option value="">Select</option>
                        @foreach (['Single', 'Married', 'Divorced', 'Widow/Widower'] as $m)
                        <option value="{{ $m }}" @selected($form['indMaritalStatus']===$m)>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fpi-form-group">
                    <label class="fpi-label">Citizenship Status <span class="fpi-req">*</span></label>
                    <select class="fpi-select" name="indCitizenshipStatus" id="indCitizenshipStatus">
                        <option value="">Select</option>
                        @foreach (['Resident' => 'Resident', 'NRI' => 'NRI', 'Foreigner' => 'Foreigner'] as $v => $l)
                        <option value="{{ $v }}" @selected($form['indCitizenshipStatus']===$v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fpi-form-group"><label class="fpi-label">Country of Citizenship <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="indCountryOfCitizenship" value="{{ $form['indCountryOfCitizenship'] }}"></div>
            </div>
            <div class="fpi-sub-heading">Parental &amp; Spouse Details</div>
            <div class="fpi-grid">
                <div class="fpi-form-group"><label class="fpi-label">Father's First Name <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="indFatherFirstName" value="{{ $form['indFatherFirstName'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Father's Middle Name</label><input class="fpi-input" type="text" name="indFatherMiddleName" value="{{ $form['indFatherMiddleName'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Father's Last Name <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="indFatherLastName" value="{{ $form['indFatherLastName'] }}"></div>
                <div class="fpi-form-group"></div>
                <div class="fpi-form-group"><label class="fpi-label">Mother's First Name <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="indMotherFirstName" value="{{ $form['indMotherFirstName'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Mother's Middle Name</label><input class="fpi-input" type="text" name="indMotherMiddleName" value="{{ $form['indMotherMiddleName'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Mother's Last Name <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="indMotherLastName" value="{{ $form['indMotherLastName'] }}"></div>
                <div class="fpi-form-group"></div>
                <div class="fpi-form-group"><label class="fpi-label">Spouse's First Name <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="indSpouseFirstName" value="{{ $form['indSpouseFirstName'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Spouse's Middle Name</label><input class="fpi-input" type="text" name="indSpouseMiddleName" value="{{ $form['indSpouseMiddleName'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Spouse's Last Name <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="indSpouseLastName" value="{{ $form['indSpouseLastName'] }}"></div>
            </div>
            @else
            <div class="fpi-grid">
                <div class="fpi-form-group">
                    <label class="fpi-label">Title <span class="fpi-req">*</span></label>
                    <select class="fpi-select" name="nameTitle">
                        <option value="" @selected($form['nameTitle']==='' )>Select</option>
                        @foreach (['M/S' => 'M/s', 'MR' => 'Mr', 'MRS' => 'Mrs', 'MS' => 'Ms'] as $v => $l)
                        <option value="{{ $v }}" @selected($form['nameTitle']===$v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fpi-form-group" style="grid-column: span 2">
                    <label class="fpi-label">Entity Name <span class="fpi-req">*</span></label>
                    <input class="fpi-input" type="text" name="entityName" value="{{ $form['entityName'] }}">
                </div>
                <div class="fpi-form-group">
                    <label class="fpi-label">Applicant Type <span class="fpi-req">*</span></label>
                    <select class="fpi-select" name="applicantType" id="applicantType">
                        <option value="" @selected($form['applicantType']==='' )>Select applicant type</option>
                        <option value="Partnership" @selected($form['applicantType']==='Partnership' )>Partnership</option>
                        <option value="Company" @selected($form['applicantType']==='Company' )>Company</option>
                        <option value="Trust" @selected($form['applicantType']==='Trust' )>Trust</option>
                        <option value="Unincorporated Association / Body of Individuals" @selected($form['applicantType']==='Unincorporated Association / Body of Individuals' )>Unincorporated Association / BOI</option>
                    </select>
                </div>
                <div class="fpi-form-group">
                    <label class="fpi-label">Ever known by another name? <span class="fpi-req">*</span></label>
                    <select class="fpi-select" name="knownByAnotherName" id="knownByAnotherName">
                        <option value="" @selected($form['knownByAnotherName']==='' )>Select</option>
                        <option value="NO" @selected($form['knownByAnotherName']==='NO' )>No</option>
                        <option value="YES" @selected($form['knownByAnotherName']==='YES' )>Yes</option>
                    </select>
                </div>

                <div class="fpi-form-group" id="otherNameTitleGroup" style="{{ $form['knownByAnotherName'] === 'YES' ? '' : 'display:none' }}">
                    <label class="fpi-label">Title</label>
                    <select class="fpi-select" name="otherTitle">
                        <option value="">Select</option>
                        @foreach (['M/S' => 'M/s', 'MR' => 'Mr', 'MRS' => 'Mrs'] as $v => $l)
                        <option value="{{ $v }}" @selected($form['otherTitle']===$v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fpi-form-group" id="otherNameGroup" style="grid-column: span 3;{{ $form['knownByAnotherName'] === 'YES' ? '' : 'display:none' }}">
                    <label class="fpi-label">Other Entity Name</label>
                    <input class="fpi-input" type="text" name="otherEntityName" value="{{ $form['otherEntityName'] }}">
                </div>

                <div class="fpi-form-group">
                    <label class="fpi-label">Date of Incorporation <span class="fpi-req">*</span></label>
                    <input class="fpi-input" type="date" name="dateOfIncorporation" max="{{ date('Y-m-d') }}" value="{{ $form['dateOfIncorporation'] }}">
                </div>
                <div class="fpi-form-group">
                    <label class="fpi-label">Date of Commencement of Business</label>
                    <input class="fpi-input" type="date" name="dateOfCommencementOfBusiness" max="{{ date('Y-m-d') }}" value="{{ $form['dateOfCommencementOfBusiness'] }}">
                </div>
                <div class="fpi-form-group">
                    <label class="fpi-label">Place of Incorporation <span class="fpi-req">*</span></label>
                    <input class="fpi-input" type="text" name="placeOfIncorporation" value="{{ $form['placeOfIncorporation'] }}">
                </div>
                <div class="fpi-form-group">
                    <label class="fpi-label">Country of Incorporation <span class="fpi-req">*</span></label>
                    <select class="fpi-select" name="countryOfIncorporation">
                        <option value="" @selected($form['countryOfIncorporation']==='' )>Select</option>
                        @foreach ($countries as $c)
                        <option value="{{ $c->country_id }}" @selected((string) $form['countryOfIncorporation']===(string) $c->country_id)>{{ $c->label_en }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fpi-form-group">
                    <label class="fpi-label">ISD Country Code (Country of Incorporation)</label>
                    <input class="fpi-input" type="text" name="incorporationIsdCode" value="{{ $form['incorporationIsdCode'] }}" placeholder="e.g. 65">
                </div>
                <div class="fpi-form-group" style="grid-column: span 2">
                    <label class="fpi-label">Legal Entity Identifier (LEI)</label>
                    <input class="fpi-input" type="text" name="lei" value="{{ $form['lei'] }}">
                </div>
                <div class="fpi-form-group">
                    <label class="fpi-label">LEI Expiry Date</label>
                    <input class="fpi-input" type="date" name="leiExpiryDate" value="{{ $form['leiExpiryDate'] }}">
                </div>
            </div>
            @endif
        </div>

        {{-- STEP 2: Contact & Address --}}
        <div class="fpi-step-panel" data-panel="contact">
            <div class="fpi-card-heading">Step 2: Contact & Address Details</div>
            <div class="fpi-sub-heading">Registered Address</div>
            <div class="fpi-grid" style="margin-bottom:16px">
                <div class="fpi-form-group"><label class="fpi-label">Address Line 1 <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="regAddressLine1" value="{{ $form['regAddressLine1'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Address Line 2 <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="regAddressLine2" value="{{ $form['regAddressLine2'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Address Line 3 <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="regAddressLine3" value="{{ $form['regAddressLine3'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Address Line 4</label><input class="fpi-input" type="text" name="regAddressLine4" value="{{ $form['regAddressLine4'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">City <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="regCity" value="{{ $form['regCity'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">State / Province <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="regState" value="{{ $form['regState'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Country <span class="fpi-req">*</span></label>
                    <select class="fpi-select" name="regCountry">
                        <option value="" @selected($form['regCountry']==='' )>Select</option>
                        @foreach ($countries as $c)
                        <option value="{{ $c->country_id }}" @selected((string) $form['regCountry']===(string) $c->country_id)>{{ $c->label_en }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fpi-form-group"><label class="fpi-label">ZIP / Postal Code <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="regZip" value="{{ $form['regZip'] }}"></div>
            </div>

            <div class="fpi-sub-heading" style="display:flex;align-items:center;gap:8px">
                <span>Correspondence Address</span>
                <label style="display:flex;align-items:center;gap:4px;font-weight:500;font-size:10.5px;color:var(--gray500)">
                    <input type="checkbox" name="sameAddress" id="sameAddress" @checked($form['sameAddress'])> Same as Registered
                </label>
            </div>
            <div class="fpi-grid" id="commAddressGrid">
                <div class="fpi-form-group"><label class="fpi-label">Address Line 1 <span class="fpi-req comm-req">*</span></label><input class="fpi-input comm-field" type="text" name="commAddressLine1" value="{{ $form['commAddressLine1'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Address Line 2 <span class="fpi-req comm-req">*</span></label><input class="fpi-input comm-field" type="text" name="commAddressLine2" value="{{ $form['commAddressLine2'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Address Line 3 <span class="fpi-req comm-req">*</span></label><input class="fpi-input comm-field" type="text" name="commAddressLine3" value="{{ $form['commAddressLine3'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Address Line 4</label><input class="fpi-input comm-field" type="text" name="commAddressLine4" value="{{ $form['commAddressLine4'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">City <span class="fpi-req comm-req">*</span></label><input class="fpi-input comm-field" type="text" name="commCity" value="{{ $form['commCity'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">State / Province <span class="fpi-req comm-req">*</span></label><input class="fpi-input comm-field" type="text" name="commState" value="{{ $form['commState'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Country <span class="fpi-req comm-req">*</span></label>
                    <select class="fpi-select comm-field" name="commCountry">
                        <option value="" @selected($form['commCountry']==='' )>Select</option>
                        @foreach ($countries as $c)
                        <option value="{{ $c->country_id }}" @selected((string) $form['commCountry']===(string) $c->country_id)>{{ $c->label_en }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fpi-form-group"><label class="fpi-label">ZIP / Postal Code <span class="fpi-req comm-req">*</span></label><input class="fpi-input comm-field" type="text" name="commZip" value="{{ $form['commZip'] }}"></div>
            </div>

            <div class="fpi-sub-heading" style="margin-top:16px">Office Address</div>
            <div class="fpi-grid">
                <div class="fpi-form-group"><label class="fpi-label">Address Line 1 <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="offAddressLine1" value="{{ $form['offAddressLine1'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Address Line 2 <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="offAddressLine2" value="{{ $form['offAddressLine2'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Address Line 3</label><input class="fpi-input" type="text" name="offAddressLine3" value="{{ $form['offAddressLine3'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Address Line 4</label><input class="fpi-input" type="text" name="offAddressLine4" value="{{ $form['offAddressLine4'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">City <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="offCity" value="{{ $form['offCity'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">State / Province <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="offState" value="{{ $form['offState'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Country <span class="fpi-req">*</span></label>
                    <select class="fpi-select" name="offCountry">
                        <option value="" @selected($form['offCountry']==='' )>Select</option>
                        @foreach ($countries as $c)
                        <option value="{{ $c->country_id }}" @selected((string) $form['offCountry']===(string) $c->country_id)>{{ $c->label_en }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fpi-form-group"><label class="fpi-label">ZIP / Postal Code <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="offZip" value="{{ $form['offZip'] }}"></div>
            </div>

            <div class="fpi-sub-heading" style="margin-top:16px">Contact Details</div>
            <div class="fpi-grid">
                <div class="fpi-form-group"><label class="fpi-label">Registered Phone – Country Code <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="telIsdCode" value="{{ $form['telIsdCode'] }}" placeholder="e.g. 65"></div>
                <div class="fpi-form-group"><label class="fpi-label">Registered Phone – Area Code <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="telAreaCode" value="{{ $form['telAreaCode'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Registered Phone Number <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="telNumber" value="{{ $form['telNumber'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Office Phone – Country Code</label><input class="fpi-input" type="text" name="offTelIsdCode" value="{{ $form['offTelIsdCode'] }}" placeholder="e.g. 65"></div>
                <div class="fpi-form-group"><label class="fpi-label">Office Phone – Area Code</label><input class="fpi-input" type="text" name="offTelAreaCode" value="{{ $form['offTelAreaCode'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Office Phone Number</label><input class="fpi-input" type="text" name="offTelNumber" value="{{ $form['offTelNumber'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Mobile Number <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="mobileNumber" value="{{ $form['mobileNumber'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Fax Number</label><input class="fpi-input" type="text" name="faxNumber" value="{{ $form['faxNumber'] }}"></div>
                <div class="fpi-form-group" style="grid-column: span 2"><label class="fpi-label">Email Address <span class="fpi-req">*</span></label><input class="fpi-input" type="email" name="email" value="{{ $form['email'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Website</label><input class="fpi-input" type="text" name="website" value="{{ $form['website'] }}" placeholder="https://"></div>
            </div>
        </div>

        @unless ($isIndividual)
        {{-- STEP 3: UBO Determination Tool (embedded) --}}
        <div class="fpi-step-panel" data-panel="ubo_tool">
            <div class="fpi-card-heading">Step 3: UBO Determination Tool</div>

            <div class="ubo-card">
                <div class="ubo-card-title" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
                    <span>Define Shareholding / Ownership Hierarchy</span>
                    <span style="font-size:10.5px;color:#0f766e;background:#ccfbf1;padding:2px 8px;border-radius:10px;font-weight:600">Threshold: 10% (SEBI FPI Regulation)</span>
                </div>
                <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;margin-bottom:12px">
                    <button type="button" class="ubo-btn ubo-btn-primary" id="resetFlow">Start / Reset Flow</button>
                    <span style="font-size:11px;color:var(--gray500);font-style:italic">Applicant Type &amp; Entity Name come from the Applicant Profile tab. SMO applies only if no natural person meets the threshold.</span>
                </div>
                <div id="hierarchy" style="display:flex;flex-direction:column;gap:20px"></div>
            </div>

            <div class="ubo-card">
                <div class="ubo-card-title">UBO Summary</div>
                <p style="font-size:12px;color:var(--gray600);margin-bottom:12px">
                    Build at least one entity or sub-fund flow and click "Evaluate UBOs" to calculate direct &amp; indirect ownership thresholds.
                </p>
                <div style="margin-bottom:16px">
                    <button type="button" class="ubo-btn ubo-btn-secondary" id="evaluateBtn">Evaluate UBOs</button>
                </div>
                <div id="evalResult">
                    <div style="border:1px dashed var(--gray300);padding:16px;text-align:center;color:var(--gray500);font-size:11.5px;border-radius:6px">
                        Awaiting evaluation...
                    </div>
                </div>
            </div>

            <div class="ubo-card">
                <div class="ubo-card-title">Ownership Diagram</div>
                <div style="display:flex;gap:10px;margin-bottom:16px">
                    <button type="button" class="ubo-btn ubo-btn-primary" id="tabDiagram">Visualize Diagram</button>
                    <button type="button" class="ubo-btn ubo-btn-ghost" id="tabMermaid">Download Mermaid Source</button>
                </div>
                <div id="diagramView"></div>
                <div id="mermaidView" style="display:none">
                    <textarea readonly id="mermaidCode" style="width:100%;height:140px;padding:10px;font-family:monospace;font-size:11.5px;border-radius:4px;border:1px solid var(--gray300);background:#fafafa;outline:none"></textarea>
                    <div style="font-size:10.5px;color:var(--gray500);margin-top:4px">
                        You can copy this Mermaid source code and paste it into any Mermaid viewer or compiler.
                    </div>
                </div>
            </div>
        </div>

        {{-- STEP 4: Beneficial Ownership --}}
        <div class="fpi-step-panel" data-panel="ubo">
            <div class="fpi-card-heading">Step 4: Beneficial Ownership &amp; Intermediate Shareholding Information</div>

            {{-- Sub-Funds --}}
            <div class="fpi-grid" style="margin-bottom:16px">
                <div class="fpi-form-group" style="grid-column: span 2">
                    <label class="fpi-label">Are there Sub-Funds? <span class="fpi-req">*</span></label>
                    <select class="fpi-select" name="hasSubFunds" id="hasSubFunds">
                        <option value="" @selected($form['hasSubFunds']==='' )>Select</option>
                        <option value="YES" @selected($form['hasSubFunds']==='YES' )>Yes</option>
                        <option value="NO" @selected($form['hasSubFunds']==='NO' )>No</option>
                    </select>
                </div>
            </div>
            <div id="subFundSection" style="{{ $form['hasSubFunds'] === 'YES' ? '' : 'display:none' }}">
                <div class="fpi-sub-heading" style="display:flex;justify-content:space-between;align-items:center">
                    <span>Sub-Funds</span>
                    <button type="button" class="btn btn-ghost btn-sm" id="subFundAddRow" style="font-size:12px">+ Add Sub-Fund</button>
                </div>
                <input type="hidden" name="subFundsJson" id="subFundsJsonField">
                <div id="subFundRowsContainer"></div>
                <div id="subFundEmptyNote" style="display:none;font-size:11.5px;color:var(--gray500);font-style:italic;border:1px dashed var(--gray300);padding:12px;border-radius:6px;margin-bottom:16px">
                    No sub-funds yet. Click “+ Add Sub-Fund”.
                </div>
            </div>

            <div class="fpi-grid" style="margin-bottom:16px">
                <div class="fpi-form-group" style="grid-column: span 2">
                    <label class="fpi-label">Does the entity have Ultimate Beneficial Owners (UBOs)? <span class="fpi-req">*</span></label>
                    <select class="fpi-select" name="hasUbos" id="hasUbos">
                        <option value="" @selected($form['hasUbos']==='' )>Select</option>
                        <option value="YES" @selected($form['hasUbos']==='YES' )>Yes</option>
                        <option value="NO" @selected($form['hasUbos']==='NO' )>No</option>
                    </select>
                </div>
            </div>
            <div id="uboBlock" style="{{ $form['hasUbos'] === 'YES' ? '' : 'display:none' }}">
                <div class="fpi-sub-heading" style="display:flex;justify-content:space-between;align-items:center">
                    <span>Ultimate Beneficial Owners</span>
                    <button type="button" class="btn btn-ghost btn-sm" id="uboAddRow" style="font-size:12px">+ Add UBO</button>
                </div>
                <div style="font-size:11px;color:var(--gray500);margin-bottom:10px">
                    Names are carried from the <strong>UBO Determination</strong> tab. Complete the remaining details for each — all fields are required.
                </div>
                <input type="hidden" name="uboRowsJson" id="uboRowsJsonField">
                <div id="uboRowsContainer"></div>
                <div id="uboEmptyNote" class="empty" style="display:none;font-size:11.5px;color:var(--gray500);font-style:italic;border:1px dashed var(--gray300);padding:12px;border-radius:6px">
                    No UBOs yet. Add natural persons in the UBO Determination tab, or click “+ Add UBO”.
                </div>
            </div>

            {{-- Intermediate Entities --}}
            <div class="fpi-sub-heading" style="display:flex;justify-content:space-between;align-items:center;margin-top:20px">
                <span>Intermediate Entities</span>
                <button type="button" class="btn btn-ghost btn-sm" id="intAddRow" style="font-size:12px">+ Add Intermediate Entity</button>
            </div>
            <div id="intermediatesErrorWrap"><input type="hidden" name="intermediatesJson" id="intermediatesJsonField"></div>
            <div id="intRowsContainer"></div>
            <div id="intEmptyNote" style="font-size:11.5px;color:var(--gray500);font-style:italic;border:1px dashed var(--gray300);padding:12px;border-radius:6px">
                No intermediate entities added.
            </div>

            {{-- Controlling Entities --}}
            <div class="fpi-sub-heading" style="display:flex;justify-content:space-between;align-items:center;margin-top:20px">
                <span>Controlling Entities</span>
                <button type="button" class="btn btn-ghost btn-sm" id="ctrlAddRow" style="font-size:12px">+ Add Controller</button>
            </div>
            <div id="controllersErrorWrap"><input type="hidden" name="controllersJson" id="controllersJsonField"></div>
            <div id="ctrlRowsContainer"></div>
            <div id="ctrlEmptyNote" style="font-size:11.5px;color:var(--gray500);font-style:italic;border:1px dashed var(--gray300);padding:12px;border-radius:6px">
                No controlling entities added.
            </div>
        </div>

        @endunless

        {{-- STEP 4: Financial & Tax --}}
        <div class="fpi-step-panel" data-panel="financial">
            <div class="fpi-card-heading">Step 5: Financial & Tax Information</div>
            <div class="fpi-grid">
                <div class="fpi-form-group">
                    <label class="fpi-label">Gross Annual Income <span class="fpi-req">*</span></label>
                    <select class="fpi-select" name="incomeRange">
                        <option value="" @selected($form['incomeRange']==='' )>Select</option>
                        @foreach (['UNDER_50K' => 'Under $50,000', '50K_250K' => '$50,000 - $250,000', '250K_1M' => '$250,000 - $1,000,000', 'ABOVE_1M' => 'Above $1,000,000'] as $v => $l)
                        <option value="{{ $v }}" @selected($form['incomeRange']===$v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fpi-form-group"><label class="fpi-label">Net Worth in USD <span class="fpi-req">*</span></label><input class="fpi-input" type="number" name="netWorth" value="{{ $form['netWorth'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Net Worth Date</label><input class="fpi-input" type="date" name="netWorthDate" value="{{ $form['netWorthDate'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Tax Residency Country <span class="fpi-req">*</span></label>
                    <select class="fpi-select" name="taxCountry">
                        <option value="" @selected($form['taxCountry']==='' )>Select</option>
                        @foreach ($countries as $c)
                        <option value="{{ $c->country_id }}" @selected((string) $form['taxCountry']===(string) $c->country_id)>{{ $c->label_en }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fpi-form-group"><label class="fpi-label">Tax Identification Number (TIN) <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="tin" value="{{ $form['tin'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Gross Annual Income (INR) @if($isIndividual)<span class="fpi-req">*</span>@endif</label><input class="fpi-input" type="number" name="grossIncome" value="{{ $form['grossIncome'] }}"></div>
                <div class="fpi-form-group">
                    <label class="fpi-label">Occupation (Non-Individual) @if($isIndividual)<span class="fpi-req">*</span>@endif</label>
                    <select class="fpi-select" name="occupation">
                        <option value="">Select</option>
                        @foreach (['privateCompany' => 'Private Company (R)', 'publicCompany' => 'Public Company (U)', 'bodyCorporate' => 'Body Corporate (D)', 'finInst' => 'Financial Institution (S)', 'noGovt' => 'Non-Government Organization (N)', 'charitableOrg' => 'Charitable Organization (C)'] as $v => $l)
                        <option value="{{ $v }}" @selected($form['occupation']===$v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="fpi-sub-heading" style="margin-top:16px;border-bottom:none;margin-bottom:4px">Source of Income (select all that apply) @if($isIndividual)<span class="fpi-req">*</span>@endif</div>
            <div id="incomeSourcesErrorWrap" style="margin-bottom:8px"><input type="hidden" name="incomeSourcesJson" id="incomeSourcesJsonField"></div>
            <div class="fpi-grid">
                @php
                $incomeOpts = ['salary' => 'Salary', 'capital_gains' => 'Capital Gains', 'business' => 'Income from Business/Profession', 'house_property' => 'Income from House Property', 'other_sources' => 'Income from Other Sources', 'no_income' => 'No Income'];
                $selInc = (array) ($form['incomeSources'] ?? []);
                @endphp
                @foreach ($incomeOpts as $v => $l)
                <label class="fpi-form-group" style="flex-direction:row;align-items:center;gap:8px;font-size:12.5px;color:var(--gray700)">
                    <input type="checkbox" name="incomeSources[]" value="{{ $v }}" @checked(in_array($v, $selInc, true)) class="js-income-src"> {{ $l }}
                </label>
                @endforeach
            </div>
            <div class="fpi-grid" id="professionCodeGroup" style="{{ in_array('business', $selInc, true) ? '' : 'display:none' }}">
                <div class="fpi-form-group" style="grid-column: span 2">
                    <label class="fpi-label">Business / Profession Code</label>
                    <select class="fpi-select" name="professionCode">
                        <option value="">-- Select Code --</option>
                        @foreach (['01'=>'Medical Profession and Business','02'=>'Engineering','03'=>'Architecture','04'=>'Chartered Accountant/Accountancy','05'=>'Interior Decoration','06'=>'Technical Consultancy','07'=>'Company Secretary','08'=>'Legal Practitioner and Solicitors','09'=>'Government Contractors','10'=>'Insurance Agency','11'=>'Films, TV and other entertainment','12'=>'Information Technology','13'=>'Builders and Developers','14'=>'Stock Brokers/Sub-Brokers','15'=>'Performing Arts and Yatra','16'=>'Operation of Ships/Aircrafts','17'=>'Transport Services','18'=>'Horse Racing','19'=>'Cinema Halls and Theatres','20'=>'Others'] as $v => $l)
                        <option value="{{ $v }}" @selected((string) $form['professionCode']===(string) $v)>{{ $v }} - {{ $l }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="fpi-sub-heading" style="margin-top:16px">Tax Residency &amp; Declarations</div>
            <div class="fpi-grid">
                <div class="fpi-form-group">
                    <label class="fpi-label">If TIN not available, reason</label>
                    <select class="fpi-select" name="tinReason">
                        <option value="">-- Select --</option>
                        @foreach (['no_tin_issued' => 'Country does not issue TINs', 'not_required' => 'Not required to provide TIN', 'other' => 'Other (provide explanation)'] as $v => $l)
                        <option value="{{ $v }}" @selected($form['tinReason']===$v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fpi-form-group"><label class="fpi-label">Explanation (if Other)</label><input class="fpi-input" type="text" name="tinExplanation" value="{{ $form['tinExplanation'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Tax Residency Certificate No.</label><input class="fpi-input" type="text" name="trcNumber" value="{{ $form['trcNumber'] }}"></div>
                <div class="fpi-form-group">
                    <label class="fpi-label">Valid FATCA/CRS self-certification provided?</label>
                    <select class="fpi-select" name="fatcaCrs">
                        <option value="">Select</option>
                        <option value="yes" @selected($form['fatcaCrs']==='yes' )>Yes</option>
                        <option value="notapplicable" @selected($form['fatcaCrs']==='notapplicable' )>Not Applicable</option>
                    </select>
                </div>
                <div class="fpi-form-group">
                    <label class="fpi-label">Any signatory/promoter/director a PEP?</label>
                    <select class="fpi-select" name="isPep">
                        <option value="">Select</option>
                        <option value="no" @selected($form['isPep']==='no' )>No</option>
                        <option value="yes" @selected($form['isPep']==='yes' )>Yes</option>
                    </select>
                </div>
                <div class="fpi-form-group">
                    <label class="fpi-label">Any of the above related to a PEP?</label>
                    <select class="fpi-select" name="relatedToPep">
                        <option value="">Select</option>
                        <option value="no" @selected($form['relatedToPep']==='no' )>No</option>
                        <option value="yes" @selected($form['relatedToPep']==='yes' )>Yes</option>
                    </select>
                </div>
            </div>

            {{-- Additional Tax Residencies --}}
            <div class="fpi-sub-heading" style="display:flex;justify-content:space-between;align-items:center;margin-top:16px">
                <span>Additional Tax Residencies</span>
                <button type="button" class="btn btn-ghost btn-sm" id="taxResAddRow" style="font-size:12px">+ Add Tax Residency</button>
            </div>
            <input type="hidden" name="taxResidenciesJson" id="taxResidenciesJsonField">
            <div id="taxResRowsContainer"></div>
            <div id="taxResEmptyNote" style="font-size:11.5px;color:var(--gray500);font-style:italic;border:1px dashed var(--gray300);padding:12px;border-radius:6px">No additional tax residencies added.</div>
        </div>

        {{-- STEP 5: Category & Regulatory --}}
        <div class="fpi-step-panel" data-panel="category">
            <div class="fpi-card-heading">Step 6: Category & Regulatory Classification</div>
            <div class="fpi-grid">
                <div class="fpi-form-group">
                    <label class="fpi-label">FPI Category <span class="fpi-req">*</span></label>
                    <select class="fpi-select" name="fpiCategory">
                        <option value="" @selected($form['fpiCategory']==='' )>Select</option>
                        <option value="CAT_I" @selected($form['fpiCategory']==='CAT_I' )>Category I</option>
                        <option value="CAT_II" @selected($form['fpiCategory']==='CAT_II' )>Category II</option>
                    </select>
                </div>
                <div class="fpi-form-group">
                    <label class="fpi-label">Regulatory Status <span class="fpi-req">*</span></label>
                    <select class="fpi-select" name="regulatoryStatus">
                        <option value="" @selected($form['regulatoryStatus']==='' )>Select</option>
                        <option value="REGULATED" @selected($form['regulatoryStatus']==='REGULATED' )>Regulated</option>
                        <option value="UNREGULATED" @selected($form['regulatoryStatus']==='UNREGULATED' )>Unregulated</option>
                    </select>
                </div>
                <div class="fpi-form-group" style="grid-column: span 2"><label class="fpi-label">Regulator Name @if($isIndividual)<span class="fpi-req">*</span>@endif</label><input class="fpi-input" type="text" name="regulatorName" value="{{ $form['regulatorName'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Registration / License Number @if($isIndividual)<span class="fpi-req">*</span>@endif</label><input class="fpi-input" type="text" name="licenseNumber" value="{{ $form['licenseNumber'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Regulator Jurisdiction @if($isIndividual)<span class="fpi-req">*</span>@endif</label>
                    <select class="fpi-select" name="regulatorJurisdiction">
                        <option value="" @selected($form['regulatorJurisdiction']==='' )>Select</option>
                        @foreach ($countries as $c)
                        <option value="{{ $c->country_id }}" @selected((string) $form['regulatorJurisdiction']===(string) $c->country_id)>{{ $c->label_en }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fpi-form-group">
                    <label class="fpi-label">Sub-Category <span class="fpi-req">*</span></label>
                    <select class="fpi-select" name="subCategory">
                        <option value="" @selected($form['subCategory']==='' )>-- Select Sub-Category --</option>
                        <optgroup label="Category I">
                            @foreach (['gov_investors'=>'Government and Government-related investors','pension_university_funds'=>'Pension Funds and University Funds','regulated_entities'=>'Appropriately regulated entities (banks, AMCs, etc.)','regulated_funds'=>'Appropriately regulated/unregulated funds with regulated IM','fpi_with_eligible_owners'=>'Entities with eligible Investment Manager or Ownership'] as $v => $l)
                            <option value="{{ $v }}" @selected($form['subCategory']===$v)>{{ $l }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Category II">
                            @foreach (['regulated_funds_not_cat1'=>'Appropriately regulated funds not eligible as Category I','endowments_foundations'=>'Endowments and Foundations','charitable_org'=>'Charitable Organisations','corporate_bodies'=>'Corporate Bodies','family_offices'=>'Family Offices','individuals'=>'Individuals','regulated_entities_client'=>'Regulated entities investing on behalf of clients','unregulated_funds'=>'Unregulated Funds (LPs, Trusts)'] as $v => $l)
                            <option value="{{ $v }}" @selected($form['subCategory']===$v)>{{ $l }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
                <div class="fpi-form-group">
                    <label class="fpi-label">MIM Structure?</label>
                    <select class="fpi-select" name="mimStructure">
                        <option value="" @selected($form['mimStructure']==='' )>Select</option>
                        <option value="yes" @selected($form['mimStructure']==='yes' )>Yes</option>
                        <option value="no" @selected($form['mimStructure']==='no' )>No</option>
                    </select>
                </div>
                <div class="fpi-form-group">
                    <label class="fpi-label">Regulator Website</label><input class="fpi-input" type="text" name="regulatorWebsite" value="{{ $form['regulatorWebsite'] }}">
                </div>
                <div class="fpi-form-group">
                    <label class="fpi-label">Capacity / Role with Regulator</label><input class="fpi-input" type="text" name="regulatorCapacity" value="{{ $form['regulatorCapacity'] }}">
                </div>
            </div>

            {{-- Investment Managers --}}
            <div class="fpi-sub-heading" style="display:flex;justify-content:space-between;align-items:center;margin-top:16px">
                <span>Investment Manager(s)</span>
                <button type="button" class="btn btn-ghost btn-sm" id="imAddRow" style="font-size:12px">+ Add Investment Manager</button>
            </div>
            <div id="investmentManagersErrorWrap"><input type="hidden" name="investmentManagersJson" id="investmentManagersJsonField"></div>
            <div id="imRowsContainer"></div>
            <div id="imEmptyNote" style="font-size:11.5px;color:var(--gray500);font-style:italic;border:1px dashed var(--gray300);padding:12px;border-radius:6px">No investment managers added.</div>

            {{-- Compliance Officer --}}
            <div class="fpi-sub-heading" style="margin-top:16px">Compliance Officer</div>
            <div class="fpi-grid">
                <div class="fpi-form-group"><label class="fpi-label">Name @if($isIndividual)<span class="fpi-req">*</span>@endif</label><input class="fpi-input" type="text" name="complianceName" value="{{ $form['complianceName'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Job Title @if($isIndividual)<span class="fpi-req">*</span>@endif</label><input class="fpi-input" type="text" name="complianceTitle" value="{{ $form['complianceTitle'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Email ID @if($isIndividual)<span class="fpi-req">*</span>@endif</label><input class="fpi-input" type="email" name="complianceEmail" value="{{ $form['complianceEmail'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Phone Number @if($isIndividual)<span class="fpi-req">*</span>@endif</label><input class="fpi-input" type="text" name="compliancePhone" value="{{ $form['compliancePhone'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Fax Number</label><input class="fpi-input" type="text" name="complianceFax" value="{{ $form['complianceFax'] }}"></div>
            </div>

            {{-- Global Custodian --}}
            <div class="fpi-sub-heading" style="margin-top:16px">Global Custodian</div>
            <div class="fpi-grid">
                <div class="fpi-form-group">
                    <label class="fpi-label">Using a Global Custodian? @if($isIndividual)<span class="fpi-req">*</span>@endif</label>
                    <select class="fpi-select" name="hasCustodian" id="hasCustodian">
                        <option value="" @selected($form['hasCustodian']==='' )>Select</option>
                        <option value="yes" @selected($form['hasCustodian']==='yes' )>Yes</option>
                        <option value="no" @selected($form['hasCustodian']==='no' )>No</option>
                    </select>
                </div>
            </div>
            <div class="fpi-grid" id="custodianInfoSection" style="{{ $form['hasCustodian'] === 'yes' ? '' : 'display:none' }}">
                <div class="fpi-form-group"><label class="fpi-label">Custodian Name</label><input class="fpi-input" type="text" name="custodianNameCat" value="{{ $form['custodianNameCat'] ?? '' }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Regulator</label><input class="fpi-input" type="text" name="custodianReg" value="{{ $form['custodianReg'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Registration No./Code with Regulator</label><input class="fpi-input" type="text" name="custRegCode" value="{{ $form['custRegCode'] }}"></div>
                <div class="fpi-form-group" style="grid-column: span 2"><label class="fpi-label">Address</label><input class="fpi-input" type="text" name="custodianAddress" value="{{ $form['custodianAddress'] }}"></div>
            </div>

            {{-- Disciplinary History --}}
            <div class="fpi-sub-heading" style="margin-top:16px">Disciplinary History</div>
            <div class="fpi-grid">
                <div class="fpi-form-group">
                    <label class="fpi-label">Any past regulatory issues? @if($isIndividual)<span class="fpi-req">*</span>@endif</label>
                    <select class="fpi-select" name="disciplinaryHistory">
                        <option value="" @selected($form['disciplinaryHistory']==='' )>Select</option>
                        <option value="no" @selected($form['disciplinaryHistory']==='no' )>No</option>
                        <option value="yes" @selected($form['disciplinaryHistory']==='yes' )>Yes</option>
                    </select>
                </div>
                <div class="fpi-form-group" style="grid-column: span 3"><label class="fpi-label">If yes, provide details</label><input class="fpi-input" type="text" name="disciplinaryDetails" value="{{ $form['disciplinaryDetails'] }}"></div>
            </div>

            {{-- Clubbing of Investment --}}
            <div class="fpi-sub-heading" style="margin-top:16px">Clubbing of Investment Limit</div>
            <div class="fpi-grid">
                <div class="fpi-form-group" style="grid-column: span 2">
                    <label class="fpi-label">Common ownership &gt;50% / common control with other FPIs (Reg 22(4))?</label>
                    <select class="fpi-select" name="clubbingDeclaration" id="clubbingDeclaration">
                        <option value="" @selected($form['clubbingDeclaration']==='' )>Select</option>
                        <option value="yes" @selected($form['clubbingDeclaration']==='yes' )>Yes</option>
                        <option value="no" @selected($form['clubbingDeclaration']==='no' )>No</option>
                    </select>
                </div>
            </div>
            <div id="investorGroupSection" style="{{ $form['clubbingDeclaration'] === 'yes' ? '' : 'display:none' }}">
                <div class="fpi-grid">
                    <div class="fpi-form-group">
                        <label class="fpi-label">Have an Investor Group Number (NSDL)?</label>
                        <select class="fpi-select" name="fpiGroupNumber" id="fpiGroupNumber">
                            <option value="" @selected($form['fpiGroupNumber']==='' )>Select</option>
                            <option value="yes" @selected($form['fpiGroupNumber']==='yes' )>Yes</option>
                            <option value="no" @selected($form['fpiGroupNumber']==='no' )>No</option>
                        </select>
                    </div>
                    <div class="fpi-form-group" id="groupNumberGroup" style="grid-column: span 3;{{ $form['fpiGroupNumber'] === 'yes' ? '' : 'display:none' }}"><label class="fpi-label">Investor Group Number</label><input class="fpi-input" type="text" name="groupNumber" value="{{ $form['groupNumber'] }}"></div>
                </div>
                <div class="fpi-sub-heading" style="display:flex;justify-content:space-between;align-items:center">
                    <span>Investor Group (if no number)</span>
                    <button type="button" class="btn btn-ghost btn-sm" id="fpiGroupAddRow" style="font-size:12px">+ Add Row</button>
                </div>
                <input type="hidden" name="fpiGroupJson" id="fpiGroupJsonField">
                <div id="fpiGroupRowsContainer"></div>
                <div id="fpiGroupEmptyNote" style="font-size:11.5px;color:var(--gray500);font-style:italic;border:1px dashed var(--gray300);padding:12px;border-radius:6px">No rows added.</div>
            </div>

            {{-- Exempt Public Retail Funds --}}
            <div class="fpi-sub-heading" style="display:flex;justify-content:space-between;align-items:center;margin-top:16px">
                <span>Exempt Public Retail Funds with Common Control</span>
                <button type="button" class="btn btn-ghost btn-sm" id="publicRetailAddRow" style="font-size:12px">+ Add Row</button>
            </div>
            <div id="publicRetailErrorWrap"><input type="hidden" name="publicRetailJson" id="publicRetailJsonField"></div>
            <div id="publicRetailRowsContainer"></div>
            <div id="publicRetailEmptyNote" style="font-size:11.5px;color:var(--gray500);font-style:italic;border:1px dashed var(--gray300);padding:12px;border-radius:6px">No rows added.</div>

            {{-- Prior Association --}}
            <div class="fpi-sub-heading" style="margin-top:16px">Prior Association with Indian Securities Market</div>
            <div class="fpi-grid">
                <div class="fpi-form-group" style="grid-column: span 2">
                    <label class="fpi-label">Ever associated as FPI, FII, sub-account, QFI or FVCI? @if($isIndividual)<span class="fpi-req">*</span>@endif</label>
                    <select class="fpi-select" name="priorAssociation" id="priorAssociation">
                        <option value="" @selected($form['priorAssociation']==='' )>Select</option>
                        <option value="yes" @selected($form['priorAssociation']==='yes' )>Yes</option>
                        <option value="no" @selected($form['priorAssociation']==='no' )>No</option>
                    </select>
                </div>
            </div>
            <div id="priorAssociationSection" style="{{ $form['priorAssociation'] === 'yes' ? '' : 'display:none' }}">
                <div class="fpi-sub-heading" style="display:flex;justify-content:space-between;align-items:center">
                    <span>Association Details</span>
                    <button type="button" class="btn btn-ghost btn-sm" id="priorAssocAddRow" style="font-size:12px">+ Add Association</button>
                </div>
                <input type="hidden" name="priorAssociationsJson" id="priorAssociationsJsonField">
                <div id="priorAssocRowsContainer"></div>
                <div id="priorAssocEmptyNote" style="font-size:11.5px;color:var(--gray500);font-style:italic;border:1px dashed var(--gray300);padding:12px;border-radius:6px">No associations added.</div>
            </div>
        </div>

        {{-- STEP 6: PAN, Bank & Depository --}}
        <div class="fpi-step-panel" data-panel="depository">
            <div class="fpi-card-heading">Step 7: PAN, Bank & Depository Details</div>
            <div class="fpi-sub-heading">Bank Account Details</div>
            <div class="fpi-grid" style="margin-bottom:16px">
                <div class="fpi-form-group"><label class="fpi-label">Bank Name <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="bankName" value="{{ $form['bankName'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Account Number <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="bankAccount" value="{{ $form['bankAccount'] }}"></div>
                <div class="fpi-form-group">
                    <label class="fpi-label">Account Type <span class="fpi-req">*</span></label>
                    <select class="fpi-select" name="bankAccountType">
                        <option value="" @selected($form['bankAccountType']==='' )>Select</option>
                        @foreach (['NRE' => 'NRE', 'NRO' => 'NRO', 'ESCROW' => 'Escrow'] as $v => $l)
                        <option value="{{ $v }}" @selected($form['bankAccountType']===$v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fpi-form-group"><label class="fpi-label">SWIFT / IFSC Code @if($isIndividual)<span class="fpi-req">*</span>@endif</label><input class="fpi-input" type="text" name="bankSwift" value="{{ $form['bankSwift'] }}"></div>
            </div>
            <div class="fpi-sub-heading">Custodian & Depository Participant Details</div>
            <div class="fpi-grid">
                <div class="fpi-form-group"><label class="fpi-label">Custodian Name @if($isIndividual)<span class="fpi-req">*</span>@endif</label><input class="fpi-input" type="text" name="custodianName" value="{{ $form['custodianName'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">DP ID @if($isIndividual)<span class="fpi-req">*</span>@endif</label><input class="fpi-input" type="text" name="dpId" value="{{ $form['dpId'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Client ID @if($isIndividual)<span class="fpi-req">*</span>@endif</label><input class="fpi-input" type="text" name="clientId" value="{{ $form['clientId'] }}"></div>
            </div>

            {{-- PAN Information --}}
            <div class="fpi-sub-heading" style="margin-top:16px">PAN Information</div>
            <div class="fpi-grid">
                <div class="fpi-form-group">
                    <label class="fpi-label">Do you already hold a PAN? @if($isIndividual)<span class="fpi-req">*</span>@endif</label>
                    <select class="fpi-select" name="hasPan" id="hasPan">
                        <option value="" @selected($form['hasPan']==='' )>Select</option>
                        <option value="yes" @selected($form['hasPan']==='yes' )>Yes</option>
                        <option value="no" @selected($form['hasPan']==='no' )>No</option>
                    </select>
                </div>
            </div>
            <div id="panDetailsGroup" style="{{ $form['hasPan'] === 'yes' ? '' : 'display:none' }}">
                <div class="fpi-grid">
                    <div class="fpi-form-group"><label class="fpi-label">Enter PAN number <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="pan" value="{{ $form['pan'] }}" maxlength="10"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Name on PAN Card <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="existingpanName" value="{{ $form['existingpanName'] }}"></div>
                </div>
            </div>
            <div id="applyPanSection" style="{{ $form['hasPan'] === 'no' ? '' : 'display:none' }}">
                <div class="fpi-grid">
                    <div class="fpi-form-group">
                        <label class="fpi-label">Status of Applicant</label>
                        <select class="fpi-select" name="statusApplicant">
                            <option value="" @selected($form['statusApplicant']==='' )>Select</option>
                            @foreach (['Company','PartnershipFirm','Government','Trusts','BodyOfIndividuals','LocalAuthority','ArtificialJuridicalPersons','AssociationOfPersons','LLP'] as $v)
                            <option value="{{ $v }}" @selected($form['statusApplicant']===$v)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fpi-form-group" style="grid-column: span 2"><label class="fpi-label">Name to be printed on PAN Card</label><input class="fpi-input" type="text" name="panName" value="{{ $form['panName'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">AO Area Code</label><input class="fpi-input" type="text" name="aoAreaCode" value="{{ $form['aoAreaCode'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">AO Type</label><input class="fpi-input" type="text" name="aoType" value="{{ $form['aoType'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">AO Range Code</label><input class="fpi-input" type="text" name="aoRangeCode" value="{{ $form['aoRangeCode'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">AO Number</label><input class="fpi-input" type="text" name="aoNo" value="{{ $form['aoNo'] }}"></div>
                    <div class="fpi-form-group" style="grid-column: span 2"><label class="fpi-label">Registration Number (Registrar/Authority)</label><input class="fpi-input" type="text" name="registrationNumber" value="{{ $form['registrationNumber'] }}"></div>
                </div>
            </div>

            {{-- Representative / Agent in India --}}
            <div class="fpi-sub-heading" style="margin-top:16px">Representative or Agent in India</div>
            <div class="fpi-grid">
                <div class="fpi-form-group">
                    <label class="fpi-label">Title</label>
                    <select class="fpi-select" name="repTitle">
                        <option value="" @selected($form['repTitle']==='' )>Select</option>
                        @foreach (['Shri/Mr.','Smt/Mrs.','Kumari/Ms.','M/s'] as $v)
                        <option value="{{ $v }}" @selected($form['repTitle']===$v)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fpi-form-group"><label class="fpi-label">Last Name / Surname</label><input class="fpi-input" type="text" name="repLastName" value="{{ $form['repLastName'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">First Name</label><input class="fpi-input" type="text" name="repFirstName" value="{{ $form['repFirstName'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Middle Name</label><input class="fpi-input" type="text" name="repMiddleName" value="{{ $form['repMiddleName'] }}"></div>
                <div class="fpi-form-group" style="grid-column: span 4"><label class="fpi-label">Address</label><input class="fpi-input" type="text" name="repAddress" value="{{ $form['repAddress'] }}"></div>
            </div>

            {{-- Stock Exchange & POI/POA --}}
            <div class="fpi-sub-heading" style="margin-top:16px">Listing, Proof of Identity &amp; Address</div>
            <div class="fpi-grid">
                <div class="fpi-form-group">
                    <label class="fpi-label">Listed on stock exchange?</label>
                    <select class="fpi-select" name="listed" id="listed">
                        <option value="" @selected($form['listed']==='' )>Select</option>
                        <option value="no" @selected($form['listed']==='no' )>No</option>
                        <option value="yes" @selected($form['listed']==='yes' )>Yes</option>
                    </select>
                </div>
                <div class="fpi-form-group" id="exchangeNameGroup" style="{{ $form['listed'] === 'yes' ? '' : 'display:none' }}"><label class="fpi-label">Exchange Name</label><input class="fpi-input" type="text" name="exchangeName" value="{{ $form['exchangeName'] }}"></div>
                @php $poiOpts = ['copy_registration_cert_overseas' => 'Certificate of Registration (overseas, attested)', 'copy_registration_cert_india' => 'Certificate of Registration (India)']; @endphp
                <div class="fpi-form-group">
                    <label class="fpi-label">POI Document</label>
                    <select class="fpi-select" name="poiType">
                        <option value="">Select</option>
                        @foreach ($poiOpts as $v => $l)<option value="{{ $v }}" @selected($form['poiType']===$v)>{{ $l }}</option>@endforeach
                    </select>
                </div>
                <div class="fpi-form-group"><label class="fpi-label">POI Document Number</label><input class="fpi-input" type="text" name="poiNumber" value="{{ $form['poiNumber'] }}"></div>
                <div class="fpi-form-group">
                    <label class="fpi-label">POA Document</label>
                    <select class="fpi-select" name="poaType">
                        <option value="">Select</option>
                        @foreach ($poiOpts as $v => $l)<option value="{{ $v }}" @selected($form['poaType']===$v)>{{ $l }}</option>@endforeach
                    </select>
                </div>
                <div class="fpi-form-group"><label class="fpi-label">POA Document Number</label><input class="fpi-input" type="text" name="poaNumber" value="{{ $form['poaNumber'] }}"></div>
            </div>

            {{-- Sensitive Activities --}}
            <div class="fpi-sub-heading" style="margin-top:16px">Sensitive Activities</div>
            <input type="hidden" name="sensitiveActivitiesJson" id="sensitiveActivitiesJsonField">
            <div class="fpi-grid">
                @php $actOpts = ['forex' => 'Foreign Exchange / Money Changer', 'gaming' => 'Gaming / Gambling / Lottery', 'moneylending' => 'Money Lending & Pawning']; $selAct = (array) ($form['sensitiveActivities'] ?? []); @endphp
                @foreach ($actOpts as $v => $l)
                <label class="fpi-form-group" style="flex-direction:row;align-items:center;gap:8px;font-size:12.5px;color:var(--gray700)">
                    <input type="checkbox" class="js-sensitive-act" value="{{ $v }}" @checked(in_array($v, $selAct, true))> {{ $l }}
                </label>
                @endforeach
            </div>

            {{-- Depository & Bank Authorization --}}
            <div class="fpi-sub-heading" style="margin-top:16px">Depository &amp; Bank Account Opening</div>
            <div class="fpi-grid">
                <div class="fpi-form-group" style="grid-column: span 2">
                    <label class="fpi-label">Depository Account Authorisation</label>
                    <select class="fpi-select" name="depositoryAuth">
                        <option value="" @selected($form['depositoryAuth']==='' )>Select</option>
                        <option value="OpenAccount" @selected($form['depositoryAuth']==='OpenAccount' )>Request to open Depository account</option>
                        <option value="NonInvestingFPI" @selected($form['depositoryAuth']==='NonInvestingFPI' )>Non-investing FPI — do not open Depository account</option>
                    </select>
                </div>
                <div class="fpi-form-group">
                    <label class="fpi-label">Mode of Operation</label>
                    <select class="fpi-select" name="modeOfOperation" id="modeOfOperation">
                        <option value="" @selected($form['modeOfOperation']==='' )>Select</option>
                        @foreach (['AnyOneSingle' => 'Any one single', 'JointlyBy' => 'Jointly by', 'AsPerResolution' => 'As per resolution', 'Others' => 'Others (specify)'] as $v => $l)
                        <option value="{{ $v }}" @selected($form['modeOfOperation']===$v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fpi-form-group" id="otherModeGroup" style="{{ $form['modeOfOperation'] === 'Others' ? '' : 'display:none' }}"><label class="fpi-label">Specify Other Mode</label><input class="fpi-input" type="text" name="otherMode" value="{{ $form['otherMode'] }}"></div>
                <div class="fpi-form-group" style="grid-column: span 2">
                    <label class="fpi-label">Bank Account Authorisation</label>
                    <select class="fpi-select" name="bankAuth">
                        <option value="" @selected($form['bankAuth']==='' )>Select</option>
                        <option value="OpenSNRA" @selected($form['bankAuth']==='OpenSNRA' )>Request to open SNRA account</option>
                        <option value="NoBankAccount" @selected($form['bankAuth']==='NoBankAccount' )>Non-investing FPI — do not open Bank account</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- STEP 7: Additional --}}
        <div class="fpi-step-panel" data-panel="additional">
            <div class="fpi-card-heading">Step 8: Additional Information</div>
            <div class="fpi-grid">
                <div class="fpi-form-group"><label class="fpi-label">Primary Contact Person <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="primaryContactName" value="{{ $form['primaryContactName'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Designation <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="primaryContactDesignation" value="{{ $form['primaryContactDesignation'] }}"></div>
                <div class="fpi-form-group" style="grid-column: span 2"><label class="fpi-label">Investment Manager Name</label><input class="fpi-input" type="text" name="investmentManagerName" value="{{ $form['investmentManagerName'] }}"></div>
                <div class="fpi-form-group" style="grid-column: span 2"><label class="fpi-label">Place of Business in India (if any)</label><input class="fpi-input" type="text" name="indiaPlaceOfBusiness" value="{{ $form['indiaPlaceOfBusiness'] }}"></div>
                <div class="fpi-form-group">
                    <label class="fpi-label">Separate Registration for ODIs with Derivatives?</label>
                    <select class="fpi-select" name="odiDerivatives">
                        <option value="" @selected($form['odiDerivatives']==='' )>Select</option>
                        <option value="yes" @selected($form['odiDerivatives']==='yes' )>Yes</option>
                        <option value="no" @selected($form['odiDerivatives']==='no' )>No</option>
                    </select>
                </div>
            </div>

            {{-- Sub-Funds / Share Classes investing in India --}}
            <div class="fpi-sub-heading" style="display:flex;justify-content:space-between;align-items:center;margin-top:16px">
                <span>Sub-Funds / Share Classes Investing in India</span>
                <button type="button" class="btn btn-ghost btn-sm" id="shareClassAddRow" style="font-size:12px">+ Add Row</button>
            </div>
            <div id="shareClassesErrorWrap"><input type="hidden" name="shareClassesJson" id="shareClassesJsonField"></div>
            <div id="shareClassRowsContainer"></div>
            <div id="shareClassEmptyNote" style="font-size:11.5px;color:var(--gray500);font-style:italic;border:1px dashed var(--gray300);padding:12px;border-radius:6px">No rows added.</div>

            {{-- Eligible Category I Entities --}}
            <div class="fpi-sub-heading" style="display:flex;justify-content:space-between;align-items:center;margin-top:16px">
                <span>Eligible Category I Entities (Reg 5(a)(v)(B))</span>
                <button type="button" class="btn btn-ghost btn-sm" id="cat1AddRow" style="font-size:12px">+ Add Entity</button>
            </div>
            <div id="categoryOneEntitiesErrorWrap"><input type="hidden" name="categoryOneEntitiesJson" id="categoryOneEntitiesJsonField"></div>
            <div id="cat1RowsContainer"></div>
            <div id="cat1EmptyNote" style="font-size:11.5px;color:var(--gray500);font-style:italic;border:1px dashed var(--gray300);padding:12px;border-radius:6px">No entities added.</div>

            {{-- Bank / Subsidiary Declaration --}}
            <div class="fpi-sub-heading" style="margin-top:16px">Bank or Subsidiary of Bank Declaration</div>
            <div class="fpi-grid">
                <div class="fpi-form-group" style="grid-column: span 3">
                    <label class="fpi-label">Declaration</label>
                    <select class="fpi-select" name="bankDeclaration" id="bankDeclaration">
                        <option value="" @selected($form['bankDeclaration']==='' )>Select</option>
                        <option value="not_bank" @selected($form['bankDeclaration']==='not_bank' )>Not a bank / subsidiary of a bank</option>
                        <option value="has_branch" @selected($form['bankDeclaration']==='has_branch' )>Bank/subsidiary WITH branch/rep office in India</option>
                        <option value="no_branch" @selected($form['bankDeclaration']==='no_branch' )>Bank/subsidiary WITHOUT branch/rep office in India</option>
                    </select>
                </div>
                <div class="fpi-form-group" id="bankEntityNameGroup" style="{{ $form['bankDeclaration'] === 'has_branch' ? '' : 'display:none' }}"><label class="fpi-label">Name of Entity</label><input class="fpi-input" type="text" name="bankEntityName" value="{{ $form['bankEntityName'] }}"></div>
            </div>

            {{-- NRI/OCI/RI Declaration --}}
            <div class="fpi-sub-heading" style="margin-top:16px">NRI / OCI / RI Declaration — Control</div>
            <div class="fpi-grid">
                <div class="fpi-form-group" style="grid-column: span 2">
                    <label class="fpi-label">Control (Group 1)</label>
                    <select class="fpi-select" name="nriControl1">
                        <option value="" @selected($form['nriControl1']==='' )>Select</option>
                        <option value="no_control" @selected($form['nriControl1']==='no_control' )>No NRI/OCI/RI exercises control</option>
                        <option value="controlled_meet_2yr" @selected($form['nriControl1']==='controlled_meet_2yr' )>NRI/OCI/RI control — will meet conditions in 2 yrs</option>
                    </select>
                </div>
                <div class="fpi-form-group" style="grid-column: span 2">
                    <label class="fpi-label">Control (Group 2)</label>
                    <select class="fpi-select" name="nriControl2" id="nriControl2">
                        <option value="" @selected($form['nriControl2']==='' )>Select</option>
                        <option value="not_controlled_by_nri_im" @selected($form['nriControl2']==='not_controlled_by_nri_im' )>Not controlled by NRI/OCI/RI-controlled IM</option>
                        <option value="controlled_by_im" @selected($form['nriControl2']==='controlled_by_im' )>Controlled by NRI/OCI/RI-controlled IM</option>
                    </select>
                </div>
                <div class="fpi-form-group" id="imTypesGroup" style="grid-column: span 4;{{ $form['nriControl2'] === 'controlled_by_im' ? '' : 'display:none' }}">
                    <label class="fpi-label">IM Type</label>
                    <input type="hidden" name="imTypesJson" id="imTypesJsonField">
                    @php $selImTypes = (array) ($form['imTypes'] ?? []); @endphp
                    <div style="display:flex;flex-direction:column;gap:6px;font-size:12.5px;color:var(--gray700)">
                        <label style="display:flex;gap:8px;align-items:center"><input type="checkbox" class="js-im-type" value="regulated_fpi" @checked(in_array('regulated_fpi', $selImTypes, true))> Regulated &amp; registered with SEBI as non-investing FPI</label>
                        <label style="display:flex;gap:8px;align-items:center"><input type="checkbox" class="js-im-type" value="indian_laws" @checked(in_array('indian_laws', $selImTypes, true))> Incorporated under Indian laws &amp; registered with SEBI</label>
                    </div>
                </div>
                <div class="fpi-form-group">
                    <label class="fpi-label">Directly/indirectly owned by NRI/OCI/RI?</label>
                    <select class="fpi-select" name="directlyControlled" id="directlyControlled">
                        <option value="" @selected($form['directlyControlled']==='' )>Select</option>
                        <option value="yes" @selected($form['directlyControlled']==='yes' )>Yes</option>
                        <option value="no" @selected($form['directlyControlled']==='no' )>No</option>
                    </select>
                </div>
                <div class="fpi-form-group" id="nriEntityNameGroup" style="{{ $form['directlyControlled'] === 'yes' ? '' : 'display:none' }}"><label class="fpi-label">Name of Entity</label><input class="fpi-input" type="text" name="nriControlEntityName" value="{{ $form['nriControlEntityName'] }}"></div>
                <div class="fpi-form-group">
                    <label class="fpi-label">Offshore fund with SEBI NOC?</label>
                    <select class="fpi-select" name="offshoreFund">
                        <option value="" @selected($form['offshoreFund']==='' )>Select</option>
                        <option value="yes" @selected($form['offshoreFund']==='yes' )>Yes</option>
                        <option value="no" @selected($form['offshoreFund']==='no' )>No</option>
                    </select>
                </div>
                <div class="fpi-form-group" style="grid-column: span 2">
                    <label class="fpi-label">Entitlement in FPI</label>
                    <select class="fpi-select" name="nriEntitlement">
                        <option value="" @selected($form['nriEntitlement']==='' )>Select</option>
                        <option value="no_investors" @selected($form['nriEntitlement']==='no_investors' )>No NRI/OCI/RI investors</option>
                        <option value="within_limit" @selected($form['nriEntitlement']==='within_limit' )>Within 25%/50% limits</option>
                        <option value="will_meet_2yr" @selected($form['nriEntitlement']==='will_meet_2yr' )>Will meet conditions in 2 yrs</option>
                        <option value="mf_only" @selected($form['nriEntitlement']==='mf_only' )>Investing only in mutual funds</option>
                    </select>
                </div>
            </div>

            {{-- Reg 5(b)(vii) Clients --}}
            <div class="fpi-sub-heading" style="margin-top:16px">Investments on Behalf of Clients — Reg 5(b)(vii)</div>
            <div class="fpi-grid">
                <div class="fpi-form-group" style="grid-column: span 2">
                    <label class="fpi-label">Seeking registration under Reg 5(b)(vii)?</label>
                    <select class="fpi-select" name="reg5b7" id="reg5b7">
                        <option value="" @selected($form['reg5b7']==='' )>Select</option>
                        <option value="Yes" @selected($form['reg5b7']==='Yes' )>Yes</option>
                        <option value="No" @selected($form['reg5b7']==='No' )>No</option>
                    </select>
                </div>
            </div>
            @php $allElig = $form['clientEligI'] && $form['clientEligII'] && $form['clientEligIII']; @endphp
            <div id="clientCheckboxSection" style="{{ $form['reg5b7'] === 'Yes' ? '' : 'display:none' }}">
                <div style="display:flex;flex-direction:column;gap:8px;font-size:11.5px;color:var(--gray700);margin-bottom:8px">
                    <label style="display:flex;gap:8px;align-items:flex-start"><input type="checkbox" name="clientEligI" class="js-client-elig" style="margin-top:2px" @checked($form['clientEligI'])> i. Clients are individuals and/or family offices.</label>
                    <label style="display:flex;gap:8px;align-items:flex-start"><input type="checkbox" name="clientEligII" class="js-client-elig" style="margin-top:2px" @checked($form['clientEligII'])> ii. Clients are eligible for registration as FPI and are not dealing on behalf of third party.</label>
                    <label style="display:flex;gap:8px;align-items:flex-start"><input type="checkbox" name="clientEligIII" class="js-client-elig" style="margin-top:2px" @checked($form['clientEligIII'])> iii. Applicable KYC prescribed by SEBI has been performed on the clients.</label>
                    <label style="display:flex;gap:8px;align-items:flex-start;opacity:.7"><input type="checkbox" id="clientEligIV" style="margin-top:2px" disabled @checked($allElig)> iv. The complete investor details of its clients is as below and we shall provide the same on a quarterly basis to the DDP.</label>
                </div>
            </div>
            <div id="clientsSection" style="{{ $form['reg5b7'] === 'Yes' && $allElig ? '' : 'display:none' }}">
                <div class="fpi-sub-heading" style="display:flex;justify-content:space-between;align-items:center">
                    <span>Client Details</span>
                    <button type="button" class="btn btn-ghost btn-sm" id="clientAddRow" style="font-size:12px">+ Add Client</button>
                </div>
                <div id="clientsErrorWrap"><input type="hidden" name="clientsJson" id="clientsJsonField"></div>
                <div id="clientRowsContainer"></div>
                <div id="clientEmptyNote" style="font-size:11.5px;color:var(--gray500);font-style:italic;border:1px dashed var(--gray300);padding:12px;border-radius:6px">No clients added.</div>
            </div>

            {{-- KRA Consent --}}
            <div class="fpi-sub-heading" style="margin-top:16px">KRA Consent</div>
            <div class="fpi-grid">
                <div class="fpi-form-group" style="grid-column: span 2">
                    <label class="fpi-label">FPI wishes KRAs to seek consent before download?</label>
                    <select class="fpi-select" name="kraConsent" id="kraConsent">
                        <option value="" @selected($form['kraConsent']==='' )>Select</option>
                        <option value="yes" @selected($form['kraConsent']==='yes' )>Yes</option>
                        <option value="no" @selected($form['kraConsent']==='no' )>No</option>
                    </select>
                </div>
            </div>
            <div class="fpi-grid" id="kraInfoSection" style="{{ $form['kraConsent'] === 'yes' ? '' : 'display:none' }}">
                <div class="fpi-form-group" style="grid-column: span 2"><label class="fpi-label">Authorized Representative Name</label><input class="fpi-input" type="text" name="kraRepName" value="{{ $form['kraRepName'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Email ID 1 (Mandatory)</label><input class="fpi-input" type="email" name="kraEmail1" value="{{ $form['kraEmail1'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Email ID 2</label><input class="fpi-input" type="email" name="kraEmail2" value="{{ $form['kraEmail2'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Email ID 3</label><input class="fpi-input" type="email" name="kraEmail3" value="{{ $form['kraEmail3'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Mobile Number</label><input class="fpi-input" type="text" name="kraMobile" value="{{ $form['kraMobile'] }}"></div>
            </div>

            {{-- Authorized Signatories --}}
            <div class="fpi-sub-heading" style="display:flex;justify-content:space-between;align-items:center;margin-top:16px">
                <span>Authorized Signatories / Senior Management</span>
                <button type="button" class="btn btn-ghost btn-sm" id="signAddRow" style="font-size:12px">+ Add Person</button>
            </div>
            <div id="signatoriesErrorWrap"><input type="hidden" name="signatoriesJson" id="signatoriesJsonField"></div>
            <div id="signRowsContainer"></div>
            <div id="signEmptyNote" style="font-size:11.5px;color:var(--gray500);font-style:italic;border:1px dashed var(--gray300);padding:12px;border-radius:6px">No signatories added.</div>
        </div>

        {{-- STEP 8: Declarations --}}
        <div class="fpi-step-panel" data-panel="declarations">
            <div class="fpi-card-heading" style="display:flex;justify-content:space-between;align-items:center;gap:12px">
                <span>{{ $isIndividual ? 'Step 7: Document Upload & Declaration' : 'Step 9: Final Declarations & Document Upload' }}</span>
                <a href="{{ route('fpi.preview') }}" target="_blank" rel="noopener" id="fpiPrintPreviewTop" class="btn btn-sm fpi-print-btn" style="display:none;padding:5px 14px;font-size:12px;text-decoration:none;white-space:nowrap">🖨 Print / Preview</a>
            </div>
            <div id="fpiSubmittedNote" style="display:none;background:#d1e7dd;color:#0f5132;border:1px solid #badbcc;border-radius:6px;padding:10px 14px;font-size:11.5px;margin-bottom:14px">
                ✅ Your application has been submitted. You can now use <strong>Print Preview</strong> to review / print the completed form.
            </div>
            <div class="fpi-sub-heading">Required Document Proofs</div>
            <div style="font-size:10.5px;color:var(--gray500);margin-bottom:10px">Only PDF or Word (.doc / .docx) files are allowed, up to 5 MB each — nothing larger.</div>
            <div class="fpi-grid" style="margin-bottom:16px">
                @php
                $uploads = [
                ['name' => 'uploadedIncorpCert', 'label' => 'Certificate of Incorporation', 'req' => true],
                ['name' => 'uploadedLeiProof', 'label' => 'Proof of LEI Registration', 'req' => false],
                ['name' => 'uploadedPanCopy', 'label' => 'Copy of Indian PAN Card', 'req' => true],
                ['name' => 'uploadedUboDecl', 'label' => 'UBO List & Declaration', 'req' => false],
                ['name' => 'uploadedPoi', 'label' => 'Proof of Identity (POI)', 'req' => false],
                ['name' => 'uploadedPoa', 'label' => 'Proof of Address (POA)', 'req' => false],
                ['name' => 'uploadedFatca', 'label' => 'FATCA/CRS Declaration Form', 'req' => false],
                ['name' => 'uploadedSignature', 'label' => 'Signature / Thumb Impression', 'req' => false],
                ];
                @endphp
                @foreach ($uploads as $u)
                @php $saved = $form[$u['name']] ?? ''; $uri = $form[$u['name'].'_uri'] ?? ''; @endphp
                <div class="fpi-form-group">
                    <label class="fpi-label">{{ $u['label'] }} @if ($u['req'])<span class="fpi-req">*</span>@endif</label>
                    <label class="fpi-file-upload">
                        <input type="file" name="{{ $u['name'] }}" accept=".pdf,.doc,.docx" @if ($saved) data-uploaded="1" @endif>
                        <span class="fpi-file-icon">{{ $saved ? '📄' : '📁' }}</span>
                        <span class="fpi-file-name">{{ $saved ? $saved : 'Click to upload (PDF / Word, ≤5 MB)' }}</span>
                    </label>
                    @if ($saved && $uri)
                    <a href="{{ asset($uri) }}" target="_blank" rel="noopener" style="font-size:10px;color:var(--primary);font-weight:600;margin-top:4px;text-decoration:none">🔍 Preview uploaded file · choose a file above to replace</a>
                    @endif
                </div>
                @endforeach
            </div>
            <div class="fpi-grid" style="margin-bottom:16px">
                <div class="fpi-form-group" style="grid-column: span 2">
                    <label class="fpi-label">Any Other Supporting Documents</label>
                    <label class="fpi-file-upload">
                        <input type="file" name="otherDocs[]" accept=".pdf,.jpg,.png" multiple>
                        <span class="fpi-file-icon">📁</span>
                        <span class="fpi-file-name">
                            @if (!empty($form['otherDocs'])){{ implode(', ', (array) $form['otherDocs']) }}@else Click to upload one or more files @endif
                        </span>
                    </label>
                </div>
            </div>
            <div style="display:flex;gap:8px;align-items:flex-start;margin-bottom:16px">
                <input type="checkbox" name="declarationAgreed" style="margin-top:2px" @checked($form['declarationAgreed'])>
                <div style="font-size:11px;color:var(--gray700)">
                    <span class="fpi-req">*</span> I/We hereby declare that all details and documents provided in this registration form are true, correct, and complete to the best of my/our knowledge and belief. I/We undertake to inform the depository participant / custodian immediately of any changes.
                </div>
            </div>
            <div class="fpi-grid">
                <div class="fpi-form-group"><label class="fpi-label">Place <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="declarationPlace" value="{{ $form['declarationPlace'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Date <span class="fpi-req">*</span></label><input class="fpi-input" type="date" name="declarationDate" value="{{ $form['declarationDate'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Name of the Applicant <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="applicantName" value="{{ $form['applicantName'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Designation <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="applicantDesignation" value="{{ $form['applicantDesignation'] }}"></div>
                <div class="fpi-form-group" style="grid-column: span 2"><label class="fpi-label">Authorized Signatory Name <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="signatureName" value="{{ $form['signatureName'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Signatory Designation <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="authDesignation" value="{{ $form['authDesignation'] }}"></div>
                <div class="fpi-form-group"><label class="fpi-label">Signatory Date <span class="fpi-req">*</span></label><input class="fpi-input" type="date" name="authDate" value="{{ $form['authDate'] }}"></div>
            </div>
            <div class="fpi-sub-heading" style="margin-top:16px">Declarations</div>
            @foreach ([['declaration1', 'The information provided is true and correct to the best of my/our knowledge.'], ['declaration2', 'I/We agree to comply with SEBI regulations and any applicable laws.'], ['declaration3', 'I/We confirm that all documents uploaded are valid and not forged.']] as [$dn, $dl])
            <label style="display:flex;gap:8px;align-items:flex-start;margin-bottom:8px;font-size:11px;color:var(--gray700)">
                <input type="checkbox" name="{{ $dn }}" style="margin-top:2px" @checked($form[$dn])> {{ $dl }} <span class="fpi-req">*</span>
            </label>
            @endforeach
        </div>

        {{-- FAQs --}}
        <div style="margin-top:24px;border-top:1px solid var(--gray200);padding-top:16px">
            <div id="faqToggle" style="display:flex;align-items:center;gap:6px;cursor:pointer;user-select:none;margin-bottom:12px">
                <span id="faqCaret" style="font-size:9px;color:var(--gray700);display:inline-block">▼</span>
                <strong style="font-size:12px;color:var(--gray900)">FAQs for <span id="faqTitle"></span></strong>
            </div>
            <ul id="faqList" style="padding-left:16px;margin:0;display:flex;flex-direction:column;gap:6px;list-style-type:disc"></ul>
        </div>

        {{-- Actions --}}
        <div class="fpi-actions">
            <div style="display:flex;gap:6px">
                <button type="button" class="btn btn-ghost btn-sm" id="fpiPrev" style="padding:5px 12px;font-size:12.5px;display:none">Previous</button>
                <button type="button" class="btn btn-outline btn-sm js-fpi-autofill" id="fpiAutofillSection" style="padding:5px 12px;font-size:12.5px">⚡ Auto-fill &amp; Save All</button>
            </div>
            <div style="display:flex;gap:6px">
                <a href="{{ route('fpi.preview') }}" target="_blank" rel="noopener" class="btn btn-sm fpi-print-btn" id="fpiPrintPreview" style="padding:5px 14px;font-size:12.5px;display:none;text-decoration:none">🖨 Print / Preview</a>
                <button type="button" class="btn btn-primary btn-sm" id="fpiSaveTab" style="padding:5px 14px;font-size:12.5px">Save Section</button>
                <button type="button" class="btn btn-primary btn-sm" id="fpiNext" style="padding:5px 14px;font-size:12.5px">Next</button>
                <button type="button" class="btn btn-primary btn-sm" id="fpiSubmit" style="padding:5px 14px;font-size:12.5px;display:none;background:var(--success)" disabled>Submit Application</button>
            </div>
        </div>
    </div>
</form>

{{-- Hidden form for final submission (manual button) --}}
<form id="fpiSubmitForm" method="POST" action="{{ route('fpi.submit') }}" style="display:none">@csrf</form>
@endsection

@push('scripts')
<script>
    (function() {
        const steps = @json($stepsJs);
        const faqs = @json($faqs);
        const savedSteps = new Set(@json($savedSections));
        let IS_SUBMITTED = @json($isSubmitted);
        const IS_INDIVIDUAL = @json($isIndividual);
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        let current = Math.max(0, steps.findIndex(s => s.id === @json($activeSection)));

        const progressBar = document.getElementById('fpiProgressBar');
        const faqTitle = document.getElementById('faqTitle');
        const faqList = document.getElementById('faqList');
        const prevBtn = document.getElementById('fpiPrev');
        const nextBtn = document.getElementById('fpiNext');

        function render() {
            const activeId = steps[current].id;

            document.querySelectorAll('.fpi-step').forEach((el, i) => {
                const done = savedSteps.has(el.getAttribute('data-step')) || i < current;
                el.classList.toggle('active', i === current);
                el.classList.toggle('completed', done);
                const dot = el.querySelector('.fpi-step-dot');
                dot.textContent = done ? '✓' : (i + 1);
            });
            document.querySelectorAll('.fpi-tab-btn').forEach(el => {
                el.classList.toggle('active', el.getAttribute('data-step') === activeId);
                el.classList.toggle('saved', savedSteps.has(el.getAttribute('data-step')));
            });
            document.querySelectorAll('.fpi-step-panel').forEach(el => {
                el.classList.toggle('active', el.getAttribute('data-panel') === activeId);
            });

            // Entering Beneficial Ownership: pull the UBO names from the tool.
            if (activeId === 'ubo' && typeof refreshUboSection === 'function') refreshUboSection();

            progressBar.style.width = (current / (steps.length - 1)) * 100 + '%';
            prevBtn.style.display = current === 0 ? 'none' : '';
            nextBtn.style.display = current === steps.length - 1 ? 'none' : '';

            // Final-tab controls: Submit + Print/Preview only on the declarations tab.
            const onFinal = activeId === 'declarations';
            const declSaved = savedSteps.has('declarations');

            const submitBtn = document.getElementById('fpiSubmit');
            if (submitBtn) {
                submitBtn.style.display = (onFinal && !IS_SUBMITTED) ? '' : 'none';
                submitBtn.disabled = !declSaved; // enabled only after Save Section done
                submitBtn.title = declSaved ? '' : 'Save this section first to enable submission';
            }
            // Save Section / Auto-fill hidden after the application is submitted (read-only).
            if (IS_SUBMITTED) {
                document.getElementById('fpiSaveTab').style.display = 'none';
                document.querySelectorAll('.js-fpi-autofill').forEach(b => {
                    b.style.display = 'none';
                });
            }

            const printBtn = document.getElementById('fpiPrintPreview');
            if (printBtn) printBtn.style.display = (IS_SUBMITTED && onFinal) ? '' : 'none';
            const printBtnTop = document.getElementById('fpiPrintPreviewTop');
            if (printBtnTop) printBtnTop.style.display = IS_SUBMITTED ? '' : 'none';
            const note = document.getElementById('fpiSubmittedNote');
            if (note) note.style.display = (IS_SUBMITTED && onFinal) ? '' : 'none';

            // FAQs
            faqTitle.textContent = steps[current].title;
            faqList.innerHTML = '';
            (faqs[activeId] || []).forEach(f => {
                const li = document.createElement('li');
                li.style.cssText = 'font-size:11px;color:var(--gray700);line-height:1.4';
                li.innerHTML = '<strong></strong> ';
                li.querySelector('strong').textContent = f.q;
                li.appendChild(document.createTextNode(f.a));
                faqList.appendChild(li);
            });
        }

        function goTo(id) {
            const idx = steps.findIndex(s => s.id === id);
            if (idx >= 0) {
                current = idx;
                render();
            }
        }

        document.querySelectorAll('.fpi-step, .fpi-tab-btn').forEach(el => {
            el.addEventListener('click', () => goTo(el.getAttribute('data-step')));
        });
        prevBtn.addEventListener('click', () => {
            if (current > 0) {
                current--;
                render();
            }
        });
        // Next = save current tab (AJAX) + advance, so nothing is lost on reload.
        nextBtn.addEventListener('click', () => saveTab());

        // FAQ collapse
        let faqOpen = true;
        document.getElementById('faqToggle').addEventListener('click', () => {
            faqOpen = !faqOpen;
            faqList.style.display = faqOpen ? 'flex' : 'none';
            document.getElementById('faqCaret').style.transform = faqOpen ? 'rotate(0deg)' : 'rotate(-90deg)';
        });

        // Known-by-another-name toggle (Non-Individual applicant)
        const knownSel = document.getElementById('knownByAnotherName');

        function toggleOtherName() {
            const show = knownSel.value === 'YES';
            document.getElementById('otherNameTitleGroup').style.display = show ? '' : 'none';
            document.getElementById('otherNameGroup').style.display = show ? '' : 'none';
        }
        if (knownSel) knownSel.addEventListener('change', toggleOtherName);

        // Known-by-another-name toggle (Individual applicant)
        const indOtherNameSel = document.getElementById('indOtherName');
        if (indOtherNameSel) {
            const apply = () => {
                document.getElementById('indOtherNameGroup').style.display = indOtherNameSel.value === 'yes' ? '' : 'none';
            };
            indOtherNameSel.addEventListener('change', apply);
            apply();
        }

        // ── Beneficial Ownership rows (seeded from the UBO Determination tool) ──
        const hasUbos = document.getElementById('hasUbos');
        const uboRowsContainer = document.getElementById('uboRowsContainer');
        const uboRowsJsonField = document.getElementById('uboRowsJsonField');
        const uboEmptyNote = document.getElementById('uboEmptyNote');
        const UBO_COUNTRIES = @json($uboCountriesJs);
        const TODAY = @json(date('Y-m-d'));
        let uboRows = @json($uboList ?? []); // [{name,dob,nationality,passport,ownership,address}]

        function countryOptionsHtml(sel) {
            let h = `<option value="">Select</option>`;
            UBO_COUNTRIES.forEach(c => {
                h += `<option value="${c.id}" ${String(sel) === c.id ? 'selected' : ''}>${c.label}</option>`;
            });
            return h;
        }

        function esc(s) {
            return String(s ?? '').replace(/"/g, '&quot;');
        }

        function renderUboRows() {
            uboRowsContainer.innerHTML = '';
            uboRows.forEach((r, i) => {
                const row = document.createElement('div');
                row.className = 'fpi-grid ubo-row';
                row.style.cssText = 'margin-bottom:12px;padding:12px;border:1px solid var(--gray200);border-radius:6px;position:relative';
                row.innerHTML = `
                    <div class="fpi-form-group"><label class="fpi-label">Full Name <span class="fpi-req">*</span></label>
                        <input class="fpi-input ubo-f" data-i="${i}" data-k="name" type="text" placeholder="Full legal name" value="${esc(r.name)}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Date of Birth <span class="fpi-req">*</span></label>
                        <input class="fpi-input ubo-f" data-i="${i}" data-k="dob" type="date" max="${TODAY}" value="${esc(r.dob)}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Tax Jurisdiction</label>
                        <select class="fpi-select ubo-f" data-i="${i}" data-k="taxJurisdiction">${countryOptionsHtml(r.taxJurisdiction)}</select></div>
                    <div class="fpi-form-group"><label class="fpi-label">Nationality <span class="fpi-req">*</span></label>
                        <select class="fpi-select ubo-f" data-i="${i}" data-k="nationality">${countryOptionsHtml(r.nationality)}</select></div>
                    <div class="fpi-form-group"><label class="fpi-label">Acting Alone / Group</label>
                        <input class="fpi-input ubo-f" data-i="${i}" data-k="actingGroup" type="text" placeholder="e.g. Alone / Group" value="${esc(r.actingGroup)}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Passport / National ID <span class="fpi-req">*</span></label>
                        <input class="fpi-input ubo-f" data-i="${i}" data-k="passport" type="text" placeholder="Passport / national ID no." value="${esc(r.passport)}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Ownership % <span class="fpi-req">*</span></label>
                        <input class="fpi-input ubo-f" data-i="${i}" data-k="ownership" type="number" min="0" max="100" step="0.01" placeholder="0 - 100" value="${esc(r.ownership)}"></div>
                    <div class="fpi-form-group" style="grid-column: span 2"><label class="fpi-label">Residential Address <span class="fpi-req">*</span></label>
                        <input class="fpi-input ubo-f" data-i="${i}" data-k="address" type="text" placeholder="Residential address" value="${esc(r.address)}"></div>
                    <div class="fpi-form-group" style="justify-content:flex-end">
                        <button type="button" class="btn btn-ghost btn-sm ubo-remove" data-i="${i}" style="color:var(--danger);border-color:#f1aaa5">Remove</button></div>`;
                uboRowsContainer.appendChild(row);
            });
            uboRowsContainer.querySelectorAll('.ubo-f').forEach(el => {
                el.addEventListener('input', e => {
                    uboRows[+e.target.dataset.i][e.target.dataset.k] = e.target.value;
                    e.target.classList.remove('is-invalid');
                });
                el.addEventListener('change', e => {
                    uboRows[+e.target.dataset.i][e.target.dataset.k] = e.target.value;
                });
            });
            uboRowsContainer.querySelectorAll('.ubo-remove').forEach(b => {
                b.addEventListener('click', e => {
                    uboRows.splice(+e.target.dataset.i, 1);
                    renderUboRows();
                });
            });
            uboEmptyNote.style.display = uboRows.length ? 'none' : '';
        }

        function seedUboRows() {
            const persons = window.__uboPersons ? window.__uboPersons() : [];
            if (!persons.length) return;
            const byName = {};
            uboRows.forEach(r => byName[(r.name || '').toLowerCase()] = r);
            const merged = persons.map(p => byName[p.name.toLowerCase()] || {
                name: p.name,
                dob: '',
                taxJurisdiction: '',
                nationality: '',
                actingGroup: '',
                passport: '',
                ownership: '',
                address: ''
            });
            const toolNames = new Set(persons.map(p => p.name.toLowerCase()));
            uboRows.forEach(r => {
                if (!toolNames.has((r.name || '').toLowerCase())) merged.push(r);
            }); // keep manual rows
            uboRows = merged;
        }

        function refreshUboSection() {
            document.getElementById('uboBlock').style.display = hasUbos.value === 'YES' ? '' : 'none';
            if (hasUbos.value === 'YES') {
                seedUboRows();
                renderUboRows();
            }
        }
        if (hasUbos) {
            hasUbos.addEventListener('change', refreshUboSection);
            document.getElementById('uboAddRow').addEventListener('click', () => {
                uboRows.push({
                    name: '',
                    dob: '',
                    taxJurisdiction: '',
                    nationality: '',
                    actingGroup: '',
                    passport: '',
                    ownership: '',
                    address: ''
                });
                renderUboRows();
            });
        }

        // Validate the UBO rows; marks invalid inputs, returns true if all good.
        function checkUboRows() {
            if (hasUbos.value !== 'YES') return true;
            let ok = true;
            const inputs = uboRowsContainer.querySelectorAll('.ubo-f');
            if (!uboRows.length) ok = false;
            const OPTIONAL_UBO = ['taxJurisdiction', 'actingGroup'];
            inputs.forEach(el => {
                el.classList.remove('is-invalid');
                const k = el.dataset.k,
                    v = (el.value || '').trim();
                let bad = !OPTIONAL_UBO.includes(k) && !v;
                if (!bad && k === 'ownership' && v) {
                    const n = parseFloat(v);
                    bad = isNaN(n) || n < 0 || n > 100;
                }
                if (!bad && k === 'dob' && v > TODAY) bad = true;
                if (bad) {
                    el.classList.add('is-invalid');
                    ok = false;
                }
            });
            return ok;
        }

        function serializeUbo() {
            uboRowsJsonField.value = JSON.stringify(uboRows);
        }

        // ── Sub-Funds, Intermediate Entities & Controlling Entities (reference Step 3) ──
        const hasSubFunds = document.getElementById('hasSubFunds');
        let subFunds = @json($form['subFundsData'] ?? []); // ['name', ...]
        let intermediates = @json($form['intermediatesData'] ?? []); // [{subFund,name,stakeType,chain,country,pct,type}]
        let controllers = @json($form['controllersData'] ?? []); // [{subFund,name,method,country,pct,type}]

        function subFundOptionsHtml(sel) {
            let h = `<option value="">— none —</option>`;
            subFunds.forEach(n => {
                if (n) h += `<option value="${esc(n)}" ${String(sel) === String(n) ? 'selected' : ''}>${esc(n)}</option>`;
            });
            return h;
        }

        function typeOptionsHtml(sel) {
            return ['', 'Individual', 'Non-Individual'].map(t =>
                `<option value="${t}" ${String(sel) === t ? 'selected' : ''}>${t || 'Select'}</option>`).join('');
        }

        function toggleSubFundSection() {
            document.getElementById('subFundSection').style.display = hasSubFunds.value === 'YES' ? '' : 'none';
            if (hasSubFunds.value === 'YES') renderSubFunds();
            renderIntermediates();
            renderControllers(); // refresh sub-fund columns
        }
        if (hasSubFunds) hasSubFunds.addEventListener('change', toggleSubFundSection);

        function renderSubFunds() {
            const c = document.getElementById('subFundRowsContainer');
            c.innerHTML = '';
            subFunds.forEach((name, i) => {
                const row = document.createElement('div');
                row.className = 'fpi-grid';
                row.style.cssText = 'margin-bottom:8px;align-items:end';
                row.innerHTML = `
                    <div class="fpi-form-group" style="grid-column: span 3"><label class="fpi-label">Sub-Fund #${i + 1} Name</label>
                        <input class="fpi-input sf-f" data-i="${i}" type="text" placeholder="Sub-fund / share-class name" value="${esc(name)}"></div>
                    <div class="fpi-form-group" style="justify-content:flex-end">
                        <button type="button" class="btn btn-ghost btn-sm sf-remove" data-i="${i}" style="color:var(--danger);border-color:#f1aaa5">Remove</button></div>`;
                c.appendChild(row);
            });
            c.querySelectorAll('.sf-f').forEach(el => el.addEventListener('input', e => {
                subFunds[+e.target.dataset.i] = e.target.value;
                renderIntermediates();
                renderControllers();
            }));
            c.querySelectorAll('.sf-remove').forEach(b => b.addEventListener('click', e => {
                subFunds.splice(+e.target.dataset.i, 1);
                renderSubFunds();
                renderIntermediates();
                renderControllers();
            }));
            document.getElementById('subFundEmptyNote').style.display = subFunds.length ? 'none' : '';
        }
        document.getElementById('subFundAddRow')?.addEventListener('click', () => {
            subFunds.push('');
            renderSubFunds();
        });

        // Generic renderer for intermediate/controller tables (share layout, differ by one field).
        function renderEntityRows(kind) {
            const isInt = kind === 'int';
            const rows = isInt ? intermediates : controllers;
            const c = document.getElementById(isInt ? 'intRowsContainer' : 'ctrlRowsContainer');
            const showSub = hasSubFunds.value === 'YES';
            c.innerHTML = '';
            rows.forEach((r, i) => {
                const row = document.createElement('div');
                row.className = 'fpi-grid';
                row.style.cssText = 'margin-bottom:12px;padding:12px;border:1px solid var(--gray200);border-radius:6px';
                const subCol = showSub ? `
                    <div class="fpi-form-group"><label class="fpi-label">Sub-Fund</label>
                        <select class="fpi-select en-f" data-kind="${kind}" data-i="${i}" data-k="subFund">${subFundOptionsHtml(r.subFund)}</select></div>` : '';
                const midCol = isInt ?
                    `<div class="fpi-form-group"><label class="fpi-label">Stake Type</label>
                           <input class="fpi-input en-f" data-kind="${kind}" data-i="${i}" data-k="stakeType" type="text" placeholder="e.g. Equity / Voting rights" value="${esc(r.stakeType)}"></div>
                       <div class="fpi-form-group" style="grid-column: span 2"><label class="fpi-label">Intermediate Chain</label>
                           <input class="fpi-input en-f" data-kind="${kind}" data-i="${i}" data-k="chain" type="text" placeholder="e.g. HoldCo A › HoldCo B › Applicant" value="${esc(r.chain)}"></div>` :
                    `<div class="fpi-form-group" style="grid-column: span 2"><label class="fpi-label">Control Method</label>
                           <input class="fpi-input en-f" data-kind="${kind}" data-i="${i}" data-k="method" type="text" placeholder="e.g. Board majority / Voting agreement" value="${esc(r.method)}"></div>`;
                row.innerHTML = `
                    ${subCol}
                    <div class="fpi-form-group"><label class="fpi-label">Name</label>
                        <input class="fpi-input en-f" data-kind="${kind}" data-i="${i}" data-k="name" type="text" placeholder="${isInt ? 'Intermediate entity name' : 'Controlling entity / person name'}" value="${esc(r.name)}"></div>
                    ${midCol}
                    <div class="fpi-form-group"><label class="fpi-label">Country</label>
                        <select class="fpi-select en-f" data-kind="${kind}" data-i="${i}" data-k="country">${countryOptionsHtml(r.country)}</select></div>
                    <div class="fpi-form-group"><label class="fpi-label">% ${isInt ? 'Stake' : 'Control'}</label>
                        <input class="fpi-input en-f" data-kind="${kind}" data-i="${i}" data-k="pct" type="number" min="0" max="100" step="0.01" placeholder="0 - 100" value="${esc(r.pct)}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Type</label>
                        <select class="fpi-select en-f" data-kind="${kind}" data-i="${i}" data-k="type">${typeOptionsHtml(r.type)}</select></div>
                    <div class="fpi-form-group" style="justify-content:flex-end">
                        <button type="button" class="btn btn-ghost btn-sm en-remove" data-kind="${kind}" data-i="${i}" style="color:var(--danger);border-color:#f1aaa5">Remove</button></div>`;
                c.appendChild(row);
            });
            c.querySelectorAll('.en-f').forEach(el => {
                const upd = e => {
                    (e.target.dataset.kind === 'int' ? intermediates : controllers)[+e.target.dataset.i][e.target.dataset.k] = e.target.value;
                };
                el.addEventListener('input', upd);
                el.addEventListener('change', upd);
            });
            c.querySelectorAll('.en-remove').forEach(b => b.addEventListener('click', e => {
                (e.target.dataset.kind === 'int' ? intermediates : controllers).splice(+e.target.dataset.i, 1);
                renderEntityRows(e.target.dataset.kind);
            }));
            document.getElementById(isInt ? 'intEmptyNote' : 'ctrlEmptyNote').style.display = rows.length ? 'none' : '';
        }

        function renderIntermediates() {
            renderEntityRows('int');
        }

        function renderControllers() {
            renderEntityRows('ctrl');
        }
        document.getElementById('intAddRow')?.addEventListener('click', () => {
            intermediates.push({
                subFund: '',
                name: '',
                stakeType: '',
                chain: '',
                country: '',
                pct: '',
                type: ''
            });
            renderIntermediates();
        });
        document.getElementById('ctrlAddRow')?.addEventListener('click', () => {
            controllers.push({
                subFund: '',
                name: '',
                method: '',
                country: '',
                pct: '',
                type: ''
            });
            renderControllers();
        });

        function serializeBo() {
            if (!hasSubFunds) return;
            document.getElementById('subFundsJsonField').value = JSON.stringify(hasSubFunds.value === 'YES' ? subFunds.filter(s => (s || '').trim() !== '') : []);
            document.getElementById('intermediatesJsonField').value = JSON.stringify(intermediates);
            document.getElementById('controllersJsonField').value = JSON.stringify(controllers);
        }

        // Initial paint (only when the beneficial-ownership panel exists)
        if (hasSubFunds) {
            toggleSubFundSection();
            renderIntermediates();
            renderControllers();
        }

        // Seed the Beneficial-Ownership tables (used by Auto-fill).
        window.__seedBo = (data) => {
            if (!hasSubFunds) return;
            if (data.hasSubFunds) {
                hasSubFunds.value = data.hasSubFunds;
                toggleSubFundSection();
            }
            if (data.subFunds) {
                subFunds.length = 0;
                data.subFunds.forEach(s => subFunds.push(s));
                renderSubFunds();
            }
            if (data.intermediates) {
                intermediates.length = 0;
                data.intermediates.forEach(r => intermediates.push(r));
                renderIntermediates();
            }
            if (data.controllers) {
                controllers.length = 0;
                data.controllers.forEach(r => controllers.push(r));
                renderControllers();
            }
        };

        // Financial: show Business/Profession Code only when "business" income is ticked.
        function refreshProfessionCode() {
            const biz = document.querySelector('.js-income-src[value="business"]');
            const grp = document.getElementById('professionCodeGroup');
            if (grp) grp.style.display = (biz && biz.checked) ? '' : 'none';
            // "No Income" is mutually exclusive with the other sources (reference behaviour).
            const noInc = document.querySelector('.js-income-src[value="no_income"]');
            const others = Array.from(document.querySelectorAll('.js-income-src')).filter(c => c.value !== 'no_income');
            const anyOther = others.some(c => c.checked);
            if (noInc) {
                if (anyOther) {
                    noInc.checked = false;
                    noInc.disabled = true;
                } else {
                    noInc.disabled = false;
                }
            }
            others.forEach(c => {
                c.disabled = noInc && noInc.checked;
            });
            // Clear the "select at least one source" error once something is ticked.
            if (document.querySelectorAll('.js-income-src:checked').length) {
                const wrap = document.getElementById('incomeSourcesErrorWrap');
                const fb = wrap && wrap.querySelector('.fpi-invalid-feedback');
                if (fb) fb.remove();
                const hid = document.getElementById('incomeSourcesJsonField');
                if (hid) hid.classList.remove('is-invalid');
            }
        }
        document.querySelectorAll('.js-income-src').forEach(cb => cb.addEventListener('change', refreshProfessionCode));
        refreshProfessionCode();

        // ── Generic add-row table (repeater) used by Steps 5–8 ──
        const REPEATERS = [];

        function makeRepeater(cfg) {
            let rows = Array.isArray(cfg.data) ? cfg.data : [];
            const container = document.getElementById(cfg.containerId);

            function render() {
                container.innerHTML = '';
                rows.forEach((r, i) => {
                    const row = document.createElement('div');
                    row.className = 'fpi-grid';
                    row.style.cssText = 'margin-bottom:12px;padding:12px;border:1px solid var(--gray200);border-radius:6px';
                    let html = '';
                    cfg.cols.forEach(col => {
                        const span = col.span ? `grid-column: span ${col.span}` : '';
                        const ph = esc(col.ph || col.label);
                        let field;
                        if (col.type === 'select') {
                            field = `<select class="fpi-select rp-f" data-i="${i}" data-k="${col.k}">` +
                                (col.opts || []).map(o => `<option value="${esc(o.v)}" ${String(r[col.k] ?? '') === String(o.v) ? 'selected' : ''}>${esc(o.l)}</option>`).join('') + `</select>`;
                        } else if (col.type === 'country') {
                            field = `<select class="fpi-select rp-f" data-i="${i}" data-k="${col.k}">${countryOptionsHtml(r[col.k])}</select>`;
                        } else if (col.type === 'textarea') {
                            field = `<textarea class="fpi-input rp-f" data-i="${i}" data-k="${col.k}" rows="2" placeholder="${ph}">${esc(r[col.k])}</textarea>`;
                        } else {
                            field = `<input class="fpi-input rp-f" data-i="${i}" data-k="${col.k}" type="${col.type || 'text'}" placeholder="${ph}" value="${esc(r[col.k])}">`;
                        }
                        html += `<div class="fpi-form-group" style="${span}"><label class="fpi-label">${col.label}</label>${field}</div>`;
                    });
                    html += `<div class="fpi-form-group" style="justify-content:flex-end"><button type="button" class="btn btn-ghost btn-sm rp-rm" data-i="${i}" style="color:var(--danger);border-color:#f1aaa5">Remove</button></div>`;
                    row.innerHTML = html;
                    container.appendChild(row);
                });
                container.querySelectorAll('.rp-f').forEach(el => {
                    const u = e => {
                        rows[+e.target.dataset.i][e.target.dataset.k] = e.target.value;
                    };
                    el.addEventListener('input', u);
                    el.addEventListener('change', u);
                });
                container.querySelectorAll('.rp-rm').forEach(b => b.addEventListener('click', e => {
                    rows.splice(+e.target.dataset.i, 1);
                    render();
                }));
                if (cfg.emptyId) document.getElementById(cfg.emptyId).style.display = rows.length ? 'none' : '';
            }
            const addBtn = document.getElementById(cfg.addBtnId);
            if (addBtn) addBtn.addEventListener('click', () => {
                const o = {};
                cfg.cols.forEach(c => o[c.k] = '');
                rows.push(o);
                render();
            });
            render();
            const seed = newRows => {
                rows.length = 0;
                (newRows || []).forEach(r => rows.push(r));
                render();
            };
            REPEATERS.push({
                section: cfg.section,
                hiddenId: cfg.hiddenId,
                json: cfg.hiddenId,
                key: cfg.containerId,
                get: () => rows,
                seed
            });
            return {
                get: () => rows,
                seed
            };
        }

        function serializeRepeaters(section) {
            REPEATERS.filter(r => r.section === section).forEach(r => {
                const h = document.getElementById(r.hiddenId);
                if (h) h.value = JSON.stringify(r.get());
            });
        }
        // Seed a repeater by its hidden-field id (used by Auto-fill).
        window.__seedRepeater = (hiddenId, rows) => {
            const r = REPEATERS.find(x => x.hiddenId === hiddenId);
            if (r) r.seed(rows);
        };
        window.__serializeRepeaters = serializeRepeaters;

        // Simple show/hide helper bound to a <select> value.
        function bindToggle(selectId, targetId, showVal) {
            const sel = document.getElementById(selectId),
                tgt = document.getElementById(targetId);
            if (!sel || !tgt) return;
            const apply = () => {
                tgt.style.display = sel.value === showVal ? '' : 'none';
            };
            sel.addEventListener('change', apply);
            apply();
        }

        // ── Step 5: Category & Regulatory repeaters + toggles ──
        makeRepeater({
            section: 'category',
            containerId: 'imRowsContainer',
            addBtnId: 'imAddRow',
            hiddenId: 'investmentManagersJsonField',
            emptyId: 'imEmptyNote',
            data: @json($form['investmentManagersData'] ?? []),
            cols: [{
                k: 'name',
                label: 'Investment Manager',
                span: 2
            }, {
                k: 'sebiReg',
                label: 'SEBI Registration No.'
            }]
        });
        makeRepeater({
            section: 'category',
            containerId: 'fpiGroupRowsContainer',
            addBtnId: 'fpiGroupAddRow',
            hiddenId: 'fpiGroupJsonField',
            emptyId: 'fpiGroupEmptyNote',
            data: @json($form['fpiGroupData'] ?? []),
            cols: [{
                k: 'fpiName',
                label: 'Name of FPI/ODI Subscriber',
                span: 2
            }, {
                k: 'dealingFpi',
                label: 'If ODI, Dealing FPI'
            }, {
                k: 'fpiRegNo',
                label: 'FPI Registration No.'
            }]
        });
        makeRepeater({
            section: 'category',
            containerId: 'publicRetailRowsContainer',
            addBtnId: 'publicRetailAddRow',
            hiddenId: 'publicRetailJsonField',
            emptyId: 'publicRetailEmptyNote',
            data: @json($form['publicRetailData'] ?? []),
            cols: [{
                k: 'fpiName',
                label: 'Name of FPI',
                span: 2
            }, {
                k: 'fpiRegNo',
                label: 'FPI Registration No.'
            }, {
                k: 'commonPerson',
                label: 'Common Controlling Person'
            }]
        });
        makeRepeater({
            section: 'category',
            containerId: 'priorAssocRowsContainer',
            addBtnId: 'priorAssocAddRow',
            hiddenId: 'priorAssociationsJsonField',
            emptyId: 'priorAssocEmptyNote',
            data: @json($form['priorAssociationsData'] ?? []),
            cols: [{
                k: 'entity',
                label: 'Name of Entity',
                span: 2
            }, {
                k: 'associationType',
                label: 'Registered / Associated As'
            }, {
                k: 'sebiReg',
                label: 'SEBI Registration No.'
            }]
        });
        bindToggle('hasCustodian', 'custodianInfoSection', 'yes');
        bindToggle('clubbingDeclaration', 'investorGroupSection', 'yes');
        bindToggle('fpiGroupNumber', 'groupNumberGroup', 'yes');
        bindToggle('priorAssociation', 'priorAssociationSection', 'yes');

        // ── Step 6: PAN / Depository toggles ──
        bindToggle('hasPan', 'panDetailsGroup', 'yes');
        bindToggle('hasPan', 'applyPanSection', 'no');
        bindToggle('listed', 'exchangeNameGroup', 'yes');
        bindToggle('modeOfOperation', 'otherModeGroup', 'Others');

        // ── Step 4: Additional tax residencies ──
        makeRepeater({
            section: 'financial',
            containerId: 'taxResRowsContainer',
            addBtnId: 'taxResAddRow',
            hiddenId: 'taxResidenciesJsonField',
            emptyId: 'taxResEmptyNote',
            data: @json($form['taxResidenciesData'] ?? []),
            cols: [{
                    k: 'country',
                    label: 'Country of Tax Residency',
                    type: 'country'
                }, {
                    k: 'tin',
                    label: 'TIN'
                }, {
                    k: 'trc',
                    label: 'TRC No.'
                },
                {
                    k: 'reason',
                    label: 'Reason (if no TIN)',
                    type: 'select',
                    opts: [{
                        v: '',
                        l: '--'
                    }, {
                        v: 'no_tin_issued',
                        l: 'Country issues no TINs'
                    }, {
                        v: 'not_required',
                        l: 'Not required'
                    }, {
                        v: 'other',
                        l: 'Other'
                    }]
                },
                {
                    k: 'explanation',
                    label: 'Explanation',
                    span: 2
                }
            ]
        });

        // ── Step 7: Additional Info repeaters + toggles ──
        makeRepeater({
            section: 'additional',
            containerId: 'shareClassRowsContainer',
            addBtnId: 'shareClassAddRow',
            hiddenId: 'shareClassesJsonField',
            emptyId: 'shareClassEmptyNote',
            data: @json($form['shareClassesData'] ?? []),
            cols: [{
                k: 'name',
                label: 'Sub-Fund / Share Class Name',
                span: 3
            }]
        });
        makeRepeater({
            section: 'additional',
            containerId: 'cat1RowsContainer',
            addBtnId: 'cat1AddRow',
            hiddenId: 'categoryOneEntitiesJsonField',
            emptyId: 'cat1EmptyNote',
            data: @json($form['categoryOneEntitiesData'] ?? []),
            cols: [{
                    k: 'name',
                    label: 'Name of Entity',
                    span: 2
                }, {
                    k: 'country',
                    label: 'Country',
                    type: 'country'
                },
                {
                    k: 'entityType',
                    label: 'Entity Type',
                    type: 'select',
                    opts: [{
                            v: '',
                            l: 'Select'
                        }, {
                            v: 'central_bank',
                            l: 'Central Bank'
                        }, {
                            v: 'sovereign_wealth_fund',
                            l: 'Sovereign Wealth Fund'
                        },
                        {
                            v: 'regulated_fund',
                            l: 'Appropriately Regulated Fund'
                        }, {
                            v: 'unregulated_with_responsible_im',
                            l: 'Unregulated Fund w/ Responsible IM'
                        },
                        {
                            v: 'university_endowment',
                            l: 'University Endowment'
                        }, {
                            v: 'investment_manager_controlled',
                            l: 'IM Controlled Entity'
                        }, {
                            v: 'other',
                            l: 'Other'
                        }
                    ]
                }
            ]
        });
        makeRepeater({
            section: 'additional',
            containerId: 'clientRowsContainer',
            addBtnId: 'clientAddRow',
            hiddenId: 'clientsJsonField',
            emptyId: 'clientEmptyNote',
            data: @json($form['clientsData'] ?? []),
            cols: [{
                k: 'name',
                label: 'Name',
                span: 2
            }, {
                k: 'country',
                label: 'Country'
            }, {
                k: 'address',
                label: 'Address',
                span: 2
            }, {
                k: 'type',
                label: 'Type (Individual/Family Office)'
            }]
        });
        makeRepeater({
            section: 'additional',
            containerId: 'signRowsContainer',
            addBtnId: 'signAddRow',
            hiddenId: 'signatoriesJsonField',
            emptyId: 'signEmptyNote',
            data: @json($form['signatoriesData'] ?? []),
            cols: [{
                    k: 'name',
                    label: 'Name',
                    span: 2
                }, {
                    k: 'relationship',
                    label: 'Relationship'
                }, {
                    k: 'pan',
                    label: 'PAN (if any)'
                },
                {
                    k: 'nationality',
                    label: 'Nationality / Residence'
                }, {
                    k: 'dob',
                    label: 'Date of Birth',
                    type: 'date'
                },
                {
                    k: 'address',
                    label: 'Address',
                    span: 2
                }, {
                    k: 'govId',
                    label: 'Govt ID Number'
                }
            ]
        });
        bindToggle('bankDeclaration', 'bankEntityNameGroup', 'has_branch');
        bindToggle('nriControl2', 'imTypesGroup', 'controlled_by_im');
        bindToggle('directlyControlled', 'nriEntityNameGroup', 'yes');
        bindToggle('kraConsent', 'kraInfoSection', 'yes');

        // ── Reg 5(b)(vii): client table gated behind three eligibility checkboxes ──
        (function() {
            const reg5b7 = document.getElementById('reg5b7');
            const checkboxSection = document.getElementById('clientCheckboxSection');
            const clientsSection = document.getElementById('clientsSection');
            const eligIV = document.getElementById('clientEligIV');
            const eligs = Array.from(document.querySelectorAll('.js-client-elig'));
            if (!reg5b7) return;

            function refresh() {
                const onReg = reg5b7.value === 'Yes';
                checkboxSection.style.display = onReg ? '' : 'none';
                const allChecked = onReg && eligs.every(c => c.checked);
                if (eligIV) eligIV.checked = allChecked;
                clientsSection.style.display = allChecked ? '' : 'none';
                if (!onReg) eligs.forEach(c => {
                    c.checked = false;
                });
            }
            reg5b7.addEventListener('change', refresh);
            eligs.forEach(c => c.addEventListener('change', refresh));
            refresh();
        })();

        // Auto-fill helper: seed rows from the tool and fill sample details.
        function fillUboSample() {
            if (hasSubFunds.value === '') {
                hasSubFunds.value = 'NO';
                toggleSubFundSection();
            }
            hasUbos.value = 'YES';
            document.getElementById('uboBlock').style.display = '';
            seedUboRows();
            uboRows = uboRows.map((r, i) => ({
                name: r.name,
                dob: r.dob || '1980-01-01',
                taxJurisdiction: r.taxJurisdiction || '2',
                nationality: r.nationality || '2',
                actingGroup: r.actingGroup || 'Alone',
                passport: r.passport || ('IDDOC' + (i + 1)),
                ownership: r.ownership || '25',
                address: r.address || '1 Sample Street, City',
            }));
            renderUboRows();
        }

        // Same-as-registered address mirroring
        const sameAddress = document.getElementById('sameAddress');
        const regMap = {
            commAddressLine1: 'regAddressLine1',
            commAddressLine2: 'regAddressLine2',
            commAddressLine3: 'regAddressLine3',
            commCity: 'regCity',
            commState: 'regState',
            commCountry: 'regCountry',
            commZip: 'regZip',
        };

        function syncComm() {
            const on = sameAddress.checked;
            Object.entries(regMap).forEach(([comm, reg]) => {
                const commEl = document.querySelector(`[name="${comm}"]`);
                const regEl = document.querySelector(`[name="${reg}"]`);
                commEl.disabled = on;
                if (on) commEl.value = regEl.value;
            });
            // Hide correspondence-address asterisks when it mirrors the registered address.
            document.querySelectorAll('.comm-req').forEach(a => {
                a.style.display = on ? 'none' : '';
            });
        }
        sameAddress.addEventListener('change', syncComm);
        Object.values(regMap).forEach(reg => {
            document.querySelector(`[name="${reg}"]`).addEventListener('input', () => {
                if (sameAddress.checked) syncComm();
            });
        });

        // ───────────────── Client-side validation (mirrors server) ─────────────────
        const form = document.getElementById('fpiForm');
        const errorBanner = document.getElementById('fpiErrorBanner');

        const REQUIRED = {
            nameTitle: 'Title',
            entityName: 'Entity Name',
            applicantType: 'Applicant Type',
            knownByAnotherName: 'Ever known by another name',
            dateOfIncorporation: 'Date of Incorporation',
            placeOfIncorporation: 'Place of Incorporation',
            countryOfIncorporation: 'Country of Incorporation',
            hasSubFunds: 'Are there Sub-Funds',
            hasUbos: 'Does the entity have UBOs',
            fpiCategory: 'FPI Category',
            regulatoryStatus: 'Regulatory Status',
            bankAccountType: 'Account Type',
            signatureName: 'Authorized Signatory Name',
            uploadedIncorpCert: 'Certificate of Incorporation',
            uploadedPanCopy: 'Copy of Indian PAN Card',
            // Contact tab — everything except telephone
            regAddressLine1: 'Address Line 1',
            regAddressLine2: 'Address Line 2',
            regAddressLine3: 'Address Line 3',
            regCity: 'City',
            regState: 'State',
            regCountry: 'Country',
            regZip: 'PIN / ZIP',
            commAddressLine1: 'Address Line 1',
            commAddressLine2: 'Address Line 2',
            commAddressLine3: 'Address Line 3',
            commCity: 'City',
            commState: 'State',
            commCountry: 'Country',
            commZip: 'PIN / ZIP',
            mobileNumber: 'Mobile Number',
            email: 'Email',
            // Office Address (except lines 3 & 4) + registered phone — mandatory on both forms
            offAddressLine1: 'Office Address Line 1',
            offAddressLine2: 'Office Address Line 2',
            offCity: 'Office City',
            offState: 'Office State',
            offCountry: 'Office Country',
            offZip: 'Office ZIP',
            telIsdCode: 'Registered Phone Country Code',
            telAreaCode: 'Registered Phone Area Code',
            telNumber: 'Registered Phone Number',
            // Financial tab — everything except Net Worth Date
            incomeRange: 'Gross Annual Income',
            netWorth: 'Net Worth in USD',
            taxCountry: 'Tax Residency Country',
            tin: 'Tax Identification Number (TIN)',
            // PAN, Bank & Depository tab
            bankName: 'Bank Name',
            bankAccount: 'Account Number',
            // Additional Info tab
            primaryContactName: 'Primary Contact Person',
            primaryContactDesignation: 'Designation',
            // Individual applicant (Step 1) — only present on the Individual form
            indTitle: 'Title',
            indOtherName: 'Ever known by another name',
            indFirstName: 'First Name',
            indLastName: 'Last Name',
            indDob: 'Date of Birth',
            indPlaceOfBirth: 'Place of Birth',
            indCountryOfBirth: 'Country of Birth',
            indNationality: 'Nationality',
            indGender: 'Gender',
            indMaritalStatus: 'Marital Status',
            indCitizenshipStatus: 'Citizenship Status',
            indCountryOfCitizenship: 'Country of Citizenship',
            indFatherFirstName: "Father's First Name",
            indFatherLastName: "Father's Last Name",
            indMotherFirstName: "Mother's First Name",
            indMotherLastName: "Mother's Last Name",
            indSpouseFirstName: "Spouse's First Name",
            indSpouseLastName: "Spouse's Last Name",
            @if($isIndividual)
            // Individual financial tab — gross income + occupation
            grossIncome: 'Gross Annual Income (INR)',
            occupation: 'Occupation',
            // Individual category tab — regulator, compliance officer (except fax), key questions
            regulatorName: 'Regulator Name',
            licenseNumber: 'Registration / License Number',
            regulatorJurisdiction: 'Regulator Jurisdiction',
            complianceName: 'Compliance Officer Name',
            complianceTitle: 'Compliance Officer Job Title',
            complianceEmail: 'Compliance Officer Email',
            compliancePhone: 'Compliance Officer Phone',
            hasCustodian: 'Global Custodian',
            disciplinaryHistory: 'Past regulatory issues',
            priorAssociation: 'Prior Association',
            // Individual depository tab — SWIFT/IFSC, custodian & DP details, PAN question
            bankSwift: 'SWIFT / IFSC Code',
            custodianName: 'Custodian Name',
            dpId: 'DP ID',
            clientId: 'Client ID',
            hasPan: 'Do you already hold a PAN?',
            @endif
            // Declarations tab — place/date/applicant + signatory details (both forms)
            declarationPlace: 'Place',
            declarationDate: 'Date',
            applicantName: 'Name of the Applicant',
            applicantDesignation: 'Designation',
            authDesignation: 'Signatory Designation',
            authDate: 'Signatory Date',
        };
        const PATTERNS = {
            pan: {
                re: /^[A-Za-z]{5}[0-9]{4}[A-Za-z]$/,
                msg: 'PAN must be 5 letters, 4 digits, then 1 letter (e.g. AAACG1234F).'
            },
            email: {
                re: /^[^@\s]+@[^@\s]+\.[^@\s]+$/,
                msg: 'Enter a valid email address.'
            },
        };
        // ISO 7064 MOD 97-10 checksum for LEI (letters A-Z -> 10-35, number mod 97 == 1).
        function leiChecksumOk(lei) {
            let d = '';
            for (const ch of lei) d += /[A-Z]/.test(ch) ? (ch.charCodeAt(0) - 55).toString() : ch;
            let rem = 0;
            for (const c of d) rem = (rem * 10 + (+c)) % 97;
            return rem === 1;
        }
        const ISD_CODES = @json(array_values($isdCodes));

        function fieldEl(name) {
            return form.querySelector(`[name="${name}"]`);
        }

        function stepOf(el) {
            const p = el && el.closest('.fpi-step-panel');
            return p ? p.getAttribute('data-panel') : null;
        }

        function clearError(el) {
            if (!el) return;
            el.classList.remove('is-invalid');
            const up = el.closest('.fpi-file-upload');
            if (up) up.classList.remove('is-invalid');
            const grp = el.closest('.fpi-form-group') || el.parentElement;
            const fb = grp && grp.querySelector('.fpi-invalid-feedback');
            if (fb) fb.remove();
        }

        function setError(el, msg) {
            if (!el) return;
            el.classList.add('is-invalid');
            const up = el.closest('.fpi-file-upload');
            if (up) up.classList.add('is-invalid'); // hidden input -> flag the tile
            const grp = el.closest('.fpi-form-group') || el.parentElement;
            if (grp && !grp.querySelector('.fpi-invalid-feedback')) {
                const fb = document.createElement('div');
                fb.className = 'fpi-invalid-feedback';
                fb.textContent = msg;
                grp.appendChild(fb);
            }
            // clear inline error as soon as the user edits the field
            el.addEventListener('input', () => clearError(el), {
                once: true
            });
            el.addEventListener('change', () => clearError(el), {
                once: true
            });
        }

        // Show chosen filename in the upload tiles
        form.querySelectorAll('.fpi-file-upload input[type="file"]').forEach(inp => {
            inp.addEventListener('change', () => {
                const nameEl = inp.parentElement.querySelector('.fpi-file-name');
                if (nameEl) nameEl.textContent = inp.files.length ? inp.files[0].name : 'Click to upload';
            });
        });

        // Validate a set of field names; returns array of {name, msg}
        function runRules(names) {
            const errs = [];
            const val = n => {
                const e = fieldEl(n);
                return e ? (e.value || '').trim() : '';
            };

            names.forEach(n => {
                const el = fieldEl(n);
                if (!el || el.disabled) return;
                clearError(el);

                // required (a previously-uploaded file counts as satisfied)
                const alreadyUploaded = el.type === 'file' && el.dataset.uploaded === '1' && el.files.length === 0;
                if (REQUIRED[n] && !val(n) && !alreadyUploaded) {
                    errs.push({
                        name: n,
                        msg: `${REQUIRED[n]} is required.`
                    });
                    return;
                }
                // pattern (only when a value is present)
                if (PATTERNS[n] && val(n) && !PATTERNS[n].re.test(val(n))) {
                    errs.push({
                        name: n,
                        msg: PATTERNS[n].msg
                    });
                    return;
                }
            });

            // conditional: other entity name when "known by another name" = YES
            if (names.includes('otherEntityName') && knownSel.value === 'YES' && !val('otherEntityName')) {
                errs.push({
                    name: 'otherEntityName',
                    msg: 'Other Entity Name is required.'
                });
            }
            // conditional: PAN number required only when "already hold a PAN" = Yes
            if (names.includes('pan')) {
                const hp = fieldEl('hasPan');
                if (hp && hp.value === 'yes' && !val('pan')) {
                    errs.push({
                        name: 'pan',
                        msg: 'Enter PAN number is required.'
                    });
                }
                // Name on PAN card also required when a PAN is held (both forms).
                if (hp && hp.value === 'yes' && !val('existingpanName')) {
                    errs.push({
                        name: 'existingpanName',
                        msg: 'Name on PAN Card is required.'
                    });
                }
            }
            // Beneficial Ownership: Intermediate/Controlling entity rows must be fully filled
            [{
                    cid: 'intRowsContainer',
                    json: 'intermediatesJson',
                    label: 'Intermediate Entity'
                },
                {
                    cid: 'ctrlRowsContainer',
                    json: 'controllersJson',
                    label: 'Controlling Entity'
                },
            ].forEach(t => {
                if (!names.includes(t.json)) return;
                const cont = document.getElementById(t.cid);
                if (!cont) return;
                let bad = false;
                cont.querySelectorAll('.en-f').forEach(inp => {
                    if (inp.dataset.k === 'subFund') return; // sub-fund association is optional
                    inp.classList.remove('is-invalid');
                    if (!(inp.value || '').trim()) {
                        inp.classList.add('is-invalid');
                        bad = true;
                    }
                });
                if (bad) errs.push({
                    name: t.json,
                    msg: `Please complete every ${t.label} row.`
                });
            });
            // Additional-Info tables (both forms): any added row must have all columns filled
            [{
                    cid: 'shareClassRowsContainer',
                    json: 'shareClassesJson',
                    label: 'Sub-Fund / Share Class'
                },
                {
                    cid: 'cat1RowsContainer',
                    json: 'categoryOneEntitiesJson',
                    label: 'Eligible Category I Entity'
                },
                {
                    cid: 'clientRowsContainer',
                    json: 'clientsJson',
                    label: 'Client'
                },
                {
                    cid: 'signRowsContainer',
                    json: 'signatoriesJson',
                    label: 'Authorized Signatory'
                },
            ].forEach(t => {
                if (!names.includes(t.json)) return;
                const cont = document.getElementById(t.cid);
                if (!cont) return;
                let bad = false;
                cont.querySelectorAll('.rp-f').forEach(inp => {
                    inp.classList.remove('is-invalid');
                    if (!(inp.value || '').trim()) {
                        inp.classList.add('is-invalid');
                        bad = true;
                    }
                });
                if (bad) errs.push({
                    name: t.json,
                    msg: `Please complete every column in the ${t.label} table.`
                });
            });
            // Category/Financial add-row tables (both forms): each added row must have its key columns
            [{
                    cid: 'imRowsContainer',
                    json: 'investmentManagersJson',
                    keys: ['name', 'sebiReg'],
                    label: 'Investment Manager'
                },
                {
                    cid: 'publicRetailRowsContainer',
                    json: 'publicRetailJson',
                    keys: ['fpiName', 'fpiRegNo'],
                    label: 'Public Retail Fund'
                },
                {
                    cid: 'fpiGroupRowsContainer',
                    json: 'fpiGroupJson',
                    keys: ['fpiName', 'fpiRegNo'],
                    label: 'Investor Group'
                },
                {
                    cid: 'priorAssocRowsContainer',
                    json: 'priorAssociationsJson',
                    keys: ['entity', 'associationType'],
                    label: 'Prior Association'
                },
                {
                    cid: 'taxResRowsContainer',
                    json: 'taxResidenciesJson',
                    keys: ['country', 'tin'],
                    label: 'Tax Residency'
                },
            ].forEach(t => {
                if (!names.includes(t.json)) return;
                const cont = document.getElementById(t.cid);
                if (!cont) return;
                let bad = false;
                cont.querySelectorAll('.rp-f').forEach(inp => {
                    if (!t.keys.includes(inp.dataset.k)) return;
                    inp.classList.remove('is-invalid');
                    if (!(inp.value || '').trim()) {
                        inp.classList.add('is-invalid');
                        bad = true;
                    }
                });
                if (bad) errs.push({
                    name: t.json,
                    msg: `Please complete every ${t.label} row.`
                });
            });
            // conditional (Individual): at least one Source of Income must be selected
            if (IS_INDIVIDUAL && names.includes('incomeSourcesJson')) {
                if (document.querySelectorAll('.js-income-src:checked').length === 0) {
                    errs.push({
                        name: 'incomeSourcesJson',
                        msg: 'Please select at least one source of income.'
                    });
                }
            }
            // conditional (Individual): other-name details required when "ever known by another name" = Yes
            if (names.includes('indOtherName')) {
                const io = fieldEl('indOtherName');
                if (io && io.value === 'yes') {
                    [
                        ['indOtherTitle', 'Other Title'],
                        ['indOtherFirstName', 'Other First Name'],
                        ['indOtherLastName', 'Other Last Name']
                    ]
                    .forEach(([n, label]) => {
                        if (!val(n)) errs.push({
                            name: n,
                            msg: `${label} is required.`
                        });
                    });
                }
            }
            // LEI — full ISO 17442 validation (only when provided; field is optional)
            if (names.includes('lei') && val('lei')) {
                const v = val('lei').toUpperCase();
                let msg = null;
                if (!/^[A-Z0-9]{18}[0-9]{2}$/.test(v)) msg = 'LEI must be 20 characters: 18 alphanumeric + 2 numeric check digits.';
                else if (v.substr(4, 2) !== '00') msg = 'LEI is invalid: characters 5–6 must be "00".';
                else if (!leiChecksumOk(v)) msg = 'LEI checksum is invalid (ISO 17442 mod-97 check).';
                if (msg) errs.push({
                    name: 'lei',
                    msg
                });
            }
            // (Beneficial Ownership rows are validated separately via checkUboRows.)
            // net worth >= 0
            if (names.includes('netWorth') && val('netWorth')) {
                const v = parseFloat(val('netWorth'));
                if (isNaN(v) || v < 0) errs.push({
                    name: 'netWorth',
                    msg: 'Net worth must be a positive number.'
                });
            }
            // dates cannot be in the future
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            [
                ['dateOfIncorporation', 'Date of Incorporation'],
                ['dateOfCommencementOfBusiness', 'Date of Commencement of Business'],
                ['uboDob', 'Date of Birth']
            ]
            .forEach(([n, label]) => {
                if (names.includes(n) && val(n)) {
                    const d = new Date(val(n));
                    if (!isNaN(d) && d > today) errs.push({
                        name: n,
                        msg: `${label} cannot be in the future.`
                    });
                }
            });
            // mobile number must match one of our countries' dialing codes
            if (names.includes('mobileNumber') && val('mobileNumber')) {
                const digits = val('mobileNumber').replace(/\D/g, '');
                const codes = ISD_CODES.slice().sort((a, b) => b.length - a.length);
                const match = codes.find(c => digits.startsWith(c));
                if (!match) {
                    errs.push({
                        name: 'mobileNumber',
                        msg: 'Mobile must start with a valid country code (' + ISD_CODES.map(c => '+' + c).join(', ') + ').'
                    });
                } else {
                    const rest = digits.slice(match.length);
                    if (rest.length < 6 || rest.length > 12) errs.push({
                        name: 'mobileNumber',
                        msg: 'Mobile number length is invalid for the selected country code.'
                    });
                }
            }
            // declaration checkbox
            if (names.includes('declarationAgreed')) {
                const d = fieldEl('declarationAgreed');
                if (d && !d.checked) errs.push({
                    name: 'declarationAgreed',
                    msg: 'You must agree to the declaration before saving.'
                });
            }
            // The three declaration checkboxes must all be ticked (both forms)
            ['declaration1', 'declaration2', 'declaration3'].forEach(n => {
                if (names.includes(n)) {
                    const d = fieldEl(n);
                    if (d && !d.checked) errs.push({
                        name: n,
                        msg: 'Please tick this declaration.'
                    });
                }
            });
            return errs;
        }

        function fieldsInStep(stepId) {
            const panel = document.querySelector(`.fpi-step-panel[data-panel="${stepId}"]`);
            if (!panel) return [];
            return Array.from(panel.querySelectorAll('[name]')).map(e => e.getAttribute('name'));
        }

        function applyErrors(errs) {
            errs.forEach(e => setError(fieldEl(e.name), e.msg));
            // flag tabs that contain errors
            const badSteps = new Set(errs.map(e => stepOf(fieldEl(e.name))).filter(Boolean));
            document.querySelectorAll('.fpi-tab-btn').forEach(btn => {
                btn.classList.toggle('has-error', badSteps.has(btn.getAttribute('data-step')));
            });
            return badSteps;
        }

        function validateStep(stepId) {
            const errs = runRules(fieldsInStep(stepId));
            applyErrors(errs);
            let ok = errs.length === 0;
            if (stepId === 'ubo' && !checkUboRows()) {
                ok = false;
                document.querySelectorAll('.fpi-tab-btn').forEach(b => {
                    if (b.getAttribute('data-step') === 'ubo') b.classList.add('has-error');
                });
            }
            return ok;
        }

        // ── Tab-wise save: validate ONLY the current section client-side, then
        //    submit the form (with a hidden `section` field) so the server saves
        //    just that section to its DB table(s) and pre-fills on reload. ──
        const sectionField = document.getElementById('fpiSection');

        // Beneficial Ownership is optional: no sub-fund/UBO choice and no rows = empty.
        function uboSectionEmpty() {
            const hs = document.getElementById('hasSubFunds');
            const hu = document.getElementById('hasUbos');
            const noChoice = (!hs || hs.value === '') && (!hu || hu.value === '');
            const noRows = (typeof uboRows === 'undefined' || !uboRows.length) &&
                (typeof intermediates === 'undefined' || !intermediates.length) &&
                (typeof controllers === 'undefined' || !controllers.length) &&
                (typeof subFunds === 'undefined' || !subFunds.length);
            return noChoice && noRows;
        }

        async function saveTab() {
            const step = steps[current];
            // The UBO Determination *tool* (Step 3) is optional — if no ownership tree was
            // built, just move on without saving/ticking. Beneficial Ownership (Step 4) is mandatory.
            if (step.id === 'ubo_tool' && (!window.__uboEmpty || window.__uboEmpty())) {
                errorBanner.style.display = 'none';
                document.querySelectorAll('.fpi-tab-btn').forEach(b => {
                    if (b.getAttribute('data-step') === 'ubo_tool') b.classList.remove('has-error');
                });
                if (current < steps.length - 1) current++;
                render();
                return;
            }
            if (!validateStep(step.id)) {
                errorBanner.style.display = '';
                const firstEl = document.querySelector(`.fpi-step-panel[data-panel="${step.id}"] .is-invalid`);
                if (firstEl && firstEl.focus) firstEl.focus();
                return;
            }
            errorBanner.style.display = 'none';
            const btn = document.getElementById('fpiSaveTab');
            btn.disabled = true;
            // AJAX save — no page reload, so data typed in OTHER tabs is never lost.
            const saved = await saveSectionAjax(step.id, false);
            btn.disabled = false;
            if (!saved.ok) {
                const detail = saved.errors ? Object.values(saved.errors).flat()[0] : saved.message;
                Swal.fire({
                    icon: 'error',
                    title: 'Could not save section',
                    text: detail || 'Please review this section.',
                    confirmButtonColor: '#3e6f7c'
                });
                return;
            }
            savedSteps.add(step.id);
            if (current < steps.length - 1) current++; // advance to the next tab
            render();
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: saved.message || 'Section saved',
                showConfirmButton: false,
                timer: 1600,
                timerProgressBar: true
            });
        }

        document.getElementById('fpiSaveTab').addEventListener('click', saveTab);
        // Enter key inside the form triggers a section save (AJAX), not a raw submit.
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            saveTab();
        });

        // ── Final submission: validate EVERY section, then submit ──
        function validateAll() {
            let all = [];
            steps.forEach(s => {
                if (s.id === 'ubo_tool') return;
                all = all.concat(runRules(fieldsInStep(s.id)));
            });
            const badSteps = applyErrors(all);

            // Beneficial Ownership rows
            if (hasUbos && hasUbos.value === 'YES' && !checkUboRows()) {
                badSteps.add('ubo');
                document.querySelectorAll('.fpi-tab-btn').forEach(b => {
                    if (b.getAttribute('data-step') === 'ubo') b.classList.add('has-error');
                });
            }

            if (badSteps.size) {
                const firstBad = steps.findIndex(s => badSteps.has(s.id));
                if (firstBad >= 0) {
                    current = firstBad;
                    render();
                }
                errorBanner.style.display = '';
                const firstEl = all.length ? fieldEl(all[0].name) : document.querySelector('.fpi-step-panel[data-panel="ubo"] .is-invalid');
                if (firstEl && firstEl.focus) firstEl.focus();
                return false;
            }
            errorBanner.style.display = 'none';
            return true;
        }

        const submitBtn = document.getElementById('fpiSubmit');
        submitBtn.addEventListener('click', () => {
            if (submitBtn.disabled) return;
            if (!validateAll()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Please complete all sections',
                    text: 'Some required fields are missing or invalid. The first section needing attention has been opened.',
                    confirmButtonColor: '#3e6f7c'
                });
                return;
            }
            Swal.fire({
                icon: 'warning',
                title: 'Confirm final submission',
                html: '<div style="text-align:left;font-size:13px;line-height:1.6;color:#3d4f5e">'
                    + 'You are about to <strong>submit this FPI application</strong>. Please make sure every detail is correct.'
                    + '<ul style="margin:10px 0 0 18px;padding:0">'
                    + '<li>This is your <strong>final submission</strong>.</li>'
                    + '<li>Once submitted, the application becomes <strong>read-only</strong> and cannot be edited.</li>'
                    + '<li>You will still be able to Print / Preview the submitted form.</li>'
                    + '</ul></div>',
                showCancelButton: true,
                reverseButtons: true,
                focusCancel: true,
                confirmButtonText: 'Yes, submit application',
                confirmButtonColor: '#27ae60',
                cancelButtonText: 'Go back &amp; review',
                cancelButtonColor: '#6c7a86',
            }).then(r => {
                if (r.isConfirmed) document.getElementById('fpiSubmitForm').submit();
            });
        });

        // ── Sequential Auto-fill: fill tab → save tab → next … → submit → Print Preview ──
        function setField(name, value) {
            const el = form.querySelector(`[name="${name}"]`);
            if (!el) return;
            if (el.type === 'checkbox') el.checked = !!value;
            else el.value = value;
            el.dispatchEvent(new Event('input', {
                bubbles: true
            }));
            el.dispatchEvent(new Event('change', {
                bubbles: true
            }));
        }
        const SAMPLE = {
            applicant: {
                nameTitle: 'M/S',
                entityName: 'GLOBAL ALPHAS FPI FUND',
                applicantType: 'Company',
                knownByAnotherName: 'NO',
                dateOfIncorporation: '2015-06-12',
                dateOfCommencementOfBusiness: '2015-07-01',
                placeOfIncorporation: 'NEW YORK',
                countryOfIncorporation: '2',
                incorporationIsdCode: '1',
                lei: '5493001KJTIIGC8Y1R12',
                leiExpiryDate: '2027-06-12'
            },
            applicantIndividual: {
                indTitle: 'Mr',
                indFirstName: 'JOHNATHAN',
                indMiddleName: 'R',
                indLastName: 'DAVIS',
                indOtherName: 'no',
                indDob: '1980-04-18',
                indPlaceOfBirth: 'NEW YORK',
                indCountryOfBirth: 'UNITED STATES',
                indBirthIsd: '1',
                indNationality: 'AMERICAN',
                indNationalityIsd: '1',
                indPassport: 'USA839103982',
                indGender: 'Male',
                indMaritalStatus: 'Married',
                indCitizenshipStatus: 'Foreigner',
                indCountryOfCitizenship: 'UNITED STATES',
                indFatherFirstName: 'ROBERT',
                indFatherLastName: 'DAVIS',
                indMotherFirstName: 'MARGARET',
                indMotherLastName: 'DAVIS',
                indSpouseFirstName: 'EMILY',
                indSpouseLastName: 'DAVIS'
            },
            contact: {
                regAddressLine1: '120 BROADWAY',
                regAddressLine2: 'SUITE 3000',
                regAddressLine3: 'FINANCIAL DISTRICT',
                regAddressLine4: 'MANHATTAN',
                regCity: 'NEW YORK',
                regState: 'NEW YORK',
                regCountry: '2',
                regZip: '10271',
                sameAddress: true,
                offAddressLine1: '120 BROADWAY',
                offAddressLine2: 'SUITE 3000',
                offAddressLine3: 'FINANCIAL DISTRICT',
                offAddressLine4: 'MANHATTAN',
                offCity: 'NEW YORK',
                offState: 'NEW YORK',
                offCountry: '2',
                offZip: '10271',
                telIsdCode: '1',
                telAreaCode: '212',
                telNumber: '5550199',
                offTelIsdCode: '1',
                offTelAreaCode: '212',
                offTelNumber: '5550200',
                mobileNumber: '+1-917-555-0144',
                faxNumber: '+1-212-555-0188',
                website: 'https://globalalphasfund.com',
                email: 'compliance@globalalphasfund.com'
            },
            ubo: {
                hasUbos: 'YES',
                uboName: 'JOHNATHAN DAVIS',
                uboDob: '1970-04-18',
                uboNationality: '2',
                uboPassport: 'USA839103982',
                uboOwnership: '35',
                uboAddress: '55 EAST 72ND ST, NEW YORK, NY 10021'
            },
            financial: {
                incomeRange: 'ABOVE_1M',
                grossIncome: '5000000',
                occupation: 'finInst',
                netWorth: '45000000',
                netWorthDate: '2026-03-31',
                taxCountry: '2',
                tin: '13-3918239',
                tinReason: '',
                tinExplanation: '',
                trcNumber: 'TRC-US-2026-0091',
                fatcaCrs: 'yes',
                isPep: 'no',
                relatedToPep: 'no'
            },
            category: {
                fpiCategory: 'CAT_I',
                subCategory: 'regulated_entities',
                mimStructure: 'no',
                regulatoryStatus: 'REGULATED',
                regulatorName: 'SECURITIES AND EXCHANGE COMMISSION (SEC)',
                licenseNumber: 'SEC-FPI-9281A',
                regulatorJurisdiction: '2',
                regulatorWebsite: 'https://sec.gov',
                regulatorCapacity: 'Registered Investment Adviser',
                complianceName: 'SARAH JENKINS',
                complianceTitle: 'COMPLIANCE OFFICER',
                complianceEmail: 'compliance@globalalphasfund.com',
                compliancePhone: '+1-212-555-0199',
                complianceFax: '+1-212-555-0188',
                hasCustodian: 'yes',
                custodianNameCat: 'DEUTSCHE BANK AG',
                custodianReg: 'BaFin',
                custRegCode: 'DB-CUST-8891',
                custodianAddress: 'TAUNUSANLAGE 12, FRANKFURT',
                disciplinaryHistory: 'no',
                disciplinaryDetails: '',
                clubbingDeclaration: 'no',
                fpiGroupNumber: '',
                groupNumber: '',
                priorAssociation: 'no'
            },
            depository: {
                hasPan: 'yes',
                pan: 'AAACG1234F',
                existingpanName: 'GLOBAL ALPHAS FPI FUND',
                bankName: 'CITIBANK N.A. MUMBAI',
                bankAccount: '98765432101',
                bankAccountType: 'NRO',
                bankSwift: 'CITIINBX',
                custodianName: 'DEUTSCHE BANK AG',
                dpId: 'IN300162',
                clientId: '10928374',
                repTitle: 'M/s',
                repLastName: 'MEHTA',
                repFirstName: 'ANIL',
                repMiddleName: 'K',
                repAddress: 'NARIMAN POINT, MUMBAI 400021',
                listed: 'yes',
                exchangeName: 'NASDAQ',
                poiType: 'copy_registration_cert_overseas',
                poiNumber: 'REG-US-77123',
                poaType: 'copy_registration_cert_overseas',
                poaNumber: 'REG-US-77123',
                depositoryAuth: 'OpenAccount',
                modeOfOperation: 'AnyOneSingle',
                otherMode: '',
                bankAuth: 'OpenSNRA'
            },
            additional: {
                primaryContactName: 'SARAH JENKINS',
                primaryContactDesignation: 'COMPLIANCE OFFICER',
                investmentManagerName: 'ALPHA ASSET MANAGEMENT LLC',
                indiaPlaceOfBusiness: 'NONE',
                odiDerivatives: 'no',
                bankDeclaration: 'not_bank',
                bankEntityName: '',
                nriControl1: 'no_control',
                nriControl2: 'not_controlled_by_nri_im',
                directlyControlled: 'no',
                nriControlEntityName: '',
                offshoreFund: 'no',
                nriEntitlement: 'no_investors',
                reg5b7: 'No',
                kraConsent: 'yes',
                kraRepName: 'SARAH JENKINS',
                kraEmail1: 'compliance@globalalphasfund.com',
                kraEmail2: '',
                kraEmail3: '',
                kraMobile: '+1-917-555-0144'
            },
            declarations: {
                signatureName: 'SARAH JENKINS',
                declarationAgreed: true,
                declarationPlace: 'MUMBAI',
                declarationDate: '2026-09-22',
                applicantName: 'SARAH JENKINS',
                applicantDesignation: 'AUTHORIZED SIGNATORY',
                authDesignation: 'AUTHORIZED SIGNATORY',
                authDate: '2026-09-22',
                declaration1: true,
                declaration2: true,
                declaration3: true
            },
        };
        const sleep = ms => new Promise(r => setTimeout(r, ms));

        async function saveSectionAjax(id, isAutofill = false) {
            // Serialize dynamic tables + checkbox groups into their hidden JSON inputs first.
            if (window.__serializeRepeaters) window.__serializeRepeaters(id);
            if (id === 'financial') {
                const inc = Array.from(document.querySelectorAll('.js-income-src:checked')).map(c => c.value);
                const h = document.getElementById('incomeSourcesJsonField');
                if (h) h.value = JSON.stringify(inc);
            }
            if (id === 'depository') {
                const act = Array.from(document.querySelectorAll('.js-sensitive-act:checked')).map(c => c.value);
                const h = document.getElementById('sensitiveActivitiesJsonField');
                if (h) h.value = JSON.stringify(act);
            }
            if (id === 'additional') {
                const imt = Array.from(document.querySelectorAll('.js-im-type:checked')).map(c => c.value);
                const h = document.getElementById('imTypesJsonField');
                if (h) h.value = JSON.stringify(imt);
            }
            if (id === 'declarations') {
                const other = document.querySelector('[name="otherDocs[]"]');
                if (other && other.files) Array.from(other.files).forEach(f => fd.append('otherDocs[]', f));
            }
            const fd = new FormData();
            fd.append('_token', CSRF);
            fd.append('section', id);
            if (isAutofill) fd.append('_autofill', '1');
            fieldsInStep(id).forEach(n => {
                if (n === 'otherDocs[]') return; // multi-file handled explicitly below
                const el = fieldEl(n);
                if (!el || el.disabled) return;
                if (el.type === 'file') {
                    if (el.files && el.files[0]) fd.append(n, el.files[0]);
                } else fd.append(n, el.type === 'checkbox' ? (el.checked ? 'on' : '') : (el.value ?? ''));
            });
            if (id === 'ubo_tool' && window.__uboSerialize) fd.append('uboStructure', window.__uboSerialize());
            if (id === 'ubo') {
                serializeUbo();
                fd.set('uboRowsJson', uboRowsJsonField.value);
                serializeBo();
                fd.set('subFundsJson', document.getElementById('subFundsJsonField').value);
                fd.set('intermediatesJson', document.getElementById('intermediatesJsonField').value);
                fd.set('controllersJson', document.getElementById('controllersJsonField').value);
            }
            const res = await fetch(@json(route('fpi.store')), {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: fd,
            });
            // store() replies with {ok, section, next, message} — keep the message
            // so each tab can be confirmed, and the errors so a failure is specific.
            const data = await res.json().catch(() => ({}));
            return {
                ok: res.ok,
                message: data.message || '',
                errors: data.errors || null
            };
        }

        // ── Stop control for the auto-fill run ──
        // The button lives inside the Swal body (not its actions area) because
        // Swal.showLoading() takes that area over. The click is delegated from
        // document so it survives every Swal.update() re-render of the body.
        let autofillStop = false;
        const STOP_BTN = '<button type="button" id="fpiAutofillStop" style="margin-top:14px;padding:6px 16px;font-size:12.5px;font-weight:600;cursor:pointer;border-radius:5px;background:#fff;border:1px solid #c0392b;color:#c0392b">■ Stop after this tab</button>';
        document.addEventListener('click', (e) => {
            const btn = e.target && e.target.closest ? e.target.closest('#fpiAutofillStop') : null;
            if (!btn || autofillStop) return;
            autofillStop = true;
            btn.disabled = true;
            btn.style.opacity = '.6';
            btn.style.cursor = 'default';
            btn.textContent = 'Stopping after this tab…';
        });

        // Halt cleanly: whatever was saved stays saved, so reload to show the ticks.
        function haltAutofill(tabName) {
            Swal.fire({
                icon: 'info',
                title: 'Auto-fill stopped',
                html: `Stopped after <strong>${tabName}</strong>. Everything saved up to this point has been kept — you can carry on manually from the next tab.`,
                confirmButtonColor: '#3e6f7c',
            }).then(() => window.location.reload());
        }

        // Auto-fill the parts setField can't: checkbox groups + dynamic add-row tables.
        function checkBoxes(selector, values) {
            values.forEach(v => {
                const c = document.querySelector(`${selector}[value="${v}"]`);
                if (c && !c.disabled) {
                    c.checked = true;
                    c.dispatchEvent(new Event('change', {
                        bubbles: true
                    }));
                }
            });
        }

        function autofillExtras(id) {
            if (id === 'financial') {
                checkBoxes('.js-income-src', ['salary', 'capital_gains']);
                window.__seedRepeater && window.__seedRepeater('taxResidenciesJsonField', [{
                    country: '2',
                    tin: '13-3918239',
                    trc: 'TRC-US-2026-0091',
                    reason: '',
                    explanation: ''
                }]);
            } else if (id === 'category') {
                window.__seedRepeater && window.__seedRepeater('investmentManagersJsonField', [{
                    name: 'ALPHA ASSET MANAGEMENT LLC',
                    sebiReg: 'INA000012345'
                }]);
            } else if (id === 'depository') {
                checkBoxes('.js-sensitive-act', ['forex']);
            } else if (id === 'additional') {
                window.__seedRepeater && window.__seedRepeater('categoryOneEntitiesJsonField', [{
                    name: 'ALPHA GOVERNMENT FUND',
                    country: '2',
                    entityType: 'sovereign_wealth_fund'
                }]);
                window.__seedRepeater && window.__seedRepeater('signatoriesJsonField', [{
                    name: 'SARAH JENKINS',
                    relationship: 'Director',
                    pan: 'AAAPS1234K',
                    nationality: 'UNITED STATES',
                    dob: '1980-05-01',
                    address: '55 EAST 72ND ST, NEW YORK',
                    govId: 'DL-99281'
                }]);
            } else if (id === 'ubo') {
                window.__seedBo && window.__seedBo({
                    intermediates: [{
                        subFund: '',
                        name: 'ALPHA HOLDINGS LLC',
                        stakeType: 'Equity',
                        chain: 'Alpha Holdings › Applicant',
                        country: '2',
                        pct: '60',
                        type: 'Non-Individual'
                    }],
                    controllers: [{
                        subFund: '',
                        name: 'JOHNATHAN DAVIS',
                        method: 'Board majority',
                        country: '2',
                        pct: '40',
                        type: 'Individual'
                    }],
                });
            }
        }

        async function autofillFlow() {
            const order = steps.map(s => s.id);
            for (const id of order) {
                current = order.indexOf(id);
                render();
                if (id === 'ubo_tool') {
                    if (window.__uboFill) window.__uboFill();
                } else if (id === 'ubo') {
                    fillUboSample();
                } else {
                    const d = (id === 'applicant' && IS_INDIVIDUAL) ? SAMPLE.applicantIndividual : SAMPLE[id];
                    if (d) Object.entries(d).forEach(([k, v]) => setField(k, v));
                }
                autofillExtras(id); // checkbox groups + dynamic tables
                Swal.update({
                    icon: 'info',
                    title: `Filling "${steps[current].tab}"…`,
                    html: 'Saving to database…' + STOP_BTN
                });
                Swal.showLoading();
                await sleep(550); // visible fill
                const saved = await saveSectionAjax(id, true);
                if (!saved.ok) {
                    const detail = saved.errors ? Object.values(saved.errors).flat()[0] : saved.message;
                    Swal.fire({
                        icon: 'error',
                        title: 'Auto-fill stopped',
                        text: detail || `Could not save the "${steps[current].tab}" section.`,
                        confirmButtonColor: '#3e6f7c'
                    });
                    return;
                }
                savedSteps.add(id);
                render();
                // Per-tab confirmation. It auto-advances rather than waiting for a
                // click, so the run stays hands-free.
                Swal.hideLoading();
                Swal.update({
                    icon: 'success',
                    title: 'Saved',
                    html: (saved.message || `${steps[current].tab} saved successfully.`) + STOP_BTN
                });
                await sleep(1200); // let the message be read

                // Stop here if asked: this tab is saved, nothing further runs.
                if (autofillStop) {
                    haltAutofill(steps[current].tab);
                    return;
                }
            }
            // Ask for the client's consent before the final submission (auto-fill still confirms).
            Swal.hideLoading();
            const confirmed = await Swal.fire({
                icon: 'warning',
                title: 'Confirm final submission',
                html: '<div style="text-align:left;font-size:13px;line-height:1.6;color:#3d4f5e">'
                    + 'All tabs have been filled and saved. Please review before you submit.'
                    + '<ul style="margin:10px 0 0 18px;padding:0">'
                    + '<li>This is your <strong>final submission</strong>.</li>'
                    + '<li>Once submitted, the application becomes <strong>read-only</strong> and cannot be edited.</li>'
                    + '<li>You will still be able to Print / Preview the submitted form.</li>'
                    + '</ul></div>',
                showCancelButton: true,
                reverseButtons: true,
                focusCancel: true,
                confirmButtonText: 'Yes, submit application',
                confirmButtonColor: '#27ae60',
                cancelButtonText: 'Go back &amp; review',
                cancelButtonColor: '#6c7a86',
                allowOutsideClick: false,
            }).then(r => r.isConfirmed);
            if (!confirmed) {
                Swal.fire({
                    icon: 'info',
                    title: 'Not submitted',
                    html: 'Everything was filled and <strong>saved</strong>, but the application was <strong>not submitted</strong>. You can review and submit it whenever you are ready.',
                    confirmButtonColor: '#3e6f7c',
                }).then(() => window.location.reload());
                return;
            }
            // Final submission
            Swal.update({
                icon: 'info',
                title: 'Submitting application…',
                html: ''
            });
            Swal.showLoading();
            const sres = await fetch(@json(route('fpi.submit')), {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: (() => {
                    const f = new FormData();
                    f.append('_token', CSRF);
                    return f;
                })(),
            });
            if (!sres.ok) {
                const j = await sres.json().catch(() => ({}));
                Swal.fire({
                    icon: 'error',
                    title: 'Submission failed',
                    text: j.message || 'Please review the sections.',
                    confirmButtonColor: '#3e6f7c'
                });
                return;
            }
            IS_SUBMITTED = true;
            current = order.indexOf('declarations');
            render();
            // Reload so the server re-renders the uploaded documents (with preview links) + submitted state.
            Swal.fire({
                    icon: 'success',
                    title: 'Application submitted',
                    text: 'All tabs were filled, saved and submitted. You can now use Print / Preview.',
                    confirmButtonColor: '#27ae60'
                })
                .then(() => window.location.reload());
        }

        document.querySelectorAll('.js-fpi-autofill').forEach(btn => btn.addEventListener('click', () => {
            Swal.fire({
                icon: 'question',
                title: 'Auto-fill & submit?',
                text: 'This fills each tab, saves it, then submits the application — step by step.',
                showCancelButton: true,
                confirmButtonText: 'Yes, run it',
                confirmButtonColor: '#3e6f7c',
                cancelButtonText: 'Cancel',
            }).then(r => {
                if (!r.isConfirmed) return;
                // Seed the icon here so Swal.update() can swap info -> success per tab.
                autofillStop = false;
                Swal.fire({
                    icon: 'info',
                    title: 'Starting…',
                    html: STOP_BTN,
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                autofillFlow();
            });
        }));

        // ── Confirm a successful tab save (saveTab() posts the form and the
        //    server redirects back with the "<Section> saved successfully." flash) ──
        const flashStatus = @json(session('status'));
        if (flashStatus) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: flashStatus,
                confirmButtonColor: '#27ae60',
                timer: 2600,
                timerProgressBar: true,
            });
        }

        // ── Render server-side validation errors returned after a failed submit ──
        const serverErrors = @json($serverErrorsJs);
        if (Object.keys(serverErrors).length) {
            const errs = Object.entries(serverErrors).map(([name, msgs]) => ({
                name,
                msg: msgs[0]
            }));
            const badSteps = applyErrors(errs);
            errorBanner.style.display = '';
            const firstBad = steps.findIndex(s => badSteps.has(s.id));
            if (firstBad >= 0) current = firstBad;
        }

        syncComm();
        render();
    })();

    // ── Embedded UBO Determination Tool (Step 3) ──
    (function() {
        const THRESHOLD = 10;
        const STORED = @json($form['uboStructure'] ?? '');
        const APPLICANT_NAME = (document.querySelector('[name="entityName"]')?.value || 'Applicant').trim() || 'Applicant';

        // A single applicant node seeded from the Entity Name (Tab 1), no owners.
        const seededEntities = () => ([{
            id: 'applicant',
            name: APPLICANT_NAME,
            type: (document.getElementById('applicantType')?.value || ''),
            owners: []
        }]);

        let entities;
        if (STORED) {
            try {
                entities = (JSON.parse(STORED).entities) || null;
            } catch (e) {
                entities = null;
            }
        }
        if (!entities || !entities.length) entities = seededEntities();

        let uid = 0;
        const newId = (p) => p + '_' + (Date.now() + (uid++));

        const hierarchyEl = document.getElementById('hierarchy');
        const evalResultEl = document.getElementById('evalResult');
        const applicantTypeEl = document.getElementById('applicantType');

        // Restore the applicant type from the stored tree (applicant node type).
        if (entities[0] && entities[0].type) applicantTypeEl.value = entities[0].type;

        // Keep the applicant node's type in sync with the dropdown.
        applicantTypeEl.addEventListener('change', () => {
            if (entities[0]) {
                entities[0].type = applicantTypeEl.value;
                renderHierarchy();
            }
            applicantTypeEl.classList.remove('is-invalid');
        });

        // Serializer + validity flag used by the "Save Section" flow.
        window.__uboSerialize = () => {
            if (entities[0]) entities[0].type = applicantTypeEl.value;
            return JSON.stringify({
                entities
            });
        };
        window.__uboValid = () => !!applicantTypeEl.value;
        // Empty = no owners defined anywhere in the ownership tree.
        window.__uboEmpty = () => !Array.isArray(entities) || !entities.some(e => e.owners && e.owners.length > 0);

        // Seed the tool with a sample ownership tree (per-tab Auto-fill).
        window.__uboFill = () => {
            const appName = (document.querySelector('[name="entityName"]')?.value || 'GLOBAL ALPHAS FPI FUND').trim();
            const subId = newId('sub');
            entities = [{
                    id: 'applicant',
                    name: appName,
                    type: 'Company',
                    owners: [{
                            id: newId('own'),
                            name: 'Alpha Holdings LLC',
                            type: 'Entity',
                            pct: 60,
                            targetId: subId
                        },
                        {
                            id: newId('own'),
                            name: 'Johnathan Davis',
                            type: 'Individual',
                            pct: 40
                        },
                    ]
                },
                {
                    id: subId,
                    name: 'Alpha Holdings LLC',
                    type: 'Company',
                    owners: [{
                            id: newId('own'),
                            name: 'Sarah Jenkins',
                            type: 'Individual',
                            pct: 80
                        },
                        {
                            id: newId('own'),
                            name: 'Mike Smith',
                            type: 'Individual',
                            pct: 20
                        },
                    ]
                },
            ];
            applicantTypeEl.value = 'Company';
            renderHierarchy();
        };

        function updateEntityName(entityId, newName) {
            entities.forEach(ent => {
                if (ent.id === entityId) ent.name = newName;
                ent.owners.forEach(o => {
                    if (o.type === 'Entity' && o.targetId === entityId) o.name = newName;
                });
            });
            renderHierarchy();
        }

        function updateOwner(entityId, ownerId, fields) {
            const ent = entities.find(e => e.id === entityId);
            if (!ent) return;
            const owner = ent.owners.find(o => o.id === ownerId);
            if (!owner) return;
            Object.assign(owner, fields);
            if (fields.type === 'Entity' && !owner.targetId) {
                const subId = newId('sub_entity');
                owner.targetId = subId;
                entities.push({
                    id: subId,
                    name: owner.name || 'New Entity',
                    type: 'Company',
                    owners: [{
                        id: newId('sub_own'),
                        name: 'Natural Person',
                        type: 'Individual',
                        pct: 100
                    }],
                });
            }
            renderHierarchy();
        }

        function addOwner(entityId) {
            const ent = entities.find(e => e.id === entityId);
            if (!ent) return;
            ent.owners.push({
                id: newId('own'),
                name: '',
                type: '',
                pct: 0
            });
            renderHierarchy();
        }

        function removeOwner(entityId, ownerId, targetId) {
            const ent = entities.find(e => e.id === entityId);
            if (ent) ent.owners = ent.owners.filter(o => o.id !== ownerId);
            if (targetId) entities = entities.filter(e => e.id !== targetId);
            renderHierarchy();
        }

        function renderHierarchy() {
            hierarchyEl.innerHTML = '';
            entities.forEach(ent => {
                const isMain = ent.id === 'applicant';
                const card = document.createElement('div');
                card.style.cssText = `border:1.5px solid var(--gray200);border-radius:6px;padding:14px;background:${isMain ? '#f8fafc' : '#ffffff'}`;

                const head = document.createElement('div');
                head.style.cssText = 'display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--gray100);padding-bottom:8px;margin-bottom:12px';
                const headLeft = document.createElement('div');
                if (isMain) {
                    const strong = document.createElement('strong');
                    strong.style.cssText = 'font-size:12.5px;color:#3e6f7c';
                    strong.textContent = ent.name + ' (Direct Applicant)';
                    headLeft.appendChild(strong);
                } else {
                    const nameInput = document.createElement('input');
                    nameInput.type = 'text';
                    nameInput.value = ent.name;
                    nameInput.style.cssText = 'font-weight:700;font-size:12px;border:none;border-bottom:1px solid #cbd5e1;outline:none;padding:2px 4px;width:200px';
                    nameInput.addEventListener('change', e => updateEntityName(ent.id, e.target.value));
                    headLeft.appendChild(nameInput);
                }
                const typeSpan = document.createElement('span');
                typeSpan.style.cssText = 'font-size:10px;color:var(--gray500);margin-left:8px';
                typeSpan.textContent = '(Type: ' + ent.type + ')';
                headLeft.appendChild(typeSpan);
                head.appendChild(headLeft);
                card.appendChild(head);

                const ownersWrap = document.createElement('div');
                ownersWrap.style.cssText = 'display:flex;flex-direction:column;gap:10px';
                ent.owners.forEach(owner => {
                    const row = document.createElement('div');
                    row.style.cssText = 'display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:10px;align-items:center';

                    const name = document.createElement('input');
                    name.className = 'ubo-input';
                    name.type = 'text';
                    name.value = owner.name;
                    name.placeholder = 'Shareholder / partner name';
                    name.addEventListener('change', e => updateOwner(ent.id, owner.id, {
                        name: e.target.value
                    }));

                    const type = document.createElement('select');
                    type.className = 'ubo-select';
                    type.innerHTML = '<option value="">Select</option><option value="Individual">Natural Person</option><option value="Entity">Corporate Entity</option>';
                    type.value = owner.type || '';
                    type.addEventListener('change', e => updateOwner(ent.id, owner.id, {
                        type: e.target.value
                    }));

                    const pctWrap = document.createElement('div');
                    pctWrap.style.cssText = 'display:flex;align-items:center;gap:4px';
                    const pct = document.createElement('input');
                    pct.className = 'ubo-input';
                    pct.type = 'number';
                    pct.min = '0';
                    pct.max = '100';
                    pct.step = '0.01';
                    pct.value = owner.pct;
                    pct.placeholder = '%';
                    pct.style.width = '60px';
                    pct.addEventListener('change', e => {
                        let v = parseFloat(e.target.value) || 0;
                        v = Math.min(100, Math.max(0, v)); // clamp 0..100
                        e.target.value = v;
                        updateOwner(ent.id, owner.id, {
                            pct: v
                        });
                    });
                    const pctLabel = document.createElement('span');
                    pctLabel.style.cssText = 'font-size:11px;color:var(--gray500)';
                    pctLabel.textContent = '%';
                    pctWrap.append(pct, pctLabel);

                    const remove = document.createElement('button');
                    remove.type = 'button';
                    remove.className = 'ubo-btn ubo-btn-ghost';
                    remove.textContent = 'Remove';
                    remove.style.cssText = 'padding:4px 8px;color:var(--danger);border-color:#f1aaa5;height:32px';
                    remove.addEventListener('click', () => removeOwner(ent.id, owner.id, owner.targetId));

                    row.append(name, type, pctWrap, remove);
                    ownersWrap.appendChild(row);
                });
                card.appendChild(ownersWrap);

                const actions = document.createElement('div');
                actions.style.cssText = 'display:flex;gap:12px;margin-top:12px;justify-content:flex-start';
                const addBtn = document.createElement('button');
                addBtn.type = 'button';
                addBtn.className = 'ubo-btn ubo-btn-ghost';
                addBtn.textContent = '+ Add Shareholder/Partner';
                addBtn.style.cssText = 'padding:4px 10px;font-size:11px';
                addBtn.addEventListener('click', () => addOwner(ent.id));
                actions.appendChild(addBtn);
                card.appendChild(actions);

                hierarchyEl.appendChild(card);
            });
            renderDiagram();
            if (mermaidVisible) document.getElementById('mermaidCode').value = generateMermaid();
        }

        // Aggregate all natural persons in the tree (by name) with their effective %.
        function computePersons() {
            const results = [];
            const visited = new Set();
            const traverse = (entityId, multiplier, path) => {
                if (visited.has(entityId)) return; // cycle guard
                visited.add(entityId);
                const entity = entities.find(e => e.id === entityId);
                if (!entity) return;
                entity.owners.forEach(owner => {
                    const eff = (owner.pct / 100) * multiplier;
                    const currentPath = [...path, {
                        entityName: entity.name,
                        pct: owner.pct
                    }];
                    if (owner.type === 'Individual') {
                        const nm = (owner.name || '').trim();
                        if (!nm) return;
                        const existing = results.find(r => r.name.toLowerCase() === nm.toLowerCase());
                        if (existing) {
                            existing.effectivePct += eff;
                            existing.paths.push(currentPath);
                        } else results.push({
                            name: nm,
                            effectivePct: eff,
                            paths: [currentPath]
                        });
                    } else if (owner.type === 'Entity') {
                        traverse(owner.targetId, eff, currentPath);
                    }
                });
                visited.delete(entityId);
            };
            traverse('applicant', 100, []);
            return results;
        }
        // Expose the identified natural persons to the Beneficial Ownership tab.
        window.__uboPersons = () => computePersons().map(r => ({
            name: r.name,
            pct: Math.round(r.effectivePct * 100) / 100
        }));

        function evaluateUbos() {
            renderEvalTable(computePersons());
        }

        function renderEvalTable(results) {
            if (!results.length) {
                evalResultEl.innerHTML = `<div style="background:#f8fafc;border-radius:6px;border:1px solid var(--gray200);padding:16px;text-align:center;color:var(--gray500);font-size:12px">
                    No natural person owners found in structural tree. Senior Managing Official (SMO) must be designated.</div>`;
                return;
            }
            let rows = '';
            results.forEach(res => {
                const isUbo = res.effectivePct >= THRESHOLD;
                rows += `<tr style="border-bottom:1px solid var(--gray100)">
                    <td style="padding:8px 12px;font-weight:600;color:var(--gray900)">${escapeHtml(res.name)}</td>
                    <td style="padding:8px 12px;text-align:right;font-weight:700">${res.effectivePct.toFixed(1)}%</td>
                    <td style="padding:8px 12px;text-align:center">
                        <span style="padding:3px 8px;border-radius:10px;font-size:9.5px;font-weight:700;background:${isUbo ? '#d1e7dd' : '#f8d7da'};color:${isUbo ? '#0f5132' : '#842029'}">
                            ${isUbo ? 'IDENTIFIED UBO' : 'BELOW THRESHOLD'}</span>
                    </td></tr>`;
            });
            evalResultEl.innerHTML = `<div style="background:#f8fafc;border-radius:6px;border:1px solid var(--gray200);overflow:hidden">
                <table style="width:100%;border-collapse:collapse;font-size:12px">
                    <thead><tr style="background:var(--gray100);border-bottom:1px solid var(--gray200)">
                        <th style="padding:8px 12px;text-align:left;font-weight:600;color:var(--gray700)">Identified Natural Person</th>
                        <th style="padding:8px 12px;text-align:right;font-weight:600;color:var(--gray700)">Effective Ownership %</th>
                        <th style="padding:8px 12px;text-align:center;font-weight:600;color:var(--gray700)">Status (Threshold &ge; ${THRESHOLD}%)</th>
                    </tr></thead><tbody>${rows}</tbody></table></div>`;
        }

        function renderDiagram() {
            const applicant = entities[0];
            if (!applicant) {
                document.getElementById('diagramView').innerHTML = '';
                return;
            }
            let ownersHtml = '';
            applicant.owners.forEach(owner => {
                const isEntity = owner.type === 'Entity';
                const child = isEntity ? entities.find(e => e.id === owner.targetId) : null;
                let subHtml = '';
                if (isEntity && child) {
                    let subs = '';
                    child.owners.forEach(so => {
                        subs += `<div style="display:flex;flex-direction:column;align-items:center">
                            <div style="background:#fff;border:1.5px solid #cbd5e1;padding:6px 10px;border-radius:4px;font-size:10px;text-align:center;min-width:100px">
                                <div style="font-weight:700;color:#1e293b">${escapeHtml(so.name)}</div>
                                <div style="font-size:9px;color:var(--gray500)">${so.pct}% of ${escapeHtml(owner.name)}</div>
                                <div style="font-size:9px;color:#0f766e;font-weight:600;margin-top:2px">(Eff: ${((so.pct / 100) * owner.pct).toFixed(1)}%)</div>
                            </div></div>`;
                    });
                    subHtml = `<div style="width:2px;height:16px;background:#cbd5e1"></div>
                        <div style="display:flex;gap:16px;justify-content:center">${subs}</div>`;
                }
                ownersHtml += `<div style="display:flex;flex-direction:column;align-items:center">
                    <div style="background:${isEntity ? '#e2f0d9' : '#fff'};border:1.5px solid ${isEntity ? '#385723' : '#cbd5e1'};padding:8px 12px;border-radius:4px;font-size:11px;text-align:center;min-width:120px">
                        <div style="font-weight:700;color:${isEntity ? '#385723' : '#1e293b'}">${escapeHtml(owner.name)}</div>
                        <div style="font-size:10px;color:var(--gray500)">${owner.pct}% Share</div>
                        <div style="font-size:9px;font-weight:600;color:${isEntity ? '#385723' : '#0f766e'};margin-top:2px">${owner.type}</div>
                    </div>${subHtml}</div>`;
            });
            document.getElementById('diagramView').innerHTML =
                `<div style="display:flex;flex-direction:column;gap:16px;align-items:center;background:#f8fafc;padding:20px;border-radius:6px;border:1px solid var(--gray200)">
                    <div style="display:flex;flex-direction:column;align-items:center">
                        <div style="background:#3e6f7c;color:#fff;padding:8px 16px;border-radius:4px;font-weight:700;font-size:12px;text-align:center;box-shadow:0 2px 4px rgba(0,0,0,.1)">
                            ${escapeHtml(applicant.name)}
                            <div style="font-size:10px;opacity:.8;font-weight:500">Applicant (${applicant.type})</div>
                        </div>
                        <div style="width:2px;height:16px;background:#cbd5e1"></div>
                        <div style="display:flex;gap:32px;justify-content:center;position:relative">${ownersHtml}</div>
                    </div></div>`;
        }

        function generateMermaid() {
            let code = 'graph TD\n';
            entities.forEach(ent => {
                ent.owners.forEach(o => {
                    const label = `${o.name} (${o.pct}%)`;
                    if (o.type === 'Entity') code += `  ${o.targetId}["${label}"] --> ${ent.id}["${ent.name}"]\n`;
                    else code += `  ${o.id}["${label}"] --> ${ent.id}["${ent.name}"]\n`;
                });
            });
            return code;
        }

        function escapeHtml(s) {
            return String(s).replace(/[&<>"']/g, c => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            } [c]));
        }

        function handleReset() {
            const type = applicantTypeEl.value;
            const name = (document.querySelector('[name="entityName"]')?.value || 'Applicant').trim() || 'Applicant';
            // Clean start: just the applicant node (from Entity Name), add owners yourself.
            entities = [{
                id: 'applicant',
                name,
                type,
                owners: []
            }];
            evalResultEl.innerHTML = `<div style="border:1px dashed var(--gray300);padding:16px;text-align:center;color:var(--gray500);font-size:11.5px;border-radius:6px">Awaiting evaluation...</div>`;
            renderHierarchy();
        }

        let mermaidVisible = false;

        function setView(m) {
            mermaidVisible = m;
            document.getElementById('diagramView').style.display = m ? 'none' : '';
            document.getElementById('mermaidView').style.display = m ? '' : 'none';
            document.getElementById('tabDiagram').className = 'ubo-btn ' + (m ? 'ubo-btn-ghost' : 'ubo-btn-primary');
            document.getElementById('tabMermaid').className = 'ubo-btn ' + (m ? 'ubo-btn-primary' : 'ubo-btn-ghost');
            if (m) document.getElementById('mermaidCode').value = generateMermaid();
        }

        document.getElementById('resetFlow').addEventListener('click', handleReset);
        document.getElementById('evaluateBtn').addEventListener('click', evaluateUbos);
        document.getElementById('tabDiagram').addEventListener('click', () => setView(false));
        document.getElementById('tabMermaid').addEventListener('click', () => setView(true));

        renderHierarchy();
    })();
</script>
@endpush