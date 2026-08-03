# CCTV Shop ERP
## Coding Standards

Version: v0.9.1

Last Updated: 2026-08-03

---

# Purpose

This document defines the official coding standards for the CCTV Shop ERP project.

Every contributor, whether human or AI, must follow these standards.

The goals are:

- Consistency
- Readability
- Maintainability
- Production-quality code
- Long-term scalability

---

# General Principles

The project values:

- Simplicity
- Readability
- Maintainability
- Correctness
- Consistency

Always choose clear and understandable code over clever or complex solutions.

---

# Laravel Standards

Follow Laravel best practices whenever possible.

Use:

- Dependency Injection
- Route Model Binding
- Form Requests
- Eloquent Relationships
- Database Transactions
- Service Layer

Avoid unnecessary custom implementations when Laravel already provides a standard solution.

---

# Folder Organization

Controllers

```
app/Http/Controllers
```

Models

```
app/Models
```

Requests

```
app/Http/Requests
```

Services

```
app/Services
```

Views

```
resources/views
```

Reports

```
resources/views/reports
```

Documentation

```
docs/
```

---

# Controller Standards

Controllers must remain thin.

Controllers should only:

- Validate requests
- Call Services
- Return Views
- Return Redirects
- Return JSON responses

Controllers must never:

- Calculate totals
- Update stock directly
- Execute business rules
- Perform financial calculations

---

# Service Standards

Every business operation belongs in a Service.

Examples:

PurchaseService

- Create Purchase
- Update Purchase
- Cancel Purchase

SaleService

- Create Sale
- Update Sale
- Cancel Sale

StockService

- Stock In
- Stock Out
- Stock Validation
- Stock Movement

Services should have a single responsibility.

---

# Model Standards

Models represent data.

Models should contain:

- Relationships
- Accessors
- Mutators
- Scopes
- Small helper methods

Models should not contain complex business workflows.

---

# Blade Standards

Blade templates are presentation only.

Allowed:

- Loops
- Conditions
- Components
- Formatting
- UI rendering

Not allowed:

- Database queries
- Business calculations
- Inventory updates
- Financial calculations

---

# JavaScript Standards

JavaScript is responsible only for user interaction.

Allowed:

- Form validation
- Dynamic tables
- UI calculations
- AJAX
- Select2
- SweetAlert2

Not allowed:

- Financial authority
- Inventory authority

The server is always the source of truth.

---

# Database Standards

Every purchase, sale or return must execute inside:

```
DB::transaction()
```

Never update multiple related tables outside a transaction.

---

# Stock Standards

Inventory changes only through:

- Purchase
- Purchase Return
- Sale
- Sales Return
- Stock Adjustment

Every inventory change creates a StockMovement record.

Never update product stock directly from Controllers or Blade templates.

---

# Error Handling

Always:

- Validate input
- Throw meaningful exceptions
- Roll back failed transactions
- Return user-friendly messages
- Log unexpected errors

Never ignore exceptions silently.

---

# Naming Conventions

Controllers

```
PurchaseController
SalesController
```

Services

```
PurchaseService
SaleService
StockService
```

Requests

```
StorePurchaseRequest
UpdateSaleRequest
```

Models

```
Purchase
Sale
Product
```

Variables

Use descriptive names.

Good

```
$totalAmount
$availableStock
$purchaseReturn
```

Avoid

```
$a
$b
$tmp
$data1
```

---

# Method Standards

Methods should:

- Do one thing
- Be easy to understand
- Have clear names

Good

```
calculateGrandTotal()

restoreStock()

validateInventory()
```

Avoid

```
processEverything()
```

---

# Database Queries

Prefer:

- Eloquent
- Query Builder

Avoid raw SQL unless necessary for performance.

Always eager-load relationships when appropriate to avoid N+1 query problems.

---

# Transactions

Wrap business operations in transactions.

Example:

Purchase

↓

Purchase Details

↓

Stock Update

↓

Stock Movement

↓

Commit

If any step fails:

Rollback everything.

---

# Comments

Write comments only when they explain business intent.

Avoid comments that simply repeat the code.

Good:

```php
// Preserve historical unit cost for future profit calculations.
```

Avoid:

```php
// Increment quantity.
$qty++;
```

---

# Git Commit Standards

Use clear commit messages.

Good:

```
Milestone 1 - Stabilize Sales Module

Fix Purchase Return Routing

Implement Stock Adjustment
```

Avoid:

```
update

fix

changes

test
```

---

# Pull Request Checklist

Before merging:

- Code compiles.
- Tests pass.
- Routes load correctly.
- Transactions are safe.
- Documentation updated.
- No unrelated changes included.

---

# AI Development Rules

AI assistants must:

- Read documentation before coding.
- Modify only files required for the assigned milestone.
- Preserve architecture.
- Preserve business rules.
- Explain all changed files.
- Stop after completing the assigned task.

---

# Quality Checklist

Every completed feature must satisfy:

- Business rules followed
- Architecture preserved
- Inventory integrity maintained
- Financial calculations verified
- Documentation updated
- Git committed
- Code reviewed

---

# Final Principle

The CCTV Shop ERP is a production system.

Every code change must improve one or more of:

- Stability
- Maintainability
- Accuracy
- Readability
- Reliability

Never sacrifice business correctness for shorter or more clever code.