# CCTV Shop ERP
## Development Roadmap

Version: v0.9.1

Last Updated: 2026-08-03

---

# Project Goal

Deliver a stable, production-ready CCTV Shop ERP suitable for daily business operations.

Version 1.0 focuses on stability, correctness, inventory integrity, reporting, and maintainability.

---

# Development Philosophy

The project is developed using milestone-based delivery.

Each milestone must satisfy:

- Functional completion
- Runtime validation
- Code review
- Documentation update
- Git checkpoint

No milestone is considered complete until all five conditions are satisfied.

---

# Version History

| Version | Status |
|----------|--------|
| v0.9.0 | Initial ERP Development |
| v0.9.1 | Project Stabilization |
| v0.9.2 | Sales Module Completion |
| v0.9.3 | Reports Stabilization |
| v1.0.0 | Production Release |

---

# Milestone 0

## Project Stabilization

Status

Completed (Pending Final Validation)

Objectives

- Remove fatal runtime errors
- Fix broken namespaces
- Repair routes
- Repair controller/service contracts
- Fix report runtime blockers

Deliverable

Stable Laravel project.

---

# Milestone 1

## Sales Module

Status

Current

Objectives

Complete:

- Create Sale
- Edit Sale
- View Sale
- Print Invoice
- SaleService
- SalesController
- Sales JavaScript
- Payment calculation
- Stock deduction
- Validation
- Duplicate prevention

Deliverable

Fully operational Sales module.

---

# Milestone 2

## Sales Return

Objectives

Complete:

- Sales Return
- Partial Return
- Full Return
- Refund calculation
- Stock restoration
- Return reports

Deliverable

Complete customer return workflow.

---

# Milestone 3

## Inventory Integrity

Objectives

- Weighted Average Cost verification
- Stock Movement verification
- Inventory reconciliation
- Concurrency safety
- Stock adjustment module

Deliverable

Reliable inventory engine.

---

# Milestone 4

## Reports

Objectives

Complete all reports:

- Purchase Report
- Sales Report
- Purchase Return Report
- Sales Return Report
- Inventory Report
- Stock Card
- Stock Valuation
- Profit & Loss
- Balance Sheet

Every report must support:

- Screen
- Print
- PDF
- Excel

Deliverable

Complete reporting system.

---

# Milestone 5

## Company Settings

Objectives

- Company branding
- Number prefixes
- Currency
- Timezone
- Invoice branding
- Receipt branding

Deliverable

Production-ready company configuration.

---

# Milestone 6

## Security

Objectives

- Roles
- Permissions
- Policies
- Authorization
- Middleware

Deliverable

Secure ERP.

---

# Milestone 7

## Quality Assurance

Objectives

- Unit Tests
- Feature Tests
- Manual Testing
- Performance Testing
- Regression Testing

Deliverable

Stable production build.

---

# Version 1.0 Release

Deliverables

- Stable ERP
- Installation Guide
- User Guide
- Sample Database
- Release Notes
- Deployment Guide

---

# Version 1.1 (Future)

Planned Features

- Accounting Journals
- Operating Expenses
- Payment History
- Audit Log
- Notifications
- Background Jobs
- API
- Multi-Branch Support

These features are intentionally excluded from Version 1.0 to maintain focus on a stable ERP release.

---

# Development Workflow

Every development cycle follows:

Planning

↓

Architecture Review

↓

Implementation

↓

Testing

↓

Code Review

↓

Documentation

↓

Git Commit

↓

Release

No feature skips this workflow.