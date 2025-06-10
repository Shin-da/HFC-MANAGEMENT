/* Add these to your theme-toggle.css or appropriate stylesheet */
.theme-transitioning * {
    transition: background-color 0.3s ease,
                color 0.3s ease,
                border-color 0.3s ease,
                box-shadow 0.3s ease;
}

.theme-btn.clicked {
    animation: click-animation 0.2s ease;
}

@keyframes click-animation {
    0% { transform: scale(1); }
    50% { transform: scale(0.9); }
    100% { transform: scale(1); }
}