# CCTV Shop ERP
## System Architecture

Version: v0.9.1

---

# Architecture Overview

The CCTV Shop ERP follows a layered architecture based on the Service Layer pattern.

The primary objectives are:

- Maintainability
- Readability
- Reusability
- Data Integrity
- Transaction Safety
- Production Reliability

Business logic must remain independent from Controllers and Views.

---

# Golden Rules

The following rules are mandatory.

Violation of these rules requires architectural review.

---

## Rule 0

Business correctness is more important than technical elegance.

When there is a conflict between writing clever code and preserving correct business behavior, always choose the business-correct solution.

Inventory integrity and financial accuracy take priority over optimization or code brevity.

---

## Rule 1

### Controllers must remain thin.

Controllers are responsible only for:

- Request validation
- Calling Services
- Returning responses
- Redirects

Controllers must NOT contain business logic.

---

## Rule 2

### Business logic belongs inside Services.

Examples:

- Stock calculation
- Cost calculation
- Purchase processing
- Sales processing
- Return processing
- Financial calculation

Business logic must never be duplicated.

---

## Rule 3

### Every inventory change must pass through StockService.

No Controller, Model or Blade view may update stock directly.

Allowed:

Purchase

↓

StockService

↓

Stock Movement

Not allowed:

Controller

↓

Product::update(stock)

---

## Rule 4

### Every purchase, sale or return must execute inside a database transaction.

Transactions guarantee:

- Inventory consistency
- Financial consistency
- Atomic operations

---

## Rule 5

### Stock Movement is the source of truth.

Product stock is a cached value.

Inventory history is always determined from StockMovement.

---

## Rule 6

### Services may call other Services only when necessary.

Preferred flow:

PurchaseService

↓

StockService

↓

CostService

Avoid circular dependencies.

---

## Rule 7

### Reports never modify business data.

Reports are read-only.

Reports must never:

- Update stock
- Update sales
- Update purchases

---

## Rule 8

### Blade templates never contain business logic.

Blade may:

- Display data
- Format values
- Render UI

Blade must NOT:

- Calculate totals
- Calculate profit
- Update models

---

## Rule 9

### JavaScript is only for user interaction.

JavaScript may:

- Improve UX
- Validate forms
- Calculate temporary UI values

The server remains the final authority.

---

## Rule 10

### The server always recalculates totals.

Never trust:

- Price
- Discount
- Tax
- Grand Total
- Profit

Values received from the browser.

---

## Rule 11

### One responsibility per Service.

Examples:

PurchaseService

Responsible only for purchases.

StockService

Responsible only for inventory.

ReportService

Responsible only for reports.

---

## Rule 12

### Every financial operation must be traceable.

Every:

- Purchase
- Sale
- Return

Must leave an auditable record.

---

## Rule 13

### No duplicated implementations.

There must be only one active implementation for:

- Sales
- Sales Return
- Purchases
- Inventory

Old implementations must be removed after migration.

---

## Rule 14

### Database schema changes require review.

Never modify tables without:

- Backward compatibility
- Migration review
- Data preservation

---

## Rule 15

### Every milestone ends with validation.

Required validation:

composer dump-autoload

php artisan optimize:clear

php artisan route:list

php artisan test

---

## Rule 16

### Every completed milestone updates documentation.

Documentation is part of the source code.

It must always reflect the current implementation.

---

# Architecture Decision Records (ADR)

## ADR-001

Decision

Use Service Layer Architecture.

Reason

Keeps Controllers small and business logic reusable.

Status

Accepted

---

## ADR-002

Decision

Use Stock Movement as inventory history.

Reason

Provides a complete audit trail.

Status

Accepted

---

## ADR-003

Decision

Use Weighted Average Cost.

Reason

Provides stable inventory valuation.

Status

Accepted

---

## ADR-004

Decision

Use Blade + AdminLTE.

Reason

Simple, maintainable and suitable for business ERP.

Status

Accepted

---

## ADR-005

Decision

Use milestone-based development.

Reason

Ensures stable releases and easier quality control.

Status

Accepted

---

# Future Architecture

Planned improvements after ERP v1.0:

- Event system
- Audit logging
- Background jobs
- Payment history
- Accounting journals
- API layer
- Multi-branch support (optional)

These features belong to future releases and must not compromise ERP v1.0 stability.