/**
 * Vireo Bird Elements JavaScript
 * Dynamic bird icon integration and animations
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize bird elements
    initBirdElements();
    initBirdAnimations();
    
    /**
     * Initialize bird elements on page load
     */
    function initBirdElements() {
        // Add bird icons to success messages dynamically
        document.querySelectorAll('.success-message').forEach(function(element) {
            if (!element.querySelector('.vireo-bird-icon')) {
                element.classList.add('bird-fade-in');
            }
        });
        
        // Add bird loading to forms during submission
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', function() {
                const submitButton = form.querySelector('[type="submit"]');
                if (submitButton) {
                    submitButton.classList.add('bird-loading');
                    
                    // Remove loading after 3 seconds (adjust based on actual processing time)
                    setTimeout(() => {
                        submitButton.classList.remove('bird-loading');
                    }, 3000);
                }
            });
        });
        
        // Add hover effects to plugin cards
        document.querySelectorAll('.plugin-card').forEach(function(card) {
            card.classList.add('bird-hover');
        });
    }
    
    /**
     * Initialize bird animations
     */
    function initBirdAnimations() {
        // Intersection observer for animating birds on scroll
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    
                    // Add fade-in animation to section dividers
                    if (element.classList.contains('section-divider')) {
                        element.classList.add('bird-fade-in');
                    }
                    
                    // Add bounce animation to bird icons in viewport
                    const birdIcons = element.querySelectorAll('.vireo-bird-icon');
                    birdIcons.forEach((icon, index) => {
                        setTimeout(() => {
                            icon.classList.add('bird-bounce');
                        }, index * 200);
                    });
                    
                    observer.unobserve(element);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        // Observe elements for animation
        document.querySelectorAll('.section-divider, .plugin-card, .feature-card').forEach(element => {
            observer.observe(element);
        });
    }
    
    /**
     * Create bird icon element
     */
    function createBirdIcon(size = 'small', color = 'primary') {
        const bird = document.createElement('span');
        bird.className = `vireo-bird-icon ${size} ${color}`;
        bird.setAttribute('aria-hidden', 'true');
        return bird;
    }
    
    /**
     * Add bird to element
     */
    function addBirdToElement(element, size = 'small', color = 'primary', position = 'after') {
        const bird = createBirdIcon(size, color);
        
        if (position === 'before') {
            element.prepend(bird);
        } else {
            element.appendChild(bird);
        }
        
        return bird;
    }
    
    /**
     * Bird loading state utility
     */
    window.VireoBird = {
        addIcon: function(element, size = 'small', color = 'primary', position = 'after') {
            return addBirdToElement(element, size, color, position);
        },
        
        showLoading: function(element) {
            element.classList.add('bird-loading');
        },
        
        hideLoading: function(element) {
            element.classList.remove('bird-loading');
        },
        
        animate: function(element, animation = 'bounce') {
            element.classList.add(`bird-${animation}`);
            
            // Remove animation class after completion
            setTimeout(() => {
                element.classList.remove(`bird-${animation}`);
            }, 2000);
        },
        
        addToSuccessMessage: function(message) {
            const container = document.createElement('div');
            container.className = 'success-message bird-fade-in';
            container.innerHTML = message;
            return container;
        }
    };
    
    /**
     * Easter egg: konami code for bird animation
     */
    let konamiCode = [];
    const konamiSequence = [
        'ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown',
        'ArrowLeft', 'ArrowRight', 'ArrowLeft', 'ArrowRight',
        'KeyB', 'KeyA'
    ];
    
    document.addEventListener('keydown', function(e) {
        konamiCode.push(e.code);
        
        if (konamiCode.length > konamiSequence.length) {
            konamiCode.shift();
        }
        
        if (JSON.stringify(konamiCode) === JSON.stringify(konamiSequence)) {
            // Activate bird party mode
            activateBirdParty();
            konamiCode = [];
        }
    });
    
    function activateBirdParty() {
        // Add bouncing birds all over the page
        for (let i = 0; i < 10; i++) {
            setTimeout(() => {
                const bird = createBirdIcon('medium', 'primary');
                bird.style.position = 'fixed';
                bird.style.left = Math.random() * window.innerWidth + 'px';
                bird.style.top = Math.random() * window.innerHeight + 'px';
                bird.style.zIndex = '9999';
                bird.style.pointerEvents = 'none';
                bird.classList.add('bird-bounce');
                
                document.body.appendChild(bird);
                
                // Remove after 3 seconds
                setTimeout(() => {
                    if (bird.parentNode) {
                        bird.parentNode.removeChild(bird);
                    }
                }, 3000);
            }, i * 200);
        }
        
        // Show notification
        if (window.VireoNotifications) {
            window.VireoNotifications.show('🐦 Bird party activated! 🎉', 'success', 3000);
        }
    }
    
    /**
     * Performance optimization: Use requestAnimationFrame for smooth animations
     */
    function optimizeAnimations() {
        const animatedElements = document.querySelectorAll('[class*="bird-"]');
        
        animatedElements.forEach(element => {
            // Use will-change for smooth animations
            element.style.willChange = 'transform, opacity';
            
            // Remove will-change after animation
            element.addEventListener('animationend', function() {
                element.style.willChange = 'auto';
            });
        });
    }
    
    // Initialize performance optimizations
    optimizeAnimations();
    
    // Global event for when bird elements are ready
    document.dispatchEvent(new CustomEvent('vireoBirdReady', {
        detail: {
            bird: window.VireoBird,
            utils: {
                createIcon: createBirdIcon,
                addToElement: addBirdToElement
            }
        }
    }));
});