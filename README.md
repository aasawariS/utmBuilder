# 🚀 Laravel UTM Generator

A simple Laravel application to **generate UTM links** for marketing campaigns.

* Enter a single URL → instantly generate a UTM-tagged link.
* Paste a whole paragraph → it will find all URLs and replace them with UTM-tagged versions.
* Stores generated UTMs into a **SingleStore database** for tracking.

---

## 📋 Features

* 🎯 Generate UTMs with parameters:

  * **Author** → `utm_source`
  * **Resource Type** → `utm_medium`
  * **Campaign / Theme** → `utm_campaign`
  * **Title Slug** → `utm_content`
  * **Keywords (optional)** → `utm_term`
* 📝 Two modes:

  1. **Single URL mode** – paste one link.
  2. **Paragraph mode** – paste a block of text, auto-detects & replaces all links.
* 📦 Built with **Laravel** + **SingleStore**.
* 💾 All generated links are stored in the database.
* 🖥️ Clean UI with tabs, copy buttons, and preview.

---

## ⚡ Requirements

Make sure you have these installed:

* [PHP 8.1+](https://www.php.net/downloads)
* [Composer](https://getcomposer.org/download/)
* [Node.js & NPM](https://nodejs.org/) (optional, if you want to recompile assets)
* A [SingleStoreDB](https://www.singlestore.com/) cluster

---

## 🔧 Installation

1. **Clone the repo / unzip**

   ```bash
   git clone https://github.com/yourusername/laravel-utm-generator.git
   cd laravel-utm-generator
   ```

2. **Install dependencies**

   ```bash
   composer install
   npm install && npm run build   # optional (for assets)
   ```

3. **Copy the `.env` file**

   ```bash
   cp .env.example .env
   ```

4. **Update your SingleStore database connection** in `.env`:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=svc-xxxxxxx.svc.singlestore.com
   DB_PORT=3306
   DB_DATABASE=demoApp
   DB_USERNAME=admin
   DB_PASSWORD=your_password_here
   ```

5. **Run migrations** (this creates all tables)

   ```bash
   php artisan migrate
   ```

6. **Start the app**

   ```bash
   php artisan serve
   ```

7. Open in browser 👉 [http://127.0.0.1:8000/utm](http://127.0.0.1:8000/utm)

---

## 🖥️ Usage

### 🔹 Single URL Mode

1. Select **Single URL** tab.
2. Paste your base URL.
3. Fill in UTM parameters (Author, Campaign, etc).
4. Click **Generate UTM Link**.
5. Copy your generated link using the **Copy** button.

### 🔹 Paragraph Mode

1. Select **Paragraph** tab.
2. Paste a paragraph with one or more links.
3. Fill in UTM parameters.
4. Click **Replace Links with UTMs**.
5. All links in the paragraph will be replaced with UTM-tagged versions.

---

## 📊 Database

All generated links are saved into the **utm_links** table in SingleStore:

* `author`, `title`, `slug`, `resource_type`, `campaign`
* `original_url`, `utm_url`, `context_text` (paragraph snippets)

---

## 🚀 Deployment

* For production, configure a web server (Nginx/Apache) to serve Laravel.
* Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`.
* Ensure your app server’s IP is whitelisted in your SingleStore cluster.

---

## 📌 Example

Input:

```
Base URL: https://www.singlestore.com/
Author: test
Resource Type: blog
Campaign: october2025
Slug: utm-demo
```

Output:

```
https://www.singlestore.com/?utm_source=test&utm_medium=blog&utm_campaign=october2025&utm_content=utm-demo
```

---

## 🛠️ Tech Stack

* Laravel 10
* TailwindCSS
* SingleStore (MySQL-compatible)

---

## 🤝 Contributing

Pull requests welcome!

---

## 📄 License

MIT License.

---
