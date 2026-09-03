<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FPI Application — {{ $corp->company_name ?? 'Preview' }}</title>
<style>
    :root {
        --ink: #1a2633; --muted: #5c6b78; --line: #dde2e6; --soft: #f4f7f9;
        --primary: #2f5560; --primary2: #3e6f7c; --ok: #0f5132; --okbg: #d1e7dd;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: var(--ink); background: #e9edf0; font-size: 12.5px; line-height: 1.45; }

    /* Screen toolbar (hidden when printing) */
    .toolbar { position: sticky; top: 0; z-index: 5; display: flex; justify-content: center; gap: 10px; padding: 12px; background: #ffffffcc; backdrop-filter: blur(4px); border-bottom: 1px solid var(--line); }
    .btn { border: none; border-radius: 6px; padding: 8px 18px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: inherit; }
    .btn-print { background: var(--primary2); color: #fff; }
    .btn-close { background: #e6ebee; color: var(--ink); }

    .sheet { width: 210mm; min-height: 297mm; margin: 18px auto; background: #fff; padding: 22mm 18mm; box-shadow: 0 2px 16px rgba(0,0,0,.12); }

    /* Letterhead */
    .lh { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid var(--primary); padding-bottom: 14px; margin-bottom: 6px; }
    .lh-brand { display: flex; align-items: center; gap: 12px; }
    .lh-logo { width: 46px; height: 46px; border-radius: 9px; background: linear-gradient(135deg, var(--primary2), var(--primary)); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: 800; letter-spacing: -1px; }
    .lh-title { font-size: 19px; font-weight: 800; color: var(--primary); letter-spacing: -.3px; }
    .lh-sub { font-size: 11px; color: var(--muted); margin-top: 2px; }
    .lh-meta { text-align: right; font-size: 10.5px; color: var(--muted); }
    .status-pill { display: inline-block; background: var(--okbg); color: var(--ok); font-weight: 700; font-size: 10.5px; padding: 3px 10px; border-radius: 20px; letter-spacing: .3px; }

    .doc-title { text-align: center; font-size: 13.5px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: var(--primary); margin: 14px 0 18px; }

    /* Sections */
    .section { margin-bottom: 16px; break-inside: avoid; }
    .section-head { background: var(--primary); color: #fff; font-size: 11.5px; font-weight: 700; letter-spacing: .5px; text-transform: uppercase; padding: 6px 12px; border-radius: 5px 5px 0 0; }
    .section-body { border: 1px solid var(--line); border-top: none; border-radius: 0 0 5px 5px; padding: 12px 14px; }

    .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 26px; }
    .grid-3 { grid-template-columns: 1fr 1fr 1fr; }
    .f { display: flex; flex-direction: column; gap: 1px; padding: 3px 0; border-bottom: 1px dotted #e7ecef; }
    .f-label { font-size: 9.5px; color: var(--muted); text-transform: uppercase; letter-spacing: .4px; font-weight: 600; }
    .f-value { font-size: 12.5px; color: var(--ink); font-weight: 500; word-break: break-word; }
    .full { grid-column: 1 / -1; }

    table.tbl { width: 100%; border-collapse: collapse; font-size: 11.5px; margin-top: 4px; }
    table.tbl th { background: var(--soft); text-align: left; padding: 6px 8px; font-size: 10px; text-transform: uppercase; letter-spacing: .3px; color: var(--muted); border: 1px solid var(--line); }
    table.tbl td { padding: 6px 8px; border: 1px solid var(--line); }

    .subhead { font-size: 11px; font-weight: 700; color: var(--primary2); margin: 10px 0 4px; }
    .empty { font-size: 11px; color: var(--muted); font-style: italic; }
    .declaration-box { border: 1px solid var(--line); background: var(--soft); border-radius: 5px; padding: 12px 14px; font-size: 11.5px; }
    .sign-row { display: flex; justify-content: space-between; margin-top: 26px; }
    .sign-cell { width: 45%; }
    .sign-line { border-top: 1px solid var(--ink); margin-top: 34px; padding-top: 4px; font-size: 10.5px; color: var(--muted); }
    .footer { margin-top: 18px; border-top: 1px solid var(--line); padding-top: 8px; font-size: 9.5px; color: var(--muted); display: flex; justify-content: space-between; }

    @media print {
        body { background: #fff; font-size: 11.5px; }
        .toolbar { display: none; }
        .sheet { width: auto; margin: 0; padding: 0; box-shadow: none; min-height: auto; }
        @page { size: A4; margin: 14mm; }
        .section { break-inside: avoid; }
    }
</style>
</head>
<body>
@php
    $val = fn ($v) => ($v === null || $v === '') ? '—' : $v;
    $country = fn ($id) => $id && isset($countries[$id]) ? $countries[$id] : '—';
    $yn = fn ($b) => $b ? 'Yes' : 'No';
    $tree = ($app->ubo_structure_json ?? null) ? json_decode($app->ubo_structure_json, true) : null;
@endphp

<div class="toolbar">
    <button class="btn btn-print" onclick="window.print()">🖨 Print / Save as PDF</button>
    <button class="btn btn-close" onclick="window.close()">Close</button>
</div>

<div class="sheet">
    {{-- Letterhead --}}
    <div class="lh">
        <div class="lh-brand">
            <div class="lh-logo">F</div>
            <div>
                <div class="lh-title">Facilon — Investor Console</div>
                <div class="lh-sub">Foreign Portfolio Investor (FPI) — Common Application Form</div>
            </div>
        </div>
        <div class="lh-meta">
            <div><span class="status-pill">{{ $app->application_status }}</span></div>
            <div style="margin-top:6px">Application Ref: <strong>FPI-{{ str_pad($app->applicant_id, 6, '0', STR_PAD_LEFT) }}</strong></div>
            <div>Generated: {{ $generatedAt }}</div>
        </div>
    </div>
    <div class="doc-title">Application Summary</div>

    {{-- 1. Applicant Profile --}}
    <div class="section">
        <div class="section-head">1 · Applicant Profile</div>
        <div class="section-body">
            <div class="grid grid-3">
                <div class="f"><span class="f-label">Title</span><span class="f-value">{{ $val($titleMap[$corp->name_title_code ?? ''] ?? null) }}</span></div>
                <div class="f" style="grid-column: span 2"><span class="f-label">Entity Name</span><span class="f-value">{{ $val($corp->company_name ?? null) }}</span></div>
                <div class="f"><span class="f-label">Entity Type</span><span class="f-value">{{ $val($app->entity_type) }}</span></div>
                <div class="f"><span class="f-label">Date of Incorporation</span><span class="f-value">{{ $val($corp->date_of_incorporation ?? null) }}</span></div>
                <div class="f"><span class="f-label">Commencement of Business</span><span class="f-value">{{ $val($corp->date_commence_business ?? null) }}</span></div>
                <div class="f"><span class="f-label">Place of Incorporation</span><span class="f-value">{{ $val($corp->place_of_incorporation ?? null) }}</span></div>
                <div class="f"><span class="f-label">Country of Incorporation</span><span class="f-value">{{ $country($corp->incorporation_country_id ?? null) }}</span></div>
                <div class="f"><span class="f-label">LEI</span><span class="f-value">{{ $val($corp->lei_number ?? null) }}</span></div>
                <div class="f"><span class="f-label">LEI Expiry</span><span class="f-value">{{ $val($corp->lei_expiry_date ?? null) }}</span></div>
                <div class="f"><span class="f-label">India Place of Business</span><span class="f-value">{{ $val($corp->india_place_of_business ?? null) }}</span></div>
            </div>
            @if ($aliases->count())
                <div class="subhead">Other / Former Names</div>
                <table class="tbl"><thead><tr><th>Title</th><th>Name</th></tr></thead><tbody>
                    @foreach ($aliases as $a)
                        <tr><td>{{ $val($titleMap[$a->alias_title_code ?? ''] ?? null) }}</td><td>{{ $val($a->alias_last_name_or_company) }}</td></tr>
                    @endforeach
                </tbody></table>
            @endif
        </div>
    </div>

    {{-- 2. Contact & Address --}}
    <div class="section">
        <div class="section-head">2 · Contact &amp; Address</div>
        <div class="section-body">
            @if ($addresses->count())
                <table class="tbl"><thead><tr><th>Type</th><th>Address</th><th>City</th><th>State</th><th>PIN/ZIP</th><th>Country</th><th>Comm.</th></tr></thead><tbody>
                    @foreach ($addresses as $ad)
                        <tr>
                            <td>{{ str_replace('_', ' / ', $ad->address_type) }}</td>
                            <td>{{ trim(collect([$ad->flat_room_block, $ad->premises_building, $ad->road_street_lane, $ad->area_locality_taluka])->filter()->implode(', ')) ?: '—' }}</td>
                            <td>{{ $val($ad->town_city_district) }}</td>
                            <td>{{ $val($ad->state_union_territory) }}</td>
                            <td>{{ $val($ad->pin_zip_code) }}</td>
                            <td>{{ $country($ad->country_id) }}</td>
                            <td>{{ $ad->is_communication_dest ? 'Yes' : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody></table>
            @else <div class="empty">No address on record.</div> @endif

            @if ($contacts->count())
                <div class="subhead">Contact Details</div>
                <table class="tbl"><thead><tr><th>Type</th><th>Officer</th><th>Telephone</th><th>Mobile</th><th>Email</th></tr></thead><tbody>
                    @foreach ($contacts as $c)
                        <tr><td>{{ $c->contact_type }}</td><td>{{ $val($c->officer_name) }}</td><td>{{ $val($c->telephone_number) }}</td><td>{{ $val($c->mobile_number) }}</td><td>{{ $val($c->email_id) }}</td></tr>
                    @endforeach
                </tbody></table>
            @endif
        </div>
    </div>

    {{-- 3. UBO Determination (ownership tree) --}}
    <div class="section">
        <div class="section-head">3 · UBO Determination — Ownership Structure</div>
        <div class="section-body">
            @if ($tree && !empty($tree['entities']))
                @foreach ($tree['entities'] as $ent)
                    <div class="subhead">{{ $ent['name'] ?? 'Entity' }} <span style="color:var(--muted);font-weight:500">({{ $ent['type'] ?? '—' }})</span></div>
                    @if (!empty($ent['owners']))
                        <table class="tbl"><thead><tr><th>Owner</th><th>Type</th><th>Ownership %</th></tr></thead><tbody>
                            @foreach ($ent['owners'] as $o)
                                <tr><td>{{ $o['name'] ?? '—' }}</td><td>{{ ($o['type'] ?? '') === 'Entity' ? 'Corporate Entity' : (($o['type'] ?? '') === 'Individual' ? 'Natural Person' : '—') }}</td><td>{{ $o['pct'] ?? '—' }}%</td></tr>
                            @endforeach
                        </tbody></table>
                    @else <div class="empty">No owners defined.</div> @endif
                @endforeach
            @else <div class="empty">No ownership structure captured.</div> @endif
        </div>
    </div>

    {{-- 4. Beneficial Ownership --}}
    <div class="section">
        <div class="section-head">4 · Beneficial Ownership (UBO)</div>
        <div class="section-body">
            @if ($ubos->count())
                <table class="tbl"><thead><tr><th>Full Name</th><th>DOB</th><th>Nationality</th><th>ID No.</th><th>Ownership %</th><th>SMO</th></tr></thead><tbody>
                    @foreach ($ubos as $u)
                        <tr><td>{{ $val($u->full_name) }}</td><td>{{ $val($u->date_of_birth) }}</td><td>{{ $country($u->nationality_country_id) }}</td><td>{{ $val($u->id_document_number) }}</td><td>{{ $u->shareholding_capital_pct !== null ? $u->shareholding_capital_pct.'%' : '—' }}</td><td>{{ $u->is_senior_managing_official ? 'Yes' : 'No' }}</td></tr>
                    @endforeach
                </tbody></table>
            @else <div class="empty">No beneficial owners declared.</div> @endif
        </div>
    </div>

    {{-- 5. Financial & Tax --}}
    <div class="section">
        <div class="section-head">5 · Financial &amp; Tax</div>
        <div class="section-body">
            <div class="grid grid-3">
                <div class="f"><span class="f-label">Gross Annual Income</span><span class="f-value">{{ $val($app->gross_annual_income_band) }}</span></div>
                <div class="f"><span class="f-label">Net Worth</span><span class="f-value">{{ $app->net_worth_inr !== null ? number_format((float) $app->net_worth_inr, 2) : '—' }}</span></div>
                <div class="f"><span class="f-label">Net Worth Date</span><span class="f-value">{{ $val($app->net_worth_date) }}</span></div>
            </div>
            @if ($tax->count())
                <div class="subhead">Tax Residencies</div>
                <table class="tbl"><thead><tr><th>Country</th><th>TIN / TRC Number</th></tr></thead><tbody>
                    @foreach ($tax as $t)<tr><td>{{ $country($t->country_id) }}</td><td>{{ $val($t->trc_number) }}</td></tr>@endforeach
                </tbody></table>
            @endif
        </div>
    </div>

    {{-- 6. Category & Regulatory --}}
    <div class="section">
        <div class="section-head">6 · Category &amp; Regulatory</div>
        <div class="section-body">
            <div class="grid grid-3">
                <div class="f"><span class="f-label">FPI Category</span><span class="f-value">{{ $val($catLabels[$app->fpi_category_code] ?? $app->fpi_category_code) }}</span></div>
                <div class="f"><span class="f-label">Regulatory Status</span><span class="f-value">{{ $app->is_regulated_fpi ? 'Regulated' : 'Unregulated' }}</span></div>
                <div class="f"><span class="f-label">Regulator</span><span class="f-value">{{ $val($regulator->regulatory_authority_name ?? null) }}</span></div>
                <div class="f"><span class="f-label">Registration / License No.</span><span class="f-value">{{ $val($regulator->regulatory_registration_no ?? null) }}</span></div>
                <div class="f"><span class="f-label">Regulator Jurisdiction</span><span class="f-value">{{ $country($regulator->regulatory_country_id ?? null) }}</span></div>
            </div>
        </div>
    </div>

    {{-- 7. PAN, Bank & Depository --}}
    <div class="section">
        <div class="section-head">7 · PAN, Bank &amp; Depository</div>
        <div class="section-body">
            <div class="grid grid-3">
                <div class="f"><span class="f-label">Indian PAN</span><span class="f-value">{{ $val($pan->existing_pan ?? null) }}</span></div>
                <div class="f"><span class="f-label">Bank Name</span><span class="f-value">{{ $val($bank->ad_category_1_bank_name ?? null) }}</span></div>
                <div class="f"><span class="f-label">SWIFT / IFSC</span><span class="f-value">{{ $val($bank->bank_swift_ifsc ?? null) }}</span></div>
                <div class="f"><span class="f-label">Account Number</span><span class="f-value">{{ $val($office->bank_account_number ?? null) }}</span></div>
                <div class="f"><span class="f-label">Account Type</span><span class="f-value">{{ $val($office->bank_account_type ?? null) }}</span></div>
                <div class="f"><span class="f-label">Custodian</span><span class="f-value">{{ $val($custodian->global_custodian_name ?? null) }}</span></div>
                <div class="f"><span class="f-label">DP ID</span><span class="f-value">{{ $val($office->dp_id ?? null) }}</span></div>
                <div class="f"><span class="f-label">Client ID</span><span class="f-value">{{ $val($office->client_id ?? null) }}</span></div>
            </div>
        </div>
    </div>

    {{-- 8. Additional --}}
    <div class="section">
        <div class="section-head">8 · Additional Information</div>
        <div class="section-body">
            @php $comp = $contacts->firstWhere('contact_type', 'Compliance'); @endphp
            <div class="grid grid-3">
                <div class="f"><span class="f-label">Primary Contact</span><span class="f-value">{{ $val($comp->officer_name ?? null) }}</span></div>
                <div class="f"><span class="f-label">Designation</span><span class="f-value">{{ $val($comp->job_title ?? null) }}</span></div>
                <div class="f"><span class="f-label">MIM Structure</span><span class="f-value">{{ $yn($app->is_mim_structure) }}</span></div>
            </div>
            @if ($ims->count())
                <div class="subhead">Investment Managers</div>
                <table class="tbl"><thead><tr><th>Manager Name</th><th>SEBI Registration No.</th></tr></thead><tbody>
                    @foreach ($ims as $m)<tr><td>{{ $val($m->manager_name) }}</td><td>{{ $val($m->sebi_registration_no) }}</td></tr>@endforeach
                </tbody></table>
            @endif
        </div>
    </div>

    {{-- 9. Declaration & Documents --}}
    <div class="section">
        <div class="section-head">9 · Declaration &amp; Documents</div>
        <div class="section-body">
            @if ($docs->count())
                <table class="tbl"><thead><tr><th>Document</th><th>File</th></tr></thead><tbody>
                    @foreach ($docs as $d)<tr><td>{{ $d->label_en }}</td><td>{{ basename($d->file_storage_uri) }}</td></tr>@endforeach
                </tbody></table>
            @else <div class="empty">No documents uploaded.</div> @endif

            <div class="declaration-box" style="margin-top:10px">
                I/We hereby declare that all details and documents provided in this registration form are true, correct, and complete to the best of my/our knowledge and belief. I/We undertake to inform the depository participant / custodian immediately of any changes.
            </div>
            <div class="sign-row">
                <div class="sign-cell">
                    <div class="sign-line">Authorised Signatory: <strong>{{ $val($declaration->authorized_signatory_name ?? null) }}</strong></div>
                </div>
                <div class="sign-cell">
                    <div class="sign-line">Date: {{ $val($declaration->declaration_date ?? null) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <span>Facilon Investor Console — FPI Common Application Form</span>
        <span>Application Ref: FPI-{{ str_pad($app->applicant_id, 6, '0', STR_PAD_LEFT) }} · Generated {{ $generatedAt }}</span>
    </div>
</div>
</body>
</html>
