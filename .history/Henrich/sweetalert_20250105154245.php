
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        function alertMe(title, text, type) {
            swal({
                title: title,
                text: text,
                icon: type,
                buttons: false,
                timer: 3000
            });
        }
    </script>
</body>

</html>
