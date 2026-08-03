# CCTV Shop ERP
## Business Rules

Version: v0.9.1

Last Updated: 2026-08-03

---

# Purpose

This document defines the business rules governing all ERP operations.

These rules take precedence over implementation details.

If code conflicts with these rules, the business rules are considered correct.

---

# Inventory Rules

## Rule 1

Inventory can only change through approved business transactions.

Allowed:

- Purchase
- Purchase Return
- Sale
- Sales Return
- Stock Adjustment

No other operation may modify inventory.

---

## Rule 2

Every inventory movement must create a Stock Movement record.

Stock Movement is the permanent inventory history.

---

## Rule 3

Product stock is a calculated operational value.

Stock Movement is the official audit record.

---

## Rule 4

Negative stock is not allowed.

Sales cannot exceed available inventory.

---

## Rule 5

Inventory changes must occur inside a database transaction.

---

# Purchasing Rules

## Rule 6

Purchases increase inventory only after completion.

Draft purchases never affect stock.

---

## Rule 7

Purchase cancellation reverses inventory only when permitted.

Completed purchases require validation before cancellation.

---

## Rule 8

Purchase Returns reduce inventory.

Returned quantity cannot exceed the remaining purchased quantity.

---

## Rule 9

Supplier information must remain linked to every purchase.

Historical purchases must never lose supplier references.

---

# Sales Rules

## Rule 10

Sales reduce inventory only after successful completion.

---

## Rule 11

A sale cannot proceed if inventory is insufficient.

---

## Rule 12

Every sale stores the historical selling price and unit cost.

Future price changes must not affect historical transactions.

---

## Rule 13

Invoice numbers are unique.

Invoice numbers must never be reused.

---

## Rule 14

Discounts and taxes are calculated on the server.

Client-side calculations are for display only.

---

# Sales Return Rules

## Rule 15

Returned quantity cannot exceed sold quantity.

---

## Rule 16

Sales Returns restore inventory.

---

## Rule 17

Sales Returns preserve historical unit cost.

Current product cost must not replace historical values.

---

# Financial Rules

## Rule 18

Every completed transaction must be traceable.

---

## Rule 19

Financial totals are recalculated on the server.

The browser is never trusted.

---

## Rule 20

Profit calculations use historical unit cost.

---

# Company Rules

## Rule 21

Company Settings are the single source of company information.

Legacy company records must not be used.

---

## Rule 22

Company branding applies to:

- Invoice
- Reports
- Print
- PDF

---

# Reporting Rules

## Rule 23

Reports are read-only.

Reports never modify business data.

---

## Rule 24

Reports always use database values.

Never calculate financial totals in Blade templates.

---

# Security Rules

## Rule 25

Every business transaction records the responsible user.

---

## Rule 26

Unauthorized users cannot modify financial transactions.

---

# Data Integrity Rules

## Rule 27

Every business transaction executes inside a database transaction.

---

## Rule 28

Database consistency is more important than application speed.

---

## Rule 29

Business correctness always takes priority over convenience.

---

## Rule 30

ERP data must always remain auditable.

Every purchase, sale, return, and inventory movement must be traceable from creation to completion.

---

# Version 1.0 Principles

ERP Version 1.0 prioritizes:

- Inventory Accuracy
- Financial Accuracy
- Stable Operations
- Reliable Reports
- Maintainable Code

Advanced accounting features belong to Version 1.1 unless required by business operations.