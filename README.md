# Scholarship Management System

A comprehensive Laravel-based Scholarship Management System with role-based authentication (Admin & Student), application processing, award management, and disbursement tracking.

## 🚀 Features

- **Role-based Authentication** (Admin & Student)
- **Scholarship Management** (Create, view, update scholarships)
- **Application System** (Apply, track status, upload documents)
- **Award Management** (Approve applications, create awards)
- **Disbursement Tracking** (Schedule payments, upload receipts)
- **Reporting** (Scholarship and award reports)

## 📋 Prerequisites

- PHP 8.1+
- Composer
- MySQL 5.7+
- Node.js & NPM
- Git

## 🛠️ Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/chamithshirantha/Scholarship-Management-
   cd scholarship-management


Database Setup
   The project includes a SQL backup file:

   Location: database/scholarship_backup.sql

   Contains sample data with:

   Admin and student users

   Scholarship programs

   Cost categories

   Sample applications

API Testing with Postman
   Import Postman Collection

   File: postman/Scholarship_Management.postman_collection.json

   Import into Postman

   Setup Environment

   Create new environment in Postman

   Add variable: base_url = http://localhost:8000/api

Test Authentication

   First, run the Register Student or Register Admin request

   Copy the authentication token from response

   Set as environment variable: auth_token

   Test API Endpoints

   Use the token in Authorization header: Bearer {auth_token}

   Test student endpoints (scholarships, applications)

   Test admin endpoints (manage applications, create awards)