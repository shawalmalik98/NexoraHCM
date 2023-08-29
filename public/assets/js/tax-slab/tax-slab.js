$('#addTaxSlabForm').submit(function (e) {
    e.preventDefault();
    send_server_request('POST', 'tax-slab/add-tax-slab', this, 'addTaxSlabButton', "No", 'tax-slab', 3000);
})


$('#editTaxSlabForm').submit(function (e) {
    e.preventDefault();
    send_server_request('POST', 'tax-slab/edit-tax-slab', this, 'editTaxSlabButton', "No", 'tax-slab', 3000);
})

var add_tax_counter;

function addSlabRow() {
    add_tax_counter = $("input[name=slab_length]").val();

    add_tax_counter++;
    $("input[name=slab_length]").val(add_tax_counter);

    $("#slab_div").append(`

        <div class="row" id="deleteSlab${add_tax_counter}">

            <div class="col-3" >
                <div class="d-flex flex-column mb-4">
                    <input type="number" step="0.01" class="form-control lowest_yearly_income${add_tax_counter}" name="lowest_yearly_income[]" placeholder="Enter Lowest Yearly Inocme" required="" readonly >
                </div>
            </div>

            <div class="col-3" >
                <div class="d-flex flex-column mb-4">
                    <input type="number" step="0.01" class="form-control ts_highest_yearly_income${add_tax_counter}" name="highest_yearly_income[]" placeholder="Enter Highest Yearly Income" required="" oninput="nextLowerIcome(${add_tax_counter},${add_tax_counter + 1})"  >
                </div>
            </div>

            <div class="col-3" >
                <div class=" d-flex flex-column mb-4">
                    <div class="input-group" >
                        <input type="number" step="0.01" class="form-control" min="0" max="100" name="deduction_percentage[]" placeholder="Enter Deduction Percentage(%)" required="" value="0" >
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>


            <div class="col-3" >
                <div class="d-flex flex-column mb-4">
                    <input type="number" class="form-control"  name="deduction_amount[]" placeholder="Enter Tax Slab Fix Amount " required="">
                </div>
            </div>
        </div>`);
    nextLowerIcome(add_tax_counter - 1, add_tax_counter);
}

function addSlabRowEdit() {
    add_tax_counter = $("input[name=edit_slab_length]").val();

    add_tax_counter++;
    $("input[name=edit_slab_length]").val(add_tax_counter);

    $("#edit_slab_div").append(`

        <div class="row" id="deleteEditSlab${add_tax_counter}">

            <div class="col-3" >
                <div class="d-flex flex-column mb-4">
                    <input type="number" step="0.01" class="form-control lowest_yearly_income_edit${add_tax_counter}" name="lowest_yearly_income[]" placeholder="Enter Lowest Yearly Inocme" required="" readonly >
                </div>
            </div>

            <div class="col-3" >
                <div class="d-flex flex-column mb-4">
                    <input type="number" step="0.01" class="form-control ts_highest_yearly_income_edit${add_tax_counter}" name="highest_yearly_income[]" placeholder="Enter Highest Yearly Income" required="" oninput="nextLowerIcomeEdit(${add_tax_counter},${add_tax_counter + 1})"  >
                </div>
            </div>

            <div class="col-3" >
                <div class=" d-flex flex-column mb-4">
                    <div class="input-group" >
                        <input type="number" step="0.01" class="form-control" max="99" name="deduction_percentage[]" placeholder="Enter Deduction Percentage(%)" required="" value="0" >
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>


            <div class="col-3" >
                <div class="d-flex flex-column mb-4">
                    <input type="number" class="form-control"  name="deduction_amount[]" placeholder="Enter Tax Slab Fix Amount " required="">
                </div>
            </div>
        </div>`);
    nextLowerIcomeEdit(add_tax_counter - 1, add_tax_counter);
}

function deleteSlabRow() {
    add_tax_counter = $("input[name=slab_length]").val();
    if (add_tax_counter > 1) {
        swal.fire({
            title: 'Confirmation Alert !',
            text: 'Are you sure to delete this last record ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel',
        }).then(function (value) {
            if (value.isConfirmed == true) {
                $("#deleteSlab" + add_tax_counter).remove();
                add_tax_counter--;
                $("input[name=slab_length]").val(add_tax_counter);
            }
        });
    }
}


function deleteSlabRowEdit() {
    add_tax_counter = $("input[name=edit_slab_length]").val();
    if (add_tax_counter > 1) {
        swal.fire({
            title: 'Confirmation Alert !',
            text: 'Are you sure to delete this last record ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel',
        }).then(function (value) {
            if (value.isConfirmed == true) {
                $("#deleteEditSlab" + add_tax_counter).remove();
                add_tax_counter--;
                $("input[name=edit_slab_length]").val(add_tax_counter);
            }
        });
    }
}

function nextLowerIcome(this_id, count) {
    $('.lowest_yearly_income' + count).val(parseFloat($('.ts_highest_yearly_income' + this_id).val()) + 1);
    $('.ts_highest_yearly_income' + count).prop('min', parseFloat($('.ts_highest_yearly_income' + this_id).val()) + 1);
}


function nextLowerIcomeEdit(this_id, count) {
    $('.lowest_yearly_income_edit' + count).val(parseFloat($('.ts_highest_yearly_income_edit' + this_id).val()) + 1);
    $('.ts_highest_yearly_income_edit' + count).prop('min', parseFloat($('.ts_highest_yearly_income_edit' + this_id).val()) + 1);
}