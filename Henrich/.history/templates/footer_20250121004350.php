<footer class="footer">
    <div class="footer-content">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h3>About HFC Management</h3>
                    <p>Providing excellent management solutions for your business needs.</p>
                </div>
                <div class="col-md-4">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="services.php">Services</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h3>Contact Info</h3>
                    <p>
                        Email: info@hfcmanagement.com<br>
                        Phone: (123) 456-7890<br>
                        Address: 123 Business Street
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> HFC Management. All rights reserved.</p>
        </div>
    </div>
</footer>

<style>
.footer {
    background-color: #333;
    color: #fff;
    padding: 40px 0 20px;
    width: 100%;
    position: ab;
    bottom: 0;
}

.footer-content {
    margin-bottom: 20px;
}

.footer h3 {
    color: #fff;
    margin-bottom: 20px;
}

.footer ul {
    list-style: none;
    padding: 0;
}

.footer ul li {
    margin-bottom: 10px;
}

.footer ul li a {
    color: #fff;
    text-decoration: none;
}

.footer-bottom {
    border-top: 1px solid #555;
    padding-top: 20px;
    text-align: center;
}
</style>

</div> <!-- End content-wrapper -->
        </div> <!-- End home-content -->
    </section> <!-- End home-section -->
    
    <!-- Scripts -->
    <?php foreach (Page::getScripts() as $script): ?>
        <script src="<?php echo $script; ?>"></script>
    <?php endforeach; ?>
</body>
</html>