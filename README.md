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
git clone https://github.com/DessieRose/Yrgopelag.git
cd Yrgopelag
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


## Code review:
- functions.php:10 Nice function (getRoomAvailability) with descriptive name. But instead of having one large function, maybe you could consider to create a separate function for "Fetch all bookings for the room in January 2026" and nest it in to make the code cleaner?
-  functions.php:61 The same with the function calculateTotalCost. Consider to create a separate function for "// Use the global $database connection"?
-  login.php:25 Great login file! Maybe row 24-32 could be moved to a view file instead, which requires in the login logics, and letting the app files only handle the logics?
-  process_booking.php:23 Nice use of try and catch to handle errors and prevent database registration if the booking fails. But instead of a single large try, you could consider using a session variable for error, redirect user to booking form with header(Location...), then use exit or die to stop the script?
-  process_booking.php:25 Is it neccessary to use htmlspecialchars on input data before inserting it into database? I've learned during project that data inserted into database shouldn't get sanitized before. Instead it should get sanitized on output/when you fetch data from database and render it out in HTML.
-  index.php:16 Great room presentation with supernice color scheme! Maybe you could use a foreach loop to present rooms? To make it more dynamic if rooms are added or closed? This would also make your code more DRY.
-  index.php:17-25-33-60: Minor thing, what about using <article> instead of <div>? 
