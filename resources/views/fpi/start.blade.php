@extends('layouts.app')

@section('title', 'Start Application')
@section('page_title', 'Start Your Application')
@section('page_desc', 'Select the type of applicant to begin your Foreign Portfolio Investor registration.')
@section('page_icon')
    <span style="font-size:22px">📝</span>
@endsection

@push('styles')
<style>
    /* Centre the card in the available viewport (below the topbar + page head). */
    .start-wrap {
        min-height: calc(100vh - 56px - 92px - 56px);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .start-card { width: 100%; max-width: 720px; }

    .start-card {
        background: #fff;
        border: 1px solid var(--gray200);
        border-radius: var(--card-radius);
        box-shadow: var(--shadow-sm);
        padding: 26px 26px 22px;
    }

    .start-card h2 {
        font-size: 16px;
        font-weight: 600;
        color: var(--gray900);
        margin-bottom: 4px;
    }
    .start-card p.sub {
        font-size: 13px;
        color: var(--gray600);
        margin-bottom: 20px;
    }

    .kind-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }
    @media (max-width: 560px) {
        .kind-grid { grid-template-columns: 1fr; }
    }

    .kind-option {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 6px;
        border: 1.5px solid var(--gray200);
        border-radius: 8px;
        padding: 18px 18px 18px 46px;
        cursor: pointer;
        transition: border-color .12s, background .12s, box-shadow .12s;
        background: var(--gray50);
    }
    .kind-option:hover { border-color: var(--primary-mid); }

    .kind-option input {
        position: absolute;
        top: 20px;
        left: 18px;
        width: 16px;
        height: 16px;
        accent-color: var(--primary);
        cursor: pointer;
    }

    .kind-option .kind-title {
        font-size: 14.5px;
        font-weight: 600;
        color: var(--gray900);
    }
    .kind-option .kind-desc {
        font-size: 12.5px;
        color: var(--gray600);
        line-height: 1.45;
    }

    /* Selected state */
    .kind-option.is-selected {
        border-color: var(--primary);
        background: var(--primary-light);
        box-shadow: 0 0 0 1px var(--primary);
    }

    .start-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
        padding-top: 18px;
        border-top: 1px solid var(--gray100);
    }

    .start-error {
        display: none;
        background: #fdecea;
        border: 1px solid #f5c6c2;
        color: var(--danger);
        border-radius: 6px;
        padding: 9px 13px;
        font-size: 12.5px;
        font-weight: 600;
        margin-bottom: 16px;
    }

    .btn:disabled { opacity: .5; cursor: not-allowed; filter: none; }
</style>
@endpush

@section('content')
<div class="start-wrap">
    <form class="start-card" method="POST" action="{{ route('fpi.begin') }}" id="startForm">
        @csrf

        <h2>Who is applying?</h2>
        <p class="sub">Choose one option below, then click <strong>Continue</strong> to open the registration form.</p>

        <div class="start-error" id="startError">Please select an applicant type to continue.</div>

        <div class="kind-grid">
            <label class="kind-option" data-kind="Individual">
                <input type="radio" name="entityType" value="Individual"
                       @checked(old('entityType', $entityType) === 'Individual')>
                <span class="kind-title">Individual</span>
                <span class="kind-desc">A natural person applying as a Foreign Portfolio Investor in their own name.</span>
            </label>

            <label class="kind-option" data-kind="Non-Individual">
                <input type="radio" name="entityType" value="Non-Individual"
                       @checked(old('entityType', $entityType) === 'Non-Individual')>
                <span class="kind-title">Non-Individual</span>
                <span class="kind-desc">A company, partnership, trust or other body applying as an entity.</span>
            </label>
        </div>

        <div class="start-footer">
            <button type="submit" class="btn btn-primary" id="continueBtn" disabled>Continue →</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const form       = document.getElementById('startForm');
        const options    = Array.from(document.querySelectorAll('.kind-option'));
        const radios      = Array.from(document.querySelectorAll('input[name="entityType"]'));
        const continueBtn = document.getElementById('continueBtn');
        const errorBox    = document.getElementById('startError');

        function refresh() {
            let anySelected = false;
            options.forEach(opt => {
                const radio = opt.querySelector('input');
                const on = radio.checked;
                opt.classList.toggle('is-selected', on);
                if (on) anySelected = true;
            });
            continueBtn.disabled = !anySelected;
            if (anySelected) errorBox.style.display = 'none';
        }

        // Clicking anywhere on the card selects its radio.
        options.forEach(opt => {
            opt.addEventListener('click', () => {
                opt.querySelector('input').checked = true;
                refresh();
            });
        });
        radios.forEach(r => r.addEventListener('change', refresh));

        form.addEventListener('submit', (e) => {
            if (!radios.some(r => r.checked)) {
                e.preventDefault();
                errorBox.style.display = 'block';
            }
        });

        refresh(); // reflect any pre-selected value on load
    })();
</script>
@endpush
