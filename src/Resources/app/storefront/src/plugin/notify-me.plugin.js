import Plugin from 'src/plugin-system/plugin.class';

export default class NotifyMe extends Plugin {
    init() {
        this.notifyButton = this.el;
        this.notifyForm = this.el.nextElementSibling;
        this.emailInput = this.notifyForm.querySelector('#notify-me-email');
        this.submitButton = this.notifyForm.querySelector('#submit-notify');

        this.registerEvents();
    }

    registerEvents() {
        this.notifyButton.addEventListener('click', () => this.showForm());
        this.submitButton.addEventListener('click', () => this.submitForm());
    }

    showForm() {
        this.notifyForm.style.display = 'block';
    }

    submitForm() {
        const email = this.emailInput.value;
        const productId = this.notifyButton.dataset.productId;

        fetch('/notification/subscribe', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `email=${encodeURIComponent(email)}&productId=${encodeURIComponent(productId)}`
        })
            .then(response => response.json())
            .then(data => alert(data.message))
            .catch(error => console.error('Error:', error));
    }
}
