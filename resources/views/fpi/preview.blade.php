@php
    $val = fn ($v) => ($v === null || $v === '') ? '—' : $v;
    $country = fn ($id) => $id && isset($countries[$id]) ? $countries[$id] : '—';
    $yn = fn ($b) => $b ? 'Yes' : 'No';
    $tree = ($app->ubo_structure_json ?? null) ? json_decode($app->ubo_structure_json, true) : null;
    $x = function ($arr, $k) { $v = $arr[$k] ?? ''; return ($v === '' || $v === null) ? '—' : (is_bool($v) ? ($v ? 'Yes' : 'No') : $v); };
    $cc = fn ($id) => ($id !== null && $id !== '' && isset($countries[(int) $id])) ? $countries[(int) $id] : ($id ?: '—');
    $isIndividual = ($app->entity_type ?? '') === 'Individual';
    $displayName = $isIndividual
        ? trim(implode(' ', array_filter([$indX['indFirstName'] ?? '', $indX['indMiddleName'] ?? '', $indX['indLastName'] ?? ''])))
        : ($corp->company_name ?? '');
    $sn = 0; // running section number (only counts rendered sections)
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FPI Application — {{ $displayName ?: 'Preview' }}</title>
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
            <div>Applicant Type: <strong>{{ $isIndividual ? 'Individual' : 'Non-Individual' }}</strong></div>
            <div>Generated: {{ $generatedAt }}</div>
        </div>
    </div>
    <div class="doc-title">Application Summary — {{ $isIndividual ? 'Individual' : 'Non-Individual' }} FPI</div>

    {{-- 1. Applicant Profile --}}
    <div class="section">
        <div class="section-head">{{ ++$sn }} · {{ $isIndividual ? 'Applicant — Personal Details' : 'Applicant Profile' }}</div>
        <div class="section-body">
            @if ($isIndividual)
                <div class="grid grid-3">
                    <div class="f"><span class="f-label">Title</span><span class="f-value">{{ $x($indX, 'indTitle') }}</span></div>
                    <div class="f"><span class="f-label">First Name</span><span class="f-value">{{ $x($indX, 'indFirstName') }}</span></div>
                    <div class="f"><span class="f-label">Middle Name</span><span class="f-value">{{ $x($indX, 'indMiddleName') }}</span></div>
                    <div class="f"><span class="f-label">Last Name</span><span class="f-value">{{ $x($indX, 'indLastName') }}</span></div>
                    <div class="f"><span class="f-label">Ever Known by Another Name</span><span class="f-value">{{ $x($indX, 'indOtherName') }}</span></div>
                    @if (($indX['indOtherName'] ?? '') === 'yes')
                        <div class="f"><span class="f-label">Other Name</span><span class="f-value">{{ trim($x($indX,'indOtherTitle').' '.$x($indX,'indOtherFirstName').' '.$x($indX,'indOtherMiddleName').' '.$x($indX,'indOtherLastName')) }}</span></div>
                    @endif
                    <div class="f"><span class="f-label">Date of Birth</span><span class="f-value">{{ $x($indX, 'indDob') }}</span></div>
                    <div class="f"><span class="f-label">Place of Birth</span><span class="f-value">{{ $x($indX, 'indPlaceOfBirth') }}</span></div>
                    <div class="f"><span class="f-label">Country of Birth</span><span class="f-value">{{ $x($indX, 'indCountryOfBirth') }} ({{ $x($indX, 'indBirthIsd') }})</span></div>
                    <div class="f"><span class="f-label">Nationality</span><span class="f-value">{{ $x($indX, 'indNationality') }} ({{ $x($indX, 'indNationalityIsd') }})</span></div>
                    <div class="f"><span class="f-label">Passport No.</span><span class="f-value">{{ $x($indX, 'indPassport') }}</span></div>
                    <div class="f"><span class="f-label">Gender</span><span class="f-value">{{ $x($indX, 'indGender') }}</span></div>
                    <div class="f"><span class="f-label">Marital Status</span><span class="f-value">{{ $x($indX, 'indMaritalStatus') }}</span></div>
                    <div class="f"><span class="f-label">Citizenship Status</span><span class="f-value">{{ $x($indX, 'indCitizenshipStatus') }}</span></div>
                    <div class="f"><span class="f-label">Country of Citizenship</span><span class="f-value">{{ $x($indX, 'indCountryOfCitizenship') }}</span></div>
                    <div class="f"><span class="f-label">Father's Name</span><span class="f-value">{{ trim($x($indX,'indFatherFirstName').' '.$x($indX,'indFatherMiddleName').' '.$x($indX,'indFatherLastName')) }}</span></div>
                    <div class="f"><span class="f-label">Mother's Name</span><span class="f-value">{{ trim($x($indX,'indMotherFirstName').' '.$x($indX,'indMotherMiddleName').' '.$x($indX,'indMotherLastName')) }}</span></div>
                    <div class="f"><span class="f-label">Spouse's Name</span><span class="f-value">{{ trim($x($indX,'indSpouseFirstName').' '.$x($indX,'indSpouseMiddleName').' '.$x($indX,'indSpouseLastName')) }}</span></div>
                </div>
            @else
                <div class="grid grid-3">
                    <div class="f"><span class="f-label">Title</span><span class="f-value">{{ $val($titleMap[$corp->name_title_code ?? ''] ?? null) }}</span></div>
                    <div class="f"><span class="f-label">Entity Name</span><span class="f-value">{{ $val($corp->company_name ?? null) }}</span></div>
                    <div class="f"><span class="f-label">Applicant Type</span><span class="f-value">{{ $val($corp->applicant_legal_type ?? null) }}</span></div>
                    <div class="f"><span class="f-label">Entity Type</span><span class="f-value">{{ $val($app->entity_type) }}</span></div>
                    <div class="f"><span class="f-label">Date of Incorporation</span><span class="f-value">{{ $val($corp->date_of_incorporation ?? null) }}</span></div>
                    <div class="f"><span class="f-label">Commencement of Business</span><span class="f-value">{{ $val($corp->date_commence_business ?? null) }}</span></div>
                    <div class="f"><span class="f-label">Place of Incorporation</span><span class="f-value">{{ $val($corp->place_of_incorporation ?? null) }}</span></div>
                    <div class="f"><span class="f-label">Country of Incorporation</span><span class="f-value">{{ $country($corp->incorporation_country_id ?? null) }}</span></div>
                    <div class="f"><span class="f-label">ISD Country Code</span><span class="f-value">{{ $val($corp->incorporation_isd_code ?? null) }}</span></div>
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
            @endif
        </div>
    </div>

    {{-- 2. Contact & Address --}}
    <div class="section">
        <div class="section-head">{{ ++$sn }} · Contact &amp; Address</div>
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

            @php $resContact = $contacts->firstWhere('contact_type', 'Residence'); @endphp
            @if ($resContact)
                <div class="subhead">Contact Details</div>
                <div class="grid grid-3">
                    <div class="f"><span class="f-label">Registered Phone</span><span class="f-value">{{ $val(trim(collect([$resContact->tel_isd_code, $resContact->tel_std_area_code, $resContact->telephone_number])->filter()->implode(' '))) }}</span></div>
                    <div class="f"><span class="f-label">Office Phone</span><span class="f-value">{{ $offContact ? $val(trim(collect([$offContact->tel_isd_code, $offContact->tel_std_area_code, $offContact->telephone_number])->filter()->implode(' '))) : '—' }}</span></div>
                    <div class="f"><span class="f-label">Mobile</span><span class="f-value">{{ $val($resContact->mobile_number) }}</span></div>
                    <div class="f"><span class="f-label">Fax</span><span class="f-value">{{ $val($resContact->fax_number) }}</span></div>
                    <div class="f"><span class="f-label">Email</span><span class="f-value">{{ $val($resContact->email_id) }}</span></div>
                    <div class="f"><span class="f-label">Website</span><span class="f-value">{{ $val($resContact->website) }}</span></div>
                </div>
            @endif
        </div>
    </div>

    @unless ($isIndividual)
    {{-- 3. UBO Determination (ownership tree) --}}
    <div class="section">
        <div class="section-head">{{ ++$sn }} · UBO Determination — Ownership Structure</div>
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

                {{-- UBO Summary (computed effective ownership) --}}
                <div class="subhead" style="margin-top:12px">UBO Summary — Identified Natural Persons (Threshold ≥ {{ $uboThreshold }}%)</div>
                @if (!empty($uboPersons))
                    <table class="tbl"><thead><tr><th>Identified Natural Person</th><th style="text-align:right">Effective Ownership %</th><th style="text-align:center">Status</th></tr></thead><tbody>
                        @foreach ($uboPersons as $person)
                            @php $isUbo = $person['effectivePct'] >= $uboThreshold; @endphp
                            <tr>
                                <td style="font-weight:600">{{ $person['name'] }}</td>
                                <td style="text-align:right;font-weight:700">{{ number_format($person['effectivePct'], 1) }}%</td>
                                <td style="text-align:center">
                                    <span style="padding:2px 8px;border-radius:10px;font-size:9px;font-weight:700;background:{{ $isUbo ? '#d1e7dd' : '#f8d7da' }};color:{{ $isUbo ? '#0f5132' : '#842029' }}">{{ $isUbo ? 'IDENTIFIED UBO' : 'BELOW THRESHOLD' }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody></table>
                @else
                    <div class="empty">No natural-person owners identified — a Senior Managing Official (SMO) should be designated.</div>
                @endif

                {{-- Ownership Diagram --}}
                @php
                    $byId = [];
                    foreach ($tree['entities'] as $e) { $byId[$e['id'] ?? ''] = $e; }
                    $renderNode = function ($entityId, $seen) use (&$renderNode, $byId) {
                        if (!isset($byId[$entityId]) || in_array($entityId, $seen, true)) return '';
                        $seen[] = $entityId;
                        $ent = $byId[$entityId];
                        $html = '';
                        foreach ($ent['owners'] ?? [] as $o) {
                            $isEntity = ($o['type'] ?? '') === 'Entity';
                            $bg = $isEntity ? '#eef3f5' : '#eaf5ee';
                            $bd = $isEntity ? '#b9ccd3' : '#b7ddc4';
                            $html .= '<div style="margin:6px 0 6px 22px;border-left:2px solid '.$bd.';padding-left:12px">';
                            $html .= '<div style="display:inline-block;background:'.$bg.';border:1px solid '.$bd.';border-radius:6px;padding:6px 12px;min-width:180px">';
                            $html .= '<div style="font-weight:700;color:#1a2633">'.e($o['name'] ?? '—').'</div>';
                            $html .= '<div style="font-size:10px;color:#5c6b78">'.e($o['pct'] ?? 0).'% Share · '.($isEntity ? 'Entity' : 'Individual').'</div>';
                            $html .= '</div>';
                            if ($isEntity) { $html .= $renderNode($o['targetId'] ?? '', $seen); }
                            $html .= '</div>';
                        }
                        return $html;
                    };
                    $applicant = $byId['applicant'] ?? ($tree['entities'][0] ?? null);
                @endphp
                @if ($applicant)
                    <div class="subhead" style="margin-top:12px">Ownership Diagram</div>
                    <div style="border:1px solid var(--line);border-radius:6px;padding:14px;background:#fafcfd">
                        <div style="display:inline-block;background:var(--primary);color:#fff;border-radius:6px;padding:8px 14px;font-weight:700">
                            {{ $applicant['name'] ?? 'Applicant' }}
                            <div style="font-size:10px;font-weight:500;opacity:.85">Applicant ({{ $applicant['type'] ?? '—' }})</div>
                        </div>
                        {!! $renderNode($applicant['id'] ?? 'applicant', []) !!}
                    </div>
                @endif
            @else <div class="empty">No ownership structure captured.</div> @endif
        </div>
    </div>

    {{-- 4. Beneficial Ownership --}}
    <div class="section">
        <div class="section-head">{{ ++$sn }} · Beneficial Ownership (UBO)</div>
        <div class="section-body">
            <div class="grid grid-3">
                <div class="f"><span class="f-label">Are there Sub-Funds?</span><span class="f-value">{{ $x($bo, 'hasSubFunds') }}</span></div>
            </div>
            @if (!empty($bo['subFunds']))
                <div class="subhead">Sub-Funds / Share Classes</div>
                <table class="tbl"><thead><tr><th>#</th><th>Name</th></tr></thead><tbody>
                    @foreach ($bo['subFunds'] as $i => $sf)<tr><td>{{ $i + 1 }}</td><td>{{ $val($sf) }}</td></tr>@endforeach
                </tbody></table>
            @endif
            <div class="subhead">Ultimate Beneficial Owners</div>
            @if ($ubos->count())
                <table class="tbl"><thead><tr><th>Full Name</th><th>DOB</th><th>Tax Jurisdiction</th><th>Nationality</th><th>Acting</th><th>ID No.</th><th>Ownership %</th></tr></thead><tbody>
                    @foreach ($ubos as $u)
                        <tr><td>{{ $val($u->full_name) }}</td><td>{{ $val($u->date_of_birth) }}</td><td>{{ $country($u->tax_residency_country_id) }}</td><td>{{ $country($u->nationality_country_id) }}</td><td>{{ $val($u->acting_group_details) }}</td><td>{{ $val($u->id_document_number) }}</td><td>{{ $u->shareholding_capital_pct !== null ? $u->shareholding_capital_pct.'%' : '—' }}</td></tr>
                    @endforeach
                </tbody></table>
            @else <div class="empty">No beneficial owners declared.</div> @endif
            @if (!empty($bo['intermediates']))
                <div class="subhead">Intermediate Entities</div>
                <table class="tbl"><thead><tr><th>Name</th><th>Stake Type</th><th>Chain</th><th>Country</th><th>% Stake</th><th>Type</th></tr></thead><tbody>
                    @foreach ($bo['intermediates'] as $r)<tr><td>{{ $val($r['name'] ?? '') }}</td><td>{{ $val($r['stakeType'] ?? '') }}</td><td>{{ $val($r['chain'] ?? '') }}</td><td>{{ $cc($r['country'] ?? '') }}</td><td>{{ $val($r['pct'] ?? '') }}</td><td>{{ $val($r['type'] ?? '') }}</td></tr>@endforeach
                </tbody></table>
            @endif
            @if (!empty($bo['controllers']))
                <div class="subhead">Controlling Entities</div>
                <table class="tbl"><thead><tr><th>Name</th><th>Control Method</th><th>Country</th><th>% Control</th><th>Type</th></tr></thead><tbody>
                    @foreach ($bo['controllers'] as $r)<tr><td>{{ $val($r['name'] ?? '') }}</td><td>{{ $val($r['method'] ?? '') }}</td><td>{{ $cc($r['country'] ?? '') }}</td><td>{{ $val($r['pct'] ?? '') }}</td><td>{{ $val($r['type'] ?? '') }}</td></tr>@endforeach
                </tbody></table>
            @endif
        </div>
    </div>

    @endunless

    {{-- 5. Financial & Tax --}}
    <div class="section">
        <div class="section-head">{{ ++$sn }} · Financial &amp; Tax</div>
        <div class="section-body">
            <div class="grid grid-3">
                <div class="f"><span class="f-label">Gross Annual Income (Band)</span><span class="f-value">{{ $val($app->gross_annual_income_band) }}</span></div>
                <div class="f"><span class="f-label">Gross Annual Income (INR)</span><span class="f-value">{{ $app->gross_annual_income_inr !== null ? number_format((float) $app->gross_annual_income_inr, 2) : '—' }}</span></div>
                <div class="f"><span class="f-label">Net Worth</span><span class="f-value">{{ $app->net_worth_inr !== null ? number_format((float) $app->net_worth_inr, 2) : '—' }}</span></div>
                <div class="f"><span class="f-label">Net Worth Date</span><span class="f-value">{{ $val($app->net_worth_date) }}</span></div>
                <div class="f"><span class="f-label">Occupation</span><span class="f-value">{{ $x($financialX, 'occupation') }}</span></div>
                <div class="f"><span class="f-label">Business/Profession Code</span><span class="f-value">{{ $x($financialX, 'professionCode') }}</span></div>
                <div class="f full"><span class="f-label">Source of Income</span><span class="f-value">{{ !empty($financialX['incomeSources']) ? implode(', ', (array) $financialX['incomeSources']) : '—' }}</span></div>
                <div class="f"><span class="f-label">TIN Reason (if no TIN)</span><span class="f-value">{{ $x($financialX, 'tinReason') }}</span></div>
                <div class="f"><span class="f-label">TIN Explanation</span><span class="f-value">{{ $x($financialX, 'tinExplanation') }}</span></div>
                <div class="f"><span class="f-label">TRC Number</span><span class="f-value">{{ $x($financialX, 'trcNumber') }}</span></div>
                <div class="f"><span class="f-label">FATCA/CRS Provided</span><span class="f-value">{{ $x($financialX, 'fatcaCrs') }}</span></div>
                <div class="f"><span class="f-label">PEP</span><span class="f-value">{{ $x($financialX, 'isPep') }}</span></div>
                <div class="f"><span class="f-label">Related to PEP</span><span class="f-value">{{ $x($financialX, 'relatedToPep') }}</span></div>
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
        <div class="section-head">{{ ++$sn }} · Category &amp; Regulatory</div>
        <div class="section-body">
            <div class="grid grid-3">
                <div class="f"><span class="f-label">FPI Category</span><span class="f-value">{{ $val($catLabels[$app->fpi_category_code] ?? $app->fpi_category_code) }}</span></div>
                <div class="f"><span class="f-label">Sub-Category</span><span class="f-value">{{ $x($categoryX, 'subCategory') }}</span></div>
                <div class="f"><span class="f-label">MIM Structure</span><span class="f-value">{{ $x($categoryX, 'mimStructure') }}</span></div>
                <div class="f"><span class="f-label">Regulatory Status</span><span class="f-value">{{ $app->is_regulated_fpi ? 'Regulated' : 'Unregulated' }}</span></div>
                <div class="f"><span class="f-label">Regulator</span><span class="f-value">{{ $val($regulator->regulatory_authority_name ?? null) }}</span></div>
                <div class="f"><span class="f-label">Registration / License No.</span><span class="f-value">{{ $val($regulator->regulatory_registration_no ?? null) }}</span></div>
                <div class="f"><span class="f-label">Regulator Jurisdiction</span><span class="f-value">{{ $country($regulator->regulatory_country_id ?? null) }}</span></div>
                <div class="f"><span class="f-label">Regulator Website</span><span class="f-value">{{ $x($categoryX, 'regulatorWebsite') }}</span></div>
                <div class="f"><span class="f-label">Regulator Capacity</span><span class="f-value">{{ $x($categoryX, 'regulatorCapacity') }}</span></div>
            </div>
            <div class="subhead">Compliance Officer</div>
            <div class="grid grid-3">
                <div class="f"><span class="f-label">Name</span><span class="f-value">{{ $x($categoryX, 'complianceName') }}</span></div>
                <div class="f"><span class="f-label">Job Title</span><span class="f-value">{{ $x($categoryX, 'complianceTitle') }}</span></div>
                <div class="f"><span class="f-label">Email</span><span class="f-value">{{ $x($categoryX, 'complianceEmail') }}</span></div>
                <div class="f"><span class="f-label">Phone</span><span class="f-value">{{ $x($categoryX, 'compliancePhone') }}</span></div>
                <div class="f"><span class="f-label">Fax</span><span class="f-value">{{ $x($categoryX, 'complianceFax') }}</span></div>
            </div>
            <div class="subhead">Global Custodian</div>
            <div class="grid grid-3">
                <div class="f"><span class="f-label">Using Custodian?</span><span class="f-value">{{ $x($categoryX, 'hasCustodian') }}</span></div>
                <div class="f"><span class="f-label">Custodian Name</span><span class="f-value">{{ $x($categoryX, 'custodianNameCat') }}</span></div>
                <div class="f"><span class="f-label">Regulator</span><span class="f-value">{{ $x($categoryX, 'custodianReg') }}</span></div>
                <div class="f"><span class="f-label">Registration Code</span><span class="f-value">{{ $x($categoryX, 'custRegCode') }}</span></div>
                <div class="f full"><span class="f-label">Address</span><span class="f-value">{{ $x($categoryX, 'custodianAddress') }}</span></div>
            </div>
            <div class="grid grid-3">
                <div class="f"><span class="f-label">Disciplinary History</span><span class="f-value">{{ $x($categoryX, 'disciplinaryHistory') }}</span></div>
                <div class="f full"><span class="f-label">Disciplinary Details</span><span class="f-value">{{ $x($categoryX, 'disciplinaryDetails') }}</span></div>
                <div class="f"><span class="f-label">Clubbing Declaration</span><span class="f-value">{{ $x($categoryX, 'clubbingDeclaration') }}</span></div>
                <div class="f"><span class="f-label">Investor Group No.?</span><span class="f-value">{{ $x($categoryX, 'fpiGroupNumber') }}</span></div>
                <div class="f"><span class="f-label">Group Number</span><span class="f-value">{{ $x($categoryX, 'groupNumber') }}</span></div>
                <div class="f"><span class="f-label">Prior Association</span><span class="f-value">{{ $x($categoryX, 'priorAssociation') }}</span></div>
            </div>
            @if (!empty($categoryX['fpiGroupData']))
                <div class="subhead">Investor Group</div>
                <table class="tbl"><thead><tr><th>Name of FPI/ODI</th><th>Dealing FPI</th><th>Reg No.</th></tr></thead><tbody>
                    @foreach ($categoryX['fpiGroupData'] as $r)<tr><td>{{ $val($r['fpiName'] ?? '') }}</td><td>{{ $val($r['dealingFpi'] ?? '') }}</td><td>{{ $val($r['fpiRegNo'] ?? '') }}</td></tr>@endforeach
                </tbody></table>
            @endif
            @if (!empty($categoryX['publicRetailData']))
                <div class="subhead">Exempt Public Retail Funds</div>
                <table class="tbl"><thead><tr><th>Name of FPI</th><th>Reg No.</th><th>Common Controlling Person</th></tr></thead><tbody>
                    @foreach ($categoryX['publicRetailData'] as $r)<tr><td>{{ $val($r['fpiName'] ?? '') }}</td><td>{{ $val($r['fpiRegNo'] ?? '') }}</td><td>{{ $val($r['commonPerson'] ?? '') }}</td></tr>@endforeach
                </tbody></table>
            @endif
            @if (!empty($categoryX['priorAssociationsData']))
                <div class="subhead">Prior Associations</div>
                <table class="tbl"><thead><tr><th>Entity</th><th>Registered / Associated As</th><th>SEBI Reg No.</th></tr></thead><tbody>
                    @foreach ($categoryX['priorAssociationsData'] as $r)<tr><td>{{ $val($r['entity'] ?? '') }}</td><td>{{ $val($r['associationType'] ?? '') }}</td><td>{{ $val($r['sebiReg'] ?? '') }}</td></tr>@endforeach
                </tbody></table>
            @endif
        </div>
    </div>

    {{-- 7. PAN, Bank & Depository --}}
    <div class="section">
        <div class="section-head">{{ ++$sn }} · PAN, Bank &amp; Depository</div>
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
                <div class="f"><span class="f-label">Already holds PAN?</span><span class="f-value">{{ $x($depositoryX, 'hasPan') }}</span></div>
                <div class="f"><span class="f-label">Name on PAN Card</span><span class="f-value">{{ $x($depositoryX, 'existingpanName') }}</span></div>
            </div>
            @if (($depositoryX['hasPan'] ?? '') === 'no')
                <div class="subhead">Apply for PAN</div>
                <div class="grid grid-3">
                    <div class="f"><span class="f-label">Status of Applicant</span><span class="f-value">{{ $x($depositoryX, 'statusApplicant') }}</span></div>
                    <div class="f"><span class="f-label">Name to Print on PAN</span><span class="f-value">{{ $x($depositoryX, 'panName') }}</span></div>
                    <div class="f"><span class="f-label">Registration Number</span><span class="f-value">{{ $x($depositoryX, 'registrationNumber') }}</span></div>
                    <div class="f"><span class="f-label">AO Area / Type / Range / No.</span><span class="f-value">{{ $x($depositoryX, 'aoAreaCode') }} / {{ $x($depositoryX, 'aoType') }} / {{ $x($depositoryX, 'aoRangeCode') }} / {{ $x($depositoryX, 'aoNo') }}</span></div>
                </div>
            @endif
            <div class="subhead">Representative / Agent in India</div>
            <div class="grid grid-3">
                <div class="f"><span class="f-label">Name</span><span class="f-value">{{ trim($x($depositoryX,'repTitle').' '.$x($depositoryX,'repFirstName').' '.$x($depositoryX,'repMiddleName').' '.$x($depositoryX,'repLastName')) ?: '—' }}</span></div>
                <div class="f full"><span class="f-label">Address</span><span class="f-value">{{ $x($depositoryX, 'repAddress') }}</span></div>
            </div>
            <div class="subhead">Listing, POI / POA &amp; Authorizations</div>
            <div class="grid grid-3">
                <div class="f"><span class="f-label">Listed on Exchange?</span><span class="f-value">{{ $x($depositoryX, 'listed') }}</span></div>
                <div class="f"><span class="f-label">Exchange Name</span><span class="f-value">{{ $x($depositoryX, 'exchangeName') }}</span></div>
                <div class="f"><span class="f-label">Sensitive Activities</span><span class="f-value">{{ !empty($depositoryX['sensitiveActivities']) ? implode(', ', (array) $depositoryX['sensitiveActivities']) : '—' }}</span></div>
                <div class="f"><span class="f-label">POI — Type / No.</span><span class="f-value">{{ $x($depositoryX, 'poiType') }} / {{ $x($depositoryX, 'poiNumber') }}</span></div>
                <div class="f"><span class="f-label">POA — Type / No.</span><span class="f-value">{{ $x($depositoryX, 'poaType') }} / {{ $x($depositoryX, 'poaNumber') }}</span></div>
                <div class="f"><span class="f-label">Depository Authorisation</span><span class="f-value">{{ $x($depositoryX, 'depositoryAuth') }}</span></div>
                <div class="f"><span class="f-label">Mode of Operation</span><span class="f-value">{{ $x($depositoryX, 'modeOfOperation') }} {{ ($depositoryX['modeOfOperation'] ?? '') === 'Others' ? '('.$x($depositoryX,'otherMode').')' : '' }}</span></div>
                <div class="f"><span class="f-label">Bank Authorisation</span><span class="f-value">{{ $x($depositoryX, 'bankAuth') }}</span></div>
            </div>
        </div>
    </div>

    {{-- 8. Additional --}}
    <div class="section">
        <div class="section-head">{{ ++$sn }} · Additional Information</div>
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
            <div class="grid grid-3">
                <div class="f"><span class="f-label">ODI with Derivatives</span><span class="f-value">{{ $x($additionalX, 'odiDerivatives') }}</span></div>
                <div class="f"><span class="f-label">Bank/Subsidiary Declaration</span><span class="f-value">{{ $x($additionalX, 'bankDeclaration') }}</span></div>
                <div class="f"><span class="f-label">Bank Entity Name</span><span class="f-value">{{ $x($additionalX, 'bankEntityName') }}</span></div>
                <div class="f"><span class="f-label">NRI/OCI/RI Control (1)</span><span class="f-value">{{ $x($additionalX, 'nriControl1') }}</span></div>
                <div class="f"><span class="f-label">NRI/OCI/RI Control (2)</span><span class="f-value">{{ $x($additionalX, 'nriControl2') }}</span></div>
                <div class="f"><span class="f-label">IM Types</span><span class="f-value">{{ !empty($additionalX['imTypes']) ? implode(', ', (array) $additionalX['imTypes']) : '—' }}</span></div>
                <div class="f"><span class="f-label">Directly Controlled by NRI?</span><span class="f-value">{{ $x($additionalX, 'directlyControlled') }}</span></div>
                <div class="f"><span class="f-label">Controlled Entity Name</span><span class="f-value">{{ $x($additionalX, 'nriControlEntityName') }}</span></div>
                <div class="f"><span class="f-label">Offshore Fund (SEBI NOC)</span><span class="f-value">{{ $x($additionalX, 'offshoreFund') }}</span></div>
                <div class="f"><span class="f-label">NRI/OCI/RI Entitlement</span><span class="f-value">{{ $x($additionalX, 'nriEntitlement') }}</span></div>
                <div class="f"><span class="f-label">Reg 5(b)(vii) Clients</span><span class="f-value">{{ $x($additionalX, 'reg5b7') }}</span></div>
                <div class="f"><span class="f-label">KRA Consent</span><span class="f-value">{{ $x($additionalX, 'kraConsent') }}</span></div>
                <div class="f"><span class="f-label">KRA Rep / Email</span><span class="f-value">{{ $x($additionalX, 'kraRepName') }} / {{ $x($additionalX, 'kraEmail1') }}</span></div>
            </div>
            @if (!empty($additionalX['shareClassesData']))
                <div class="subhead">Sub-Funds / Share Classes</div>
                <table class="tbl"><thead><tr><th>#</th><th>Name</th></tr></thead><tbody>
                    @foreach ($additionalX['shareClassesData'] as $i => $r)<tr><td>{{ $i + 1 }}</td><td>{{ $val($r['name'] ?? '') }}</td></tr>@endforeach
                </tbody></table>
            @endif
            @if (!empty($additionalX['categoryOneEntitiesData']))
                <div class="subhead">Eligible Category I Entities</div>
                <table class="tbl"><thead><tr><th>Name</th><th>Country</th><th>Entity Type</th></tr></thead><tbody>
                    @foreach ($additionalX['categoryOneEntitiesData'] as $r)<tr><td>{{ $val($r['name'] ?? '') }}</td><td>{{ $cc($r['country'] ?? '') }}</td><td>{{ $val($r['entityType'] ?? '') }}</td></tr>@endforeach
                </tbody></table>
            @endif
            @if (!empty($additionalX['clientsData']))
                <div class="subhead">Clients</div>
                <table class="tbl"><thead><tr><th>Name</th><th>Country</th><th>Address</th><th>Type</th></tr></thead><tbody>
                    @foreach ($additionalX['clientsData'] as $r)<tr><td>{{ $val($r['name'] ?? '') }}</td><td>{{ $val($r['country'] ?? '') }}</td><td>{{ $val($r['address'] ?? '') }}</td><td>{{ $val($r['type'] ?? '') }}</td></tr>@endforeach
                </tbody></table>
            @endif
            @if (!empty($additionalX['signatoriesData']))
                <div class="subhead">Authorized Signatories</div>
                <table class="tbl"><thead><tr><th>Name</th><th>Relationship</th><th>PAN</th><th>Nationality</th><th>DOB</th><th>Address</th><th>Govt ID</th></tr></thead><tbody>
                    @foreach ($additionalX['signatoriesData'] as $r)<tr><td>{{ $val($r['name'] ?? '') }}</td><td>{{ $val($r['relationship'] ?? '') }}</td><td>{{ $val($r['pan'] ?? '') }}</td><td>{{ $val($r['nationality'] ?? '') }}</td><td>{{ $val($r['dob'] ?? '') }}</td><td>{{ $val($r['address'] ?? '') }}</td><td>{{ $val($r['govId'] ?? '') }}</td></tr>@endforeach
                </tbody></table>
            @endif
        </div>
    </div>

    {{-- 9. Declaration & Documents --}}
    <div class="section">
        <div class="section-head">{{ ++$sn }} · Declaration &amp; Documents</div>
        <div class="section-body">
            @if ($docs->count())
                <table class="tbl"><thead><tr><th>Document</th><th>File</th></tr></thead><tbody>
                    @foreach ($docs as $d)<tr><td>{{ $d->label_en }}</td><td>{{ basename($d->file_storage_uri) }}</td></tr>@endforeach
                </tbody></table>
            @else <div class="empty">No documents uploaded.</div> @endif

            <div class="declaration-box" style="margin-top:10px">
                I/We hereby declare that all details and documents provided in this registration form are true, correct, and complete to the best of my/our knowledge and belief. I/We undertake to inform the depository participant / custodian immediately of any changes.
            </div>
            <div class="grid grid-3" style="margin-top:8px">
                <div class="f"><span class="f-label">Place</span><span class="f-value">{{ $x($declX, 'declarationPlace') }}</span></div>
                <div class="f"><span class="f-label">Date</span><span class="f-value">{{ $x($declX, 'declarationDate') }}</span></div>
                <div class="f"><span class="f-label">Name of Applicant</span><span class="f-value">{{ $x($declX, 'applicantName') }}</span></div>
                <div class="f"><span class="f-label">Designation</span><span class="f-value">{{ $x($declX, 'applicantDesignation') }}</span></div>
                <div class="f"><span class="f-label">Signatory Designation</span><span class="f-value">{{ $x($declX, 'authDesignation') }}</span></div>
                <div class="f"><span class="f-label">Signatory Date</span><span class="f-value">{{ $x($declX, 'authDate') }}</span></div>
                <div class="f"><span class="f-label">Declaration 1 (true &amp; correct)</span><span class="f-value">{{ !empty($declX['declaration1']) ? 'Agreed' : '—' }}</span></div>
                <div class="f"><span class="f-label">Declaration 2 (comply SEBI)</span><span class="f-value">{{ !empty($declX['declaration2']) ? 'Agreed' : '—' }}</span></div>
                <div class="f"><span class="f-label">Declaration 3 (docs valid)</span><span class="f-value">{{ !empty($declX['declaration3']) ? 'Agreed' : '—' }}</span></div>
            </div>
            @php $extraDocs = array_filter(['Proof of Identity' => $declX['uploadedPoi'] ?? '', 'Proof of Address' => $declX['uploadedPoa'] ?? '', 'FATCA/CRS Form' => $declX['uploadedFatca'] ?? '', 'Signature' => $declX['uploadedSignature'] ?? '']); @endphp
            @if (!empty($extraDocs) || !empty($declX['otherDocs']))
                <div class="subhead">Additional Uploads</div>
                <table class="tbl"><thead><tr><th>Document</th><th>File</th></tr></thead><tbody>
                    @foreach ($extraDocs as $label => $fn)<tr><td>{{ $label }}</td><td>{{ $fn }}</td></tr>@endforeach
                    @foreach ((array) ($declX['otherDocs'] ?? []) as $fn)<tr><td>Other Supporting Document</td><td>{{ $fn }}</td></tr>@endforeach
                </tbody></table>
            @endif
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
