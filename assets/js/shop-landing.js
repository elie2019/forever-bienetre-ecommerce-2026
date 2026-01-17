// ==================== CAROUSEL SLIDER ====================
let currentSlide = 0;
const totalSlides = 3;
const slidesContainer = document.getElementById('carouselSlides');
const slides = document.querySelectorAll('.carousel-slide');
const dots = document.querySelectorAll('.carousel-dot');
let autoSlideInterval;

function updateCarousel() {
    slidesContainer.style.transform = `translateX(-${currentSlide * 33.333}%)`;

    // Update active states
    slides.forEach((slide, index) => {
        slide.classList.toggle('active', index === currentSlide);
    });
    dots.forEach((dot, index) => {
        dot.classList.toggle('active', index === currentSlide);
    });
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
    updateCarousel();
    resetAutoSlide();
}

function prevSlide() {
    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
    updateCarousel();
    resetAutoSlide();
}

function goToSlide(index) {
    currentSlide = index;
    updateCarousel();
    resetAutoSlide();
}

function startAutoSlide() {
    autoSlideInterval = setInterval(nextSlide, 6000);
}

function resetAutoSlide() {
    clearInterval(autoSlideInterval);
    startAutoSlide();
}

// Start auto-slide on load
startAutoSlide();

// Pause on hover
document.querySelector('.carousel-section').addEventListener('mouseenter', () => {
    clearInterval(autoSlideInterval);
});

document.querySelector('.carousel-section').addEventListener('mouseleave', () => {
    startAutoSlide();
});

// Touch support for mobile
let touchStartX = 0;
let touchEndX = 0;

document.querySelector('.carousel-section').addEventListener('touchstart', e => {
    touchStartX = e.changedTouches[0].screenX;
});

document.querySelector('.carousel-section').addEventListener('touchend', e => {
    touchEndX = e.changedTouches[0].screenX;
    if (touchStartX - touchEndX > 50) nextSlide();
    if (touchEndX - touchStartX > 50) prevSlide();
});

// Stripe Configuration
const stripe = Stripe('pk_test_51RM6xS01muTCbxCUhgscA5xCcefwBdf9CL1pvRMaqGKlUQqe9lOndeGdgO0nXrgAB4ISBWjkOniNbSFIcP0WxZK100DGhVeLSD');

// ==================== CART SYSTEM ====================
let cart = JSON.parse(localStorage.getItem('cart')) || [];

// DOM Elements
const cartBtn = document.getElementById('cartBtn');
const cartSidebar = document.getElementById('cartSidebar');
const cartOverlay = document.getElementById('cartOverlay');
const cartClose = document.getElementById('cartClose');
const cartItems = document.getElementById('cartItems');
const cartCount = document.getElementById('cartCount');
const cartSubtotal = document.getElementById('cartSubtotal');
const cartTotal = document.getElementById('cartTotal');
const checkoutBtn = document.getElementById('checkoutBtn');

// Open Cart
function openCart() {
    cartSidebar.classList.add('active');
    cartOverlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

// Close Cart
function closeCart() {
    cartSidebar.classList.remove('active');
    cartOverlay.classList.remove('active');
    document.body.style.overflow = '';
}

// Add to Cart
function addToCart(product) {
    const existingItem = cart.find(item => item.id === product.id);

    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            ...product,
            quantity: 1
        });
    }

    saveCart();
    updateCartUI();
    showToast(`${product.name} ajoute au panier`);
}

// Remove from Cart
function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    saveCart();
    updateCartUI();
}

// Update Quantity
function updateQuantity(productId, change) {
    const item = cart.find(item => item.id === productId);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) {
            removeFromCart(productId);
        } else {
            saveCart();
            updateCartUI();
        }
    }
}

// Save Cart to LocalStorage
function saveCart() {
    localStorage.setItem('cart', JSON.stringify(cart));
}

// Update Cart UI
function updateCartUI() {
    // Update count
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartCount.textContent = totalItems;
    cartCount.classList.toggle('visible', totalItems > 0);

    // Update items
    if (cart.length === 0) {
        cartItems.innerHTML = `
            <div class="cart-empty">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5">
                    <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4zM3 6h18M16 10a4 4 0 01-8 0"/>
                </svg>
                <p>Votre panier est vide</p>
            </div>
        `;
        checkoutBtn.disabled = true;
    } else {
        cartItems.innerHTML = cart.map(item => `
            <div class="cart-item">
                <img src="${item.image}" alt="${item.name}" class="cart-item-image">
                <div class="cart-item-details">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-price">${formatPrice(item.price)}</div>
                    <div class="cart-item-quantity">
                        <button class="qty-btn" onclick="updateQuantity('${item.id}', -1)">-</button>
                        <span class="qty-value">${item.quantity}</span>
                        <button class="qty-btn" onclick="updateQuantity('${item.id}', 1)">+</button>
                    </div>
                    <button class="cart-item-remove" onclick="removeFromCart('${item.id}')">Supprimer</button>
                </div>
            </div>
        `).join('');
        checkoutBtn.disabled = false;
    }

    // Update totals
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    cartSubtotal.textContent = formatPrice(total);
    cartTotal.textContent = formatPrice(total);
}

// Format Price
function formatPrice(cents) {
    return (cents / 100).toFixed(2).replace('.', ',') + ' EUR';
}

// Show Toast
function showToast(message) {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    toastMessage.textContent = message;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3000);
}

// Cart Event Listeners
cartBtn.addEventListener('click', openCart);
cartClose.addEventListener('click', closeCart);
cartOverlay.addEventListener('click', closeCart);

// Add to Cart Buttons
document.querySelectorAll('.btn-add-cart').forEach(button => {
    button.addEventListener('click', function() {
        const card = this.closest('.product-card');
        const product = {
            id: card.dataset.id,
            name: card.dataset.name,
            price: parseInt(card.dataset.price),
            image: card.dataset.image
        };

        addToCart(product);

        // Visual feedback
        this.classList.add('added');
        this.innerHTML = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 12l2 2 4-4"/>
            </svg>
            Ajoute!
        `;

        setTimeout(() => {
            this.classList.remove('added');
            this.innerHTML = `
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Ajouter
            `;
        }, 1500);
    });
});

// Checkout
checkoutBtn.addEventListener('click', async function() {
    if (cart.length === 0) return;

    this.classList.add('loading');
    this.disabled = true;

    try {
        // Build line items for Stripe
        const lineItems = cart.map(item => ({
            name: item.name,
            price: item.price,
            quantity: item.quantity
        }));

        const response = await fetch('stripe-checkout.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                items: lineItems,
                // For single item fallback
                name: cart.length === 1 ? cart[0].name : 'Commande Forever Bien-Etre',
                price: cart.reduce((sum, item) => sum + (item.price * item.quantity), 0)
            })
        });

        const data = await response.json();

        if (data.error) {
            throw new Error(data.message || data.error);
        }

        // Clear cart before redirect
        cart = [];
        saveCart();

        // Redirect to Stripe
        if (data.url) {
            window.location.href = data.url;
        }

    } catch (error) {
        console.error('Checkout error:', error);
        this.classList.remove('loading');
        this.disabled = false;
        alert('Erreur: ' + error.message);
    }
});

// Initialize cart UI
updateCartUI();

// ==================== HEADER SCROLL ====================
const header = document.getElementById('header');

window.addEventListener('scroll', () => {
    if (window.scrollY > 100) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }
});

// ==================== PARALLAX ====================
const heroPattern = document.getElementById('heroPattern');
let ticking = false;

function updateParallax() {
    const scrolled = window.pageYOffset;
    const heroHeight = document.querySelector('.hero').offsetHeight;

    if (scrolled < heroHeight) {
        heroPattern.style.transform = `translateY(${scrolled * 0.3}px)`;
    }

    document.querySelectorAll('.parallax-bg').forEach(bg => {
        const rect = bg.parentElement.getBoundingClientRect();
        const isVisible = rect.top < window.innerHeight && rect.bottom > 0;

        if (isVisible) {
            const offset = (rect.top - window.innerHeight) * 0.3;
            bg.style.transform = `translateY(${offset}px)`;
        }
    });

    ticking = false;
}

window.addEventListener('scroll', () => {
    if (!ticking) {
        requestAnimationFrame(updateParallax);
        ticking = true;
    }
});

// ==================== PRODUCT CARDS ANIMATION ====================
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, index) => {
        if (entry.isIntersecting) {
            setTimeout(() => {
                entry.target.classList.add('visible');
            }, index * 100);
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

document.querySelectorAll('.product-card').forEach(card => {
    observer.observe(card);
});

// ==================== URL PARAMS (Stripe Return) ====================
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('success') === 'true') {
    // Vider le panier après achat réussi
    cart = [];
    saveCart();
    updateCartUI();
    showSuccessModal();
    window.history.replaceState({}, document.title, window.location.pathname);
}
if (urlParams.get('canceled') === 'true') {
    showToast('Paiement annulé - Votre panier a été conservé');
    window.history.replaceState({}, document.title, window.location.pathname);
}

// ==================== MODAL ====================
function showSuccessModal() {
    document.getElementById('successModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('successModal').classList.remove('active');
    document.body.style.overflow = '';
}

document.getElementById('successModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
        closeCart();
    }
});

// ==================== NEWSLETTER ====================
document.getElementById('newsletterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const email = this.querySelector('input').value;
    console.log('Newsletter subscription:', email);
    this.querySelector('input').value = '';
    showToast('Merci pour votre inscription!');
});

// ==================== SMOOTH SCROLL ====================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            const headerHeight = document.querySelector('.header').offsetHeight;
            const targetPosition = target.offsetTop - headerHeight;
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        }
    });
});

// ==================== FITTRACK DROPDOWN ====================
const fittrackToggle = document.getElementById('fittrackToggle');
const fittrackDropdown = document.querySelector('.nav-links .fittrack-dropdown');

if (fittrackToggle && fittrackDropdown) {
    // Desktop (>768px): Hover uniquement, pas de click
    // Mobile (≤768px): Click pour ouvrir/fermer

    // Fonction pour vérifier si on est sur mobile
    function isMobile() {
        return window.innerWidth <= 768;
    }

    // Sur desktop, empêcher le comportement click du bouton
    fittrackToggle.addEventListener('click', function(e) {
        if (!isMobile()) {
            e.preventDefault(); // Juste empêcher l'action par défaut, le hover CSS gère l'ouverture
            return;
        }
        // Sur mobile, toggle le dropdown
        e.preventDefault();
        e.stopPropagation();
        fittrackDropdown.classList.toggle('active');
    });

    // Close dropdown when clicking outside (mobile uniquement)
    document.addEventListener('click', function(e) {
        if (isMobile() && !fittrackDropdown.contains(e.target)) {
            fittrackDropdown.classList.remove('active');
        }
    });

    // Close dropdown when clicking submenu link (mobile uniquement)
    const submenuLinks = fittrackDropdown.querySelectorAll('.fittrack-submenu a');
    submenuLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (isMobile()) {
                fittrackDropdown.classList.remove('active');
            }
        });
    });

    // Close dropdown on ESC key (mobile uniquement)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isMobile() && fittrackDropdown.classList.contains('active')) {
            fittrackDropdown.classList.remove('active');
        }
    });
}

// ==================== MOBILE MENU ====================
const menuToggle = document.getElementById('menuToggle');
const mobileMenu = document.getElementById('mobileMenu');
const mobileOverlay = document.getElementById('mobileOverlay');
const mobileMenuClose = document.getElementById('mobileMenuClose');

function openMobileMenu() {
    mobileMenu.classList.add('active');
    mobileOverlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeMobileMenu() {
    mobileMenu.classList.remove('active');
    mobileOverlay.classList.remove('active');
    document.body.style.overflow = '';
}

if (menuToggle) {
    menuToggle.addEventListener('click', openMobileMenu);
}

if (mobileMenuClose) {
    mobileMenuClose.addEventListener('click', closeMobileMenu);
}

if (mobileOverlay) {
    mobileOverlay.addEventListener('click', closeMobileMenu);
}

// FitTrack dropdown dans le menu mobile
const fittrackToggleMobile = document.getElementById('fittrackToggleMobile');
const fittrackDropdownMobile = document.querySelector('.mobile-menu .fittrack-dropdown');

if (fittrackToggleMobile && fittrackDropdownMobile) {
    fittrackToggleMobile.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        fittrackDropdownMobile.classList.toggle('active');
    });
}

// Fermer le menu mobile avec ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && mobileMenu.classList.contains('active')) {
        closeMobileMenu();
    }
});
