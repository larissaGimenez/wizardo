@if (session()->has('message'))
    <div class="alert-toast-magical" x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition>
        <div class="toast-content">
            <span class="toast-icon">✨</span>
            <span class="toast-message">{{ session('message') }}</span>
        </div>
        <button @click="show = false" class="toast-close">✕</button>
    </div>
@endif

@if (session()->has('error'))
    <div class="alert-toast-error" x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition>
        <div class="toast-content">
            <span class="toast-icon">⚡</span>
            <span class="toast-message">{{ session('error') }}</span>
        </div>
        <button @click="show = false" class="toast-close">✕</button>
    </div>
@endif

<style>
    /* Alert Toast */
    .alert-toast-magical,
    .alert-toast-error {
        position: fixed;
        top: 1.5rem;
        right: 1.5rem;
        padding: 0.8rem 1.2rem;
        border-radius: 0.8rem;
        font-family: 'Cinzel', serif;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        z-index: 3000;
        animation: slideInRight 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        max-width: calc(100vw - 3rem);
        width: 350px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    }

    .alert-toast-magical {
        background: linear-gradient(135deg, #fcf8ed, #f5ecd5);
        border: 2px solid #d4af37;
        color: #2c1810;
    }

    .alert-toast-error {
        background: linear-gradient(135deg, #fcf8ed, #fee2e2);
        border: 2px solid #dc2626;
        color: #2c1810;
    }

    .toast-content {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        flex: 1;
    }

    .toast-icon {
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    .toast-message {
        font-weight: 600;
        letter-spacing: 0.5px;
        text-align: left;
    }

    .toast-close {
        background: none;
        border: none;
        color: #2c1810;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 0.2rem 0.4rem;
        opacity: 0.6;
        transition: opacity 0.2s;
        font-weight: bold;
        flex-shrink: 0;
    }

    .toast-close:hover {
        opacity: 1;
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
</style>
