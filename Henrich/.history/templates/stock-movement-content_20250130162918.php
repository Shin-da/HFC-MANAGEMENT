<section class="panel">
    <div class="container-fluid">
        <div class="table-header" style="border-left: 8px solid var(--primary-color);">
            <div class="title">
                <span><h2>Stock Movement</h2></span>
                <span style="font-size: 12px;">Display only</span>
            </div>
            <div class="title">
                <span><?php echo date('l, F jS') ?></span>
            </div>
        </div>

        <div class="table-header">
            <!-- Search form -->
            <div>
                <form class="form">
                    <button>
                        <svg width="17" height="16" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="search">
                            <path d="M7.667 12.667A5.333 5.333 0 107.667 2a5.333 5.333 0 000 10.667zM14.334 14l-2.9-2.9" stroke="currentColor" stroke-width="1.333" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                    <input class="input" id="general-search" onkeyup="search()" placeholder="Search the table..." required="" type="text">
                    <button class="reset" type="reset">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </form>
            </div>

        <div class="container-fluid" style="overflow-x:scroll;">
            <table class="table" id="myTable">
                <!-- ...existing table header... -->
                <tbody id="table-body">
                    <?php
                    $items = Page::get('items');
                    if ($items->num_rows > 0) {
                        while ($row = $items->fetch_assoc()) {
                            // ...existing row rendering code...
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="container pagination-container">
            <!-- ...existing pagination code... -->
        </div>
    </div>
</section>
