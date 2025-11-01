Curacel Laravel Engineer Challenge — Claims Batching System

Overview

This project implements a claims batching system for insurers, designed to minimize processing costs while respecting each insurer’s operational constraints (capacity, batch size, and date preference).

It includes:
	•	A Vue 3 + Inertia frontend for submitting claims
	•	A Laravel REST API backend for batching, computing costs, and sending notifications
	•	Automated tests validating batching logic and insurer constraints



Tech Stack
	•	Backend: Laravel 11
	•	Frontend: Vue 3 (Inertia + Vite)
	•	Database: MySQL
	•	Mail: Laravel Mailer (log driver)
	•	Testing: PHPUnit



Problem Definition

Providers submit claims that contain multiple items.  Each claim includes:
	•	Encounter date & submission date
	•	Specialty & priority level
	•	Claim total (sum of item subtotals)

Insurers define rules that affect batching and cost:
	•	Daily claim capacity
	•	Minimum and maximum batch size
	•	Date preference (batch by encounter or submission date)
	•	Variable cost factors (time of month, specialty, priority level, claim value)

Goal: batch claims optimally to minimize insurer processing cost.



Solution Overview

API Endpoint

POST /api/claims
Receives claim data from frontend and performs:
	1.	Validates input (via StoreClaimRequest)
	2.	Creates claim & items
	3.	Batches claim optimally using BatchingService
	4.	Calculates processing cost
	5.	Sends insurer notification email

Example response:

{
  "message": "Claim submitted and batched successfully",
  "claim": {
    "id": 1,
    "provider_name": "Provider A",
    "batch": {
      "batch_code": "Provider A Oct 16 2025 #1",
      "status": "queued"
    }
  }
}



Batching Algorithm Explained

Objective

Assign incoming claims into batches that minimize processing cost while respecting insurer constraints.

Algorithm Steps
	1.	Input Handling: Compute total claim value as sum of all item subtotals.
	2.	Batch Selection Logic:
	•	Determine batch date based on insurer’s date_preference (encounter vs submission)
	•	Reuse an existing batch if:
	•	It matches insurer, provider, batch_date
	•	claims_count < max_batch_size
	•	Otherwise, create a new batch
	3.	Daily Capacity Guard: Count how many claims an insurer has accepted today. If daily_capacity is reached, throw RuntimeException → return HTTP 422.
	4.	Cost Formula:

cost = total_value * time_factor(day) * specialty_factor * priority_factor
        + value_slope * total_value

	•	time_factor interpolates between time_cost_min and time_cost_max
	•	specialty_factor & priority_factor come from insurer’s JSON multipliers

	5.	Batch Code Format: {provider_name} {Month} {Day} {Year} #{sequence}
	•	Example: Provider Z Oct 10 2025 #2
	6.	Complexity: O(1) per claim: one indexed query + minimal memory.



Implementation Summary

Key Files
	•	app/Http/Controllers/ClaimController.php — API controller
	•	app/Services/BatchingService.php - batching and cost logic
	•	app/Mail/InsurerBatchNotification.php — insurer notification email
	•	resources/js/Pages/SubmitClaim.vue — claim submission form
	•	resources/js/Pages/Batches.vue — batch listing page
	•	resources/js/Layouts/AppLayout.vue — navigation layout

Routes

Endpoint	Description
POST /api/claims	Submit claim & auto batch
GET /api/insurers	List active insurers
GET /api/batches	View batches (filter by date)
GET /claims	Inertia page for submitting claims
GET /batches	Inertia page for viewing batches




Tests

File: tests/Feature/ClaimFlowTest.php

Included Scenarios
	•	Happy Path: claim, items, and batch creation
	•	Batch Rollover: new batch after reaching max_batch_size
	•	Daily Capacity Guard: HTTP 422 when daily capacity exceeded

Run:

php artisan test

Expected:

PASS  Tests\\Feature\\ClaimFlowTest
  ✓ happy path creates claim items and batches
  ✓ claims reuse batch until max size then roll over
  ✓ daily capacity guard blocks when exceeded



Frontend Pages

SubmitClaim.vue
	•	Insurer code, provider name, encounter date inputs
	•	Specialty, priority level inputs
	•	Multi-item table (name, unit price, quantity)
	•	Read-only total field (auto-updates)
	•	Submit button calls /api/claims

Batches.vue
	•	Lists batches for a selected date
	•	Default: yesterday’s batches
	•	Optional date filter
	•	Fetches data from /api/insurers and /api/batches

AppLayout.vue

Top navigation bar:

Curacel Test | Submit Claim | View Batches



🧭 Running the Project

git clone <repo-url>
cd laravel-engr-test

composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm run dev
php artisan serve

Visit:
	•	http://localhost:8000/claims — Submit Claim form
	•	http://localhost:8000/batches — View Batches list


Evaluation Mapping

Criteria	Implementation
Algorithm Efficiency	O(1) lookup per claim; indexed DB queries
Adaptability	Dynamic insurer configs from DB
Code Quality	Clean structure (Controller, Service, Mail, Test)
Innovation	Tunable multipliers & linear cost slope
Documentation	Comprehensive README & tests



Deliverables Summary

Deliverable	Status
Simple frontend for claim submission
Fully tested REST API
Optimal batching & cost algorithm
Insurer notification email
Batches viewer UI
Documentation & algorithm explanation



Author: Christian Fega Onokharigho
Email: krixfega@gmail.com
Challenge: Curacel Senior Full-Stack Laravel Engineer Test