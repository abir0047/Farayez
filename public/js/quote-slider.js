document.addEventListener('DOMContentLoaded', function() {
    const swiper = new Swiper('.quote-swiper', {
        // Optional parameters
        loop: true,
        slidesPerView: 1,
        spaceBetween: 30,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        
        // Pagination
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },

        // Add custom styles for pagination dots
        on: {
            init: function() {
                // Add custom styles to pagination dots
                const paginationBullets = document.querySelectorAll('.quote-swiper .swiper-pagination-bullet');
                paginationBullets.forEach(bullet => {
                    bullet.style.backgroundColor = '#41AB5D';
                    bullet.style.opacity = '0.5';
                });
            }
        }
    });
});
