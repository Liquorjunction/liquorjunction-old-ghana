<a href="{{ route('cart') }}">
<div class="floating-cart pulse" id="floatingCart">

    <!-- 🛒 Cart Icon -->
    <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
        <path d="M6 6H21L20 14H7L6 6Z" stroke="#fff" stroke-width="1.5"/>
        <circle cx="9" cy="20" r="1.5" fill="#fff"/>
        <circle cx="18" cy="20" r="1.5" fill="#fff"/>
    </svg>

    <!-- 🔴 Item Count -->
    <span class="cart-item-total-count-floating" id="cartCount"></span>

</div>
</a>
<style>
.floating-cart {
    position: fixed;
    right: 80px;
    bottom: 30px;
    z-index: 9999;

    height: 70px;
    width: 70px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    /* 🔥 Gradient background */
    background: linear-gradient(135deg, #fdbc00, #ff7a00);

    /* ✨ Glass effect */
    backdrop-filter: blur(10px);

    /* 💎 Modern shadow */
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);

    cursor: pointer;

    transition: all 0.3s ease;
}

/* 🟢 Hover effect */
.floating-cart:hover {
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 15px 35px rgba(0,0,0,0.3);
}

/* 🔥 Click animation */
.floating-cart:active {
    transform: scale(0.95);
}

/* 🔔 Cart count badge */
.cart-item-total-count-floating {
    position: absolute;
    top: -5px;
    right: -5px;

    background: #ff3b3b;
    color: #fff;

    font-size: 12px;
    font-weight: bold;

    padding: 4px 7px;
    border-radius: 50%;

    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

/* 🔥 Pulse animation (attract attention) */
.floating-cart.pulse {
    animation: pulse 1.5s infinite;
}

.floating-cart:hover::after {
    content: "View Cart";
    position: absolute;
    bottom: 80px;
    background: #000;
    color: #fff;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 12px;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(255, 123, 0, 0.6);
    }
    70% {
        box-shadow: 0 0 0 15px rgba(255, 123, 0, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(255, 123, 0, 0);
    }
}

/* 🔥 Shake (your existing improved) */
.floating-cart.shake {
    animation: shake 0.4s ease-in-out;
}
</style>
<script>
function shakeFloatingCart() {
    const cart = document.getElementById("floatingCart");

    cart.classList.remove("shake");
    void cart.offsetWidth; // reset animation
    cart.classList.add("shake");
}
// Optionally, update cart count here as well
// Example: window.updateFloatingCartCount = function(count) { ... }
</script>
