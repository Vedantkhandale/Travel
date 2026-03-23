
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
