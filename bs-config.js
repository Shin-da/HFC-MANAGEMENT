module.exports = {
    proxy: "http://localhost/HFC%20MANAGEMENT",
    files: [
        "**/*.php",
        "**/*.html",
        "**/*.css",
        "**/*.js",
        "assets/**/*"
    ],
    ignore: [
        "node_modules"
    ],
    reloadDelay: 0,
    notify: false,
    open: false,
    port: 3000,
    serveStatic: [{
        route: '/HFC MANAGEMENT',
        dir: './'
    }]
}; 