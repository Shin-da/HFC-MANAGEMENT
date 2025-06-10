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
            const card = document.querySelector(`[data-request-id="${requestId}"]`);
            card.dataset.status = action === 'approve' ? 'approved' : 'rejected';
            showNotification(result.message, 'success');
            
            if (action === 'approve') {
                animateApproval(card);
            } else {
                animateRejection(card);
            }
        } else {
            showNotification(result.message, 'error');
        }
    } catch (error) {
        showNotification('Error processing request', 'error');
    }
}

function animateApproval(card) {
    card.classList.add('approved-animation');
    setTimeout(() => {
        if (document.getElementById('statusFilter').value === 'pending') {
            card.style.display = 'none';
        }
    }, 500);
}

function animateRejection(card) {
    card.classList.add('rejected-animation');
    setTimeout(() => {
        if (document.getElementById('statusFilter').value === 'pending') {
            card.style.display = 'none';
        }
    }, 500);
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}
