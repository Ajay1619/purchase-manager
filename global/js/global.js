// global.js
function initializeSidebar() {
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');

    menuToggle.addEventListener('click', function () {
        sidebar.classList.toggle('open');
        menuToggle.classList.toggle('open');
    });

    // Close sidebar when clicking outside it
    document.addEventListener('click', function (e) {
        if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
            sidebar.classList.remove('open');
            menuToggle.classList.remove('open');
        }
    });


    // Toggle shrink class on sidebar when clicking on arrow icon
    const arrowIcons = document.querySelectorAll('.sidebar .navbar-brand .arrow-icon');
    arrowIcons.forEach(function (arrow) {
        arrow.addEventListener('click', function (e) {
            e.preventDefault();
            sidebar.classList.toggle('shrink');
        });
    });
}
function initializeTopbar() {
    const avatar = document.getElementById('userAvatar');
    const dropdownMenu = document.getElementById('dropdownMenu');

    avatar.addEventListener('click', function () {
        // Toggle the dropdown menu visibility
        if (dropdownMenu.style.display === 'block') {
            dropdownMenu.style.display = 'none';
        } else {
            dropdownMenu.style.display = 'block';
        }
    });
}
// Hide the dropdown when clicking outside of it
document.addEventListener('click', function (event) {
    const avatar = document.getElementById('userAvatar');
    if (!avatar.contains(event.target) && !dropdownMenu.contains(event.target)) {
        dropdownMenu.style.display = 'none';
    }
});
const toggleSwitches = document.querySelectorAll('.switch input[type="checkbox"]');

toggleSwitches.forEach((toggle) => {
    toggle.addEventListener('change', function () {
        const status = this.checked ? 'Active' : 'Inactive';
        // You can perform actions based on status change here, e.g., update backend via AJAX
    });
});


// Toast
let toastTimeout;

function showToast(type, message) {
    const toastContainer = document.getElementById('toast-container');

    // Create toast element
    const toast = document.createElement('div');
    toast.classList.add('toast', `${type}-toast`);

    // Create icon based on type
    const icon = document.createElement('span');
    icon.classList.add('toast-icon');
    icon.textContent = getIcon(type);

    // Create message element
    const msg = document.createElement('span');
    msg.classList.add('toast-message');
    msg.textContent = message;

    // Append icon and message to toast
    toast.appendChild(icon);
    toast.appendChild(msg);

    // Append toast to container
    toastContainer.appendChild(toast);

    // Show toast
    setTimeout(() => {
        toast.classList.add('show');
    }, 10); // Small delay to trigger CSS transition

    // Hide toast after 3 seconds
    startToastTimer(toast);

    // Pause the timer when hovering over the toast
    toast.addEventListener('mouseover', () => {
        clearTimeout(toastTimeout);
    });

    // Resume the timer when mouse leaves the toast
    toast.addEventListener('mouseout', () => {
        startToastTimer(toast);
    });
}

function startToastTimer(toast) {
    toastTimeout = setTimeout(() => {
        // Apply the hide class to trigger the fade-out effect
        toast.classList.add('hide');
        setTimeout(() => {
            toast.remove(); // Remove toast from DOM
        }, 500); // Match CSS transition duration
    }, 3000); // 3 seconds
}

function getIcon(type) {
    switch (type) {
        case 'info': return 'ℹ️'; // Info icon
        case 'success': return '✅'; // Success icon
        case 'warning': return '⚠️'; // Warning icon
        case 'error': return '❌'; // Error icon
        default: return 'ℹ️'; // Default to info icon
    }
}

