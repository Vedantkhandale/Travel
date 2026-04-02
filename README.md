# 🌍 TravelBlog - Share Your Adventures

A modern, responsive travel blogging platform built with PHP, MySQL, HTML, CSS, and JavaScript. Share your travel stories, upload stunning photos, and discover amazing destinations from fellow explorers.

## ✨ Features

### 🔐 User Authentication
- Secure user registration and login
- Password hashing with PHP's built-in functions
- Session-based authentication

### 📝 Blog Management
- Create and publish travel stories
- Image upload functionality
- SEO-friendly URL slugs
- Edit and delete posts
- Rich text descriptions

### 🔍 Search & Discovery
- Real-time search functionality
- Filter posts by title
- Explore by categories (Adventure, Cuisine, City Guides, etc.)

### 🎨 Modern UI/UX
- Fully responsive design (mobile-first)
- Dark/Light theme toggle
- Smooth animations and transitions
- Glassmorphism effects
- Interactive elements

### 📱 Responsive Design
- Mobile-friendly navigation with hamburger menu
- Adaptive layouts for all screen sizes
- Touch-friendly interactions

## 🛠️ Tech Stack

- **Backend**: PHP 7+
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Styling**: Custom CSS with CSS Variables
- **Icons**: Font Awesome 6
- **Fonts**: Google Fonts (Plus Jakarta Sans)
- **Server**: XAMPP / Apache

## 🚀 Installation & Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/travelblog.git
   cd travelblog
   ```

2. **Database Setup**
   - Import `database.sql` into your MySQL database
   - Update database credentials in `includes/db.php`

3. **Server Configuration**
   - Place the project in your web server's root directory (e.g., `htdocs/travelblog`)
   - Ensure PHP and MySQL are running

4. **File Permissions**
   - Make `uploads/` directory writable: `chmod 755 uploads/`

5. **Access the Application**
   - Open `http://localhost/travelblog` in your browser
   - Register a new account or login

## 📁 Project Structure

```
travelblog/
├── includes/
│   └── db.php              # Database configuration
├── uploads/                # Uploaded images
├── css/
│   └── style.css           # Additional styles
├── js/
│   └── script.js           # JavaScript functionality
├── index.php               # Home page with blog feed
├── login.php               # User login
├── signup.php              # User registration
├── add-post.php            # Create new blog post
├── post.php                # Individual post view
├── edit-post.php           # Edit existing posts
├── delete-post.php         # Delete posts
├── logout.php              # User logout
└── README.md               # Project documentation
```

## 🎯 Usage

1. **Registration**: Create an account with your name, email, and password
2. **Login**: Access your dashboard
3. **Create Posts**: Share your travel experiences with photos
4. **Explore**: Browse and search other travelers' stories
5. **Interact**: Like posts and engage with the community

## 🔧 Configuration

Update `includes/db.php` with your database credentials:

```php
<?php
$servername = "localhost";
$username = "your_db_user";
$password = "your_db_password";
$dbname = "travelblog";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
?>
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Icons by [Font Awesome](https://fontawesome.com/)
- Fonts by [Google Fonts](https://fonts.google.com/)
- Images from [Unsplash](https://unsplash.com/)

---

**Made with ❤️ for travel enthusiasts worldwide**

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
