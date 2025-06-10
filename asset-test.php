<?php
// Include necessary files
require_once 'Henrich/includes/config.php';
require_once 'Henrich/includes/Page.php';

// Initialize Page
Page::setTitle('Asset Loading Test');
Page::setBodyClass('test-page');

// Add styles and scripts
Page::addStyle('assets/css/variables.css');
Page::addStyle('assets/css/main.css');
Page::addStyle('assets/css/themes.css');
Page::addScript('assets/js/theme.js');

// Start output buffering
ob_start();
?>

<div class="container">
    <h1>Asset Loading Test</h1>
    <div class="test-panel">
        <h2>CSS Loading Test</h2>
        <div class="color-box primary"></div>
        <div class="color-box secondary"></div>
        <div class="color-box success"></div>
        <div class="color-box danger"></div>
    </div>
    
    <div class="test-panel">
        <h2>JavaScript Loading Test</h2>
        <button id="testButton">Click Me</button>
        <div id="jsOutput"></div>
    </div>
    
    <div class="test-panel">
        <h2>Image Loading Test</h2>
        <img src="<?php echo BASE_URL; ?>resources/images/henrichlogo.png" alt="Logo" class="test-image">
    </div>
    
    <div class="test-results">
        <h2>Loaded Resources</h2>
        <ul id="resourceList">
            <!-- Will be populated by JavaScript -->
        </ul>
    </div>
</div>

<style>
    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        font-family: 'Montserrat', sans-serif;
    }
    
    .test-panel {
        margin-bottom: 20px;
        padding: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    
    .color-box {
        display: inline-block;
        width: 50px;
        height: 50px;
        margin-right: 10px;
        border-radius: 5px;
    }
    
    .primary {
        background-color: var(--primary-500, blue);
    }
    
    .secondary {
        background-color: var(--secondary-500, purple);
    }
    
    .success {
        background-color: var(--success-500, green);
    }
    
    .danger {
        background-color: var(--danger-500, red);
    }
    
    .test-image {
        max-width: 150px;
        height: auto;
    }
    
    #jsOutput {
        margin-top: 10px;
        padding: 10px;
        background-color: #f5f5f5;
        border-radius: 5px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Test button click
        const testButton = document.getElementById('testButton');
        const jsOutput = document.getElementById('jsOutput');
        const resourceList = document.getElementById('resourceList');
        
        if (testButton) {
            testButton.addEventListener('click', function() {
                jsOutput.textContent = 'JavaScript is working correctly! Theme.js should be loaded too.';
                testButton.classList.add('success');
            });
        }
        
        // List loaded stylesheets
        const stylesheets = Array.from(document.styleSheets);
        const scripts = Array.from(document.scripts);
        
        // Display loaded resources
        stylesheets.forEach(sheet => {
            if (sheet.href) {
                const li = document.createElement('li');
                li.textContent = `CSS: ${sheet.href}`;
                resourceList.appendChild(li);
            }
        });
        
        scripts.forEach(script => {
            if (script.src) {
                const li = document.createElement('li');
                li.textContent = `JS: ${script.src}`;
                resourceList.appendChild(li);
            }
        });
    });
</script>

<?php
$pageContent = ob_get_clean();
Page::render($pageContent);
?> 