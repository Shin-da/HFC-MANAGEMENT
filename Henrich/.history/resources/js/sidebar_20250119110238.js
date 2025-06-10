const body = document.querySelector("body"),
    sidebar = body.querySelector(".sidebar"),
    toggle = document.getElementById("toggle"),
    panel = document.querySelector(".panel"),
    searchBtn = body.querySelector(".search-box"),
    modeSwitch = body.querySelector(".toggle-switch"),
    modeText = body.querySelector(".mode-text");

// State Management
const sidebarStates = {
    load: () => ({
        hidden: localStorage.getItem("sidebarHidden") === "true",
        closed: localStorage.getItem("sidebarClosed") === "true",
        open: localStorage.getItem("sidebarOpen") === "true"
    }),
    save: () => {
        localStorage.setItem("sidebarOpen", sidebar.classList.contains("open"));
        localStorage.setItem("sidebarClosed", sidebar.classList.contains("close"));
        localStorage.setItem("sidebarHidden", sidebar.classList.contains("hidden"));
    }
};

const currentTheme = localStorage.getItem("theme");
const { hidden: sidebarHidden, closed: sidebarClosed, open: sidebarOpen } = sidebarStates.load();

if (sidebarHidden) {
    sidebar.classList.toggle("hidden", sidebarHidden);
    toggle.classList.toggle("hidden", sidebarHidden);
    console.log("Sidebar is hidden: ", sidebarHidden);
}

if (sidebarClosed) {
    sidebar.classList.toggle("close", sidebarClosed);
    toggle.classList.toggle("close", sidebarClosed);
    console.log("Sidebar is closed: ", sidebarClosed);
}

console.log("Window width: ", window.innerWidth);

let resizeTimeout;

window.addEventListener("resize", () => {
  clearTimeout(resizeTimeout);


    sidebar.classList.toggle("close", sidebarClosed === "true");
    toggle.classList.toggle("close", sidebarClosed === "true");
    console.log("Sidebar is closed: ", sidebarClosed);
}

console.log("Window width: ", window.innerWidth);

let resizeTimeout;

window.addEventListener("resize", () => {
  clearTimeout(resizeTimeout);
  resizeTimeout = setTimeout(() => {
    // Your sidebar toggle logic here
    if (window.innerWidth > 992) {
      sidebar.classList.remove("hidden");
      sidebar.classList.remove("close");
      sidebar.classList.add("open");
      localStorage.setItem("sidebarOpen", sidebar.classList.contains("open"));
    } else if (window.innerWidth > 600 && window.innerWidth < 992) {
      sidebar.classList.remove("hidden");
      sidebar.classList.toggle("close");
      sidebar.classList.remove("open");
      localStorage.setItem("sidebarClosed", sidebar.classList.contains("close"));
    } else {
      sidebar.classList.toggle("hidden");
      sidebar.classList.remove("close");
      sidebar.classList.remove("open");
      localStorage.setItem("sidebarHidden", sidebar.classList.contains("hidden"));
    }
  }, 500); // 500ms delay
});

toggle.addEventListener("click", () => {
  
    if (window.innerWidth < 600) {
      sidebar.classList.toggle("hidden");
      sidebar.classList.remove("open");
      toggle.classList.toggle("hidden");
      localStorage.setItem("sidebarHidden", sidebar.classList.contains("hidden"));
    } else if (window.innerWidth < 992 && window.innerWidth > 600) {
      sidebar.classList.remove("hidden");
      sidebar.classList.toggle("close");
      sidebar.classList.remove("open");
      toggle.classList.toggle("close");
      localStorage.setItem("sidebarClosed", sidebar.classList.contains("close"));
    } else {
      sidebar.classList.toggle("open");
      sidebar.classList.toggle("close");
      localStorage.setItem("sidebarOpen", sidebar.classList.contains("open"));
      localStorage.setItem("sidebarClosed", sidebar.classList.contains("close"));
    }

console.log("Sidebar state: ", sidebar.classList);
  localStorage.setItem("sidebarOpen", sidebar.classList.contains("open"));
  localStorage.setItem("sidebarClosed", sidebar.classList.contains("close"));
  localStorage.setItem("sidebarHidden", sidebar.classList.contains("hidden"));
}
);