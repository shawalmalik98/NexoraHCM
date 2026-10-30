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


// Pakistan salaried individuals, Finance Act 2026 (tax year 2027, from 1 July 2026): [highest yearly income, % of income above the slab start, fixed tax]
// Verify against the Finance Act of the selected fiscal year before saving.
var pakistan_salaried_slabs = [
    [600000, 0, 0],
    [1200000, 1, 0],
    [2200000, 11, 6000],
    [3200000, 20, 116000],
    [4100000, 25, 316000],
    [5600000, 29, 541000],
    [7000000, 32, 976000],
    [99999999, 35, 1424000]
];

function fillPakistanSalariedSlabs() {
    while (parseInt($("input[name=slab_length]").val()) > 1) {
        var last = parseInt($("input[name=slab_length]").val());
        $("#deleteSlab" + last).remove();
        $("input[name=slab_length]").val(last - 1);
    }
    for (var i = 1; i < pakistan_salaried_slabs.length; i++) {
        addSlabRow();
    }
    pakistan_salaried_slabs.forEach(function (slab, i) {
        var row = $('#slab_div > .row').eq(i);
        row.find('input[name="highest_yearly_income[]"]').val(slab[0]);
        row.find('input[name="deduction_percentage[]"]').val(slab[1]);
        row.find('input[name="deduction_amount[]"]').val(slab[2]);
    });
    for (var j = 1; j < pakistan_salaried_slabs.length; j++) {
        nextLowerIcome(j, j + 1);
    }
}

function nextLowerIcomeEdit(this_id, count) {
    $('.lowest_yearly_income_edit' + count).val(parseFloat($('.ts_highest_yearly_income_edit' + this_id).val()) + 1);
    $('.ts_highest_yearly_income_edit' + count).prop('min', parseFloat($('.ts_highest_yearly_income_edit' + this_id).val()) + 1);
}