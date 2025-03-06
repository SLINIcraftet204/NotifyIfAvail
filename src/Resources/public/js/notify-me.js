document.addEventListener('DOMContentLoaded', function () {
    console.log("✅ NotifyMe Script geladen");

    const notifyContainer = document.getElementById('notify-me-container');
    if (!notifyContainer) {
        console.error("❌ NotifyMe: Container nicht gefunden!");
        return;
    }

    const notifyButton = document.getElementById('notify-me-button');
    const submitButton = document.getElementById('submit-notify');
    const emailInput = document.getElementById('notify-me-email');

    if (notifyButton) {
        notifyButton.addEventListener('click', function () {
            const email = this.dataset.customerEmail;
            const productId = this.dataset.productId;
            sendNotificationRequest(email, productId);
        });
    }

    if (submitButton) {
        submitButton.addEventListener('click', function () {
            const email = emailInput.value;
            const productId = this.dataset.productId;

            if (!email) {
                alert("Bitte geben Sie eine gültige E-Mail-Adresse ein.");
                return;
            }

            sendNotificationRequest(email, productId);
        });
    }

    function sendNotificationRequest(email, productId) {
        console.log(`📩 Anfrage für Produkt: ${productId}, Email: ${email}`);

        fetch('/notification/subscribe', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `email=${encodeURIComponent(email)}&productId=${encodeURIComponent(productId)}`
        })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
            })
            .catch(error => console.error('❌ Fehler:', error));
    }
});
