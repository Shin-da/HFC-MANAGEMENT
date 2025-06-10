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
const sidebarOpen = localStorage.getItem("sidebarOpen");
// if (currentTheme) {
//     body.classList.toggle("dark", currentTheme === "dark");
//     modeText.innerText = currentTheme === "dark" ? "Light mode" : "Dark mode";
// }

if (sidebarHidden) {
    sidebar.classList.toggle("hidden", sidebarHidden === "true");
    toggle.classList.toggle("hidden", sidebarHidden === "true");
    console.log("Sidebar is hidden: ", sidebarHidden);
}

if (sidebarClosed) {
    sidebar.classList.toggle("close", sidebarClosed === "true");
    toggle.classList.toggle("close", sidebarClosed === "true");
    console.log("Sidebar is closed: ", sidebarClosed);
}

console.log("Window width: ", window.innerWidth);

window.addEventListener("resize", () => {
    if (window.innerWidth > 991 && window.innerWidth < 2) {
        sidebar.classList.remove("hidden");
        sidebar.classList.remove("close");
        sidebar.classList.add("open");
        console.log("Sidebar is open: ", window.innerWidth);
    }
    if (window.innerWidth < 991 && window.innerWidth > 600) {
        sidebar.classList.remove("hidden");
        sidebar.classList.toggle("close");
        sidebar.classList.remove("open");
        console.log("Sidebar is close: ", window.innerWidth);
    }
    if (window.innerWidth < 600) {
        sidebar.classList.add("hidden");
        if (sidebar.classList.contains("hidden")) {
            toggle.classList.add("hidden");
        }
        console.log("Sidebar is hidden: ", window.innerWidth);
    }

});

toggle.addEventListener("click", () => {
    if (window.innerWidth < 600) {
        sidebar.classList.toggle("hidden");
        sidebar.classList.remove("open");
        toggle.classList.toggle("hidden");
        localStorage.setItem("sidebarHidden", sidebar.classList.contains("hidden"));
        console.log("Sidebar is hidden: ", window.innerWidth);
    } else if (window.innerWidth < 1991 && window.innerWidth > 600) {
        sidebar.classList.remove("hidden");
        sidebar.classList.toggle("close");
        sidebar.classList.remove("open");
        toggle.classList.toggle("close");
        localStorage.setItem("sidebarClosed", sidebar.classList.contains("close"));
        console.log("Sidebar is closed: ", window.innerWidth);
    } else {
        sidebar.classList.remove("hidden");
        sidebar.classList.remove("close");
        sidebar.classList.add("open");
        toggle.classList.remove("open");
        localStorage.setItem("sidebarOpen", sidebar.classList.contains("open"));
        console.log("Sidebar is open: ", window.innerWidth);
    }
    localStorage.setItem("sidebarHidden", sidebar.classList.contains("hidden"));
    localStorage.setItem("sidebarClosed", sidebar.classList.contains("close"));
    localStorage.setItem("sidebarOpen", sidebar.classList.contains("open"));
    console.log("Sidebar state: ", sidebar.classList);
});
// if the screen is less than 600px, sidebar will be hidden

const navLinkEls = document.querySelectorAll(".nav-link");
const windowpathname = window.location.pathname;


navLinkEls.forEach((navLinkEl) => {
    if (navLinkEl.href.includes(windowpathname)) {
        navLinkEl.classList.add("active");
    }
});


