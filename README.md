
# Lexa University Student ID Card Request System

## 📌 Project Overview

This is a **Laravel-based web application** built for **Lexa University** to automate student ID card requests and issuance.
The system allows students to log in with their matric number, submit ID card requests, upload photos, and track request status.
Administrators can review requests, approve or reject them, generate ID cards with QR codes, and manage records efficiently.

---

## 🎯 Features

### For Students

- Secure login using **Matric Number** + password.
- Submit ID card request (biodata auto-filled from records).
- Upload a passport photo.
- Track request status (Pending → Approved/Rejected → Printed).
- Get notified when ID card is ready for collection.
- Request for reprint in case of loss/damage.

### For Administrators

- Login using **Email + password**.
- Dashboard with statistics (total requests, approved, rejected, printed).
- Review, approve, or reject student requests.
- Generate ID cards with embedded **QR codes**.
- Print or export ID cards as PDF.
- Manage student records and reprint requests.
- Audit log for login and request activities.

---

## 👥 Roles

- **Student**

  - Logs in with Matric Number.
  - Submits ID card requests.
  - Uploads photo and tracks status.
- **Admin**

  - Logs in with Email.
  - Reviews and manages student requests.
  - Approves/rejects, generates, and prints ID cards.

---

## 🗂️ Tech Stack

- **Backend**: Laravel (PHP Framework)
- **Frontend**: Blade, TailwindCSS/Bootstrap, JavaScript
- **Database**: MySQL
- **Key Packages**:
  - `laravel/breeze` or `laravel/jetstream` → Authentication
  - `spatie/laravel-permission` → Roles & Permissions
  - `simplesoftwareio/simple-qrcode` → QR Code generation
  - `intervention/image` → Image handling (photos)
  - `barryvdh/laravel-dompdf` → PDF generation for ID cards

---

## ⚙️ Installation Guide

1. **Clone the repository**

   ```bash
   git clone https://github.com/your-username/lexa-id-system.git
   cd lexa-id-system
   ```
2. **Install dependencies**

   ```bash
   composer install
   npm install && npm run dev
   ```
3. **Environment setup**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. **Configure database in `.env`**Example:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=lexa_id_system
   DB_USERNAME=root
   DB_PASSWORD=
   ```
5. **Run migrations**

   ```bash
   php artisan migrate --seed
   ```
6. **Start development server**

   ```bash
   php artisan serve
   ```

   Access the system at [http://localhost:8000](http://localhost:8000).

---

## 🛠️ Troubleshooting (Imagick / GD Issue)

If you see this error while generating ID cards:

```
You need to install the imagick extension to use this back end
```

### Fix Option 1: Install Imagick (recommended)

- **Ubuntu/Debian**

  ```bash
  sudo apt-get install php-imagick
  sudo service apache2 restart
  ```
- **Windows (XAMPP/WAMP)**

  - Download `php_imagick.dll` matching your PHP version.
  - Add to `ext/` folder and enable in `php.ini`:
    ```ini
    extension=php_imagick.dll
    ```
  - Restart Apache.

### Fix Option 2: Force GD Driver

Update your Intervention/Image usage:

```php
use Intervention\Image\ImageManager;
$manager = new ImageManager(['driver' => 'gd']);
```

This avoids Imagick dependency but still generates images correctly.

---

## 📸 ID Card Features

- Includes **student photo**, **matric number**, **name**, **department**.
- Embedded **QR code** for verification.
- Printable/exportable in PDF format.

---

## 📝 License

This project is for **academic purposes** under Lexa University.
You may extend and adapt it for production use with proper security and compliance.

---

## 📩 Contact

For inquiries or contributions:
**Email:** Godfreyj.sule1@gmail.com
