import '../scss/style.scss';

class WooQuiBuy {
    private modal: HTMLElement | null;
    private closeBtn: HTMLElement | null;
    private overlay: HTMLElement | null;
    private btns: NodeListOf<HTMLElement>;
    private form: HTMLFormElement | null;

    constructor() {
        this.modal = document.getElementById('woo-quibuy-modal');
        this.closeBtn = this.modal?.querySelector('.woo-quibuy-close') || null;
        this.overlay = this.modal?.querySelector('.woo-quibuy-overlay') || null;
        this.btns = document.querySelectorAll('.woo-quibuy-btn');
        this.form = document.getElementById('woo-quibuy-checkout-form') as HTMLFormElement;

        this.initEvents();
    }

    private initEvents(): void {
        this.btns.forEach(btn => {
            btn.addEventListener('click', (e) => this.openModal(e));
        });

        this.closeBtn?.addEventListener('click', () => this.closeModal());
        this.overlay?.addEventListener('click', () => this.closeModal());

        this.form?.addEventListener('submit', (e) => this.submitForm(e));
    }

    private openModal(e: Event): void {
        e.preventDefault();
        const btn = e.currentTarget as HTMLElement;
        const productId = btn.dataset.product_id;
        const productImage = btn.dataset.product_image;
        const productTitle = btn.dataset.product_title;

        // Logic to update productId in form
        const inputId = document.getElementById('woo-quibuy-product-id') as HTMLInputElement;
        if (inputId && productId) {
            inputId.value = productId;
        }

        // Update Product Summary
        const summary = this.modal?.querySelector('.woo-quibuy-product-summary');
        if (summary) {
            summary.innerHTML = `
                <div class="woo-quibuy-product-info" style="display: flex; align-items: center; margin-bottom: 20px;">
                    <div class="woo-quibuy-thumb" style="width: 60px; height: 60px; margin-right: 15px; flex-shrink: 0;">
                        <img src="${productImage}" alt="${productTitle}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;">
                    </div>
                    <div class="woo-quibuy-details">
                        <h4 style="margin: 0; font-size: 16px;">${productTitle}</h4>
                    </div>
                </div>
            `;
        }

        if (this.modal) {
            this.modal.style.display = 'block';
            this.modal.setAttribute('aria-hidden', 'false');
        }
    }

    private closeModal(): void {
        if (this.modal) {
            this.modal.style.display = 'none';
            this.modal.setAttribute('aria-hidden', 'true');
        }
    }

    private submitForm(e: Event): void {
        e.preventDefault();
        if (!this.form) return;

        const formData = new FormData(this.form);
        formData.append('action', 'woo_quibuy_process_order');
        // @ts-ignore
        formData.append('nonce', wooQuiBuyParams.nonce);

        const submitBtn = this.form.querySelector('button[type="submit"]') as HTMLButtonElement;
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerText = 'Đang xử lý...';
        }

        fetch(
            // @ts-ignore
            wooQuiBuyParams.ajaxurl,
            {
                method: 'POST',
                body: formData
            }
        )
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.data.redirect_url) {
                        window.location.href = data.data.redirect_url;
                    } else {
                        const formBody = this.modal?.querySelector('.woo-quibuy-body');
                        if (formBody) {
                            formBody.innerHTML = `<div class="woo-quibuy-success-message" style="text-align:center; padding: 20px;">
                                <h4 style="color: green;">${data.data.message}</h4>
                                <p>${'Cảm ơn bạn đã đặt hàng.'}</p>
                                <button class="button woo-quibuy-close-btn" onclick="document.querySelector('.woo-quibuy-close').click()">Đóng</button>
                            </div>`;
                        }
                    }
                } else {
                    alert(data.data.message || 'Có lỗi xảy ra.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra, vui lòng thử lại.');
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Hoàn tất đơn hàng';
                }
            });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new WooQuiBuy();
});
