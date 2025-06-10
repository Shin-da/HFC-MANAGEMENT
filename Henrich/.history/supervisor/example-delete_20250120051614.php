function deleteItem(id, name) {
    showConfirm(
        'Delete Item',
        `Are you sure you want to delete ${name}?`,
        'warning'
    ).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `process/delete.process.php?id=${id}`;
        }
    });
}
