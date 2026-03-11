# ElevateSafe - Intelligent Maintenance Management Portal

ElevateSafe is a premium, AI-powered maintenance tracking system designed for high-performance building management teams. It transforms reactive maintenance into proactive intelligence.

---

## ✨ Key Features

### 🛡️ Security Hardened
- **CSRF Protection**: Comprehensive protection against Cross-Site Request Forgery.
- **XSS Remediation**: Strict input sanitization and output encoding using allow-lists.
- **Secure Sessions**: Hardened cookie settings (HttpOnly, SameSite=Lax, Secure).
- **Environment Variables**: Sensitive data is managed via `.env` files (powered by a custom PHP loader).

### 🤖 AI-Powered Intelligence
- **Automated Categorization**: Real-time analysis of maintenance reports using **Google Gemini AI**.
- **Smart Insights**: Automated executive summaries and trend analysis.
- **Technical Classification**: Automatic tagging of incidents (Electrical, Mechanical, Safety, etc.).

### 📈 Advanced Analytics
- **Dynamic Dashboard**: Real-time data visualization using **Chart.js**.
- **Performance Trends**: 6-month reliability tracking.
- **Fault Analysis**: Identify recurring hardware failures across specific elevator units.

### 💎 Premium Experience
- **Modern UI/UX**: Professional Design System with Inter typography.
- **Glassmorphism**: Sleek, frosted-glass interface elements.
- **Micro-animations**: Reactive UI with smooth transitions and entrance effects.

---

## 🚀 Quick Start

### 1. Requirements
- PHP 7.4+
- MySQL 5.7+
- Composer
- Gemini API Key (Optional for AI features)

### 2. Installation
```bash
git clone https://github.com/your-username/ElevateSafe.git
cd ElevateSafe
composer install
```

### 3. Configuration
Copy the environment template and add your credentials:
```bash
cp .env.example .env
```
Update `.env` with your database info and Gemini API Key.

### 4. Database Setup
Import the pre-configured schema:
1. Create a database `ElevateSafe`.
2. Import `file/database.sql` into phpMyAdmin.
3. Default Login: `user` / `user123`
4. Admin Password:`Admin123`

### 5. Demo Dataset (Optional)
To load sample maintenance records for testing charts and history views:
1. Import `file/demo_data.sql` after importing `file/database.sql`.
2. Refresh the dashboard and analytics pages.

---

## 📂 Project Structure
- `index.php`: Secure Login Portal.
- `page/dashboard.php`: Reactive breakdown reporting.
- `page/analytics.php`: AI-driven visual insights.
- `page/dataHistory.php`: Filterable maintenance logs.
- `include/ai_service.php`: Core Gemini API integration.

## 📄 License
This project is licensed under the MIT License.

---
*Elevate your maintenance standards with intelligence.*