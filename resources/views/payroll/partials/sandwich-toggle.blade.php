{{-- Sandwich (club) rule switch for one payroll run; defaults to the system setting --}}
<div class="row mt-5">
    <div class="col-md-12 fv-row">
        <input type="hidden" name="sandwich_rule_choice" value="1" />
        <label class="form-check form-switch form-check-custom form-check-solid">
            <input class="form-check-input" type="checkbox" name="apply_sandwich_rule" value="1" id="{{ $toggle_id }}"
                @if(!empty($tax_system_setting) && ($tax_system_setting[0]['club_rule_apply'] ?? 0) == 1) checked @endif />
            <span class="form-check-label fw-semibold">
                Apply Sandwich Rule
                <span class="d-block text-muted fs-7">Off day(s) between two absent days are counted as absent.</span>
            </span>
        </label>
    </div>
</div>
