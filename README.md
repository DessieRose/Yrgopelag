# 🌴 Yrgopelag Hotel Booking System

A dynamic, PHP-based hotel booking website for the fictional high-end resort **Yrgopelag**. This project simulates a real-world booking platform with features like room availability checking, activity add-ons, loyalty discounts, and external API integration for banking receipts.

## 🚀 Key Features

* **Dynamic Booking Engine:** Users can select rooms, dates, and add-on features. Costs are calculated in real-time using JavaScript.
* **Loyalty Program:** Recurring guests (identified by username) automatically receive a loyalty discount.
* **Admin Panel:** An administration interface to manage room prices, toggle activities (active/inactive), and update hotel star ratings.
* **Central Bank API:** Integration with an external "Central Bank" API to verify transactions and generate official receipts.
* **Database Driven:** All content (rooms, activities, settings) is dynamically fetched from a SQL database.

## 🛠️ Tech Stack

* **Backend:** PHP 8+
* **Database:** SQLite / MySQL (PDO)
* **Frontend:** HTML5, CSS3, Vanilla JavaScript
* **Dependencies:**
    * `guzzlehttp/guzzle` (For API requests)
    * `vlucas/phpdotenv` (For environment variables)

## 📂 Project Structure

```text
/
├── app/
│   ├── src/           # PHP Logic (Booking process, DB connection, Admin interface)
│   └── database/      # Database files
├── assets/
│   ├── styles/        # CSS files
│   ├── scripts/       # JavaScript files
│   └── images/        # Hotel and room images
├── views/             # Reusable HTML components (Header, Footer, Calendar)
├── index.php          # Landing Page
└── composer.json      # Dependencies
```
---

## ⚙️ Installation & Setup
### 1. Clone the repository
```text
git clone [https://github.com/YOUR_USERNAME/yrgopelag.git](https://github.com/YOUR_USERNAME/yrgopelag.git)
cd yrgopelag
```

### 2. Install Dependencies 
Ensure you have Composer installed, then run:
```text
composer install
```

### 3. Database Setup
* Ensure the hotel.db (SQLite) is present in the database directory.
* Check `app/src/autoload.php` (or your connection file) to ensure the path to the database is correct. 

### 4. Environment Configuration 
Create a .env file in the root directory and add your API keys and configuration.
view .env.example to see what you need.

### 5. Run the Server 
You can use the built-in PHP server for testing:
```text
php -S localhost:8000
```

## 🧪 Usage

### Booking a Room:
* Navigate to the Home page and click "Book Now".

* Enter your name. If you are a returning guest (check database for names), a discount is applied automatically.

* Select dates and extra activities (Scuba Diving, etc.).

* Submit the form to receive a booking confirmation and JSON receipt.

### Admin Panel:

* login to the admin page by clicking 'Owner Login'.

* User your name (ISLAND_USER) and API-KEY from your .env file.

* Use this to change room prices, update the hotel's star rating or activate/disable specific activities.

## 🔗 API Integration
This project communicates with the **Yrgopelag Central Bank**.
* Endpoint: POST https://www.yrgopelag.se/centralbank/receipt

* **Logic:** When a booking is confirmed locally, a request is sent to the bank. The bank validates the guest name, dates, and feature tiers (basic, standard, premium) before issuing a transaction ID.
