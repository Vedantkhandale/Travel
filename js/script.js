
document.addEventListener("DOMContentLoaded", function(){

    // accordion fix
    document.querySelectorAll(".acc-item button").forEach(btn => {
        btn.addEventListener("click", () => {
            let content = btn.nextElementSibling;
            content.style.display =
                content.style.display === "block" ? "none" : "block";
        });
    });

    // search fix
    let search = document.getElementById("searchInput");
    if(search){
        search.addEventListener("keyup", function() {
            let value = this.value.toLowerCase();
            document.querySelectorAll(".card").forEach(card => {
                card.style.display = card.innerText.toLowerCase().includes(value)
                    ? "block"
                    : "none";
            });
        });
    }

});

// index .php code 

        // Smooth Scroll Navbar
        window.onscroll = () => {
            const nav = document.getElementById('mainNav');
            if (window.scrollY > 50) nav.classList.add('scrolled');
            else nav.classList.remove('scrolled');
        };

        function toggleTheme() {
            const body = document.body;
            body.classList.toggle("dark");
            const isDark = body.classList.contains("dark");
            document.querySelector("#themeBtn i").className = isDark ? "fas fa-sun" : "fas fa-moon";
            document.cookie = "theme=" + (isDark ? "dark" : "light") + ";path=/";
        }

        function toggleLike(el) {
            el.classList.toggle("active");
            const icon = el.querySelector("i");
            if (el.classList.contains("active")) {
                icon.className = "fas fa-heart";
            } else {
                icon.className = "far fa-heart";
            }
        }

        document.getElementById("searchInput").addEventListener("input", function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll(".card").forEach(card => {
                card.style.display = card.getAttribute("data-title").includes(query) ? "flex" : "none";
            });
        });

        window.onload = () => {
            if (document.body.classList.contains("dark")) {
                document.querySelector("#themeBtn i").className = "fas fa-sun";
            }
        }

        