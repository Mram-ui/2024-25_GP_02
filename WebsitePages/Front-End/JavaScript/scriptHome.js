// JavaScript to change the header background color on scroll
window.addEventListener('scroll', function() {
    const header = document.querySelector('.header');
    if (window.scrollY > 50) { // Change 50 to the number of pixels you want to scroll before changing the color
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }
});



// JavaScript for auto-resizing the massage box in contact us 
document.addEventListener('DOMContentLoaded', function () {
    const textarea = document.getElementById('message');

    textarea.addEventListener('input', function () {
        this.style.height = 'auto'; // Reset the height
        this.style.height = (this.scrollHeight) + 'px'; // Adjust height based on content
    });
});

