# Web-Based Student ID Card Request System

## 📌 Project Overview

This project is a **Laravel-based web application** designed to automate the process of student ID card requests and issuance.
It replaces the manual, paper-based system with a digital platform that improves efficiency, reduces errors, and enhances both student and administrator experiences.

---

## 🎯 Features

### For Students:

- Register/Login securely.
- Submit ID card request form (biodata + photo upload).
- Track request status in real-time (Pending, Approved, Rejected).
- Receive notifications (email/in-app).
- Request reprint of ID card if lost/damaged.

### For Administrators:

- Secure login & role-based access control.
- Dashboard with statistics of all requests.
- View, approve, or reject ID card requests.
- Generate ID cards with QR codes.
- Print/export ID cards.
- Manage student records and reprints.

---

## 👥 Roles

- **Student** → Requests ID card, uploads photo, tracks request status.
- **Admin (Department Staff)** → Reviews requests, approves/rejects applications, generates and prints ID cards.

---

## 🗂️ Tech Stack

- **Backend**: Laravel (PHP Framework)
- **Frontend**: Blade, TailwindCSS/Bootstrap, JavaScript
- **Database**: MySQL
- **Packages**:
  - `laravel/breeze` or `laravel/jetstream` → Authentication
  - `spatie/laravel-permission` → Roles & Permissions
  - `simplesoftwareio/simple-qrcode` → QR Code generation
  - `intervention/image` → Image handling

---

## ⚙️ Installation Guide

1. Clone the repository:

   ```bash
   git clone https://github.com/your-username/student-id-system.git
   cd student-id-system
   ```
2. Install dependencies:

   ```bash
   composer install
   npm install && npm run dev
   ```
3. Create a `.env` file and configure your database:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Run migrations and seeders:

   ```bash
   php artisan migrate --seed
   ```
5. Start the development server:

   ```bash
   php artisan serve
   ```
6. Access the app in your browser at:
   [http://localhost:8000](http://localhost:8000)

---

## 📸 Screenshots (optional)

You can add screenshots here to show the student dashboard, admin dashboard, and ID card preview.

---

## 📝 License

This project is for **academic purposes** and showcases the use of Laravel in building a web-based administrative system.
Feel free to extend and adapt it for real-world deployment with proper security enhancements.

---

## 📩 Contact

For inquiries or contributions:
**Email:** Godfreyj.sule1@gmail.com`<p align="center"><a href="https://laravel.com" target="_blank">``<img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a>``</p>`
