@push('js')
<script>

$(function () {

    initializeSelect2();

    bindEvents();

    renumberRows();

    calculateTotals();

});

let itemIndex = $('#sale-items-body tr').not('#empty-row').length;

//====================================================
// Select2
//====================================================

function initializeSelect2()
{
    if ($.fn.select2) {

        $('#product_selector').select2({
            placeholder: 'Search SKU / Barcode / Product',
            allowClear: true,
            width: '100%'
        });

    } else {

        console.warn('Select2 plugin not loaded.');

    }
}

//====================================================
// Event Binding
//====================================================

function bindEvents()
{
    // Add product
    $('#product_selector').on('change', function () {

        const option = $(this).find(':selected');

        if (!option.val()) {
            return;
        }

        addProduct(option);

        $(this).val(null).trigger('change');
    });

    // Qty
    $(document).on('input', '.qty', function () {

        calculateRow($(this).closest('tr'));

    });

    // Price
    $(document).on('input', '.unit-price', function () {

        calculateRow($(this).closest('tr'));

    });

    // Discount %
    $(document).on('input', '.discount-percent', function () {

        calculateRow($(this).closest('tr'));

    });

    // Remove row
    $(document).on('click', '.btn-remove-item', function () {

        $(this).closest('tr').remove();

        renumberRows();

        calculateTotals();

        toggleEmptyRow();

    });

    // Header Discount
    $('#discount_percent,#discount_amount').on('input', function () {

        calculateTotals();

    });

    // Tax
    $('#tax_percent,#tax_amount').on('input', function () {

        calculateTotals();

    });

    // Payment
    $('#paid_amount').on('input', function () {

        calculatePayment();

    });

}

//====================================================
// Add Product
//====================================================

function addProduct(option)
{
    let productId = option.data('id');

    //------------------------------------------------
    // Prevent duplicate product
    //------------------------------------------------

    let existing = null;

    $('#sale-items-body .product-id').each(function(){

        if ($(this).val() == productId) {

            existing = $(this).closest('tr');

            return false;
        }

    });

    if(existing){

        let qty = existing.find('.qty');

        qty.val(parseFloat(qty.val()) + 1);

        calculateRow(existing);

        return;
    }

    //------------------------------------------------
    // Hide empty row
    //------------------------------------------------

    $('#empty-row').hide();

    //------------------------------------------------
    // Clone template
    //------------------------------------------------

    let template = document
        .getElementById('sale-item-template')
        .content
        .cloneNode(true);

    let row = $(template).find('tr');

    //------------------------------------------------
    // Fill product information
    //------------------------------------------------

    row.find('.sku')
        .text(option.data('sku'));

    row.find('.product-name')
        .text(option.data('name'));

    row.find('.barcode')
        .text(option.data('barcode'));

    row.find('.unit')
        .text(option.data('unit'));

    row.find('.stock')
        .text(option.data('stock'));

    row.find('.product-id')
        .val(productId);

    row.find('.qty')
        .val(1);

    row.find('.unit-price')
        .val(option.data('price'));

    row.find('.discount-percent')
        .val(0);

    row.find('.discount-amount')
        .val(0);

    //------------------------------------------------
    // Append
    //------------------------------------------------

    $('#sale-items-body').append(row);

    renumberRows();

    updateInputNames();

    calculateRow($('#sale-items-body tr:last'));

}

//====================================================
// Renumber
//====================================================

function renumberRows()
{
    $('#sale-items-body tr').not('#empty-row').each(function(index){

        $(this)
            .find('.row-number')
            .text(index + 1);

    });

    itemIndex = $('#sale-items-body tr').not('#empty-row').length;

    updateInputNames();

}

//====================================================
// Update Laravel Input Names
//====================================================

function updateInputNames()
{
    $('#sale-items-body tr').not('#empty-row').each(function(index){

        $(this).find('.product-id')
            .attr('name','items['+index+'][product_id]');

        $(this).find('.qty')
            .attr('name','items['+index+'][qty]');

        $(this).find('.unit-price')
            .attr('name','items['+index+'][unit_price]');

        $(this).find('.discount-percent')
            .attr('name','items['+index+'][discount_percent]');

        $(this).find('.discount-amount')
            .attr('name','items['+index+'][discount_amount]');

    });

}

//====================================================
// Empty Row
//====================================================

function toggleEmptyRow()
{
    if($('#sale-items-body tr').not('#empty-row').length===0){

        $('#empty-row').show();

    }else{

        $('#empty-row').hide();

    }
}
//====================================================
// Calculate Row
//====================================================

function calculateRow(row)
{
    let qty = parseFloat(row.find('.qty').val()) || 0;
    let price = parseFloat(row.find('.unit-price').val()) || 0;
    let discountPercent = parseFloat(row.find('.discount-percent').val()) || 0;

    if (qty < 0) qty = 0;
    if (price < 0) price = 0;

    let gross = qty * price;

    let discountAmount = gross * (discountPercent / 100);

    let subtotal = gross - discountAmount;

    row.find('.discount-amount').val(discountAmount.toFixed(2));

    row.find('.subtotal').val(subtotal.toFixed(2));

    calculateTotals();
}

//====================================================
// Calculate Invoice Totals
//====================================================

function calculateTotals()
{
    let subtotal = 0;

    $('#sale-items-body tr').not('#empty-row').each(function () {

        subtotal += parseFloat($(this).find('.subtotal').val()) || 0;

    });

    $('#subtotal').val(subtotal.toFixed(2));

    $('#subtotal_display').val(subtotal.toFixed(2));

    //------------------------------------------------
    // Invoice Discount
    //------------------------------------------------

    let discountPercent = parseFloat($('#discount_percent').val()) || 0;

    let discountAmount = parseFloat($('#discount_amount').val()) || 0;

    if (document.activeElement.id === 'discount_percent') {

        discountAmount = subtotal * discountPercent / 100;

        $('#discount_amount').val(discountAmount.toFixed(2));

    }

    if (document.activeElement.id === 'discount_amount') {

        if (subtotal > 0) {

            discountPercent = (discountAmount / subtotal) * 100;

            $('#discount_percent').val(discountPercent.toFixed(2));

        }

    }

    //------------------------------------------------
    // After Discount
    //------------------------------------------------

    let afterDiscount = subtotal - discountAmount;

    if (afterDiscount < 0) {

        afterDiscount = 0;

    }

    //------------------------------------------------
    // Tax
    //------------------------------------------------

    let taxPercent = parseFloat($('#tax_percent').val()) || 0;

    let taxAmount = parseFloat($('#tax_amount').val()) || 0;

    if (document.activeElement.id === 'tax_percent') {

        taxAmount = afterDiscount * taxPercent / 100;

        $('#tax_amount').val(taxAmount.toFixed(2));

    }

    if (document.activeElement.id === 'tax_amount') {

        if (afterDiscount > 0) {

            taxPercent = (taxAmount / afterDiscount) * 100;

            $('#tax_percent').val(taxPercent.toFixed(2));

        }

    }

    //------------------------------------------------
    // Grand Total
    //------------------------------------------------

    let grandTotal = afterDiscount + taxAmount;

    $('#grand_total').val(grandTotal.toFixed(2));

    $('#grand_total_display').val(grandTotal.toFixed(2));

    calculatePayment();
}

//====================================================
// Payment
//====================================================

function calculatePayment()
{
    let grandTotal = parseFloat($('#grand_total').val()) || 0;

    let paid = parseFloat($('#paid_amount').val()) || 0;

    let balance = 0;

    let change = 0;

    if (paid >= grandTotal) {

        change = paid - grandTotal;

    } else {

        balance = grandTotal - paid;

    }

    $('#balance').val(balance.toFixed(2));

    $('#balance_display').val(balance.toFixed(2));

    $('#change_amount').val(change.toFixed(2));

    $('#change_display').val(change.toFixed(2));
}

//====================================================
// Validate Before Submit
//====================================================

$('form').on('submit', function (e) {

    let rows = $('#sale-items-body tr').not('#empty-row');

    if (rows.length === 0) {

        e.preventDefault();

       if (typeof Swal !== 'undefined') {

    Swal.fire({
        icon: 'warning',
        title: 'No Items',
        text: 'Please add at least one product.'
    });

} else {

    alert('Please add at least one product.');

}

    let valid = true;

    rows.each(function () {

        let qty = parseFloat($(this).find('.qty').val()) || 0;

        let stock = parseFloat($(this).find('.stock').text()) || 0;

        if (qty <= 0) {

            valid = false;

            if (typeof Swal !== 'undefined') {

    Swal.fire({
        icon: 'warning',
        title: 'No Items',
        text: 'Please add at least one product.'
    });

} else {

    alert('Please add at least one product.');

}

        if (qty > stock) {

            valid = false;

            if (typeof Swal !== 'undefined') {

    if (typeof Swal !== 'undefined') {

    Swal.fire({
        icon: 'warning',
        title: 'No Items',
        text: 'Please add at least one product.'
    });

} else {

    alert('Please add at least one product.');

}

                text: 'Requested quantity exceeds available stock.'

            });

            return false;
        }

    });

    if (!valid) {

        e.preventDefault();

        return false;
    }

});

//====================================================
// Initialize Existing Rows (Edit Mode)
//====================================================

$('#sale-items-body tr').not('#empty-row').each(function () {

    calculateRow($(this));

});

toggleEmptyRow();

</script>
@endpush