// assets/js/form_handler.js

/**
 * Handles form submissions via Fetch API
 * @param {Event} event - The submit event
 * @param {string} formType - The type of form ('contacto', 'newsletter', 'presupuesto')
 */
async function handleFormSubmit(event, formType) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    // Add form type to the payload
    data.form_type = formType;

    const submitButton = form.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.innerHTML;

    // Disable button and show loading state
    submitButton.disabled = true;
    submitButton.innerHTML = 'Enviando...';

    // Remove any existing messages
    const existingMessage = form.querySelector('.form-message');
    if (existingMessage) {
        existingMessage.remove();
    }

    try {
        // Adjust the URL if the API is hosted elsewhere
        const response = await fetch('api/form_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (response.ok && result.success) {
            showMessage(form, result.message, 'success');
            form.reset();
        } else {
            showMessage(form, result.message || 'Ha ocurrido un error.', 'error');
        }

    } catch (error) {
        console.error('Error submitting form:', error);
        showMessage(form, 'Error de conexión. Inténtalo de nuevo.', 'error');
    } finally {
        // Restore button state
        submitButton.disabled = false;
        submitButton.innerHTML = originalButtonText;
    }
}

/**
 * Displays a success or error message inside the form
 * @param {HTMLFormElement} form
 * @param {string} message
 * @param {string} type - 'success' or 'error'
 */
function showMessage(form, message, type) {
    const msgDiv = document.createElement('div');
    msgDiv.className = `form-message mt-4 p-3 rounded text-center ${type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`;
    msgDiv.textContent = message;
    form.appendChild(msgDiv);

    // Auto-remove success messages after a few seconds
    if (type === 'success') {
        setTimeout(() => {
            msgDiv.remove();
        }, 5000);
    }
}
