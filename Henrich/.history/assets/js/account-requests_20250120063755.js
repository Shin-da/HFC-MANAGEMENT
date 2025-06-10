document.addEventListener('DOMContentLoaded', function() {
    initializeRequestCards();
});

function filterRequests(status) {
    const cards = document.querySelectorAll('.request-card');
    cards.forEach(card => {
        if (status === 'all' || card.dataset.status === status) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

async function processRequest(requestId, action) {
    try {
        const response = await fetch('process-request.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ request_id: requestId, action: action })
        });
        
        const result = await response.json();
        if (result.success) {