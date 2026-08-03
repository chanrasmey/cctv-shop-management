# CCTV Shop ERP
## Sales Module Specification

Version: v0.9.1

Last Updated: 2026-08-03

---

# Purpose

This document defines the complete functional specification for the Sales Module.

This specification is the authoritative reference for implementation.

If implementation differs from this specification, the specification takes precedence unless officially revised.

---

# Module Objectives

The Sales Module must provide a complete workflow for selling products while maintaining inventory integrity, financial accuracy, and transaction history.

The module must:

- Create Sales
- Edit Sales
- View Sales
- Print Invoices
- Cancel Sales
- Record Payments
- Deduct Inventory
- Record Stock Movements

---

# Sales Workflow

Customer

↓

Create Sale

↓

Add Products

↓

Calculate Totals

↓

Receive Payment

↓

Save Transaction

↓

Deduct Stock

↓

Create Stock Movement

↓

Generate Invoice

↓

Print Invoice

---

# Screen Layout

The Create Sale screen contains five sections.

1. Sale Information
2. Customer Information
3. Product Selection
4. Sale Items
5. Totals & Payment

---

# Section 1 — Sale Information

Fields

| Field | Required |
|------|----------|
| Invoice Number | Auto |
| Sale Date | Yes |
| Salesperson | Auto |
| Status | Auto |

Invoice numbers are generated automatically.

Users cannot manually edit invoice numbers.

---

# Section 2 — Customer

Fields

| Field | Required |
|------|----------|
| Customer | Yes |

Requirements

- Searchable Select2
- Existing customers only
- Future version may allow Walk-in Customer

---

# Section 3 — Product Selection

Requirements

Search products using Select2.

Display:

- SKU
- Barcode
- Product Name
- Current Stock
- Selling Price

Unavailable products should be clearly indicated.

Products with zero stock cannot be added.

---

# Product Selection Workflow

User selects product

↓

Read Product

↓

Validate stock

↓

Append row

↓

Reset product selector

↓

Focus selector

No page refresh.

---

# Section 4 — Sale Items

Each row contains

| Column |
|---------|
| Product |
| SKU |
| Quantity |
| Unit Price |
| Discount % |
| Discount Amount |
| Line Total |
| Delete |

Users may edit:

- Quantity
- Discount

Users may NOT edit:

- Historical Cost

---

# Quantity Rules

Quantity

- Must be greater than zero.
- Must not exceed available stock.
- Must be numeric.

---

# Price Rules

Selling price

Default

Product selling price.

Administrator may edit if permitted.

Historical selling price must be stored.

---

# Discount Rules

Discount may be:

- Percentage
- Amount

The server recalculates the final values.

---

# Totals Section

Display

- Subtotal
- Discount
- Tax
- Grand Total

All values update immediately in the browser.

The server recalculates all totals before saving.

---

# Payment Section

Fields

| Field |
|--------|
| Paid Amount |
| Balance |
| Change |

Rules

If Paid > Grand Total

Display Change.

If Paid < Grand Total

Display Balance.

Negative values are not allowed.

---

# Save Workflow

User clicks Save

↓

Validate Form

↓

Start Database Transaction

↓

Create Sale

↓

Create Sale Details

↓

Validate Inventory

↓

Deduct Stock

↓

Create Stock Movements

↓

Commit Transaction

↓

Redirect to Invoice

---

# Database Updates

Saving a Sale updates:

- Sales
- Sale Details
- Products (Stock)
- Stock Movements

No other tables should be modified.

---

# Inventory Rules

Stock decreases only after successful transaction commit.

Failed transactions must never modify stock.

Every stock deduction creates one Stock Movement record.

---

# Historical Data

Every Sale Detail stores:

- Product ID
- Product Name (optional snapshot if implemented later)
- Quantity
- Unit Price
- Unit Cost
- Discount
- Tax
- Profit

Historical values never change.

---

# Validation Rules

Customer required.

At least one product required.

Quantity > 0.

Quantity <= Stock.

Paid Amount >= 0.

Grand Total > 0.

Duplicate products should merge into one row instead of creating duplicate lines.

---

# Cancel Sale

Requirements

Canceling a Sale:

- Restores inventory.
- Creates reverse Stock Movements.
- Preserves historical transaction.
- Does not delete the Sale.

Status becomes:

Cancelled

---

# Print Invoice

Invoice includes

Company Logo

Company Information

Invoice Number

Customer

Sale Date

Items

Totals

Payment

Balance / Change

Customer Signature

Salesperson Signature

Footer Message

---

# Business Rules

Inventory must never become negative.

Totals are recalculated on the server.

Historical prices remain unchanged.

Historical costs remain unchanged.

Inventory updates occur only through StockService.

All operations execute inside DB::transaction().

---

# Error Handling

Rollback transaction if:

- Stock validation fails.
- Database insert fails.
- Stock update fails.
- Stock Movement fails.

Display user-friendly messages.

Log unexpected exceptions.

---

# Test Cases

Successful Sale

Sale with multiple items

Sale with discounts

Sale with tax

Insufficient stock

Duplicate products

Large quantity

Exact payment

Partial payment

Overpayment

Cancelled Sale

Invoice printing

Concurrent sales

---

# Acceptance Criteria

The Sales Module is considered complete only when:

- Create Sale works.
- Edit Sale works.
- View Sale works.
- Print Invoice works.
- Cancel Sale works.
- Stock is deducted correctly.
- Stock Movements are created.
- Totals are correct.
- Validation passes.
- Tests pass.
- Documentation is updated.
---

# User Stories

## S-001 Create a Sale

**As a** salesperson

**I want** to create a new sales invoice

**So that** I can sell products to a customer.

### Acceptance Criteria

- Customer can be selected.
- At least one product is required.
- Invoice number is generated automatically.
- Sale saves successfully.
- Inventory is reduced.
- Stock Movement records are created.
- Sale appears in Sales History.

---

## S-002 Add Products

**As a** salesperson

**I want** to add multiple products

**So that** I can complete customer orders.

### Acceptance Criteria

- Product is searchable.
- Product is added without page refresh.
- Duplicate products merge into one line.
- Quantity updates correctly.
- Totals recalculate automatically.

---

## S-003 Calculate Totals

**As a** salesperson

**I want** totals to calculate automatically

**So that** I know exactly how much the customer must pay.

### Acceptance Criteria

- Subtotal updates immediately.
- Discount updates immediately.
- Tax updates immediately.
- Grand Total updates immediately.
- Server recalculates before saving.

---

## S-004 Receive Payment

**As a** salesperson

**I want** to record customer payment

**So that** the invoice shows the correct balance or change.

### Acceptance Criteria

- Exact payment accepted.
- Partial payment calculates balance.
- Overpayment calculates change.
- Negative payment rejected.

---

## S-005 Print Invoice

**As a** salesperson

**I want** to print a professional invoice

**So that** I can provide proof of sale.

### Acceptance Criteria

- Company branding displayed.
- Customer information displayed.
- Items listed correctly.
- Totals correct.
- Payment information correct.
- Invoice printable without layout issues.

---

## S-006 Cancel Sale

**As a** manager

**I want** to cancel an existing sale

**So that** inventory and financial records remain accurate.

### Acceptance Criteria

- Sale status changes to Cancelled.
- Inventory is restored.
- Reverse Stock Movement created.
- Historical transaction retained.
- Sale is not physically deleted.

---

# Edge Cases

The implementation must correctly handle the following situations.

## Inventory

- Product stock equals zero.
- Product stock becomes insufficient before save.
- Concurrent sales of the same product.

---

## Customer

- Customer not selected.
- Customer deleted after historical sale.

Historical sales must remain valid.

---

## Products

- Duplicate product selection.
- Quantity set to zero.
- Quantity entered as decimal (if not allowed).
- Extremely large quantities.
- Product removed from catalog after historical sale.

Historical sales must remain intact.

---

## Pricing

- 100% discount.
- Zero discount.
- Large discount values.
- Tax disabled.
- Tax enabled.

---

## Payment

- Exact payment.
- Partial payment.
- Overpayment.
- Zero payment.
- Invalid negative payment.

---

## Transactions

- Database failure during save.
- Stock update failure.
- Stock Movement creation failure.
- Network interruption during submission.

Expected Result

Entire transaction rolls back.

No partial data remains.

---

# Non-Functional Requirements

## Performance

- Create Sale page loads in under 2 seconds under normal business usage.
- Product search should remain responsive with at least 10,000 products.
- Totals should update instantly after user input.

---

## Reliability

- No inventory corruption.
- No duplicate invoices.
- No duplicate Stock Movement records.
- Transactions must be atomic.

---

## Security

- Only authenticated users may create sales.
- Invoice numbers cannot be manipulated by users.
- Financial values are recalculated on the server.

---

## Maintainability

- Business logic remains inside Services.
- Controllers remain thin.
- Blade contains presentation logic only.
- JavaScript never becomes the source of truth.

---

## Auditability

Every completed sale must be traceable through:

- Sale
- Sale Details
- Stock Movement
- User
- Timestamp

Historical data must never be overwritten.

---

# Definition of Done

The Sales Module is considered complete only when all of the following are true:

## Functional

- Create Sale works.
- Edit Sale works.
- View Sale works.
- Cancel Sale works.
- Print Invoice works.

## Inventory

- Stock deducted correctly.
- Stock restored correctly.
- Stock Movement accurate.

## Financial

- Totals verified.
- Discounts verified.
- Taxes verified.
- Payments verified.
- Profit calculated correctly.

## Quality

- Unit Tests pass.
- Feature Tests pass.
- Manual Testing completed.
- No Critical defects.
- No High severity defects.

## Documentation

- Specification updated.
- Module Status updated.
- Changelog updated.
- Known Issues updated (if applicable).

Only after all of the above are satisfied may the Sales Module be marked **Complete**.