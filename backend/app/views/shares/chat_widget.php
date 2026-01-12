<?php
/**
 * File: app/views/shares/chat_widget.php
 * Tích hợp Chat Zalo, Facebook Messenger, Phone
 * 
 * Cách sử dụng: Include file này vào footer.php
 * <?php include 'app/views/shares/chat_widget.php'; ?>
 */
?>

<style>
/* Chat Widget Container */
.chat-widget-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 15px;
    align-items: flex-end;
}

/* Chat Button Base Style */
.chat-button {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
    text-decoration: none;
    position: relative;
    animation: pulse 2s infinite;
}

.chat-button:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
}

/* Zalo Button - Fixed */
.zalo-button {
    background: linear-gradient(135deg, #0068FF 0%, #0084FF 100%);
    position: relative;
}

.zalo-button .zalo-icon {
    width: 38px;
    height: 38px;
    background: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 24px;
    color: #0068FF;
    font-family: Arial, sans-serif;
    letter-spacing: -1px;
}

/* Alternative: Use SVG logo */
.zalo-button .zalo-logo-svg {
    width: 35px;
    height: 35px;
    fill: white;
}

/* Facebook Messenger Button */
.messenger-button {
    background: linear-gradient(135deg, #00B2FF 0%, #006AFF 100%);
}

.messenger-button i {
    font-size: 30px;
    color: white;
}

/* Phone Button */
.phone-button {
    background: linear-gradient(135deg, #00A74F 0%, #00c853 100%);
}

.phone-button i {
    font-size: 28px;
    color: white;
    animation: shake 1s infinite;
}

/* Tooltip */
.chat-tooltip {
    position: absolute;
    right: 75px;
    background: white;
    color: #333;
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    white-space: nowrap;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
}

.chat-button:hover .chat-tooltip {
    opacity: 1;
}

/* Pulse Animation */
@keyframes pulse {
    0%, 100% {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15), 0 0 0 0 rgba(0, 168, 255, 0.7);
    }
    50% {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15), 0 0 0 10px rgba(0, 168, 255, 0);
    }
}

/* Shake Animation for Phone */
@keyframes shake {
    0%, 100% { transform: rotate(0deg); }
    10%, 30%, 50%, 70%, 90% { transform: rotate(-10deg); }
    20%, 40%, 60%, 80% { transform: rotate(10deg); }
}

/* Bounce In Animation */
@keyframes bounceIn {
    0% {
        opacity: 0;
        transform: scale(0.3) translateY(100px);
    }
    50% {
        opacity: 1;
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        transform: scale(1);
    }
}

.chat-button {
    animation: bounceIn 0.6s ease-out, pulse 2s infinite 1s;
}

.chat-button:nth-child(2) {
    animation-delay: 0.1s;
}

.chat-button:nth-child(3) {
    animation-delay: 0.2s;
}

/* Online Badge */
.online-badge {
    position: absolute;
    top: 5px;
    right: 5px;
    width: 14px;
    height: 14px;
    background: #4CAF50;
    border: 2px solid white;
    border-radius: 50%;
    animation: blink 2s infinite;
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* Responsive */
@media (max-width: 768px) {
    .chat-widget-container {
        bottom: 15px;
        right: 15px;
    }

    .chat-button {
        width: 55px;
        height: 55px;
    }

    .zalo-button .zalo-icon {
        width: 32px;
        height: 32px;
        font-size: 20px;
    }

    .messenger-button i,
    .phone-button i {
        font-size: 26px;
    }

    .chat-tooltip {
        display: none;
    }
}
</style>

<!-- Chat Widget Container -->
<div class="chat-widget-container">
    <!-- Zalo Button với logo mới -->
    <a href="https://zalo.me/0346024870" 
       target="_blank" 
       class="chat-button zalo-button"
       title="Chat với chúng tôi qua Zalo">
        <span class="online-badge"></span>
        <!-- Option 1: Dùng text "Z" style giống logo Zalo -->
        <span class="zalo-icon">Z</span>
        
        <!-- Option 2: Hoặc dùng SVG logo Zalo (uncomment nếu muốn dùng) -->
        <!-- <svg class="zalo-logo-svg" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
            <path d="M24 4C12.96 4 4 12.96 4 24s8.96 20 20 20 20-8.96 20-20S35.04 4 24 4zm0 36c-8.82 0-16-7.18-16-16S15.18 8 24 8s16 7.18 16 16-7.18 16-16 16zm-3.5-23h-4v2h4v-2zm0 4h-7v2h7v-2zm0 4h-5v2h5v-2zm7-8v8l6-4-6-4z"/>
        </svg> -->
        
        <span class="chat-tooltip">Chat qua Zalo</span>
    </a>

    <!-- Facebook Messenger Button -->
    <a href="https://www.facebook.com/onguyen.40148" 
       target="_blank" 
       class="chat-button messenger-button"
       title="Nhắn tin qua Facebook Messenger">
        <span class="online-badge"></span>
        <i class="bi bi-messenger"></i>
        <span class="chat-tooltip">Chat qua Messenger</span>
    </a>

    <!-- Phone Button -->
    <a href="tel:0346024870" 
       class="chat-button phone-button"
       title="Gọi điện thoại ngay">
        <i class="bi bi-telephone-fill"></i>
        <span class="chat-tooltip">0346 024 870</span>
    </a>
</div>

<script>
// Thêm hiệu ứng khi click
document.querySelectorAll('.chat-button').forEach(button => {
    button.addEventListener('click', function(e) {
        // Tạo hiệu ứng ripple
        const ripple = document.createElement('div');
        ripple.style.position = 'absolute';
        ripple.style.borderRadius = '50%';
        ripple.style.background = 'rgba(255, 255, 255, 0.6)';
        ripple.style.width = '100%';
        ripple.style.height = '100%';
        ripple.style.top = '0';
        ripple.style.left = '0';
        ripple.style.transform = 'scale(0)';
        ripple.style.transition = 'transform 0.6s ease-out, opacity 0.6s ease-out';
        ripple.style.opacity = '1';
        
        this.appendChild(ripple);
        
        setTimeout(() => {
            ripple.style.transform = 'scale(2)';
            ripple.style.opacity = '0';
        }, 10);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    });

    // Tracking click (Google Analytics - tùy chọn)
    button.addEventListener('click', function() {
        const platform = this.classList.contains('zalo-button') ? 'Zalo' :
                        this.classList.contains('messenger-button') ? 'Messenger' : 'Phone';
        
        // Nếu có Google Analytics
        if (typeof gtag !== 'undefined') {
            gtag('event', 'click', {
                'event_category': 'Contact',
                'event_label': platform
            });
        }
        
        console.log('User clicked:', platform);
    });
});

// Tự động ẩn/hiện khi scroll
let lastScrollTop = 0;
window.addEventListener('scroll', function() {
    const chatWidget = document.querySelector('.chat-widget-container');
    if (!chatWidget) return;
    
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    if (scrollTop > lastScrollTop && scrollTop > 300) {
        chatWidget.style.opacity = '0.7';
    } else {
        chatWidget.style.opacity = '1';
    }
    lastScrollTop = scrollTop;
});
</script>