// Navigation mobile
const mobileMenuButton = document.getElementById('mobile-menu-button');
const mobileMenu = document.getElementById('mobile-menu');
const menuIcon = document.querySelector('.menu-icon');
const closeIcon = document.querySelector('.close-icon');

mobileMenuButton.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
    menuIcon.classList.toggle('hidden');
    closeIcon.classList.toggle('hidden');
});

// Effet de scroll sur la navigation
window.addEventListener('scroll', () => {
    const nav = document.querySelector('nav');
    if (window.scrollY > 50) {
        nav.classList.add('scrolled');
    } else {
        nav.classList.remove('scrolled');
    }
});

// Initialisation du carousel
const swiper = new Swiper('.swiper', {
    effect: 'coverflow',
    grabCursor: true,
    centeredSlides: true,
    slidesPerView: 'auto',
    loop: true,
    speed: 1000,
    autoplay: {
        delay: 3000,
        disableOnInteraction: false,
        pauseOnMouseEnter: true
    },
    coverflowEffect: {
        rotate: 5,
        stretch: 0,
        depth: 100,
        modifier: 2.5,
        slideShadows: true
    },
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
        dynamicBullets: true
    },
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev'
    },
    breakpoints: {
        640: {
            slidesPerView: 1.2,
        },
        768: {
            slidesPerView: 2,
        },
        1024: {
            slidesPerView: 2.5,
        },
        1280: {
            slidesPerView: 3,
        }
    }
});

// Gestion du survol des slides
const slides = document.querySelectorAll('.swiper-slide');
slides.forEach(slide => {
    slide.addEventListener('mouseenter', () => {
        swiper.autoplay.stop();
    });
    slide.addEventListener('mouseleave', () => {
        swiper.autoplay.start();
    });
});

// Animation au scroll avec Intersection Observer
const observerOptions = {
    root: null,
    rootMargin: '0px',
    threshold: 0.1
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate-fade-in');
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Appliquer l'animation à tous les éléments d'expertise
document.querySelectorAll('.expertise-card').forEach(card => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(20px)';
    card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
    observer.observe(card);
});

// Gestion des animations de survol pour tous les boutons
document.querySelectorAll('button, a').forEach(element => {
    if (element.classList.contains('bg-yellow-500')) {
        element.addEventListener('mouseenter', () => {
            element.style.transform = 'translateY(-2px)';
        });
        element.addEventListener('mouseleave', () => {
            element.style.transform = 'translateY(0)';
        });
    }
});