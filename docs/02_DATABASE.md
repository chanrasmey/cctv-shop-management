# CCTV Shop ERP
## Database Design

Version: v0.9.1

Last Updated: 2026-08-03

---

# Purpose

This document describes the database design of the CCTV Shop ERP.

It provides:

- Table overview
- Relationships
- Data ownership
- Business purpose
- Design principles

This document must remain synchronized with database migrations.

---

# Database Engine

Database

MySQL

Character Set

utf8mb4

Collation

utf8mb4_unicode_ci

Framework

Laravel 13

ORM

Eloquent ORM

---

# Database Principles

The database is designed according to the following principles.

- Data integrity
- Referential integrity
- Transaction safety
- Historical accuracy
- Business traceability

Historical records must never change because master data changes.

---

# Master Data

## Categories

Purpose

Organize products into logical groups.

Relationship

Category

↓

Products

---

## Brands

Purpose

Store manufacturer information.

Relationship

Brand

↓

Products

---

## Units

Purpose

Store product measurement units.

Examples

- PCS
- Box
- Meter

Relationship

Unit

↓

Products

---

## Suppliers

Purpose

Store supplier information.

Relationship

Supplier

↓

Purchases

---

## Customers

Purpose

Store customer information.

Relationship

Customer

↓

Sales

↓

Sales Returns

---

## Products

Purpose

Central inventory item.

Key Data

- SKU
- Barcode
- Product Name
- Buy Price
- Sell Price
- Average Cost
- Current Stock

Relationships

Category

↓

Product

↓

Purchase Details

↓

Sale Details

↓

Stock Movements

---

# Purchasing

## Purchases

Purpose

Purchase document header.

Contains

- Purchase Number
- Supplier
- Purchase Date
- Status
- Totals

Relationship

Purchase

↓

Purchase Details

---

## Purchase Details

Purpose

Purchased items.

Contains

- Product
- Quantity
- Unit Price
- Discount
- Total

Relationship

Purchase

↓

Purchase Details

↓

Product

---

## Purchase Returns

Purpose

Return purchased items to supplier.

Relationship

Purchase

↓

Purchase Return

↓

Purchase Return Details

---

# Sales

## Sales

Purpose

Sales invoice header.

Contains

- Invoice Number
- Customer
- Sale Date
- Totals
- Payment
- Status

Relationship

Sale

↓

Sale Details

---

## Sale Details

Purpose

Sold products.

Contains

- Product
- Quantity
- Selling Price
- Historical Cost
- Discount
- Profit

Historical cost must never change after the sale.

---

## Sales Returns

Purpose

Customer return transactions.

Relationship

Sale

↓

Sales Return

↓

Sales Return Details

Returned quantity cannot exceed sold quantity.

---

# Inventory

## Stock Movement

Purpose

Permanent inventory ledger.

Every inventory change creates one Stock Movement.

Movement Types

- Purchase
- Purchase Return
- Sale
- Sales Return
- Stock Adjustment

Stock Movement is the official inventory history.

---

# Company

## Company Settings

Purpose

Store system-wide company configuration.

Contains

- Company Name
- Logo
- Address
- Phone
- Email
- Currency
- Timezone
- Invoice Prefix
- Purchase Prefix

Company Settings are the single source of company information.

---

# Relationships

```
Category
    │
    ▼
 Product
    ▲
    │
Brand

Product
│
├──────────────┐
│              │
▼              ▼
Purchase Detail    Sale Detail
│              │
▼              ▼
Purchase      Sale
│              │
▼              ▼
Purchase Return    Sales Return

Product
│
▼
Stock Movement
```

---

# Transaction Flow

Purchase

↓

Purchase Details

↓

Stock Movement

↓

Product Stock

---

Sale

↓

Sale Details

↓

Stock Movement

↓

Product Stock

---

Purchase Return

↓

Purchase Return Details

↓

Stock Movement

↓

Product Stock

---

Sales Return

↓

Sales Return Details

↓

Stock Movement

↓

Product Stock

---

# Historical Data Policy

Historical transactions must preserve:

- Unit Price
- Unit Cost
- Discount
- Tax
- Profit

Changes to product prices must never modify historical transactions.

---

# Primary Keys

Every table uses:

```
id
```

as the primary key unless explicitly documented otherwise.

---

# Foreign Keys

Relationships should enforce:

- Referential integrity
- Cascade restrictions where appropriate
- Business consistency

---

# Indexing Strategy

Indexes should exist for:

- SKU
- Barcode
- Invoice Number
- Purchase Number
- Foreign Keys
- Dates used in reports

---

# Audit Requirements

Every operational transaction should be traceable through:

- Stock Movement
- Purchase History
- Sales History
- Return History

Future versions may include a dedicated audit log.

---

# Future Database Expansion

Planned for Version 1.1

- Accounting Journals
- Operating Expenses
- Payment History
- Notification Queue
- Audit Log
- API Tokens
- Multi-Branch Support

These additions should preserve backward compatibility with Version 1.0.