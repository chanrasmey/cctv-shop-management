<div class="card card-primary">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-calculator mr-2"></i>

            Sale Totals

        </h3>

    </div>

    <div class="card-body">

        <div class="form-group">

            <label>Subtotal</label>

            <input type="text"
                   class="form-control text-right"
                   id="subtotal_display"
                   value="0.00"
                   readonly>

            <input type="hidden"
                   name="subtotal"
                   id="subtotal"
                   value="0">

        </div>

        <div class="form-group">

            <label>Discount (%)</label>

            <input type="number"
                   class="form-control text-right"
                   name="discount_percent"
                   id="discount_percent"
                   value="0"
                   min="0"
                   max="100"
                   step="0.01">

        </div>

        <div class="form-group">

            <label>Discount Amount</label>

            <input type="number"
                   class="form-control text-right"
                   name="discount_amount"
                   id="discount_amount"
                   value="0"
                   min="0"
                   step="0.01">

        </div>

        <div class="form-group">

            <label>Tax (%)</label>

            <input type="number"
                   class="form-control text-right"
                   name="tax_percent"
                   id="tax_percent"
                   value="0"
                   min="0"
                   max="100"
                   step="0.01">

        </div>

        <div class="form-group">

            <label>Tax Amount</label>

            <input type="number"
                   class="form-control text-right"
                   name="tax_amount"
                   id="tax_amount"
                   value="0"
                   min="0"
                   step="0.01">

        </div>

        <hr>

        <div class="form-group mb-0">

            <label class="font-weight-bold">

                Grand Total

            </label>

            <input type="text"
                   class="form-control form-control-lg text-right font-weight-bold bg-warning"
                   id="grand_total_display"
                   value="0.00"
                   readonly>

            <input type="hidden"
                   name="grand_total"
                   id="grand_total"
                   value="0">

        </div>

    </div>

</div>