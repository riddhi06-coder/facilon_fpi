@extends('layouts.app')

@section('title', 'FPI Registration')
@section('page_title', 'FPI Registration')
@section('page_desc', 'Complete your Foreign Portfolio Investor registration details.')
@section('page_icon')
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.8"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
@endsection

@php
    $steps = [
        ['id' => 'applicant',    'num' => 'Profile',      'tab' => 'Applicant Profile',      'title' => 'Step 1: Basic Applicant Details'],
        ['id' => 'contact',      'num' => 'Contact',      'tab' => 'Contact & Address',      'title' => 'Step 2: Contact & Address Details'],
        ['id' => 'ubo_tool',     'num' => 'UBO Tool',     'tab' => 'UBO Determination',      'title' => 'Step 3: UBO Determination Tool'],
        ['id' => 'ubo',          'num' => 'UBO',          'tab' => 'Beneficial Ownership',   'title' => 'Step 4: Beneficial Ownership Information'],
        ['id' => 'financial',    'num' => 'Financial',    'tab' => 'Financial & Tax',        'title' => 'Step 5: Financial & Tax Information'],
        ['id' => 'category',     'num' => 'Category',     'tab' => 'Category & Regulatory',  'title' => 'Step 6: Category & Regulatory Classification'],
        ['id' => 'depository',   'num' => 'PAN & Bank',   'tab' => 'PAN, Bank & Depository', 'title' => 'Step 7: PAN, Bank & Depository Details'],
        ['id' => 'additional',   'num' => 'Additional',   'tab' => 'Additional Info',        'title' => 'Step 8: Additional Information'],
        ['id' => 'declarations', 'num' => 'Declarations', 'tab' => 'Final Declarations',     'title' => 'Step 9: Final Declarations & Document Upload'],
    ];

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
@endphp

@push('styles')
<style>
    .fpi-container { padding: 0 0 20px; }
    .fpi-header-banner {
        background: linear-gradient(90deg, #2c5e6a, #355f69);
        border-radius: 8px; padding: 20px 24px; color: #fff; margin-bottom: 16px;
    }
    .fpi-stepper { display: flex; width: 100%; justify-content: space-between; position: relative; align-items: center; }
    .fpi-stepper-line { position: absolute; top: 10px; left: 4%; right: 4%; height: 3px; background: rgba(255,255,255,.25); z-index: 0; }
    .fpi-stepper-line > div { height: 100%; background: #2ecc71; transition: width .3s ease-in-out; width: 0; }
    .fpi-step { display: flex; flex-direction: column; align-items: center; position: relative; z-index: 1; cursor: pointer; width: 60px; }
    .fpi-step-dot {
        width: 20px; height: 20px; border-radius: 50%; background: #457985; display: flex;
        align-items: center; justify-content: center; color: #c9dce0; font-size: 10px; font-weight: 700; transition: all .25s;
    }
    .fpi-step.active .fpi-step-dot { background: #fff; color: #2c5e6a; box-shadow: 0 0 0 4px rgba(255,255,255,.2); }
    .fpi-step.completed .fpi-step-dot { background: #2ecc71; color: #fff; }
    .fpi-step-label { font-size: 9px; color: #fff; margin-top: 6px; font-weight: 500; text-align: center; white-space: nowrap; opacity: .8; }
    .fpi-step.active .fpi-step-label { font-weight: 700; opacity: 1; text-shadow: 0 1px 2px rgba(0,0,0,.1); }

    .fpi-tabs {
        display: flex; flex-wrap: wrap; background: #fff; border: 1px solid var(--gray200); border-bottom: none;
        border-top-left-radius: 8px; border-top-right-radius: 8px; row-gap: 2px;
    }
    .fpi-tab-btn {
        flex: 1 1 auto; text-align: center; padding: 11px 12px; font-size: 12.5px; font-weight: 500; color: var(--gray500);
        background: transparent; border: none; border-bottom: 3px solid transparent; white-space: nowrap; cursor: pointer; transition: all .15s;
    }
    .fpi-tab-btn:hover { color: var(--primary); background: #f1fcfe; }
    .fpi-tab-btn.active { color: var(--primary); background: #dff1f5; border-bottom-color: var(--primary); font-weight: 600; }

    .fpi-card { background: #fff; border: 1px solid var(--gray200); border-top: none; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px; padding: 16px; margin-bottom: 16px; }
    .fpi-card-heading { font-size: 15px; font-weight: 600; color: var(--gray900); margin-bottom: 12px; padding-bottom: 6px; border-bottom: 1px solid var(--gray100); }
    .fpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px 16px; }
    .fpi-form-group { display: flex; flex-direction: column; gap: 4px; }
    .fpi-form-group.col-span-full { grid-column: 1 / -1; }
    .fpi-label { font-size: 12px; font-weight: 500; color: var(--gray700); display: flex; align-items: center; gap: 2px; }
    .fpi-req { color: var(--danger); }
    .fpi-input, .fpi-select {
        width: 100%; height: 34px; padding: 6px 10px; background: #fff; border: 1px solid var(--gray200);
        border-radius: 4px; font-size: 13px; font-weight: 500; color: var(--gray900); outline: none; transition: border-color .15s;
    }
    .fpi-input:focus, .fpi-select:focus { border-color: var(--primary); }
    .fpi-input:disabled, .fpi-select:disabled { background: #f7f9fa; color: #9ab0b8; cursor: not-allowed; }
    .fpi-sub-heading { font-size: 12.5px; font-weight: 600; color: var(--gray700); margin-bottom: 8px; }
    .fpi-file-upload {
        border: 1.5px dashed var(--gray200); background: #f8fafb; border-radius: 4px; padding: 12px;
        display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; text-align: center; min-height: 80px; transition: all .15s;
    }
    .fpi-file-upload:hover { background: #f1f5f7; border-color: var(--primary); }
    .fpi-file-upload input { display: none; }
    .fpi-file-upload.is-invalid { border-color: var(--danger); background: #fdf3f2; }
    .fpi-file-icon { font-size: 20px; color: var(--primary); margin-bottom: 4px; }
    .fpi-file-name { font-size: 10px; color: var(--primary); font-weight: 700; margin-top: 4px; word-break: break-all; }
    .fpi-actions { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; border-top: 1px solid var(--gray100); padding-top: 12px; }
    .fpi-step-panel { display: none; }
    .fpi-step-panel.active { display: block; }

    /* validation states */
    .fpi-input.is-invalid, .fpi-select.is-invalid { border-color: var(--danger); background: #fdf3f2; }
    .fpi-invalid-feedback { font-size: 10px; color: var(--danger); margin-top: 2px; }
    /* Highlighted Print / Preview button (post-submission) */
    .fpi-print-btn {
        background: #f0a500; color: #3d2c00; border: 1px solid #d98f00; font-weight: 700;
        box-shadow: 0 0 0 0 rgba(240, 165, 0, .55); animation: fpiPulse 1.7s ease-out infinite;
    }
    .fpi-print-btn:hover { background: #ffb717; filter: none; }
    @keyframes fpiPulse {
        0%   { box-shadow: 0 0 0 0 rgba(240, 165, 0, .55); }
        70%  { box-shadow: 0 0 0 9px rgba(240, 165, 0, 0); }
        100% { box-shadow: 0 0 0 0 rgba(240, 165, 0, 0); }
    }

    .fpi-tab-btn.has-error { color: var(--danger); }
    .fpi-tab-btn.has-error::after { content: ' !'; font-weight: 700; }
    .fpi-tab-btn.saved::before { content: '✓ '; color: var(--success); font-weight: 700; }
    .fpi-form-error-banner {
        background: #fdecea; border: 1px solid #f5c6c2; color: #842029; border-radius: 6px;
        padding: 10px 14px; font-size: 11.5px; margin-bottom: 14px;
    }

    /* UBO determination tool (embedded) */
    .ubo-card { background: #fff; border: 1px solid var(--gray200); border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: var(--shadow-sm); }
    .ubo-card-title { font-size: 15.5px; font-weight: 600; color: var(--gray900); margin-bottom: 12px; }
    .ubo-input, .ubo-select { height: 36px; padding: 6px 10px; border: 1px solid var(--gray200); border-radius: 4px; font-size: 13.5px; outline: none; background: #fff; font-family: inherit; }
    .ubo-btn { padding: 6px 14px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer; border: none; transition: background .15s; font-family: inherit; }
    .ubo-btn-primary { background: #0b5ed7; color: #fff; }
    .ubo-btn-primary:hover { background: #0a58ca; }
    .ubo-btn-secondary { background: #198754; color: #fff; }
    .ubo-btn-secondary:hover { background: #157347; }
    .ubo-btn-ghost { background: transparent; color: var(--gray700); border: 1px solid var(--gray200); }
    .ubo-btn-ghost:hover { background: var(--gray100); }
    .ubo-select.is-invalid, .ubo-input.is-invalid { border-color: var(--danger); background: #fdf3f2; }
</style>
@endpush

@section('content')
    <div class="fpi-form-error-banner" id="fpiErrorBanner" style="display:none">
        Please correct the highlighted fields in this section.
    </div>

    {{-- Application switcher: reopen any earlier application, or start a new one --}}
    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:14px">
        <span style="font-size:12.5px;color:var(--gray600);font-weight:600">Application:</span>
        <select id="appSelect" class="fpi-select" style="width:auto;min-width:300px;height:32px">
            <option value="">— New (unsaved) application —</option>
            @foreach ($applications as $a)
                <option value="{{ route('fpi.load', $a->applicant_id) }}" @selected($currentApplicantId == $a->applicant_id)>
                    FPI-{{ str_pad($a->applicant_id, 6, '0', STR_PAD_LEFT) }} · {{ $a->company_name ?: '(draft)' }} · {{ $a->application_status }}
                </option>
            @endforeach
        </select>
        <button type="button" class="btn btn-ghost btn-sm" style="padding:5px 12px;font-size:12.5px" onclick="var v=document.getElementById('appSelect').value; if(v) window.location.href=v;">Open</button>
        <button type="submit" form="fpiNewForm" class="btn btn-primary btn-sm" style="padding:5px 12px;font-size:12.5px">＋ New Application</button>
        {{-- Same action as the one in the footer bar; margin-left:auto pins it to the top right. --}}
        <button type="button" class="btn btn-outline btn-sm js-fpi-autofill" style="margin-left:auto;padding:5px 12px;font-size:12.5px">⚡ Auto-fill &amp; Save All</button>
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
                <div class="fpi-stepper-line"><div id="fpiProgressBar"></div></div>
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
                <div class="fpi-grid">
                    <div class="fpi-form-group">
                        <label class="fpi-label">Title <span class="fpi-req">*</span></label>
                        <select class="fpi-select" name="nameTitle">
                            <option value="" @selected($form['nameTitle'] === '')>Select</option>
                            @foreach (['M/S' => 'M/s', 'MR' => 'Mr', 'MRS' => 'Mrs', 'MS' => 'Ms'] as $v => $l)
                                <option value="{{ $v }}" @selected($form['nameTitle'] === $v)>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fpi-form-group" style="grid-column: span 2">
                        <label class="fpi-label">Entity Name <span class="fpi-req">*</span></label>
                        <input class="fpi-input" type="text" name="entityName" value="{{ $form['entityName'] }}">
                    </div>
                    <div class="fpi-form-group">
                        <label class="fpi-label">Ever known by another name? <span class="fpi-req">*</span></label>
                        <select class="fpi-select" name="knownByAnotherName" id="knownByAnotherName">
                            <option value="" @selected($form['knownByAnotherName'] === '')>Select</option>
                            <option value="NO" @selected($form['knownByAnotherName'] === 'NO')>No</option>
                            <option value="YES" @selected($form['knownByAnotherName'] === 'YES')>Yes</option>
                        </select>
                    </div>

                    <div class="fpi-form-group" id="otherNameTitleGroup" style="{{ $form['knownByAnotherName'] === 'YES' ? '' : 'display:none' }}">
                        <label class="fpi-label">Title</label>
                        <select class="fpi-select" name="otherTitle">
                            <option value="">Select</option>
                            @foreach (['M/S' => 'M/s', 'MR' => 'Mr', 'MRS' => 'Mrs'] as $v => $l)
                                <option value="{{ $v }}" @selected($form['otherTitle'] === $v)>{{ $l }}</option>
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
                            <option value="" @selected($form['countryOfIncorporation'] === '')>Select</option>
                            @foreach ($countries as $c)
                                <option value="{{ $c->country_id }}" @selected((string) $form['countryOfIncorporation'] === (string) $c->country_id)>{{ $c->label_en }}</option>
                            @endforeach
                        </select>
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
            </div>

            {{-- STEP 2: Contact & Address --}}
            <div class="fpi-step-panel" data-panel="contact">
                <div class="fpi-card-heading">Step 2: Contact & Address Details</div>
                <div class="fpi-sub-heading">Registered Address</div>
                <div class="fpi-grid" style="margin-bottom:16px">
                    <div class="fpi-form-group"><label class="fpi-label">Address Line 1 <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="regAddressLine1" value="{{ $form['regAddressLine1'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Address Line 2 <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="regAddressLine2" value="{{ $form['regAddressLine2'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Address Line 3 <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="regAddressLine3" value="{{ $form['regAddressLine3'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">City <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="regCity" value="{{ $form['regCity'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">State / Province <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="regState" value="{{ $form['regState'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Country <span class="fpi-req">*</span></label>
                        <select class="fpi-select" name="regCountry">
                            <option value="" @selected($form['regCountry'] === '')>Select</option>
                            @foreach ($countries as $c)
                                <option value="{{ $c->country_id }}" @selected((string) $form['regCountry'] === (string) $c->country_id)>{{ $c->label_en }}</option>
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
                    <div class="fpi-form-group"><label class="fpi-label">City <span class="fpi-req comm-req">*</span></label><input class="fpi-input comm-field" type="text" name="commCity" value="{{ $form['commCity'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">State / Province <span class="fpi-req comm-req">*</span></label><input class="fpi-input comm-field" type="text" name="commState" value="{{ $form['commState'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Country <span class="fpi-req comm-req">*</span></label>
                        <select class="fpi-select comm-field" name="commCountry">
                            <option value="" @selected($form['commCountry'] === '')>Select</option>
                            @foreach ($countries as $c)
                                <option value="{{ $c->country_id }}" @selected((string) $form['commCountry'] === (string) $c->country_id)>{{ $c->label_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fpi-form-group"><label class="fpi-label">ZIP / Postal Code <span class="fpi-req comm-req">*</span></label><input class="fpi-input comm-field" type="text" name="commZip" value="{{ $form['commZip'] }}"></div>
                </div>

                <div class="fpi-sub-heading" style="margin-top:16px">Contact Details</div>
                <div class="fpi-grid">
                    <div class="fpi-form-group"><label class="fpi-label">Telephone Number</label><input class="fpi-input" type="text" name="telNumber" value="{{ $form['telNumber'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Mobile Number <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="mobileNumber" value="{{ $form['mobileNumber'] }}"></div>
                    <div class="fpi-form-group" style="grid-column: span 2"><label class="fpi-label">Email Address <span class="fpi-req">*</span></label><input class="fpi-input" type="email" name="email" value="{{ $form['email'] }}"></div>
                </div>
            </div>

            {{-- STEP 3: UBO Determination Tool (embedded) --}}
            <div class="fpi-step-panel" data-panel="ubo_tool">
                <div class="fpi-card-heading">Step 3: UBO Determination Tool</div>

                <div class="ubo-card">
                    <div class="ubo-card-title">Step 1: What type of applicant is this?</div>
                    <div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap;margin-bottom:12px">
                        <select class="ubo-select" id="applicantType" style="width:280px">
                            <option value="">Select applicant type</option>
                            <option value="Partnership">Partnership</option>
                            <option value="Company">Company</option>
                            <option value="Trust">Trust</option>
                            <option value="Unincorporated Association / Body of Individuals">Unincorporated Association / BOI</option>
                        </select>
                        <button type="button" class="ubo-btn ubo-btn-primary" id="resetFlow">Start / Reset Flow</button>
                    </div>
                    <div style="font-size:11px;color:var(--gray500);font-style:italic;background:#f8f9fa;padding:8px 12px;border-radius:4px;border-left:3px solid var(--primary-mid)">
                        SMO should be used only if no natural person is identified through ownership threshold or control.
                    </div>
                </div>

                <div class="ubo-card">
                    <div class="ubo-card-title" style="display:flex;justify-content:space-between;align-items:center">
                        <span>Define Shareholding / Ownership Hierarchy</span>
                        <span style="font-size:10.5px;color:#0f766e;background:#ccfbf1;padding:2px 8px;border-radius:10px;font-weight:600">Threshold: 10% (SEBI FPI Regulation)</span>
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
                <div class="fpi-card-heading">Step 4: Beneficial Ownership Information</div>
                <div class="fpi-grid" style="margin-bottom:16px">
                    <div class="fpi-form-group" style="grid-column: span 2">
                        <label class="fpi-label">Does the entity have Ultimate Beneficial Owners (UBOs)? <span class="fpi-req">*</span></label>
                        <select class="fpi-select" name="hasUbos" id="hasUbos">
                            <option value="" @selected($form['hasUbos'] === '')>Select</option>
                            <option value="YES" @selected($form['hasUbos'] === 'YES')>Yes</option>
                            <option value="NO" @selected($form['hasUbos'] === 'NO')>No</option>
                        </select>
                    </div>
                </div>
                <div id="uboBlock" style="{{ $form['hasUbos'] === 'YES' ? '' : 'display:none' }}">
                    <div class="fpi-sub-heading">Ultimate Beneficial Owner 1</div>
                    <div class="fpi-grid">
                        <div class="fpi-form-group"><label class="fpi-label">Full Name <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="uboName" value="{{ $form['uboName'] }}"></div>
                        <div class="fpi-form-group"><label class="fpi-label">Date of Birth <span class="fpi-req">*</span></label><input class="fpi-input" type="date" name="uboDob" max="{{ date('Y-m-d') }}" value="{{ $form['uboDob'] }}"></div>
                        <div class="fpi-form-group"><label class="fpi-label">Nationality <span class="fpi-req">*</span></label>
                            <select class="fpi-select" name="uboNationality">
                                <option value="" @selected($form['uboNationality'] === '')>Select</option>
                                @foreach ($countries as $c)
                                    <option value="{{ $c->country_id }}" @selected((string) $form['uboNationality'] === (string) $c->country_id)>{{ $c->label_en }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="fpi-form-group"><label class="fpi-label">Passport / National ID <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="uboPassport" value="{{ $form['uboPassport'] }}"></div>
                        <div class="fpi-form-group"><label class="fpi-label">Ownership % <span class="fpi-req">*</span></label><input class="fpi-input" type="number" name="uboOwnership" min="0" max="100" step="0.01" value="{{ $form['uboOwnership'] }}"></div>
                        <div class="fpi-form-group" style="grid-column: span 3"><label class="fpi-label">Residential Address <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="uboAddress" value="{{ $form['uboAddress'] }}"></div>
                    </div>
                </div>
            </div>

            {{-- STEP 4: Financial & Tax --}}
            <div class="fpi-step-panel" data-panel="financial">
                <div class="fpi-card-heading">Step 5: Financial & Tax Information</div>
                <div class="fpi-grid">
                    <div class="fpi-form-group">
                        <label class="fpi-label">Gross Annual Income <span class="fpi-req">*</span></label>
                        <select class="fpi-select" name="incomeRange">
                            <option value="" @selected($form['incomeRange'] === '')>Select</option>
                            @foreach (['UNDER_50K' => 'Under $50,000', '50K_250K' => '$50,000 - $250,000', '250K_1M' => '$250,000 - $1,000,000', 'ABOVE_1M' => 'Above $1,000,000'] as $v => $l)
                                <option value="{{ $v }}" @selected($form['incomeRange'] === $v)>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fpi-form-group"><label class="fpi-label">Net Worth in USD <span class="fpi-req">*</span></label><input class="fpi-input" type="number" name="netWorth" value="{{ $form['netWorth'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Net Worth Date</label><input class="fpi-input" type="date" name="netWorthDate" value="{{ $form['netWorthDate'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Tax Residency Country <span class="fpi-req">*</span></label>
                        <select class="fpi-select" name="taxCountry">
                            <option value="" @selected($form['taxCountry'] === '')>Select</option>
                            @foreach ($countries as $c)
                                <option value="{{ $c->country_id }}" @selected((string) $form['taxCountry'] === (string) $c->country_id)>{{ $c->label_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fpi-form-group"><label class="fpi-label">Tax Identification Number (TIN) <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="tin" value="{{ $form['tin'] }}"></div>
                </div>
            </div>

            {{-- STEP 5: Category & Regulatory --}}
            <div class="fpi-step-panel" data-panel="category">
                <div class="fpi-card-heading">Step 6: Category & Regulatory Classification</div>
                <div class="fpi-grid">
                    <div class="fpi-form-group">
                        <label class="fpi-label">FPI Category <span class="fpi-req">*</span></label>
                        <select class="fpi-select" name="fpiCategory">
                            <option value="" @selected($form['fpiCategory'] === '')>Select</option>
                            <option value="CAT_I" @selected($form['fpiCategory'] === 'CAT_I')>Category I</option>
                            <option value="CAT_II" @selected($form['fpiCategory'] === 'CAT_II')>Category II</option>
                        </select>
                    </div>
                    <div class="fpi-form-group">
                        <label class="fpi-label">Regulatory Status <span class="fpi-req">*</span></label>
                        <select class="fpi-select" name="regulatoryStatus">
                            <option value="" @selected($form['regulatoryStatus'] === '')>Select</option>
                            <option value="REGULATED" @selected($form['regulatoryStatus'] === 'REGULATED')>Regulated</option>
                            <option value="UNREGULATED" @selected($form['regulatoryStatus'] === 'UNREGULATED')>Unregulated</option>
                        </select>
                    </div>
                    <div class="fpi-form-group" style="grid-column: span 2"><label class="fpi-label">Regulator Name</label><input class="fpi-input" type="text" name="regulatorName" value="{{ $form['regulatorName'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Registration / License Number</label><input class="fpi-input" type="text" name="licenseNumber" value="{{ $form['licenseNumber'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Regulator Jurisdiction</label>
                        <select class="fpi-select" name="regulatorJurisdiction">
                            <option value="" @selected($form['regulatorJurisdiction'] === '')>Select</option>
                            @foreach ($countries as $c)
                                <option value="{{ $c->country_id }}" @selected((string) $form['regulatorJurisdiction'] === (string) $c->country_id)>{{ $c->label_en }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- STEP 6: PAN, Bank & Depository --}}
            <div class="fpi-step-panel" data-panel="depository">
                <div class="fpi-card-heading">Step 7: PAN, Bank & Depository Details</div>
                <div class="fpi-grid" style="margin-bottom:16px">
                    <div class="fpi-form-group"><label class="fpi-label">Indian PAN <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="pan" value="{{ $form['pan'] }}"></div>
                </div>
                <div class="fpi-sub-heading">Bank Account Details</div>
                <div class="fpi-grid" style="margin-bottom:16px">
                    <div class="fpi-form-group"><label class="fpi-label">Bank Name <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="bankName" value="{{ $form['bankName'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Account Number <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="bankAccount" value="{{ $form['bankAccount'] }}"></div>
                    <div class="fpi-form-group">
                        <label class="fpi-label">Account Type <span class="fpi-req">*</span></label>
                        <select class="fpi-select" name="bankAccountType">
                            <option value="" @selected($form['bankAccountType'] === '')>Select</option>
                            @foreach (['NRE' => 'NRE', 'NRO' => 'NRO', 'ESCROW' => 'Escrow'] as $v => $l)
                                <option value="{{ $v }}" @selected($form['bankAccountType'] === $v)>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fpi-form-group"><label class="fpi-label">SWIFT / IFSC Code</label><input class="fpi-input" type="text" name="bankSwift" value="{{ $form['bankSwift'] }}"></div>
                </div>
                <div class="fpi-sub-heading">Custodian & Depository Participant Details</div>
                <div class="fpi-grid">
                    <div class="fpi-form-group"><label class="fpi-label">Custodian Name</label><input class="fpi-input" type="text" name="custodianName" value="{{ $form['custodianName'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">DP ID</label><input class="fpi-input" type="text" name="dpId" value="{{ $form['dpId'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Client ID</label><input class="fpi-input" type="text" name="clientId" value="{{ $form['clientId'] }}"></div>
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
                </div>
            </div>

            {{-- STEP 8: Declarations --}}
            <div class="fpi-step-panel" data-panel="declarations">
                <div class="fpi-card-heading">Step 9: Final Declarations & Document Upload</div>
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
                <div style="display:flex;gap:8px;align-items:flex-start;margin-bottom:16px">
                    <input type="checkbox" name="declarationAgreed" style="margin-top:2px" @checked($form['declarationAgreed'])>
                    <div style="font-size:11px;color:var(--gray700)">
                        <span class="fpi-req">*</span> I/We hereby declare that all details and documents provided in this registration form are true, correct, and complete to the best of my/our knowledge and belief. I/We undertake to inform the depository participant / custodian immediately of any changes.
                    </div>
                </div>
                <div class="fpi-grid">
                    <div class="fpi-form-group" style="grid-column: span 2"><label class="fpi-label">Authorized Signatory Name <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="signatureName" value="{{ $form['signatureName'] }}"></div>
                </div>
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
    (function () {
        const steps = @json($stepsJs);
        const faqs = @json($faqs);
        const savedSteps = new Set(@json($savedSections));
        let IS_SUBMITTED = @json($isSubmitted);
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

            progressBar.style.width = (current / (steps.length - 1)) * 100 + '%';
            prevBtn.style.display = current === 0 ? 'none' : '';
            nextBtn.style.display = current === steps.length - 1 ? 'none' : '';

            // Final-tab controls: Submit + Print/Preview only on the declarations tab.
            const onFinal = activeId === 'declarations';
            const declSaved = savedSteps.has('declarations');

            const submitBtn = document.getElementById('fpiSubmit');
            if (submitBtn) {
                submitBtn.style.display = (onFinal && !IS_SUBMITTED) ? '' : 'none';
                submitBtn.disabled = !declSaved;                 // enabled only after Save Section done
                submitBtn.title = declSaved ? '' : 'Save this section first to enable submission';
            }
            // Save Section / Auto-fill hidden after the application is submitted (read-only).
            if (IS_SUBMITTED) {
                document.getElementById('fpiSaveTab').style.display = 'none';
                document.querySelectorAll('.js-fpi-autofill').forEach(b => { b.style.display = 'none'; });
            }

            const printBtn = document.getElementById('fpiPrintPreview');
            if (printBtn) printBtn.style.display = (IS_SUBMITTED && onFinal) ? '' : 'none';
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
            if (idx >= 0) { current = idx; render(); }
        }

        document.querySelectorAll('.fpi-step, .fpi-tab-btn').forEach(el => {
            el.addEventListener('click', () => goTo(el.getAttribute('data-step')));
        });
        prevBtn.addEventListener('click', () => { if (current > 0) { current--; render(); } });
        nextBtn.addEventListener('click', () => {
            if (!validateStep(steps[current].id)) return;   // block advancing on invalid step
            if (current < steps.length - 1) { current++; render(); }
        });

        // FAQ collapse
        let faqOpen = true;
        document.getElementById('faqToggle').addEventListener('click', () => {
            faqOpen = !faqOpen;
            faqList.style.display = faqOpen ? 'flex' : 'none';
            document.getElementById('faqCaret').style.transform = faqOpen ? 'rotate(0deg)' : 'rotate(-90deg)';
        });

        // Known-by-another-name toggle
        const knownSel = document.getElementById('knownByAnotherName');
        function toggleOtherName() {
            const show = knownSel.value === 'YES';
            document.getElementById('otherNameTitleGroup').style.display = show ? '' : 'none';
            document.getElementById('otherNameGroup').style.display = show ? '' : 'none';
        }
        knownSel.addEventListener('change', toggleOtherName);

        // hasUbos toggle
        const hasUbos = document.getElementById('hasUbos');
        hasUbos.addEventListener('change', () => {
            document.getElementById('uboBlock').style.display = hasUbos.value === 'YES' ? '' : 'none';
        });

        // Same-as-registered address mirroring
        const sameAddress = document.getElementById('sameAddress');
        const regMap = {
            commAddressLine1: 'regAddressLine1', commAddressLine2: 'regAddressLine2', commAddressLine3: 'regAddressLine3',
            commCity: 'regCity', commState: 'regState', commCountry: 'regCountry', commZip: 'regZip',
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
            document.querySelectorAll('.comm-req').forEach(a => { a.style.display = on ? 'none' : ''; });
        }
        sameAddress.addEventListener('change', syncComm);
        Object.values(regMap).forEach(reg => {
            document.querySelector(`[name="${reg}"]`).addEventListener('input', () => { if (sameAddress.checked) syncComm(); });
        });

        // ───────────────── Client-side validation (mirrors server) ─────────────────
        const form = document.getElementById('fpiForm');
        const errorBanner = document.getElementById('fpiErrorBanner');

        const REQUIRED = {
            nameTitle: 'Title', entityName: 'Entity Name', knownByAnotherName: 'This field',
            placeOfIncorporation: 'Place of Incorporation', countryOfIncorporation: 'Country of Incorporation',
            hasUbos: 'This field', fpiCategory: 'FPI Category', regulatoryStatus: 'Regulatory Status',
            pan: 'Indian PAN', bankAccountType: 'Account Type', signatureName: 'Authorized Signatory Name',
            uploadedIncorpCert: 'Certificate of Incorporation', uploadedPanCopy: 'Copy of Indian PAN Card',
            // Contact tab — everything except telephone
            regAddressLine1: 'Address Line 1', regAddressLine2: 'Address Line 2', regAddressLine3: 'Address Line 3',
            regCity: 'City', regState: 'State', regCountry: 'Country', regZip: 'PIN / ZIP',
            commAddressLine1: 'Address Line 1', commAddressLine2: 'Address Line 2', commAddressLine3: 'Address Line 3',
            commCity: 'City', commState: 'State', commCountry: 'Country', commZip: 'PIN / ZIP',
            mobileNumber: 'Mobile Number', email: 'Email',
            // Financial tab — everything except Net Worth Date
            incomeRange: 'Gross Annual Income', netWorth: 'Net Worth in USD',
            taxCountry: 'Tax Residency Country', tin: 'Tax Identification Number (TIN)',
            // PAN, Bank & Depository tab
            bankName: 'Bank Name', bankAccount: 'Account Number',
            // Additional Info tab
            primaryContactName: 'Primary Contact Person', primaryContactDesignation: 'Designation',
        };
        const PATTERNS = {
            pan: { re: /^[A-Za-z]{5}[0-9]{4}[A-Za-z]$/, msg: 'PAN must be 5 letters, 4 digits, then 1 letter (e.g. AAACG1234F).' },
            email: { re: /^[^@\s]+@[^@\s]+\.[^@\s]+$/, msg: 'Enter a valid email address.' },
            lei: { re: /^[A-Za-z0-9]{20}$/, msg: 'LEI must be 20 alphanumeric characters.' },
        };
        const ISD_CODES = @json(array_values($isdCodes));

        function fieldEl(name) { return form.querySelector(`[name="${name}"]`); }
        function stepOf(el) { const p = el && el.closest('.fpi-step-panel'); return p ? p.getAttribute('data-panel') : null; }

        function clearError(el) {
            if (!el) return;
            el.classList.remove('is-invalid');
            const up = el.closest('.fpi-file-upload'); if (up) up.classList.remove('is-invalid');
            const grp = el.closest('.fpi-form-group') || el.parentElement;
            const fb = grp && grp.querySelector('.fpi-invalid-feedback');
            if (fb) fb.remove();
        }
        function setError(el, msg) {
            if (!el) return;
            el.classList.add('is-invalid');
            const up = el.closest('.fpi-file-upload'); if (up) up.classList.add('is-invalid'); // hidden input -> flag the tile
            const grp = el.closest('.fpi-form-group') || el.parentElement;
            if (grp && !grp.querySelector('.fpi-invalid-feedback')) {
                const fb = document.createElement('div');
                fb.className = 'fpi-invalid-feedback';
                fb.textContent = msg;
                grp.appendChild(fb);
            }
            // clear inline error as soon as the user edits the field
            el.addEventListener('input', () => clearError(el), { once: true });
            el.addEventListener('change', () => clearError(el), { once: true });
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
            const val = n => { const e = fieldEl(n); return e ? (e.value || '').trim() : ''; };

            names.forEach(n => {
                const el = fieldEl(n);
                if (!el || el.disabled) return;
                clearError(el);

                // required (a previously-uploaded file counts as satisfied)
                const alreadyUploaded = el.type === 'file' && el.dataset.uploaded === '1' && el.files.length === 0;
                if (REQUIRED[n] && !val(n) && !alreadyUploaded) { errs.push({ name: n, msg: `${REQUIRED[n]} is required.` }); return; }
                // pattern (only when a value is present)
                if (PATTERNS[n] && val(n) && !PATTERNS[n].re.test(val(n))) { errs.push({ name: n, msg: PATTERNS[n].msg }); return; }
            });

            // conditional: other entity name when "known by another name" = YES
            if (names.includes('otherEntityName') && knownSel.value === 'YES' && !val('otherEntityName')) {
                errs.push({ name: 'otherEntityName', msg: 'Other Entity Name is required.' });
            }
            // conditional: UBO block when hasUbos = YES
            if (hasUbos.value === 'YES') {
                [['uboName', 'Full Name'], ['uboDob', 'Date of Birth'], ['uboNationality', 'Nationality'],
                 ['uboPassport', 'Passport / National ID'], ['uboOwnership', 'Ownership %'], ['uboAddress', 'Residential Address']]
                .forEach(([n, label]) => {
                    if (names.includes(n) && !val(n)) errs.push({ name: n, msg: `${label} is required.` });
                });
            }
            // ownership % range
            if (names.includes('uboOwnership') && val('uboOwnership')) {
                const v = parseFloat(val('uboOwnership'));
                if (isNaN(v) || v < 0 || v > 100) errs.push({ name: 'uboOwnership', msg: 'Ownership % must be between 0 and 100.' });
            }
            // net worth >= 0
            if (names.includes('netWorth') && val('netWorth')) {
                const v = parseFloat(val('netWorth'));
                if (isNaN(v) || v < 0) errs.push({ name: 'netWorth', msg: 'Net worth must be a positive number.' });
            }
            // dates cannot be in the future
            const today = new Date(); today.setHours(0, 0, 0, 0);
            [['dateOfIncorporation', 'Date of Incorporation'], ['dateOfCommencementOfBusiness', 'Date of Commencement of Business'], ['uboDob', 'Date of Birth']]
            .forEach(([n, label]) => {
                if (names.includes(n) && val(n)) {
                    const d = new Date(val(n));
                    if (!isNaN(d) && d > today) errs.push({ name: n, msg: `${label} cannot be in the future.` });
                }
            });
            // mobile number must match one of our countries' dialing codes
            if (names.includes('mobileNumber') && val('mobileNumber')) {
                const digits = val('mobileNumber').replace(/\D/g, '');
                const codes = ISD_CODES.slice().sort((a, b) => b.length - a.length);
                const match = codes.find(c => digits.startsWith(c));
                if (!match) {
                    errs.push({ name: 'mobileNumber', msg: 'Mobile must start with a valid country code (' + ISD_CODES.map(c => '+' + c).join(', ') + ').' });
                } else {
                    const rest = digits.slice(match.length);
                    if (rest.length < 6 || rest.length > 12) errs.push({ name: 'mobileNumber', msg: 'Mobile number length is invalid for the selected country code.' });
                }
            }
            // declaration checkbox
            if (names.includes('declarationAgreed')) {
                const d = fieldEl('declarationAgreed');
                if (d && !d.checked) errs.push({ name: 'declarationAgreed', msg: 'You must agree to the declaration before saving.' });
            }
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
            return errs.length === 0;
        }

        // ── Tab-wise save: validate ONLY the current section client-side, then
        //    submit the form (with a hidden `section` field) so the server saves
        //    just that section to its DB table(s) and pre-fills on reload. ──
        const sectionField = document.getElementById('fpiSection');

        function saveTab() {
            const step = steps[current];
            if (!validateStep(step.id)) {
                errorBanner.style.display = '';
                const firstEl = document.querySelector(`.fpi-step-panel[data-panel="${step.id}"] .is-invalid`);
                if (firstEl && firstEl.focus) firstEl.focus();
                return;
            }
            if (step.id === 'ubo_tool') {
                if (window.__uboValid && !window.__uboValid()) {
                    const at = document.getElementById('applicantType');
                    at.classList.add('is-invalid'); at.focus();
                    Swal.fire({ icon: 'error', title: 'Applicant type required', text: 'Please select the applicant type before saving the UBO Determination.', confirmButtonColor: '#3e6f7c' });
                    return;
                }
                document.getElementById('uboStructureField').value = window.__uboSerialize();
            }
            errorBanner.style.display = 'none';
            sectionField.value = step.id;
            form.submit();   // programmatic submit bypasses the submit listener below
        }

        document.getElementById('fpiSaveTab').addEventListener('click', saveTab);
        // Enter key inside the form triggers a section save, not a raw submit.
        form.addEventListener('submit', (e) => { e.preventDefault(); saveTab(); });

        // ── Final submission: validate EVERY section, then submit ──
        function validateAll() {
            let all = [];
            steps.forEach(s => { if (s.id !== 'ubo_tool') all = all.concat(runRules(fieldsInStep(s.id))); });
            const badSteps = applyErrors(all);

            let uboBad = false;
            if (window.__uboValid && !window.__uboValid()) {
                uboBad = true;
                const at = document.getElementById('applicantType'); if (at) at.classList.add('is-invalid');
                document.querySelectorAll('.fpi-tab-btn').forEach(b => { if (b.getAttribute('data-step') === 'ubo_tool') b.classList.add('has-error'); });
            }

            if (all.length || uboBad) {
                let firstBad = steps.findIndex(s => badSteps.has(s.id));
                if (uboBad) { const ui = steps.findIndex(s => s.id === 'ubo_tool'); if (firstBad < 0 || ui < firstBad) firstBad = ui; }
                if (firstBad >= 0) { current = firstBad; render(); }
                errorBanner.style.display = '';
                const firstEl = all.length ? fieldEl(all[0].name) : document.getElementById('applicantType');
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
                Swal.fire({ icon: 'error', title: 'Please complete all sections', text: 'Some required fields are missing or invalid. The first section needing attention has been opened.', confirmButtonColor: '#3e6f7c' });
                return;
            }
            Swal.fire({
                icon: 'question', title: 'Submit application?',
                text: 'Please review all details. After submission the form becomes read-only.',
                showCancelButton: true, confirmButtonText: 'Yes, submit', confirmButtonColor: '#27ae60', cancelButtonText: 'Cancel',
            }).then(r => { if (r.isConfirmed) document.getElementById('fpiSubmitForm').submit(); });
        });

        // ── Sequential Auto-fill: fill tab → save tab → next … → submit → Print Preview ──
        function setField(name, value) {
            const el = form.querySelector(`[name="${name}"]`);
            if (!el) return;
            if (el.type === 'checkbox') el.checked = !!value; else el.value = value;
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));
        }
        const SAMPLE = {
            applicant: { nameTitle: 'M/S', entityName: 'GLOBAL ALPHAS FPI FUND', knownByAnotherName: 'NO', dateOfIncorporation: '2015-06-12', dateOfCommencementOfBusiness: '2015-07-01', placeOfIncorporation: 'NEW YORK', countryOfIncorporation: '2', lei: '549300INF823N7179062', leiExpiryDate: '2027-06-12' },
            contact: { regAddressLine1: '120 BROADWAY', regAddressLine2: 'SUITE 3000', regAddressLine3: 'FINANCIAL DISTRICT', regCity: 'NEW YORK', regState: 'NEW YORK', regCountry: '2', regZip: '10271', sameAddress: true, telNumber: '+1-212-555-0199', mobileNumber: '+1-917-555-0144', email: 'compliance@globalalphasfund.com' },
            ubo: { hasUbos: 'YES', uboName: 'JOHNATHAN DAVIS', uboDob: '1970-04-18', uboNationality: '2', uboPassport: 'USA839103982', uboOwnership: '35', uboAddress: '55 EAST 72ND ST, NEW YORK, NY 10021' },
            financial: { incomeRange: 'ABOVE_1M', netWorth: '45000000', netWorthDate: '2026-03-31', taxCountry: '2', tin: '13-3918239' },
            category: { fpiCategory: 'CAT_I', regulatoryStatus: 'REGULATED', regulatorName: 'SECURITIES AND EXCHANGE COMMISSION (SEC)', licenseNumber: 'SEC-FPI-9281A', regulatorJurisdiction: '2' },
            depository: { pan: 'AAACG1234F', bankName: 'CITIBANK N.A. MUMBAI', bankAccount: '98765432101', bankAccountType: 'NRO', bankSwift: 'CITIINBX', custodianName: 'DEUTSCHE BANK AG', dpId: 'IN300162', clientId: '10928374' },
            additional: { primaryContactName: 'SARAH JENKINS', primaryContactDesignation: 'COMPLIANCE OFFICER', investmentManagerName: 'ALPHA ASSET MANAGEMENT LLC', indiaPlaceOfBusiness: 'NONE' },
            declarations: { signatureName: 'SARAH JENKINS', declarationAgreed: true },
        };
        const sleep = ms => new Promise(r => setTimeout(r, ms));

        async function saveSectionAjax(id) {
            const fd = new FormData();
            fd.append('_token', CSRF);
            fd.append('section', id);
            fd.append('_autofill', '1');
            fieldsInStep(id).forEach(n => {
                const el = fieldEl(n);
                if (!el) return;
                fd.append(n, el.type === 'checkbox' ? (el.checked ? 'on' : '') : (el.value ?? ''));
            });
            if (id === 'ubo_tool' && window.__uboSerialize) fd.append('uboStructure', window.__uboSerialize());
            const res = await fetch(@json(route('fpi.store')), {
                method: 'POST', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, body: fd,
            });
            // store() replies with {ok, section, next, message} — keep the message
            // so each tab can be confirmed, and the errors so a failure is specific.
            const data = await res.json().catch(() => ({}));
            return { ok: res.ok, message: data.message || '', errors: data.errors || null };
        }

        async function autofillFlow() {
            const order = steps.map(s => s.id);
            for (const id of order) {
                current = order.indexOf(id); render();
                if (id === 'ubo_tool') { if (window.__uboFill) window.__uboFill(); }
                else { const d = SAMPLE[id]; if (d) Object.entries(d).forEach(([k, v]) => setField(k, v)); }
                Swal.update({ icon: 'info', title: `Filling "${steps[current].tab}"…`, html: 'Saving to database…' });
                Swal.showLoading();
                await sleep(550);                       // visible fill
                const saved = await saveSectionAjax(id);
                if (!saved.ok) {
                    const detail = saved.errors ? Object.values(saved.errors).flat()[0] : saved.message;
                    Swal.fire({ icon: 'error', title: 'Auto-fill stopped', text: detail || `Could not save the "${steps[current].tab}" section.`, confirmButtonColor: '#3e6f7c' });
                    return;
                }
                savedSteps.add(id); render();
                // Per-tab confirmation. It auto-advances rather than waiting for a
                // click, so the run stays hands-free.
                Swal.hideLoading();
                Swal.update({ icon: 'success', title: 'Saved', html: saved.message || `${steps[current].tab} saved successfully.` });
                await sleep(1200);                      // let the message be read
            }
            // Final submission
            Swal.update({ icon: 'info', title: 'Submitting application…', html: '' });
            Swal.showLoading();
            const sres = await fetch(@json(route('fpi.submit')), {
                method: 'POST', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: (() => { const f = new FormData(); f.append('_token', CSRF); return f; })(),
            });
            if (!sres.ok) { const j = await sres.json().catch(() => ({})); Swal.fire({ icon: 'error', title: 'Submission failed', text: j.message || 'Please review the sections.', confirmButtonColor: '#3e6f7c' }); return; }
            IS_SUBMITTED = true;
            current = order.indexOf('declarations'); render();
            // Reload so the server re-renders the uploaded documents (with preview links) + submitted state.
            Swal.fire({ icon: 'success', title: 'Application submitted', text: 'All tabs were filled, saved and submitted. You can now use Print / Preview.', confirmButtonColor: '#27ae60' })
                .then(() => window.location.reload());
        }

        document.querySelectorAll('.js-fpi-autofill').forEach(btn => btn.addEventListener('click', () => {
            Swal.fire({
                icon: 'question', title: 'Auto-fill & submit?',
                text: 'This fills each tab, saves it, then submits the application — step by step.',
                showCancelButton: true, confirmButtonText: 'Yes, run it', confirmButtonColor: '#3e6f7c', cancelButtonText: 'Cancel',
            }).then(r => {
                if (!r.isConfirmed) return;
                // Seed the icon here so Swal.update() can swap info -> success per tab.
                Swal.fire({ icon: 'info', title: 'Starting…', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                autofillFlow();
            });
        }));

        // ── Confirm a successful tab save (saveTab() posts the form and the
        //    server redirects back with the "<Section> saved successfully." flash) ──
        const flashStatus = @json(session('status'));
        if (flashStatus) {
            Swal.fire({
                icon: 'success', title: 'Success', text: flashStatus,
                confirmButtonColor: '#27ae60', timer: 2600, timerProgressBar: true,
            });
        }

        // ── Render server-side validation errors returned after a failed submit ──
        const serverErrors = @json($errors->messages());
        if (Object.keys(serverErrors).length) {
            const errs = Object.entries(serverErrors).map(([name, msgs]) => ({ name, msg: msgs[0] }));
            const badSteps = applyErrors(errs);
            errorBanner.style.display = '';
            const firstBad = steps.findIndex(s => badSteps.has(s.id));
            if (firstBad >= 0) current = firstBad;
        }

        syncComm();
        render();
    })();

    // ── Embedded UBO Determination Tool (Step 3) ──
    (function () {
        const THRESHOLD = 10;
        const STORED = @json($form['uboStructure'] ?? '');
        const APPLICANT_NAME = (document.querySelector('[name="entityName"]')?.value || 'Applicant').trim() || 'Applicant';

        // A single applicant node seeded from the Entity Name (Tab 1), no owners.
        const seededEntities = () => ([{ id: 'applicant', name: APPLICANT_NAME, type: (document.getElementById('applicantType')?.value || ''), owners: [] }]);

        let entities;
        if (STORED) {
            try { entities = (JSON.parse(STORED).entities) || null; } catch (e) { entities = null; }
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
            if (entities[0]) { entities[0].type = applicantTypeEl.value; renderHierarchy(); }
            applicantTypeEl.classList.remove('is-invalid');
        });

        // Serializer + validity flag used by the "Save Section" flow.
        window.__uboSerialize = () => {
            if (entities[0]) entities[0].type = applicantTypeEl.value;
            return JSON.stringify({ entities });
        };
        window.__uboValid = () => !!applicantTypeEl.value;

        // Seed the tool with a sample ownership tree (per-tab Auto-fill).
        window.__uboFill = () => {
            const appName = (document.querySelector('[name="entityName"]')?.value || 'GLOBAL ALPHAS FPI FUND').trim();
            const subId = newId('sub');
            entities = [
                { id: 'applicant', name: appName, type: 'Company', owners: [
                    { id: newId('own'), name: 'Alpha Holdings LLC', type: 'Entity', pct: 60, targetId: subId },
                    { id: newId('own'), name: 'Johnathan Davis', type: 'Individual', pct: 40 },
                ] },
                { id: subId, name: 'Alpha Holdings LLC', type: 'Company', owners: [
                    { id: newId('own'), name: 'Sarah Jenkins', type: 'Individual', pct: 80 },
                    { id: newId('own'), name: 'Mike Smith', type: 'Individual', pct: 20 },
                ] },
            ];
            applicantTypeEl.value = 'Company';
            renderHierarchy();
        };

        function updateEntityName(entityId, newName) {
            entities.forEach(ent => {
                if (ent.id === entityId) ent.name = newName;
                ent.owners.forEach(o => { if (o.type === 'Entity' && o.targetId === entityId) o.name = newName; });
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
                    id: subId, name: owner.name || 'New Entity', type: 'Company',
                    owners: [{ id: newId('sub_own'), name: 'Natural Person', type: 'Individual', pct: 100 }],
                });
            }
            renderHierarchy();
        }

        function addOwner(entityId) {
            const ent = entities.find(e => e.id === entityId);
            if (!ent) return;
            ent.owners.push({ id: newId('own'), name: '', type: '', pct: 0 });
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
                    name.className = 'ubo-input'; name.type = 'text'; name.value = owner.name; name.placeholder = 'Shareholder / partner name';
                    name.addEventListener('change', e => updateOwner(ent.id, owner.id, { name: e.target.value }));

                    const type = document.createElement('select');
                    type.className = 'ubo-select';
                    type.innerHTML = '<option value="">Select</option><option value="Individual">Natural Person</option><option value="Entity">Corporate Entity</option>';
                    type.value = owner.type || '';
                    type.addEventListener('change', e => updateOwner(ent.id, owner.id, { type: e.target.value }));

                    const pctWrap = document.createElement('div');
                    pctWrap.style.cssText = 'display:flex;align-items:center;gap:4px';
                    const pct = document.createElement('input');
                    pct.className = 'ubo-input'; pct.type = 'number'; pct.min = '0'; pct.max = '100'; pct.step = '0.01';
                    pct.value = owner.pct; pct.placeholder = '%'; pct.style.width = '60px';
                    pct.addEventListener('change', e => {
                        let v = parseFloat(e.target.value) || 0;
                        v = Math.min(100, Math.max(0, v));   // clamp 0..100
                        e.target.value = v;
                        updateOwner(ent.id, owner.id, { pct: v });
                    });
                    const pctLabel = document.createElement('span');
                    pctLabel.style.cssText = 'font-size:11px;color:var(--gray500)'; pctLabel.textContent = '%';
                    pctWrap.append(pct, pctLabel);

                    const remove = document.createElement('button');
                    remove.type = 'button';
                    remove.className = 'ubo-btn ubo-btn-ghost'; remove.textContent = 'Remove';
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
                addBtn.className = 'ubo-btn ubo-btn-ghost'; addBtn.textContent = '+ Add Shareholder/Partner';
                addBtn.style.cssText = 'padding:4px 10px;font-size:11px';
                addBtn.addEventListener('click', () => addOwner(ent.id));
                actions.appendChild(addBtn);
                card.appendChild(actions);

                hierarchyEl.appendChild(card);
            });
            renderDiagram();
            if (mermaidVisible) document.getElementById('mermaidCode').value = generateMermaid();
        }

        function evaluateUbos() {
            const results = [];
            const visited = new Set();
            const traverse = (entityId, multiplier, path) => {
                if (visited.has(entityId)) return; // cycle guard
                visited.add(entityId);
                const entity = entities.find(e => e.id === entityId);
                if (!entity) return;
                entity.owners.forEach(owner => {
                    const eff = (owner.pct / 100) * multiplier;
                    const currentPath = [...path, { entityName: entity.name, pct: owner.pct }];
                    if (owner.type === 'Individual') {
                        const existing = results.find(r => r.name.toLowerCase() === owner.name.toLowerCase());
                        if (existing) { existing.effectivePct += eff; existing.paths.push(currentPath); }
                        else results.push({ name: owner.name, effectivePct: eff, paths: [currentPath] });
                    } else if (owner.type === 'Entity') {
                        traverse(owner.targetId, eff, currentPath);
                    }
                });
                visited.delete(entityId);
            };
            traverse('applicant', 100, []);
            renderEvalTable(results);
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
            if (!applicant) { document.getElementById('diagramView').innerHTML = ''; return; }
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
            return String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        }

        function handleReset() {
            const type = applicantTypeEl.value;
            const name = (document.querySelector('[name="entityName"]')?.value || 'Applicant').trim() || 'Applicant';
            // Clean start: just the applicant node (from Entity Name), add owners yourself.
            entities = [{ id: 'applicant', name, type, owners: [] }];
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
