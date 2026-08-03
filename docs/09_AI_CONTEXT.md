# CCTV Shop ERP
## AI Development Context

Version: v0.9.1

Last Updated: 2026-08-03

---

# Purpose

This document provides permanent context for AI assistants working on the CCTV Shop ERP project.

Every AI must read this document before making code changes.

This document supplements:

- 00_PROJECT_OVERVIEW.md
- 01_ARCHITECTURE.md
- 03_BUSINESS_RULES.md
- 05_ROADMAP.md
- 10_MODULE_STATUS.md

---

# Project Summary

CCTV Shop ERP is a production-oriented ERP system for a single CCTV retail and installation business.

The project focuses on:

- Purchasing
- Inventory
- Sales
- Returns
- Reports
- Company Management

The project values stability and maintainability over rapid feature development.

---

# Technology Stack

Backend

- Laravel 13
- PHP 8.5

Frontend

- Blade
- AdminLTE
- Bootstrap
- jQuery
- Select2
- SweetAlert2

Database

- MySQL

Development

- Composer
- Vite
- Git
- GitHub

---

# Architecture

The project uses:

MVC

+

Service Layer

Business logic belongs inside Services.

Controllers remain thin.

Views never contain business logic.

---

# Service Structure

Current service organization:

```
app/Services/

Core/
Dashboard/
Finance/
Inventory/
Purchases/
Reports/
Sales/
```

Every new feature should follow the existing structure.

---

# Development Rules

Always preserve:

- Service Layer architecture
- Existing database schema
- Backward compatibility
- Inventory integrity
- Financial accuracy

Never redesign the project unless explicitly requested.

---

# Forbidden Changes

Do NOT:

- Replace Blade with Livewire
- Introduce Vue
- Introduce React
- Replace AdminLTE
- Rewrite completed modules
- Duplicate business logic
- Modify unrelated modules

---

# Business Priorities

Highest priority:

1. Inventory integrity
2. Financial correctness
3. Stable transactions
4. Reliable reports
5. Maintainable source code

Performance optimization is secondary.

---

# Source of Truth

Inventory

StockMovement

Financial Totals

Server-side calculations

Company Information

CompanySetting

Historical Prices

Transaction Details

---

# Current Project Status

Completed

- Authentication
- Dashboard
- Categories
- Brands
- Units
- Suppliers
- Customers
- Products
- Purchases
- Purchase Returns
- Company Settings

In Progress

- Sales Module

Pending

- Sales Return
- Reports Stabilization
- Finance
- QA

---

# Current Milestone

Milestone 1

Objective

Finish the Sales Module.

Tasks

- Create Sale
- Edit Sale
- View Sale
- Print Invoice
- Sales JavaScript
- SaleService stabilization
- SalesController stabilization
- Payment calculation
- Validation
- Stock deduction

Stop after completing the milestone.

Do not continue to the next milestone.

---

# AI Workflow

Before changing code:

1. Read all documentation.
2. Understand the current milestone.
3. Modify only the files required.
4. Preserve architecture.
5. Preserve business rules.
6. Explain every changed file.

---

# Validation Checklist

Every completed milestone should pass:

```
composer dump-autoload

php artisan optimize:clear

php artisan route:list

php artisan test

php artisan migrate --pretend
```

---

# Git Workflow

Recommended workflow:

```
Feature Branch

↓

Implementation

↓

Testing

↓

Code Review

↓

Git Commit

↓

Documentation Update

↓

Merge
```

Never work directly on the production release without review.

---

# Coding Philosophy

The project follows these principles:

- Simplicity over complexity
- Readability over cleverness
- Business correctness over optimization
- Maintainability over shortcuts
- Stability over unnecessary features

---

# Communication Rules

When assisting with development:

- Explain architectural decisions.
- Avoid unnecessary refactoring.
- Keep responses focused on the assigned milestone.
- Never introduce unrelated changes.
- Always preserve backward compatibility.

---

# Success Criteria

ERP Version 1.0 is complete when:

- Sales is fully operational.
- Sales Returns are complete.
- Inventory is validated.
- Reports are stable.
- Company Settings are fully integrated.
- Tests pass.
- Documentation is up to date.
- The application is suitable for daily production use.

---

# Final Instruction

If uncertain about any implementation, prioritize:

1. Business correctness.
2. Existing architecture.
3. Data integrity.

Never sacrifice these principles for convenience or code brevity.