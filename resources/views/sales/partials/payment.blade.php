<div class="card card-success">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-money-bill-wave mr-2"></i>

            Payment

        </h3>

    </div>

    <div class="card-body">

        <div class="form-group">

            <label>Payment Method</label>

            <select name="payment_method"
                    id="payment_method"
                    class="form-control">

                <option value="Cash">Cash</option>
                <option value="ABA">ABA KHQR</option>
                <option value="Bank">Bank Transfer</option>
                <option value="Credit">Credit</option>

            </select>

        </div>

        <div class="form-group">

            <label>Paid Amount</label>

            <input type="number"
                   class="form-control form-control-lg text-right"
                   name="paid_amount"
                   id="paid_amount"
                   value="0"
                   min="0"
                   step="0.01">

        </div>

        <div class="form-group">

            <label>Balance</label>

            <input type="text"
                   class="form-control text-right"
                   id="balance_display"
                   value="0.00"
                   readonly>

            <input type="hidden"
                   name="balance"
                   id="balance"
                   value="0">

        </div>

        <div class="form-group">

            <label>Change</label>

            <input type="text"
                   class="form-control text-right"
                   id="change_display"
                   value="0.00"
                   readonly>

            <input type="hidden"
                   name="change_amount"
                   id="change_amount"
                   value="0">

        </div>

        <div class="form-group">

            <label>Status</label>

            <select name="status"
                    class="form-control">

                <option value="Draft">Draft</option>
                <option value="Pending">Pending</option>
                <option value="Completed" selected>
                    Completed
                </option>

            </select>

        </div>

        <div class="form-group mb-0">

            <button type="submit"
                    class="btn btn-success btn-block">

                <i class="fas fa-save"></i>

                Save Sale

            </button>

        </div>

    </div>

</div>