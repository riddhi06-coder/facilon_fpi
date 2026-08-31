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
        display: flex; background: #fff; border: 1px solid var(--gray200); border-bottom: none;
        border-top-left-radius: 8px; border-top-right-radius: 8px; overflow-x: auto; scrollbar-width: none;
    }
    .fpi-tabs::-webkit-scrollbar { display: none; }
    .fpi-tab-btn {
        padding: 10px 14px; font-size: 11px; font-weight: 500; color: var(--gray500); background: transparent;
        border: none; border-bottom: 3px solid transparent; white-space: nowrap; cursor: pointer; transition: all .15s;
    }
    .fpi-tab-btn:hover { color: var(--primary); background: #f1fcfe; }
    .fpi-tab-btn.active { color: var(--primary); background: #dff1f5; border-bottom-color: var(--primary); font-weight: 600; }

    .fpi-card { background: #fff; border: 1px solid var(--gray200); border-top: none; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px; padding: 16px; margin-bottom: 16px; }
    .fpi-card-heading { font-size: 13px; font-weight: 600; color: var(--gray900); margin-bottom: 12px; padding-bottom: 6px; border-bottom: 1px solid var(--gray100); }
    .fpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px 16px; }
    .fpi-form-group { display: flex; flex-direction: column; gap: 4px; }
    .fpi-form-group.col-span-full { grid-column: 1 / -1; }
    .fpi-label { font-size: 10.5px; font-weight: 500; color: var(--gray700); display: flex; align-items: center; gap: 2px; }
    .fpi-req { color: var(--danger); }
    .fpi-input, .fpi-select {
        width: 100%; height: 28px; padding: 4px 8px; background: #fff; border: 1px solid var(--gray200);
        border-radius: 4px; font-size: 11px; font-weight: 500; color: var(--gray900); outline: none; transition: border-color .15s;
    }
    .fpi-input:focus, .fpi-select:focus { border-color: var(--primary); }
    .fpi-input:disabled, .fpi-select:disabled { background: #f7f9fa; color: #9ab0b8; cursor: not-allowed; }
    .fpi-sub-heading { font-size: 11px; font-weight: 600; color: var(--gray700); margin-bottom: 8px; }
    .fpi-file-upload {
        border: 1.5px dashed var(--gray200); background: #f8fafb; border-radius: 4px; padding: 12px;
        display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; text-align: center; min-height: 80px; transition: all .15s;
    }
    .fpi-file-upload:hover { background: #f1f5f7; border-color: var(--primary); }
    .fpi-file-icon { font-size: 20px; color: var(--primary); margin-bottom: 4px; }
    .fpi-file-name { font-size: 10px; color: var(--primary); font-weight: 700; margin-top: 4px; }
    .fpi-actions { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; border-top: 1px solid var(--gray100); padding-top: 12px; }
    .fpi-step-panel { display: none; }
    .fpi-step-panel.active { display: block; }

    /* UBO determination tool (embedded) */
    .ubo-card { background: #fff; border: 1px solid var(--gray200); border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: var(--shadow-sm); }
    .ubo-card-title { font-size: 14px; font-weight: 600; color: var(--gray900); margin-bottom: 12px; }
    .ubo-input, .ubo-select { height: 32px; padding: 6px 10px; border: 1px solid var(--gray200); border-radius: 4px; font-size: 12px; outline: none; background: #fff; font-family: inherit; }
    .ubo-btn { padding: 6px 14px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer; border: none; transition: background .15s; font-family: inherit; }
    .ubo-btn-primary { background: #0b5ed7; color: #fff; }
    .ubo-btn-primary:hover { background: #0a58ca; }
    .ubo-btn-secondary { background: #198754; color: #fff; }
    .ubo-btn-secondary:hover { background: #157347; }
    .ubo-btn-ghost { background: transparent; color: var(--gray700); border: 1px solid var(--gray200); }
    .ubo-btn-ghost:hover { background: var(--gray100); }
</style>
@endpush

@section('content')
    @if (session('status'))
        <div class="flash-success">{{ session('status') }}</div>
    @endif

    <form class="fpi-container" method="POST" action="{{ route('fpi.store') }}" id="fpiForm">
        @csrf

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
                        <label class="fpi-label">Date of Incorporation</label>
                        <input class="fpi-input" type="date" name="dateOfIncorporation" value="{{ $form['dateOfIncorporation'] }}">
                    </div>
                    <div class="fpi-form-group">
                        <label class="fpi-label">Date of Commencement of Business</label>
                        <input class="fpi-input" type="date" name="dateOfCommencementOfBusiness" value="{{ $form['dateOfCommencementOfBusiness'] }}">
                    </div>
                    <div class="fpi-form-group">
                        <label class="fpi-label">Place of Incorporation <span class="fpi-req">*</span></label>
                        <input class="fpi-input" type="text" name="placeOfIncorporation" value="{{ $form['placeOfIncorporation'] }}">
                    </div>
                    <div class="fpi-form-group">
                        <label class="fpi-label">Country of Incorporation <span class="fpi-req">*</span></label>
                        <input class="fpi-input" type="text" name="countryOfIncorporation" value="{{ $form['countryOfIncorporation'] }}">
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
                    <div class="fpi-form-group"><label class="fpi-label">Address Line 1</label><input class="fpi-input" type="text" name="regAddressLine1" value="{{ $form['regAddressLine1'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Address Line 2</label><input class="fpi-input" type="text" name="regAddressLine2" value="{{ $form['regAddressLine2'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Address Line 3</label><input class="fpi-input" type="text" name="regAddressLine3" value="{{ $form['regAddressLine3'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">City</label><input class="fpi-input" type="text" name="regCity" value="{{ $form['regCity'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">State / Province</label><input class="fpi-input" type="text" name="regState" value="{{ $form['regState'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Country</label><input class="fpi-input" type="text" name="regCountry" value="{{ $form['regCountry'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">ZIP / Postal Code</label><input class="fpi-input" type="text" name="regZip" value="{{ $form['regZip'] }}"></div>
                </div>

                <div class="fpi-sub-heading" style="display:flex;align-items:center;gap:8px">
                    <span>Correspondence Address</span>
                    <label style="display:flex;align-items:center;gap:4px;font-weight:500;font-size:10.5px;color:var(--gray500)">
                        <input type="checkbox" name="sameAddress" id="sameAddress" @checked($form['sameAddress'])> Same as Registered
                    </label>
                </div>
                <div class="fpi-grid" id="commAddressGrid">
                    <div class="fpi-form-group"><label class="fpi-label">Address Line 1</label><input class="fpi-input comm-field" type="text" name="commAddressLine1" value="{{ $form['commAddressLine1'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Address Line 2</label><input class="fpi-input comm-field" type="text" name="commAddressLine2" value="{{ $form['commAddressLine2'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Address Line 3</label><input class="fpi-input comm-field" type="text" name="commAddressLine3" value="{{ $form['commAddressLine3'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">City</label><input class="fpi-input comm-field" type="text" name="commCity" value="{{ $form['commCity'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">State / Province</label><input class="fpi-input comm-field" type="text" name="commState" value="{{ $form['commState'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Country</label><input class="fpi-input comm-field" type="text" name="commCountry" value="{{ $form['commCountry'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">ZIP / Postal Code</label><input class="fpi-input comm-field" type="text" name="commZip" value="{{ $form['commZip'] }}"></div>
                </div>

                <div class="fpi-sub-heading" style="margin-top:16px">Contact Details</div>
                <div class="fpi-grid">
                    <div class="fpi-form-group"><label class="fpi-label">Telephone Number</label><input class="fpi-input" type="text" name="telNumber" value="{{ $form['telNumber'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Mobile Number</label><input class="fpi-input" type="text" name="mobileNumber" value="{{ $form['mobileNumber'] }}"></div>
                    <div class="fpi-form-group" style="grid-column: span 2"><label class="fpi-label">Email Address</label><input class="fpi-input" type="email" name="email" value="{{ $form['email'] }}"></div>
                </div>
            </div>

            {{-- STEP 3: UBO Determination Tool (embedded) --}}
            <div class="fpi-step-panel" data-panel="ubo_tool">
                <div class="fpi-card-heading">Step 3: UBO Determination Tool</div>

                <div class="ubo-card">
                    <div class="ubo-card-title">Step 1: What type of applicant is this?</div>
                    <div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap;margin-bottom:12px">
                        <select class="ubo-select" id="applicantType" style="width:280px">
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
                        <label class="fpi-label">Does the entity have Ultimate Beneficial Owners (UBOs)?</label>
                        <select class="fpi-select" name="hasUbos" id="hasUbos">
                            <option value="YES" @selected($form['hasUbos'] === 'YES')>Yes</option>
                            <option value="NO" @selected($form['hasUbos'] === 'NO')>No</option>
                        </select>
                    </div>
                </div>
                <div id="uboBlock" style="{{ $form['hasUbos'] === 'YES' ? '' : 'display:none' }}">
                    <div class="fpi-sub-heading">Ultimate Beneficial Owner 1</div>
                    <div class="fpi-grid">
                        <div class="fpi-form-group"><label class="fpi-label">Full Name <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="uboName" value="{{ $form['uboName'] }}"></div>
                        <div class="fpi-form-group"><label class="fpi-label">Date of Birth <span class="fpi-req">*</span></label><input class="fpi-input" type="date" name="uboDob" value="{{ $form['uboDob'] }}"></div>
                        <div class="fpi-form-group"><label class="fpi-label">Nationality <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="uboNationality" value="{{ $form['uboNationality'] }}"></div>
                        <div class="fpi-form-group"><label class="fpi-label">Passport / National ID <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="uboPassport" value="{{ $form['uboPassport'] }}"></div>
                        <div class="fpi-form-group"><label class="fpi-label">Ownership % <span class="fpi-req">*</span></label><input class="fpi-input" type="number" name="uboOwnership" value="{{ $form['uboOwnership'] }}"></div>
                        <div class="fpi-form-group" style="grid-column: span 3"><label class="fpi-label">Residential Address <span class="fpi-req">*</span></label><input class="fpi-input" type="text" name="uboAddress" value="{{ $form['uboAddress'] }}"></div>
                    </div>
                </div>
            </div>

            {{-- STEP 4: Financial & Tax --}}
            <div class="fpi-step-panel" data-panel="financial">
                <div class="fpi-card-heading">Step 5: Financial & Tax Information</div>
                <div class="fpi-grid">
                    <div class="fpi-form-group">
                        <label class="fpi-label">Gross Annual Income</label>
                        <select class="fpi-select" name="incomeRange">
                            @foreach (['UNDER_50K' => 'Under $50,000', '50K_250K' => '$50,000 - $250,000', '250K_1M' => '$250,000 - $1,000,000', 'ABOVE_1M' => 'Above $1,000,000'] as $v => $l)
                                <option value="{{ $v }}" @selected($form['incomeRange'] === $v)>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fpi-form-group"><label class="fpi-label">Net Worth in USD</label><input class="fpi-input" type="number" name="netWorth" value="{{ $form['netWorth'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Net Worth Date</label><input class="fpi-input" type="date" name="netWorthDate" value="{{ $form['netWorthDate'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Tax Residency Country</label><input class="fpi-input" type="text" name="taxCountry" value="{{ $form['taxCountry'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Tax Identification Number (TIN)</label><input class="fpi-input" type="text" name="tin" value="{{ $form['tin'] }}"></div>
                </div>
            </div>

            {{-- STEP 5: Category & Regulatory --}}
            <div class="fpi-step-panel" data-panel="category">
                <div class="fpi-card-heading">Step 6: Category & Regulatory Classification</div>
                <div class="fpi-grid">
                    <div class="fpi-form-group">
                        <label class="fpi-label">FPI Category</label>
                        <select class="fpi-select" name="fpiCategory">
                            <option value="CAT_I" @selected($form['fpiCategory'] === 'CAT_I')>Category I</option>
                            <option value="CAT_II" @selected($form['fpiCategory'] === 'CAT_II')>Category II</option>
                        </select>
                    </div>
                    <div class="fpi-form-group">
                        <label class="fpi-label">Regulatory Status</label>
                        <select class="fpi-select" name="regulatoryStatus">
                            <option value="REGULATED" @selected($form['regulatoryStatus'] === 'REGULATED')>Regulated</option>
                            <option value="UNREGULATED" @selected($form['regulatoryStatus'] === 'UNREGULATED')>Unregulated</option>
                        </select>
                    </div>
                    <div class="fpi-form-group" style="grid-column: span 2"><label class="fpi-label">Regulator Name</label><input class="fpi-input" type="text" name="regulatorName" value="{{ $form['regulatorName'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Registration / License Number</label><input class="fpi-input" type="text" name="licenseNumber" value="{{ $form['licenseNumber'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Regulator Jurisdiction</label><input class="fpi-input" type="text" name="regulatorJurisdiction" value="{{ $form['regulatorJurisdiction'] }}"></div>
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
                    <div class="fpi-form-group"><label class="fpi-label">Bank Name</label><input class="fpi-input" type="text" name="bankName" value="{{ $form['bankName'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Account Number</label><input class="fpi-input" type="text" name="bankAccount" value="{{ $form['bankAccount'] }}"></div>
                    <div class="fpi-form-group">
                        <label class="fpi-label">Account Type</label>
                        <select class="fpi-select" name="bankAccountType">
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
                    <div class="fpi-form-group"><label class="fpi-label">Primary Contact Person</label><input class="fpi-input" type="text" name="primaryContactName" value="{{ $form['primaryContactName'] }}"></div>
                    <div class="fpi-form-group"><label class="fpi-label">Designation</label><input class="fpi-input" type="text" name="primaryContactDesignation" value="{{ $form['primaryContactDesignation'] }}"></div>
                    <div class="fpi-form-group" style="grid-column: span 2"><label class="fpi-label">Investment Manager Name</label><input class="fpi-input" type="text" name="investmentManagerName" value="{{ $form['investmentManagerName'] }}"></div>
                    <div class="fpi-form-group" style="grid-column: span 2"><label class="fpi-label">Place of Business in India (if any)</label><input class="fpi-input" type="text" name="indiaPlaceOfBusiness" value="{{ $form['indiaPlaceOfBusiness'] }}"></div>
                </div>
            </div>

            {{-- STEP 8: Declarations --}}
            <div class="fpi-step-panel" data-panel="declarations">
                <div class="fpi-card-heading">Step 9: Final Declarations & Document Upload</div>
                <div class="fpi-sub-heading">Required Document Proofs</div>
                <div class="fpi-grid" style="margin-bottom:16px">
                    <div class="fpi-form-group">
                        <label class="fpi-label">Certificate of Incorporation <span class="fpi-req">*</span></label>
                        <div class="fpi-file-upload"><span class="fpi-file-icon">📁</span><span class="fpi-file-name">{{ $form['uploadedIncorpCert'] }}</span></div>
                    </div>
                    <div class="fpi-form-group">
                        <label class="fpi-label">Proof of LEI Registration</label>
                        <div class="fpi-file-upload"><span class="fpi-file-icon">📁</span><span class="fpi-file-name">{{ $form['uploadedLeiProof'] }}</span></div>
                    </div>
                    <div class="fpi-form-group">
                        <label class="fpi-label">Copy of Indian PAN Card <span class="fpi-req">*</span></label>
                        <div class="fpi-file-upload"><span class="fpi-file-icon">📁</span><span class="fpi-file-name">{{ $form['uploadedPanCopy'] }}</span></div>
                    </div>
                    <div class="fpi-form-group">
                        <label class="fpi-label">UBO List & Declaration</label>
                        <div class="fpi-file-upload"><span class="fpi-file-icon">📁</span><span class="fpi-file-name">{{ $form['uploadedUboDecl'] }}</span></div>
                    </div>
                </div>
                <div style="display:flex;gap:8px;align-items:flex-start;margin-bottom:16px">
                    <input type="checkbox" name="declarationAgreed" style="margin-top:2px" @checked($form['declarationAgreed'])>
                    <div style="font-size:11px;color:var(--gray700)">
                        I/We hereby declare that all details and documents provided in this registration form are true, correct, and complete to the best of my/our knowledge and belief. I/We undertake to inform the depository participant / custodian immediately of any changes.
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
                <div>
                    <button type="button" class="btn btn-ghost btn-sm" id="fpiPrev" style="padding:4px 10px;font-size:11px;display:none">Previous</button>
                </div>
                <div style="display:flex;gap:6px">
                    <button type="button" class="btn btn-ghost btn-sm" onclick="window.print()" style="padding:4px 10px;font-size:11px">Print Preview</button>
                    <button type="submit" class="btn btn-primary btn-sm" style="padding:4px 12px;font-size:11px">Save</button>
                    <button type="button" class="btn btn-primary btn-sm" id="fpiNext" style="padding:4px 12px;font-size:11px">Next</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    (function () {
        const steps = @json(array_map(fn ($s) => ['id' => $s['id'], 'title' => $s['title']], $steps));
        const faqs = @json($faqs);
        let current = 0;

        const progressBar = document.getElementById('fpiProgressBar');
        const faqTitle = document.getElementById('faqTitle');
        const faqList = document.getElementById('faqList');
        const prevBtn = document.getElementById('fpiPrev');
        const nextBtn = document.getElementById('fpiNext');

        function render() {
            const activeId = steps[current].id;

            document.querySelectorAll('.fpi-step').forEach((el, i) => {
                el.classList.toggle('active', i === current);
                el.classList.toggle('completed', i < current);
                const dot = el.querySelector('.fpi-step-dot');
                dot.textContent = i < current ? '✓' : el.getAttribute('data-index') * 1 + 1;
            });
            document.querySelectorAll('.fpi-tab-btn').forEach(el => {
                el.classList.toggle('active', el.getAttribute('data-step') === activeId);
            });
            document.querySelectorAll('.fpi-step-panel').forEach(el => {
                el.classList.toggle('active', el.getAttribute('data-panel') === activeId);
            });

            progressBar.style.width = (current / (steps.length - 1)) * 100 + '%';
            prevBtn.style.display = current === 0 ? 'none' : '';
            nextBtn.style.display = current === steps.length - 1 ? 'none' : '';

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
        nextBtn.addEventListener('click', () => { if (current < steps.length - 1) { current++; render(); } });

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
        }
        sameAddress.addEventListener('change', syncComm);
        Object.values(regMap).forEach(reg => {
            document.querySelector(`[name="${reg}"]`).addEventListener('input', () => { if (sameAddress.checked) syncComm(); });
        });

        syncComm();
        render();
    })();

    // ── Embedded UBO Determination Tool (Step 3) ──
    (function () {
        const DEFAULT_ENTITIES = [
            {
                id: 'applicant', name: 'GLOBAL ALPHAS FPI FUND', type: 'Partnership',
                owners: [
                    { id: 'own1', name: 'Alpha Holdings LLC', type: 'Entity', pct: 60, targetId: 'alpha_holdings' },
                    { id: 'own2', name: 'Johnathan Davis', type: 'Individual', pct: 40 },
                ],
            },
            {
                id: 'alpha_holdings', name: 'Alpha Holdings LLC', type: 'Company',
                owners: [
                    { id: 'own3', name: 'Sarah Jenkins', type: 'Individual', pct: 80 },
                    { id: 'own4', name: 'Mike Smith', type: 'Individual', pct: 20 },
                ],
            },
        ];
        const THRESHOLD = 10;

        let entities = structuredClone(DEFAULT_ENTITIES);
        let uid = 0;
        const newId = (p) => p + '_' + (Date.now() + (uid++));

        const hierarchyEl = document.getElementById('hierarchy');
        const evalResultEl = document.getElementById('evalResult');
        const applicantTypeEl = document.getElementById('applicantType');

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
            ent.owners.push({ id: newId('own'), name: 'New Shareholder', type: 'Individual', pct: 10 });
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
                    name.className = 'ubo-input'; name.type = 'text'; name.value = owner.name; name.placeholder = 'Owner Name';
                    name.addEventListener('change', e => updateOwner(ent.id, owner.id, { name: e.target.value }));

                    const type = document.createElement('select');
                    type.className = 'ubo-select';
                    type.innerHTML = '<option value="Individual">Natural Person</option><option value="Entity">Corporate Entity</option>';
                    type.value = owner.type;
                    type.addEventListener('change', e => updateOwner(ent.id, owner.id, { type: e.target.value }));

                    const pctWrap = document.createElement('div');
                    pctWrap.style.cssText = 'display:flex;align-items:center;gap:4px';
                    const pct = document.createElement('input');
                    pct.className = 'ubo-input'; pct.type = 'number'; pct.value = owner.pct; pct.placeholder = '%'; pct.style.width = '60px';
                    pct.addEventListener('change', e => updateOwner(ent.id, owner.id, { pct: parseFloat(e.target.value) || 0 }));
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
            const subId = newId('sub_entity');
            entities = [
                {
                    id: 'applicant', name: 'GLOBAL ALPHAS FPI FUND', type,
                    owners: [
                        { id: newId('own'), name: 'Owner A (Entity)', type: 'Entity', pct: 50, targetId: subId },
                        { id: newId('own'), name: 'Owner B (Individual)', type: 'Individual', pct: 50 },
                    ],
                },
                { id: subId, name: 'Owner A (Entity)', type: 'Company', owners: [{ id: newId('own'), name: 'Sub-Owner 1', type: 'Individual', pct: 100 }] },
            ];
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
