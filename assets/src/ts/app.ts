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
        const productPrice = parseFloat(btn.dataset.product_price || '0');

        // Logic to update productId in form
        const inputId = document.getElementById('woo-quibuy-product-id') as HTMLInputElement;
        const inputQty = document.getElementById('woo-quibuy-quantity') as HTMLInputElement;

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
                    <div class="woo-quibuy-details" style="flex-grow: 1;">
                        <h4 style="margin: 0 0 5px; font-size: 16px;">${productTitle}</h4>
                        <div class="woo-quibuy-price-calc" style="display: flex; align-items: center; justify-content: space-between;">
                            <div class="qty-wrap">
                                <label style="font-size: 12px; margin-right: 5px;">SL:</label>
                                <input type="number" id="woo-quibuy-qty-display" value="1" min="1" style="width: 60px; padding: 5px; text-align: center;">
                            </div>
                            <div class="total-wrap">
                                <span id="woo-quibuy-total-price" style="color: #d26e4b; font-weight: bold; font-size: 18px;">
                                    ${this.formatMoney(productPrice)}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Bind Qty Event
            const qtyDisplay = document.getElementById('woo-quibuy-qty-display') as HTMLInputElement;
            const priceDisplay = document.getElementById('woo-quibuy-total-price');

            if (qtyDisplay && priceDisplay) {
                qtyDisplay.addEventListener('change', () => {
                    let qty = parseInt(qtyDisplay.value) || 1;
                    if (qty < 1) qty = 1;
                    qtyDisplay.value = qty.toString();

                    // Sync with hidden input
                    if (inputQty) inputQty.value = qty.toString();

                    const total = qty * productPrice;
                    priceDisplay.innerText = this.formatMoney(total);

                    // Trigger callback
                    this.triggerCallback(qty, total, productPrice);
                });
            }
        }

        if (this.modal) {
            this.modal.style.display = 'block';
            this.modal.setAttribute('aria-hidden', 'false');
        }
    }

    private formatMoney(amount: number): string {
        // Simple formatter, can be improved or overridden via JS if needed
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
    }

    private triggerCallback(quantity: number, total: number, price: number) {
        // Dispatch Custom Event
        const event = new CustomEvent('woo_quibuy_price_updated', {
            detail: {
                quantity: quantity,
                total: total,
                price: price,
                formattedTotal: this.formatMoney(total)
            }
        });
        document.dispatchEvent(event);
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
