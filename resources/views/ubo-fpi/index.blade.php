@extends('layouts.app')

@section('title', 'UBO FPI Determination')
@section('page_title', 'UBO FPI Determination')
@section('page_desc', 'Determine the Ultimate Beneficial Owners (UBOs) for FPI applicants based on SEBI threshold norms.')
@section('page_icon')
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.8"><path d="M12 2l7 4v5c0 5-3.5 8.5-7 10-3.5-1.5-7-5-7-10V6z"/></svg>
@endsection

@push('styles')
<style>
    .ubo-container { padding: 0 0 20px; }
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
    <div class="ubo-container">
        <div style="font-size:16px;font-weight:700;color:var(--gray900);margin-bottom:20px">
            UBO Determination Tool for FPI Applicant
        </div>

        {{-- Step 1: applicant type --}}
        <div class="ubo-card">
            <div class="ubo-card-title">Step 1: What type of applicant is this?</div>
            <div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap;margin-bottom:12px">
                <select class="ubo-select" id="applicantType" style="width:280px">
                    <option value="Partnership">Partnership</option>
                    <option value="Company">Company</option>
                    <option value="Trust">Trust</option>
                    <option value="Unincorporated Association / Body of Individuals">Unincorporated Association / BOI</option>
                </select>
                <button class="ubo-btn ubo-btn-primary" id="resetFlow">Start / Reset Flow</button>
            </div>
            <div style="font-size:11px;color:var(--gray500);font-style:italic;background:#f8f9fa;padding:8px 12px;border-radius:4px;border-left:3px solid var(--primary-mid)">
                SMO should be used only if no natural person is identified through ownership threshold or control.
            </div>
        </div>

        {{-- Ownership hierarchy editor --}}
        <div class="ubo-card">
            <div class="ubo-card-title" style="display:flex;justify-content:space-between;align-items:center">
                <span>Define Shareholding / Ownership Hierarchy</span>
                <span style="font-size:10.5px;color:#0f766e;background:#ccfbf1;padding:2px 8px;border-radius:10px;font-weight:600">Threshold: 10% (SEBI FPI Regulation)</span>
            </div>
            <div id="hierarchy" style="display:flex;flex-direction:column;gap:20px"></div>
        </div>

        {{-- Summary / evaluation --}}
        <div class="ubo-card">
            <div class="ubo-card-title">UBO Summary</div>
            <p style="font-size:12px;color:var(--gray600);margin-bottom:12px">
                Build at least one entity or sub-fund flow and click "Evaluate UBOs" to calculate direct &amp; indirect ownership thresholds.
            </p>
            <div style="margin-bottom:16px">
                <button class="ubo-btn ubo-btn-secondary" id="evaluateBtn">Evaluate UBOs</button>
            </div>
            <div id="evalResult">
                <div style="border:1px dashed var(--gray300);padding:16px;text-align:center;color:var(--gray500);font-size:11.5px;border-radius:6px">
                    Awaiting evaluation...
                </div>
            </div>
        </div>

        {{-- Diagram --}}
        <div class="ubo-card">
            <div class="ubo-card-title">Ownership Diagram</div>
            <div style="display:flex;gap:10px;margin-bottom:16px">
                <button class="ubo-btn ubo-btn-primary" id="tabDiagram">Visualize Diagram</button>
                <button class="ubo-btn ubo-btn-ghost" id="tabMermaid">Download Mermaid Source</button>
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
@endsection

@push('scripts')
<script>
    (function () {
        const DEFAULT_ENTITIES = @json($entities);
        const THRESHOLD = 10;

        let entities = structuredClone(DEFAULT_ENTITIES);
        let uid = 0;
        const newId = (p) => p + '_' + (Date.now() + (uid++));

        const hierarchyEl = document.getElementById('hierarchy');
        const evalResultEl = document.getElementById('evalResult');
        const applicantTypeEl = document.getElementById('applicantType');

        /* ---------- state mutations ---------- */
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
                    id: subId,
                    name: owner.name || 'New Entity',
                    type: 'Company',
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

        /* ---------- render: hierarchy editor ---------- */
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
                    name.placeholder = 'Owner Name';
                    name.addEventListener('change', e => updateOwner(ent.id, owner.id, { name: e.target.value }));

                    const type = document.createElement('select');
                    type.className = 'ubo-select';
                    type.innerHTML = '<option value="Individual">Natural Person</option><option value="Entity">Corporate Entity</option>';
                    type.value = owner.type;
                    type.addEventListener('change', e => updateOwner(ent.id, owner.id, { type: e.target.value }));

                    const pctWrap = document.createElement('div');
                    pctWrap.style.cssText = 'display:flex;align-items:center;gap:4px';
                    const pct = document.createElement('input');
                    pct.className = 'ubo-input';
                    pct.type = 'number';
                    pct.value = owner.pct;
                    pct.placeholder = '%';
                    pct.style.width = '60px';
                    pct.addEventListener('change', e => updateOwner(ent.id, owner.id, { pct: parseFloat(e.target.value) || 0 }));
                    const pctLabel = document.createElement('span');
                    pctLabel.style.cssText = 'font-size:11px;color:var(--gray500)';
                    pctLabel.textContent = '%';
                    pctWrap.append(pct, pctLabel);

                    const remove = document.createElement('button');
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

        /* ---------- evaluate ---------- */
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

        /* ---------- diagram ---------- */
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

        /* ---------- reset ---------- */
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

        /* ---------- diagram / mermaid tabs ---------- */
        let mermaidVisible = false;
        function setView(m) {
            mermaidVisible = m;
            document.getElementById('diagramView').style.display = m ? 'none' : '';
            document.getElementById('mermaidView').style.display = m ? '' : 'none';
            document.getElementById('tabDiagram').className = 'ubo-btn ' + (m ? 'ubo-btn-ghost' : 'ubo-btn-primary');
            document.getElementById('tabMermaid').className = 'ubo-btn ' + (m ? 'ubo-btn-primary' : 'ubo-btn-ghost');
            if (m) document.getElementById('mermaidCode').value = generateMermaid();
        }

        /* ---------- wire up ---------- */
        document.getElementById('resetFlow').addEventListener('click', handleReset);
        document.getElementById('evaluateBtn').addEventListener('click', evaluateUbos);
        document.getElementById('tabDiagram').addEventListener('click', () => setView(false));
        document.getElementById('tabMermaid').addEventListener('click', () => setView(true));

        renderHierarchy();
    })();
</script>
@endpush
