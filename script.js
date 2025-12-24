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

// Countdown Timer
function updateCountdown() {
    const now = new Date().getTime();
    const endOfDay = new Date();
    endOfDay.setHours(23, 59, 59, 999);
    const distance = endOfDay.getTime() - now;

    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
    document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
    document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
}

// Update countdown every second
setInterval(updateCountdown, 1000);
updateCountdown();

// FAQ Toggle (if needed)
document.querySelectorAll('.faq-item h3').forEach(question => {
    question.addEventListener('click', function() {
        const answer = this.nextElementSibling;
        const isOpen = answer.style.display === 'block';
        
        // Close all other answers
        document.querySelectorAll('.faq-item p').forEach(p => {
            p.style.display = 'none';
        });
        
        // Toggle current answer
        answer.style.display = isOpen ? 'none' : 'block';
    });
});

// Load dynamic data on page load
window.addEventListener('load', function() {
    loadCollections();
    loadBestsellerProducts();
});

// Load collections from database
async function loadCollections() {
    try {
        const response = await fetch('api/get-data.php?type=collections');
        const collections = await response.json();
        
        const collectionsGrid = document.querySelector('.collections-grid');
        if (collectionsGrid && collections.length > 0) {
            collectionsGrid.innerHTML = collections.map(collection => `
                <div class="collection-item">
                    <img src="${collection.image}" alt="${collection.name}">
                    <div class="collection-content">
                        <h3>${collection.name}</h3>
                        <p>${collection.description}</p>
                        <a href="#order" class="btn-outline">Lihat Koleksi</a>
                    </div>
                </div>
            `).join('');
        }
    } catch (error) {
        console.error('Error loading collections:', error);
    }
}

// Load bestseller products from database
async function loadBestsellerProducts() {
    try {
        const response = await fetch('api/get-data.php?type=products&category=bestseller');
        const products = await response.json();
        
        const productsGrid = document.querySelector('.products-grid');
        const productSelect = document.querySelector('select[name="product"]');
        
        if (productsGrid && products.length > 0) {
            productsGrid.innerHTML = products.map(product => `
                <div class="product-card">
                    <img src="${product.image}" alt="${product.name}">
                    <div class="product-info">
                        <h3>${product.name}</h3>
                        <p>${product.description}</p>
                        <div class="price">Rp ${parseInt(product.price).toLocaleString('id-ID')}</div>
                        <a href="#order" class="btn-primary" onclick="selectProduct('${product.name}', ${product.price})">Pesan Sekarang</a>
                    </div>
                </div>
            `).join('');
        }
        
        // Update product select options
        if (productSelect && products.length > 0) {
            const currentOptions = productSelect.innerHTML;
            const newOptions = products.map(product => 
                `<option value="${product.name}">${product.name} - Rp ${parseInt(product.price).toLocaleString('id-ID')}</option>`
            ).join('');
            productSelect.innerHTML = currentOptions + newOptions;
        }
    } catch (error) {
        console.error('Error loading products:', error);
    }
}

// Select product function
function selectProduct(productName, price) {
    const productSelect = document.querySelector('select[name="product"]');
    if (productSelect) {
        productSelect.value = productName;
    }
}

// Form submission with database save
document.querySelector('.form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Get form data
    const name = this.querySelector('input[type="text"]').value;
    const phone = this.querySelector('input[type="tel"]').value;
    const address = this.querySelector('textarea').value;
    const product = this.querySelector('select').value;
    const notes = this.querySelectorAll('textarea')[1].value;
    
    // Save to database
    try {
        const response = await fetch('api/save-order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                name: name,
                phone: phone,
                address: address,
                product: product,
                notes: notes
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Create WhatsApp message
            const message = `Halo Bloom & Bliss! Saya ingin memesan:

*Order ID:* #${result.order_id}
*Nama:* ${name}
*No. WhatsApp:* ${phone}
*Alamat:* ${address}
*Produk:* ${product}
*Catatan:* ${notes}

Mohon info lebih lanjut. Terima kasih!`;
            
            // WhatsApp URL
            const whatsappURL = `https://wa.me/6281234567890?text=${encodeURIComponent(message)}`;
            
            // Open WhatsApp
            window.open(whatsappURL, '_blank');
            
            // Show success message
            alert('Pesanan berhasil disimpan! Anda akan diarahkan ke WhatsApp untuk melanjutkan pemesanan.');
            
            // Reset form
            this.reset();
        } else {
            alert('Gagal menyimpan pesanan: ' + result.message);
        }
    } catch (error) {
        console.error('Error saving order:', error);
        alert('Terjadi kesalahan saat menyimpan pesanan.');
    }
});

// Navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 100) {
        navbar.style.background = 'rgba(255, 255, 255, 0.98)';
        navbar.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.1)';
    } else {
        navbar.style.background = 'rgba(255, 255, 255, 0.95)';
        navbar.style.boxShadow = 'none';
    }
});

// Intersection Observer for animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe elements for animation
document.querySelectorAll('.feature, .collection-item, .product-card, .testimonial, .step').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(el);
});

// Mobile menu toggle (if needed)
function toggleMobileMenu() {
    const navLinks = document.querySelector('.nav-links');
    navLinks.classList.toggle('active');
}

// Add mobile menu button if screen is small
if (window.innerWidth <= 768) {
    const navbar = document.querySelector('.navbar .container');
    const mobileMenuBtn = document.createElement('button');
    mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
    mobileMenuBtn.className = 'mobile-menu-btn';
    mobileMenuBtn.onclick = toggleMobileMenu;
    navbar.appendChild(mobileMenuBtn);
}

// Gallery lightbox effect (simple version)
document.querySelectorAll('.gallery-grid img').forEach(img => {
    img.addEventListener('click', function() {
        const lightbox = document.createElement('div');
        lightbox.className = 'lightbox';
        lightbox.innerHTML = `
            <div class="lightbox-content">
                <img src="${this.src}" alt="${this.alt}">
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

// Add loading animation
window.addEventListener('load', function() {
    document.body.classList.add('loaded');
});

// Lazy loading for images
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src || img.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });

    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}