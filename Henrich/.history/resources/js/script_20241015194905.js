const body = document.querySelector("body"),
    sidebar = body.querySelector(".sidebar"),
    toggle = document.getElementById("toggle"),
    panel = document.querySelector(".panel"),
    searchBtn = body.querySelector(".search-box"),
    modeSwitch = body.querySelector(".toggle-switch"),
    modeText = body.querySelector(".mode-text");


// searchBtn.addEventListener("click", () => {
//     sidebar.classList.remove("close");
// });


// modeSwitch.addEventListener("click", () => {
//     body.classList.toggle("dark");

//     if (body.classList.contains("dark")) {
//         modeText.innerText = "Light mode";
//         localStorage.setItem("theme", "dark");
//     } else {
//         modeText.innerText = "Dark mode";
//         localStorage.setItem("theme", "light");
//     }
// }
// );

const currentTheme = localStorage.getItem("theme");
const sidebarClosed = localStorage.getItem("sidebarClosed");
const sidebarHidden = localStorage.getItem("sidebarHidden");

// if (currentTheme) {
//     body.classList.toggle("dark", currentTheme === "dark");
//     modeText.innerText = currentTheme === "dark" ? "Light mode" : "Dark mode";
// }

if (sidebarHidden) {
    sidebar.classList.toggle("hidden", sidebarHidden === "true");
    toggle.classList.toggle("hidden", sidebarHidden === "true");
}

if (sidebarClosed) {
    sidebar.classList.toggle("close", sidebarClosed === "true");
    toggle.classList.toggle("close", sidebarClosed === "true");
}


window.addEventListener("resize", () => {
    if (window.innerWidth > 991) {
        sidebar.classList.remove("hidden");
        sidebar.classList.remove("close");
    }
    if (window.innerWidth < 991 && window.innerWidth > 600) {
        sidebar.classList.remove("hidden");
        sidebar.classList.add("close");
    }
    if (window.innerWidth < 600) {
        sidebar.classList.add("hidden");
        if (sidebar.classList.contains("hidden")) {
            toggle.classList.add("hidden");
        }
    }

});

toggle.addEventListener("click", () => {
    if (window.innerWidth < 600) {
        sidebar.classList.toggle("hidden");
        sidebar.classList.toggle("close");
    } else if (window.innerWidth < 991) {
        sidebar.classList.toggle("close");
        toggle.classList.toggle("close");
    } else {
        sidebar.classList.remove("hidden");
        sidebar.classList.remove("close");
    }
    localStorage.setItem("sidebarHidden", sidebar.classList.contains("hidden"));
    localStorage.setItem("sidebarClosed", sidebar.classList.contains("close"));
    localStorage.setItem("sidebarHidden", sidebar.classList.contains("hidden"));
});

// if the screen is less than 600px, sidebar will be hidden



const navLinkEls = document.querySelectorAll(".nav-link");
const windowpathname = window.location.pathname;


navLinkEls.forEach((navLinkEl) => {
    if (navLinkEl.href.includes(windowpathname)) {
        navLinkEl.classList.add("active");
    }
});


