# CCTV Shop ERP
## Test Plan

Version: v0.9.1

Last Updated: 2026-08-03

---

# Purpose

This document defines the official testing strategy for CCTV Shop ERP.

Every milestone must pass this test plan before it is considered complete.

The objective is to ensure:

- Stable operation
- Inventory integrity
- Financial accuracy
- Business rule compliance
- Production readiness

---

# Testing Principles

Testing follows four levels.

1. Unit Testing
2. Feature Testing
3. Integration Testing
4. Manual Acceptance Testing

All four levels are required before releasing ERP Version 1.0.

---

# Validation Gate

Every completed milestone must pass:

```bash
composer dump-autoload

php artisan optimize:clear

php artisan route:list

php artisan test

php artisan migrate --pretend

npm run build
```

No milestone is complete if any validation step fails.

---

# Module Test Checklist

## Authentication

Verify

- Login
- Logout
- Password Reset
- Profile Update
- Password Change
- Unauthorized access

Expected Result

Authentication functions correctly.

---

## Dashboard

Verify

- Summary cards
- Statistics
- Navigation
- Latest transactions

Expected Result

Dashboard loads without errors.

---

## Categories

Verify

- Create
- Edit
- Delete
- Validation

Expected Result

CRUD works correctly.

---

## Brands

Verify

- CRUD
- Validation

---

## Units

Verify

- CRUD
- Validation

---

## Suppliers

Verify

- CRUD
- Search
- Validation

---

## Customers

Verify

- CRUD
- Search
- Validation

---

## Products

Verify

- Create Product
- Edit Product
- Delete Product
- Barcode
- Image Upload
- Stock Display
- Average Cost
- Export

Expected Result

Product data remains consistent.

---

# Purchase Module

Verify

- Create Purchase
- Draft Purchase
- Complete Purchase
- Edit Purchase
- Cancel Purchase
- Delete Draft Purchase

Check

- Inventory increases correctly.
- Stock Movement created.
- Average Cost updated.
- Totals correct.
- Supplier history preserved.

Expected Result

Purchase workflow is correct.

---

# Purchase Return

Verify

- Create Return
- Partial Return
- Full Return
- Cancel Return
- Print Return

Check

- Inventory decreases correctly.
- Stock Movement created.
- Returned quantity validation.
- Purchase history remains accurate.

Expected Result

Purchase Return works correctly.

---

# Sales Module

Verify

- Create Sale
- Edit Sale
- View Sale
- Print Invoice
- Payment
- Discount
- Tax
- Save
- Cancel

Check

- Inventory decreases.
- Stock Movement created.
- Historical unit cost stored.
- Totals recalculated by server.
- Invoice number unique.

Expected Result

Sales workflow is correct.

---

# Sales Return

Verify

- Partial Return
- Full Return
- Refund
- Cancel Return

Check

- Inventory restored.
- Returned quantity validation.
- Historical cost preserved.
- Refund calculation.

Expected Result

Sales Return works correctly.

---

# Inventory

Verify

- Stock In
- Stock Out
- Stock Movement
- Average Cost
- Inventory Balance

Expected Result

Inventory matches transaction history.

---

# Reports

Verify every report:

- Screen
- Print
- PDF
- Excel

Reports

- Purchase
- Sales
- Purchase Return
- Sales Return
- Inventory
- Stock Card
- Stock Valuation
- Profit & Loss
- Balance Sheet

Expected Result

Reports match database records.

---

# Company Settings

Verify

- Company Information
- Logo
- Invoice Branding
- Currency
- Timezone
- Prefix Settings

Expected Result

Changes appear throughout the system.

---

# Security

Verify

- Authentication
- Authorization
- Roles
- Permissions
- Policies

Expected Result

Unauthorized access is blocked.

---

# Database Integrity Tests

Verify

- Foreign Keys
- Cascade Rules
- Transactions
- Rollback

Expected Result

Database remains consistent.

---

# Performance Tests

Verify

- Dashboard loading
- Product search
- Sales screen
- Reports

Expected Result

Acceptable response time under normal business usage.

---

# Regression Tests

Whenever a module changes, retest:

- Inventory
- Purchases
- Purchase Returns
- Sales
- Sales Returns
- Reports

No unrelated module should break.

---

# Manual Acceptance Checklist

Before every release:

☐ Application starts correctly

☐ Login works

☐ Dashboard works

☐ Products work

☐ Purchases work

☐ Purchase Returns work

☐ Sales work

☐ Sales Returns work

☐ Inventory correct

☐ Reports correct

☐ Company branding correct

☐ PDF exports correct

☐ Excel exports correct

☐ No JavaScript errors

☐ No PHP errors

☐ No failed tests

☐ Documentation updated

---

# Release Criteria

ERP Version 1.0 may be released only if:

- All validation commands pass.
- All module tests pass.
- No Critical issues remain open.
- No High severity defects remain unresolved.
- Documentation is current.
- Final review completed.

---

# Defect Severity

Critical

- Application crash
- Data corruption
- Inventory corruption
- Financial calculation error

High

- Feature unusable
- Report incorrect
- Broken workflow

Medium

- Minor feature issue
- UI inconsistency
- Non-critical validation problem

Low

- Cosmetic issue
- Documentation issue
- Minor usability improvement

---

# Testing Responsibility

Developer

- Unit Tests
- Feature Tests
- Code Validation

Reviewer

- Architecture Review
- Business Rule Review

Product Owner

- Manual Acceptance Testing
- Business Workflow Verification

Release Approval

- Final QA
- Documentation Review
- Git Tag
- Production Deployment