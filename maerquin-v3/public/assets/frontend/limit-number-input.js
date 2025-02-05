document.addEventListener('DOMContentLoaded', () => {
    const numberInputs = document.querySelectorAll('input[numbers]');

    numberInputs.forEach(input => {
        input.addEventListener('input', (event) => {
            event.target.value = event.target.value.replace(/[^0-9]/g, '');
        });
    });
});
