<script>

document.addEventListener('DOMContentLoaded', function () {

    const tableBody = document.getElementById('purchaseItems');
    const addButton = document.getElementById('btnAddRow');

    //----------------------------------------------------------
    // Renumber rows
    //----------------------------------------------------------

    function renumberRows()
    {
        tableBody.querySelectorAll('tr').forEach((row,index)=>{

            row.querySelector('.row-number').innerText = index + 1;

        });
    }

    //----------------------------------------------------------
    // Calculate Row
    //----------------------------------------------------------

    function calculateRow(row)
    {
        let qty = parseFloat(row.querySelector('.qty').value) || 0;

        let cost = parseFloat(row.querySelector('.unit-cost').value) || 0;

        let discountPercent =
            parseFloat(row.querySelector('.discount-percent').value) || 0;

        let discountAmount =
            parseFloat(row.querySelector('.discount-amount').value) || 0;

        let total = qty * cost;

        total -= (total * discountPercent / 100);

        total -= discountAmount;

        if(total < 0)
            total = 0;

        row.querySelector('.subtotal').value = total.toFixed(2);

        calculateSummary();
    }

    //----------------------------------------------------------
    // Summary
    //----------------------------------------------------------

    function calculateSummary()
    {
        let subtotal = 0;

        document.querySelectorAll('.subtotal').forEach(item=>{

            subtotal += parseFloat(item.value) || 0;

        });

        document.getElementById('subtotal').value =
            subtotal.toFixed(2);

        //------------------------------------------------------

        let discountPercent =
            parseFloat(document.getElementById('discount_percent').value) || 0;

        let discountAmount =
            parseFloat(document.getElementById('discount_amount').value) || 0;

        let taxPercent =
            parseFloat(document.getElementById('tax_percent').value) || 0;

        //------------------------------------------------------

        let invoiceDiscount = subtotal * discountPercent / 100;

        invoiceDiscount += discountAmount;

        let afterDiscount = subtotal - invoiceDiscount;

        if(afterDiscount < 0)
            afterDiscount = 0;

        let taxAmount = afterDiscount * taxPercent / 100;

        document.getElementById('tax_amount').value =
            taxAmount.toFixed(2);

        let grandTotal = afterDiscount + taxAmount;

        document.getElementById('grand_total').value =
            grandTotal.toFixed(2);

        //------------------------------------------------------

        let paid =
            parseFloat(document.getElementById('paid_amount').value) || 0;

        let balance = grandTotal - paid;

        document.getElementById('balance').value =
            balance.toFixed(2);

    }

    //----------------------------------------------------------
    // Product Changed
    //----------------------------------------------------------

    function bindProduct(row)
    {
        let select = row.querySelector('.product-select');

        select.addEventListener('change',function(){

            let option =
                this.options[this.selectedIndex];

            row.querySelector('.sku').value =
                option.dataset.sku || '';

            row.querySelector('.current-stock').value =
                option.dataset.stock || '';

            row.querySelector('.unit-cost').value =
                option.dataset.price || 0;

            calculateRow(row);

        });

    }

    //----------------------------------------------------------
    // Bind Inputs
    //----------------------------------------------------------

    function bindInputs(row)
    {
        row.querySelectorAll(
            '.qty,.unit-cost,.discount-percent,.discount-amount'
        ).forEach(input=>{

            input.addEventListener('input',function(){

                calculateRow(row);

            });

        });

    }

    //----------------------------------------------------------
    // Remove Row
    //----------------------------------------------------------

    function bindRemove(row)
    {
        row.querySelector('.btnRemoveRow')
            .addEventListener('click',function(){

                if(tableBody.rows.length===1)
                {
                    alert('At least one item is required.');

                    return;
                }

                row.remove();

                renumberRows();

                calculateSummary();

            });

    }

    //----------------------------------------------------------
    // First Row
    //----------------------------------------------------------

    function bindRow(row)
    {
        bindProduct(row);

        bindInputs(row);

        bindRemove(row);
    }

    tableBody.querySelectorAll('tr').forEach(row=>{

        bindRow(row);

        calculateRow(row);

    });

    //----------------------------------------------------------
    // Add Row
    //----------------------------------------------------------

    addButton.addEventListener('click',function(){

        let row = tableBody.querySelector('tr').cloneNode(true);

        row.querySelectorAll('input').forEach(input=>{

            if(input.classList.contains('qty'))

                input.value = 1;

            else

                input.value='';

        });

        row.querySelector('.unit-cost').value=0;
        row.querySelector('.discount-percent').value=0;
        row.querySelector('.discount-amount').value=0;
        row.querySelector('.subtotal').value=0;

        row.querySelector('.product-select').selectedIndex=0;

        tableBody.appendChild(row);

        renumberRows();

        bindRow(row);

    });

    //----------------------------------------------------------

    document.getElementById('discount_percent')
        .addEventListener('input',calculateSummary);

    document.getElementById('discount_amount')
        .addEventListener('input',calculateSummary);

    document.getElementById('tax_percent')
        .addEventListener('input',calculateSummary);

    document.getElementById('paid_amount')
        .addEventListener('input',calculateSummary);

    //----------------------------------------------------------

    calculateSummary();

});

</script>