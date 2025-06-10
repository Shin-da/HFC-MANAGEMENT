<footer class="footer">
  \
    <div class="footer-bottom">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> HFC Management. All rights reserved.</p>
        </div>
    </div>
</footer>

<style>
.footer {
    {% comment %} background-color: #333;
    color: #fff;
    padding: 40px 0 20px; {% endcomment %}
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