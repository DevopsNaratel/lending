// Initialize Swiper Sliders
document.addEventListener('DOMContentLoaded', function() {
    // Hero Slider
    const heroSwiper = new Swiper('.heroSwiper', {
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        }
    });

    // Bestseller Slider
    const bestsellerSwiper = new Swiper('.bestsellerSwiper', {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            640: {
                slidesPerView: 2,
            },
            768: {
                slidesPerView: 3,
            },
            1024: {
                slidesPerView: 4,
            },
        }
    });

    // Testimonial Slider
    const testimonialSwiper = new Swiper('.testimonialSwiper', {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            768: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 3,
            },
        }
    });

    // Load initial data
    loadProducts();
    loadBestsellerProducts();
    startCountdown();
});

// Sample product data
const sampleProducts = [
    {
        id: 1,
        name: "Rose Elegance Bouquet",
        description: "Buket mawar merah premium dengan baby breath dan eucalyptus",
        price: 350000,
        image: "https://images.unsplash.com/photo-1563241527-3004b7be0ffd?w=300&h=300&fit=crop",
        category: "bouquet",
        badge: "Bestseller"
    },
    {
        id: 2,
        name: "Standing Flower Grand Opening",
        description: "Karangan bunga standing untuk grand opening dengan desain elegan",
        price: 750000,
        image: "https://images.unsplash.com/photo-1606041008023-472dfb5e530f?w=300&h=300&fit=crop",
        category: "standing",
        badge: "Popular"
    },
    {
        id: 3,
        name: "Wedding Board Flower",
        description: "Bunga papan pernikahan dengan desain romantis dan elegan",
        price: 850000,
        image: "https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=300&h=300&fit=crop",
        category: "board",
        badge: "Premium"
    },
    {
        id: 4,
        name: "Luxury Hampers Premium",
        description: "Paket lengkap bunga + coklat Ferrero + wine + kartu ucapan",
        price: 950000,
        image: "https://images.unsplash.com/photo-1549298916-b41d501d3772?w=300&h=300&fit=crop",
        category: "hampers",
        badge: "Exclusive"
    },
    {
        id: 5,
        name: "Pastel Dream Bouquet",
        description: "Mix bunga pastel dengan hydrangea dan lisianthus yang lembut",
        price: 425000,
        image: "https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=300&h=300&fit=crop",
        category: "bouquet",
        badge: "New"
    },
    {
        id: 6,
        name: "Sunflower Joy Bouquet",
        description: "Buket bunga matahari ceria dengan chrysanthemum kuning",
        price: 285000,
        image: "https://images.unsplash.com/photo-1549298916-b41d501d3772?w=300&h=300&fit=crop",
        category: "bouquet",
        badge: "Bestseller"
    }
];

// Load products based on category
function loadProducts(category = 'all') {
    const productsGrid = document.getElementById('productsGrid');
    if (!productsGrid) return;

    let filteredProducts = category === 'all' ? sampleProducts : sampleProducts.filter(p => p.category === category);
    
    productsGrid.innerHTML = filteredProducts.map(product => `
        <div class="product-card">
            <div class="product-image">
                <img src="${product.image}" alt="${product.name}">
                <div class="product-badge">${product.badge}</div>
            </div>
            <div class="product-info">
                <h3>${product.name}</h3>
                <p>${product.description}</p>
                <div class="product-price">Rp ${product.price.toLocaleString('id-ID')}</div>
                <div class="product-actions">
                    <button class="btn-cart" onclick="addToCart(${product.id})">
                        <i class="fas fa-heart"></i>
                    </button>
                    <a href="#order" class="btn-order-now" onclick="selectProduct('${product.name}', ${product.price})">
                        Pesan Sekarang
                    </a>
                </div>
            </div>
        </div>
    `).join('');
}

// Load bestseller products for slider
function loadBestsellerProducts() {
    const bestsellerContainer = document.getElementById('bestsellerProducts');
    if (!bestsellerContainer) return;

    const bestsellerProducts = sampleProducts.filter(p => p.badge === 'Bestseller' || p.badge === 'Popular');
    
    bestsellerContainer.innerHTML = bestsellerProducts.map(product => `
        <div class="swiper-slide">
            <div class="product-card">
                <div class="product-image">
                    <img src="${product.image}" alt="${product.name}">
                    <div class="product-badge">${product.badge}</div>
                </div>
                <div class="product-info">
                    <h3>${product.name}</h3>
                    <p>${product.description}</p>
                    <div class="product-price">Rp ${product.price.toLocaleString('id-ID')}</div>
                    <div class="product-actions">
                        <button class="btn-cart" onclick="addToCart(${product.id})">
                            <i class="fas fa-heart"></i>
                        </button>
                        <a href="#order" class="btn-order-now" onclick="selectProduct('${product.name}', ${product.price})">
                            Pesan Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

// Category tab functionality
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('tab-btn')) {
        // Remove active class from all tabs
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        
        // Add active class to clicked tab
        e.target.classList.add('active');
        
        // Load products for selected category
        const category = e.target.getAttribute('data-category');
        loadProducts(category);
    }
});

// Countdown timer
function startCountdown() {
    function updateCountdown() {
        const now = new Date().getTime();
        const endOfDay = new Date();
        endOfDay.setHours(23, 59, 59, 999);
        const distance = endOfDay.getTime() - now;

        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        const hoursEl = document.getElementById('hours');
        const minutesEl = document.getElementById('minutes');
        const secondsEl = document.getElementById('seconds');

        if (hoursEl) hoursEl.textContent = hours.toString().padStart(2, '0');
        if (minutesEl) minutesEl.textContent = minutes.toString().padStart(2, '0');
        if (secondsEl) secondsEl.textContent = seconds.toString().padStart(2, '0');
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
}

// Add to cart/wishlist functionality
function addToCart(productId) {
    const product = sampleProducts.find(p => p.id === productId);
    if (product) {
        // Simple notification
        alert(`${product.name} ditambahkan ke wishlist!`);
        
        // You can implement actual cart functionality here
        console.log('Added to cart:', product);
    }
}

// Select product for order form
function selectProduct(productName, price) {
    // Scroll to order form
    document.getElementById('order').scrollIntoView({ behavior: 'smooth' });
    
    // Pre-fill form if needed
    setTimeout(() => {
        const categorySelect = document.querySelector('select[name="category"]');
        if (categorySelect && productName.toLowerCase().includes('bouquet')) {
            categorySelect.value = 'bouquet';
        } else if (productName.toLowerCase().includes('standing')) {
            categorySelect.value = 'standing';
        } else if (productName.toLowerCase().includes('board') || productName.toLowerCase().includes('papan')) {
            categorySelect.value = 'board';
        } else if (productName.toLowerCase().includes('hampers')) {
            categorySelect.value = 'hampers';
        }
        
        // Set budget based on price
        const budgetSelect = document.querySelector('select[name="budget"]');
        if (budgetSelect) {
            if (price <= 500000) {
                budgetSelect.value = '200000-500000';
            } else if (price <= 1000000) {
                budgetSelect.value = '500000-1000000';
            } else if (price <= 2000000) {
                budgetSelect.value = '1000000-2000000';
            } else {
                budgetSelect.value = '2000000+';
            }
        }
        
        // Add product name to notes
        const notesTextarea = document.querySelector('textarea[name="notes"]');
        if (notesTextarea) {
            notesTextarea.value = `Tertarik dengan produk: ${productName}`;
        }
    }, 500);
}

// Form submission
document.getElementById('orderForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const orderData = {
        name: formData.get('name'),
        phone: formData.get('phone'),
        email: formData.get('email'),
        address: formData.get('address'),
        category: formData.get('category'),
        budget: formData.get('budget'),
        notes: formData.get('notes')
    };
    
    // Save to database
    try {
        const response = await fetch('api/save-order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                name: orderData.name,
                phone: orderData.phone,
                address: orderData.address,
                product: `${orderData.category} (Budget: ${orderData.budget})`,
                notes: `Email: ${orderData.email}\nBudget: ${orderData.budget}\nCatatan: ${orderData.notes}`
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Create WhatsApp message
            const message = `Halo Bloom & Bliss! Saya ingin memesan bunga:

*Order ID:* #${result.order_id}
*Nama:* ${orderData.name}
*No. HP:* ${orderData.phone}
*Email:* ${orderData.email}
*Alamat:* ${orderData.address}
*Kategori:* ${orderData.category}
*Budget:* ${orderData.budget}
*Catatan:* ${orderData.notes}

Mohon info lebih lanjut. Terima kasih!`;
            
            // WhatsApp URL
            const whatsappURL = `https://wa.me/6281234567890?text=${encodeURIComponent(message)}`;
            
            // Open WhatsApp
            window.open(whatsappURL, '_blank');
            
            // Show success message
            alert('Pesanan berhasil dikirim! Anda akan diarahkan ke WhatsApp.');
            
            // Reset form
            this.reset();
        } else {
            alert('Gagal mengirim pesanan: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        
        // Fallback: direct WhatsApp without saving
        const message = `Halo Bloom & Bliss! Saya ingin memesan bunga:

*Nama:* ${orderData.name}
*No. HP:* ${orderData.phone}
*Email:* ${orderData.email}
*Alamat:* ${orderData.address}
*Kategori:* ${orderData.category}
*Budget:* ${orderData.budget}
*Catatan:* ${orderData.notes}

Mohon info lebih lanjut. Terima kasih!`;
        
        const whatsappURL = `https://wa.me/6281234567890?text=${encodeURIComponent(message)}`;
        window.open(whatsappURL, '_blank');
        
        alert('Anda akan diarahkan ke WhatsApp untuk melanjutkan pemesanan.');
        this.reset();
    }
});

// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Gallery lightbox
document.querySelectorAll('.gallery-item').forEach(item => {
    item.addEventListener('click', function() {
        const img = this.querySelector('img');
        const lightbox = document.createElement('div');
        lightbox.className = 'lightbox';
        lightbox.innerHTML = `
            <div class="lightbox-content">
                <img src="${img.src}" alt="${img.alt}">
                <button class="lightbox-close">&times;</button>
            </div>
        `;
        
        lightbox.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            animation: fadeIn 0.3s ease;
        `;
        
        lightbox.querySelector('.lightbox-content').style.cssText = `
            position: relative;
            max-width: 90%;
            max-height: 90%;
        `;
        
        lightbox.querySelector('img').style.cssText = `
            width: 100%;
            height: auto;
            border-radius: 10px;
        `;
        
        lightbox.querySelector('.lightbox-close').style.cssText = `
            position: absolute;
            top: -40px;
            right: 0;
            background: none;
            border: none;
            color: white;
            font-size: 2rem;
            cursor: pointer;
        `;
        
        document.body.appendChild(lightbox);
        
        lightbox.addEventListener('click', function(e) {
            if (e.target === lightbox || e.target.className === 'lightbox-close') {
                document.body.removeChild(lightbox);
            }
        });
    });
});

// Add CSS animation
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
`;
document.head.appendChild(style);