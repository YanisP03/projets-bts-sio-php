class Carousel {
    constructor() {
        // Configuration
        this.slides = [
            {
                image: '../ImagesRealisations/Chantier1.jpg',
                title: 'Construction moderne',
                description: 'Un savoir-faire d\'excellence'
            },
            {
                image: '../ImagesRealisations/Chantier2.jpg',
                title: 'Rénovation',
                description: 'Des projets sur mesure'
            },
            {
                image: '../ImagesRealisations/Chantier4.jpg',
                title: 'Aménagement',
                description: 'Des espaces repensés'
            },
            {
                image: '../ImagesRealisations/Chantier5.jpg',
                title: 'Rénovation intérieure',
                description: 'Excellence et précision'
            },
            {
                image: '../ImagesRealisations/Chantier6.jpg',
                title: 'Construction neuve',
                description: 'Innovation et qualité'
            }
        ];

        // États
        this.currentIndex = 0;
        this.isTransitioning = false;
        this.autoplayInterval = null;
        this.autoplayDelay = 5000;
        this.touchStartX = null;
        this.transitionDuration = 500;

        // DOM Elements
        this.track = document.getElementById('carouselTrack');
        this.indicatorsContainer = document.getElementById('indicators');
        this.prevBtn = document.getElementById('prevBtn');
        this.nextBtn = document.getElementById('nextBtn');

        // Initialisation
        this.init();
    }

    init() {
        this.createSlides();
        this.createIndicators();
        this.setupEventListeners();
        this.startAutoplay();
        this.updateCarousel();
    }

    createSlides() {
        this.slides.forEach((slide, index) => {
            const slideElement = document.createElement('div');
            slideElement.className = 'carousel-slide';
            slideElement.innerHTML = `
                <img 
                    src="${slide.image}" 
                    alt="${slide.title}"
                    class="carousel-image"
                    loading="${index === 0 ? 'eager' : 'lazy'}"
                >
                <div class="carousel-overlay">
                    <h2 class="text-2xl font-bold mb-2">${slide.title}</h2>
                    <p class="text-gray-200">${slide.description}</p>
                </div>
            `;
            this.track.appendChild(slideElement);
        });
    }

    createIndicators() {
        this.slides.forEach((_, index) => {
            const indicator = document.createElement('button');
            indicator.className = 'slide-indicator';
            indicator.setAttribute('aria-label', `Diapositive ${index + 1}`);
            indicator.addEventListener('click', () => this.goToSlide(index));
            this.indicatorsContainer.appendChild(indicator);
        });
    }

    setupEventListeners() {
        // Navigation buttons
        this.prevBtn.addEventListener('click', () => this.prevSlide());
        this.nextBtn.addEventListener('click', () => this.nextSlide());

        // Touch events
        this.track.addEventListener('touchstart', (e) => {
            this.touchStartX = e.touches[0].clientX;
            this.pauseAutoplay();
        }, { passive: true });

        this.track.addEventListener('touchmove', (e) => {
            if (this.touchStartX === null) return;
            
            const touchCurrentX = e.touches[0].clientX;
            const diff = this.touchStartX - touchCurrentX;
            
            // Applique un déplacement en temps réel
            const offset = -this.currentIndex * 100 - (diff / this.track.offsetWidth * 100);
            this.track.style.transform = `translateX(${offset}%)`;
        }, { passive: true });

        this.track.addEventListener('touchend', (e) => {
            if (this.touchStartX === null) return;
            
            const touchEndX = e.changedTouches[0].clientX;
            const diff = this.touchStartX - touchEndX;
            
            if (Math.abs(diff) > 50) {
                if (diff > 0) this.nextSlide();
                else this.prevSlide();
            } else {
                // Retour à la position initiale si le swipe n'est pas assez important
                this.updateCarousel();
            }

            this.touchStartX = null;
            this.startAutoplay();
        }, { passive: true });

        // Mouse events
        this.track.addEventListener('mouseenter', () => this.pauseAutoplay());
        this.track.addEventListener('mouseleave', () => this.startAutoplay());

        // Visibility change
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) this.pauseAutoplay();
            else this.startAutoplay();
        });

        // Window events
        window.addEventListener('blur', () => this.pauseAutoplay());
        window.addEventListener('focus', () => this.startAutoplay());
        
        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') this.prevSlide();
            if (e.key === 'ArrowRight') this.nextSlide();
        });
    }

    updateCarousel() {
        // Update track position
        this.track.style.transform = `translateX(-${this.currentIndex * 100}%)`;

        // Update indicators
        const indicators = this.indicatorsContainer.children;
        Array.from(indicators).forEach((indicator, index) => {
            indicator.classList.toggle('active', index === this.currentIndex);
        });
    }

    goToSlide(index) {
        if (this.isTransitioning || index === this.currentIndex) return;
        
        this.isTransitioning = true;
        this.currentIndex = index;
        this.updateCarousel();
        
        setTimeout(() => {
            this.isTransitioning = false;
        }, this.transitionDuration);
    }

    nextSlide() {
        if (this.isTransitioning) return;
        const nextIndex = (this.currentIndex + 1) % this.slides.length;
        this.goToSlide(nextIndex);
    }

    prevSlide() {
        if (this.isTransitioning) return;
        const prevIndex = (this.currentIndex - 1 + this.slides.length) % this.slides.length;
        this.goToSlide(prevIndex);
    }

    startAutoplay() {
        this.pauseAutoplay();
        this.autoplayInterval = setInterval(() => this.nextSlide(), this.autoplayDelay);
    }

    pauseAutoplay() {
        if (this.autoplayInterval) {
            clearInterval(this.autoplayInterval);
            this.autoplayInterval = null;
        }
    }
}

// Initialize carousel when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new Carousel();
});