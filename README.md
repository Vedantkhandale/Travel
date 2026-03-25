🌍 Travel Blog Web Application

A modern **Travel Blog Web App** built using **PHP, MySQL, HTML, CSS, and JavaScript** where users can share their travel experiences, upload images, and explore blogs with a clean and responsive UI.


🚀 Live Features

✨ User Authentication

* Signup & Login system
* Secure password hashing

📝 Blog System

* Add travel blogs with images
* SEO-friendly slug URLs
* Dynamic blog listing
  
🖼️ Image Upload

* Upload travel photos
* Preview before posting

🔍 Search Functionality

* Live search filter
* Instant results

🎨 Modern UI/UX

* Responsive design (mobile friendly 📱)
* Hero section + Slider
* Animated cards & smooth effects
* Interactive accordion

⚡ Extra Features

* Loading animation
* Scroll animations
* Clean navigation


 🛠️ Tech Stack

* Frontend:HTML, CSS, JavaScript
* Backend: PHP
* Database: MySQL
* server: XAMPP / Apache

---

 📁 Project Structure

```
Traveling/
│── index.php
│── login.php
│── signup.php
│── add-post.php
│── post.php
│── logout.php
│
├── includes/
│   └── db.php
│
├── uploads/
│   └── (uploaded images)
│
├── css/
│   └── style.css
│
└── images/
    └── (slider images)
```

---

 ⚙️ Installation & Setup

1. Clone the repository:

bash
git clone https://github.com/vedantkhandale/travel.git
```

2. Move project to:

```
C:\xampp\htdocs\
```

3. Start XAMPP:

* Apache ✅
* MySQL ✅

4. Import database:

* Open **phpMyAdmin**
* Create database → `travel_blog`
* Import SQL file

5. Update DB config:

```php
$conn = mysqli_connect("localhost", "root", "", "travel_blog");
```

6. Run project:

```
http://localhost/Traveling
```

---

## 📸 Screenshots 
```

---

## 💡 Future Improvements

* ❤️ Like & Comment system
* 👤 User profile dashboard
* 🌙 Dark mode toggle
* 🔎 Advanced search & filters
* ✏️ Edit/Delete blog feature

---

## 🤝 Contributing

Feel free to fork this repository and contribute to improve the project.

---

## 📄 License

This project is open-source and free to use.

---

👨‍💻 Author

**Vedant Khandale**
🚀 Web Developer | PHP & JavaScript Enthusiast

---

⭐ If you like this project, don't forget to **star the repo!**
