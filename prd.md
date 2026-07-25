# Product Requirement Document (PRD)

# Brigida Finance
**Version:** 2.0 (Hackathon 24 Jam)

**Status:** Final — Ready for Development

**Project Type:** Personal Finance Management Web Application

**Tech Stack**
- Laravel 13
- Tailwind CSS
- Supabase (PostgreSQL)
- Blade Template
- Eloquent ORM
- LLM API (AI Financial Insight)

---

# 1. Overview

## Background

Managing personal finances is one of the biggest challenges faced by students, employees, and freelancers. Many people still record their finances manually—or do not record them at all—making it difficult to understand where their money is spent.

As a result, users often:
- Overspend before the end of the month
- Fail to save consistently
- Have no visibility into their spending habits

Brigida Finance is a lightweight personal finance tracker that combines simple budgeting with AI-generated financial insights, helping users understand their spending habits without manual analysis.

---

# 2. Vision

To become a simple, intelligent, and accessible personal finance platform that helps people build healthier financial habits.

---

# 3. Problem Statement

Users struggle to manage their personal finances because they lack an easy and structured system to record income and expenses, resulting in uncontrolled spending and no clear understanding of their financial behavior.

---

# 4. Goals

### Hackathon Goals (24 Hours)

- Ship a deployed, working full-stack MVP
- Demonstrate a real LLM API integration (not decorative)
- Clean, no-hardcode, documented codebase with commit history

### User Goals

- Record daily income and expenses easily
- Monitor monthly spending against a budget
- Get an AI-generated summary of spending behavior, in plain language

---

# 5. Target Users

## Primary User

### Students & Young Employees
Age: 18-27

Pain Points
- Monthly allowance/salary runs out quickly
- No spending records
- Don't understand *why* they overspend, just that they do

---

# 6. User Persona

**Name:** Andi Pratama
**Age:** 22
**Occupation:** University Student
**Income:** Rp2.500.000/month

**Goals:** Save money, control expenses, understand where money goes
**Frustrations:** Forgets to record expenses, doesn't know monthly spending pattern, money disappears unexpectedly

---

# 7. Value Proposition

Brigida Finance helps users track income and expenses in one place, and turns raw transaction data into a plain-language AI insight — no manual analysis needed.

---

# 8. Scope

## In Scope (MVP — 24 Hours)

1. Authentication (Register/Login/Logout)
2. Dashboard (Balance, Income, Expense, Budget summary)
3. Transaction Management (Add/Edit/Delete Income & Expense)
4. Categories (default seeded list)
5. Monthly Budget (manual input, single total — no auto-allocation)
6. Expense Monitoring (spending per category, simple progress bar)
7. **AI Financial Insight (LLM API)** — plain-language summary of the month's spending

## Explicitly Out of Scope (Post-Hackathon Roadmap)

- Payment Reminder
- Transaction Tags
- Export PDF
- Dark Mode
- Auto budget allocation by percentage
- Separate Monthly Report page (merged into Dashboard instead)
- Bank Integration / OCR Receipt Scanner / Investment Tracking

> Cutting these is intentional — a working AI-integrated MVP beats a half-finished feature-complete app when judged after 24 hours.

---

# 9. Features & Functional Requirements

## 9.1 Authentication

**User Story:** As a user, I want to register and login, so I can securely manage my personal finance.

FR-01 Users can register
FR-02 Users can login
FR-03 Users can logout
FR-04 Passwords are hashed
FR-05 Protected routes require authentication (middleware)

---

## 9.2 Dashboard

**User Story:** As a user, I want a quick overview of my finances, so I don't need to dig through transaction lists.

Components:
- Current Balance (Income − Expense)
- Total Income (this month)
- Total Expense (this month)
- Budget Remaining (Budget − Total Expense)
- AI Insight card (see 9.7)

FR-06 System calculates and displays current balance
FR-07 System calculates remaining budget

---

## 9.3 Transaction Management

**User Story:** As a user, I want to add, edit, and delete income/expense entries, so my records stay accurate.

Fields: `type` (income/expense), `amount`, `category_id`, `date`, `description`

FR-08 Users can add income
FR-09 Users can add expense
FR-10 Users can edit transactions
FR-11 Users can delete transactions

---

## 9.4 Categories

Default seeded categories: Food, Transportation, Shopping, Entertainment, Education, Bills, Healthcare, Others

FR-12 Categories are seeded on install (no manual CRUD needed for MVP — reduces scope)

---

## 9.5 Monthly Budget

**User Story:** As a user, I want to set a total monthly budget, so I know how much I have left to spend.

FR-13 Users can set one total budget amount per month
FR-14 System shows: Used / Remaining / Percentage used

> Simplified from v1.0: no per-category auto-allocation. One total number is enough for a 24-hour MVP and still supports the Expense Monitoring feature below.

---

## 9.6 Expense Monitoring

Display:
- Spending by category (simple list, sorted by highest)
- Budget usage (single progress bar: used vs. total)

FR-15 System aggregates expenses grouped by category for the current month

---

## 9.7 AI Financial Insight (LLM API) — Core Differentiator

**User Story:** As a user, I want an AI-generated summary of my spending, so I understand my habits without doing the analysis myself.

**How it works:**
1. User clicks "Analisa Keuanganku" on the Dashboard (or it auto-loads)
2. Backend aggregates: total income, total expense, spending per category, budget used %
3. Backend sends this structured summary (not raw transaction rows) to an LLM API with a system prompt instructing it to act as a friendly financial assistant
4. LLM returns a short, plain-language insight (2-4 sentences) — e.g. highlighting the largest expense category, whether they're on track with budget, and one actionable tip
5. Insight is displayed as a card on the Dashboard, cached per day to avoid repeated API calls

FR-16 System sends aggregated (not raw) financial data to the LLM API
FR-17 System displays the AI-generated insight on the Dashboard
FR-18 AI insight is cached (e.g. once per day) to control API cost/latency
FR-19 API key is stored in environment variables, never hardcoded

**Suggested LLM providers:** Anthropic Claude API, OpenAI API, or Google Gemini API — any works; pick based on what's fastest to integrate with existing keys.

---

# 10. Non-Functional Requirements

- **Performance:** Dashboard loads under 2 seconds (excluding first AI insight generation)
- **Security:** Password hashing, CSRF protection, SQL injection protection (Eloquent), Auth middleware, no hardcoded secrets (`.env` only)
- **Responsive:** Must work on desktop and mobile browsers
- **Resilience:** If the LLM API call fails or times out, the Dashboard must still render normally with a graceful fallback message (e.g. "Insight belum tersedia, coba lagi nanti") — the app must never crash because of the AI feature

---

# 11. User Flow

```text
Login
  │
  ▼
Dashboard ──────────────► AI Insight (async, cached)
  │
  ├──────────────┐
  ▼              ▼
Add Income   Add Expense
  │              │
  └──────┬───────┘
         ▼
  Database Updated (Supabase)
         │
         ▼
  Dashboard Recalculated
```

---

# 12. Database Design (Supabase / PostgreSQL)

## users
- id, name, email, password, created_at, updated_at

## categories
- id, name, icon, color

## transactions
- id, user_id, category_id, type, amount, description, transaction_date, created_at, updated_at

## budgets
- id, user_id, month, year, total_budget, created_at, updated_at

## ai_insights *(new — for caching)*
- id, user_id, month, year, content, created_at

---

# 13. Hackathon Timeline (24 Hours)

| Time Block | Task |
|---|---|
| 0:00 – 2:00 | Setup Supabase project, migrate schema, connect Laravel to Supabase |
| 2:00 – 6:00 | Auth (Breeze) + Transaction CRUD + Category seeding |
| 6:00 – 9:00 | Dashboard (balance, income, expense, budget bar) |
| 9:00 – 13:00 | AI Financial Insight — LLM API integration + caching + fallback handling |
| 13:00 – 17:00 | UI polish with Tailwind, responsive check |
| 17:00 – 20:00 | Testing, bug fixing, deploy to production |
| 20:00 – 22:00 | Buffer for unexpected issues (deployment, env config, etc.) |
| 22:00 – 24:00 | README, demo script, final submission |

---

# 14. Product Backlog

| Priority | Feature |
|---|---|
| Must Have | Login/Register |
| Must Have | Dashboard |
| Must Have | Add/Edit/Delete Transactions |
| Must Have | Categories (seeded) |
| Must Have | Monthly Budget (simple) |
| Must Have | **AI Financial Insight (LLM API)** |
| Should Have | Expense Monitoring (category breakdown) |
| Won't Have (this hackathon) | Reminder, Tags, Export PDF, Dark Mode |

---

# 15. Risks

| Risk | Mitigation |
|---|---|
| LLM API integration takes longer than expected | Start it right after core CRUD is done (block 9:00-13:00), not last; have a static fallback insight ready as backup |
| Supabase migration issues (as seen with prior MySQL/Railway setup) | Test connection early (block 0:00-2:00), don't leave DB setup for later |
| Running out of time for polish | Core features (auth, CRUD, dashboard, AI insight) are prioritized over nice-to-haves |
| LLM API cost/rate limits during demo | Cache insight per user per day (FR-18) |

---

# 16. Future Roadmap (Post-Hackathon)

## Version 1.1
- Payment Reminder
- Transaction Tags
- Export PDF
- Dark Mode

## Version 1.2
- Per-category auto budget allocation
- Multi Wallet
- Goal Saving

## Version 2.0
- Deeper AI Financial Assistant (chat-based, not just summary)
- Bank API Integration
- OCR Receipt Scanner

---

# 17. Success Definition

For this hackathon, Brigida Finance is considered successful when:

- The app is deployed and publicly accessible via URL
- A user can register, log in, and record income/expense
- The Dashboard reflects accurate real-time calculations
- The AI Insight feature returns a real, dynamically-generated response from an LLM API (not a static/hardcoded string)
- The codebase has clear commit history and no hardcoded secrets or values

---

# 18. SDGs Alignment

**SDG 12** — Responsible Consumption and Production: encourages responsible spending through budgeting and AI-driven awareness.

**SDG 11** — Sustainable Cities and Communities: helps individuals build stronger financial resilience.

---

# 19. Conclusion

Brigida Finance (v2.0) is a scoped-down, hackathon-ready MVP that keeps the core value proposition of v1.0 — simple budgeting and expense tracking — while adding a genuine LLM-powered financial insight feature. The scope has been deliberately reduced to what is achievable in 24 hours, prioritizing a fully working, deployed, AI-integrated product over a longer but incomplete feature list.
