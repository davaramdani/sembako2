// File: public/js/countdown-timer.js
class CountdownTimer {
    constructor(targetElement, endTime) {
        this.targetElement = targetElement;
        this.endTime = new Date(endTime).getTime();
        this.interval = null;
        this.isExpired = false;
        
        this.init();
    }

    init() {
        if (isNaN(this.endTime)) {
            this.displayError('Invalid date format');
            return;
        }
        
        this.updateDisplay();
        this.startTimer();
    }

    startTimer() {
        this.interval = setInterval(() => {
            this.updateDisplay();
        }, 1000);
    }

    updateDisplay() {
        const now = new Date().getTime();
        const timeLeft = this.endTime - now;
        
        if (timeLeft <= 0) {
            this.handleExpiration();
            return;
        }
        
        const time = this.calculateTime(timeLeft);
        this.renderTimer(time);
    }

    calculateTime(timeLeft) {
        const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
        const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
        
        return {
            days: this.formatNumber(days),
            hours: this.formatNumber(hours),
            minutes: this.formatNumber(minutes),
            seconds: this.formatNumber(seconds)
        };
    }

    formatNumber(num) {
        return num < 10 ? `0${num}` : num;
    }

    renderTimer(time) {
        this.targetElement.innerHTML = `
            <div class="countdown-display">
                <div class="countdown-section">
                    <div class="countdown-number">${time.days}</div>
                    <div class="countdown-label">Hari</div>
                </div>
                <div class="countdown-separator">:</div>
                <div class="countdown-section">
                    <div class="countdown-number">${time.hours}</div>
                    <div class="countdown-label">Jam</div>
                </div>
                <div class="countdown-separator">:</div>
                <div class="countdown-section">
                    <div class="countdown-number">${time.minutes}</div>
                    <div class="countdown-label">Menit</div>
                </div>
                <div class="countdown-separator">:</div>
                <div class="countdown-section">
                    <div class="countdown-number">${time.seconds}</div>
                    <div class="countdown-label">Detik</div>
                </div>
            </div>
        `;
    }

    handleExpiration() {
        if (this.isExpired) return;
        
        this.isExpired = true;
        clearInterval(this.interval);
        
        this.targetElement.innerHTML = `
            <div class="countdown-expired">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl mb-2"></i>
                <div class="text-lg font-bold text-red-600">Waktu Pembayaran Habis!</div>
                <div class="text-sm text-gray-600 mt-1">Silakan buat pesanan baru</div>
            </div>
        `;
        
        // Optional: Redirect or show modal
        this.onExpiration();
    }

    onExpiration() {
        // Custom action when timer expires
        console.log('⏰ Timer expired!');
        
        // Example: Show modal or redirect
        // window.location.href = '/cart';
        
        // Example: Show notification
        if (window.showSingleNotification) {
            window.showSingleNotification('Waktu pembayaran telah habis!', 'error');
        }
    }

    displayError(message) {
        this.targetElement.innerHTML = `
            <div class="countdown-error">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                <span class="ml-2 text-red-600">${message}</span>
            </div>
        `;
    }

    destroy() {
        if (this.interval) {
            clearInterval(this.interval);
        }
    }
}

// Global function to initialize countdown timers
window.initCountdownTimer = function(selector, endTime) {
    const element = document.querySelector(selector);
    if (element) {
        return new CountdownTimer(element, endTime);
    }
    console.error('Countdown element not found:', selector);
    return null;
};

// Auto-initialize timers with data attributes
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-countdown]').forEach(element => {
        const endTime = element.getAttribute('data-countdown');
        new CountdownTimer(element, endTime);
    });
});
