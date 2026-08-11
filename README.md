# 🪄 **Remove Background System**

A modern **Remove Background System** built with **PHP**, **MySQL**, **HTML**, **CSS**, and **JavaScript**.
Upload a photo, remove the background in one click, and download a clean **transparent PNG** — with a built-in English User Manual at the top of the page.

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-Optional-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/JavaScript-ES_Modules-F7DF1E?style=for-the-badge&logo=javascript&logoColor=111" alt="JavaScript">
  <img src="https://img.shields.io/badge/XAMPP-Apache-FB7A24?style=for-the-badge&logo=xampp&logoColor=white" alt="XAMPP">
  <img src="https://img.shields.io/badge/License-MIT_Non--Commercial-2dd4bf?style=for-the-badge" alt="License">
</p>

✨ Feel free to explore, contribute, and enhance the project! 🚀

---

## ✨ **Features**

- 📤 **Easy Upload** — click **Choose Image** or drag & drop a photo onto the Original panel
- 🖼️ **Format Friendly** — supports **JPG**, **PNG**, and **WEBP** (max **10 MB**)
- ✂️ **One-Click Removal** — AI-assisted background removal with a local fallback
- 🏁 **Transparent PNG** — preview on a checkered canvas, then download a clean PNG
- 🕘 **Recent Results** — optional MySQL history for completed images
- 📘 **Built-in User Manual** — English step-by-step guide from the **top navigation** (one click)
- ©️ **Copyright Footer** — ownership notice on the page and inside the manual
- 🖥️ **Runs Locally** — XAMPP / Apache, no cloud account required

---

## 🏗️ **Tech Stack**

| **Category** | **Technology** |
| --- | --- |
| 🖥️ **Frontend** | HTML5, CSS3, JavaScript (ES Modules) |
| 🔙 **Backend** | Native PHP 8+ (prepared statements) |
| 🗄️ **Database** | MySQL (`remove_bg_system`) — optional history |
| 🧠 **Removal Engine** | `@imgly/background-removal` + local edge fallback |
| 🎨 **UI Design** | Custom modern CSS (Outfit + Space Grotesk) |
| 🌐 **Server** | Apache (XAMPP) |

---

## 🖼️ **Project Screenshots**

<p align="center">
  <img src="docs/screenshots/01-home.png" alt="Home / Hero" width="900">
</p>
<p align="center"><b>🏠 Home / Hero</b> — brand intro, Choose Image, and How to Use</p>

<br>

<table>
  <tr>
    <td align="center" width="50%">
      <img src="docs/screenshots/02-workspace.png" alt="Workspace" width="100%">
      <br>
      <b>⚙️ Workspace</b><br>
      Original + Result panels
    </td>
    <td align="center" width="50%">
      <img src="docs/screenshots/04-result.png" alt="Completed result" width="100%">
      <br>
      <b>✨ Completed Result</b><br>
      Transparent PNG preview
    </td>
  </tr>
  <tr>
    <td align="center" width="50%">
      <img src="docs/screenshots/03-user-manual.png" alt="User Manual" width="100%">
      <br>
      <b>📘 User Manual</b><br>
      Step-by-step English guide
    </td>
    <td align="center" width="50%">
      <img src="docs/screenshots/05-history.png" alt="Recent Results" width="100%">
      <br>
      <b>🕘 Recent Results</b><br>
      Optional saved history
    </td>
  </tr>
</table>

---

## 📋 **Requirements**

- 💻 XAMPP (**Apache** + **PHP 8.0+**) — start **MySQL** only if you want history
- 🌐 Modern browser (Chrome / Edge / Firefox)
- ⚡ JavaScript enabled (first AI run may download a model)

---

## 🚀 **Installation**

1. 📁 Place the project in your web root, for example:
   ```text
   C:\xampp\htdocs\Remove Background System
   ```
2. ▶️ Start **Apache** in the XAMPP Control Panel.
3. 🗄️ *(Optional)* Import `database/schema.sql` in phpMyAdmin to enable **Recent Results**.
4. 🌐 Open the app:
   ```text
   http://localhost/Remove%20Background%20System/
   ```
5. 📘 Click **User Manual** in the top bar for the full English guide.

> 💡 After updates, hard refresh with **Ctrl + F5**.

---

## 🧭 **How to Use**

1. 🖼️ Click **Choose Image** or drop a photo onto the **Original** panel
2. ✂️ Click **Remove Background** and wait until status shows **Completed**
3. ⬇️ Click **Download PNG** to save the transparent result
4. 🔄 Click **Reset** to process another image
5. 📘 Open **User Manual** anytime from the top-right button

---

## 📁 **Folder Structure**

```text
Remove Background System/
├── index.php                 # Main UI + User Manual modal
├── api/
│   ├── upload.php            # Save original upload
│   ├── save.php              # Save processed PNG
│   └── history.php           # Recent completed results
├── includes/
│   ├── config.php            # App name, copyright, DB settings
│   └── db.php                # MySQLi connection helper
├── assets/
│   ├── css/style.css         # Modern dark UI
│   └── js/app.js             # Upload, remove, download, manual
├── database/schema.sql       # Optional history tables
├── uploads/                  # Original images
├── processed/                # Transparent PNG outputs
├── docs/screenshots/         # README project screenshots
├── README.md
├── CONTRIBUTING.md
└── LICENSE
```

---

## 🤝 **Contributing**

Contributions are welcome! Please read [CONTRIBUTING.md](CONTRIBUTING.md) before opening a pull request.

💡 To contribute, check the guidelines and open a PR with a clear description of what you changed.

---

## 📝 **License**

This project is licensed under the [MIT Non-Commercial License](LICENSE).

---

## ©️ **Copyright**

**Copyright © 2026 Eng Choon Hao. All Rights Reserved.**

Unauthorized copying or redistribution of this project without permission is prohibited.

---

⭐ If you find this project helpful, don't forget to **star** the repository! 🌟

Happy coding! 💻🎉
