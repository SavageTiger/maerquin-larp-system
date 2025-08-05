function serializeForm(formElementId) {
    const serializedForm = {};

    const parentForm = document.getElementById(formElementId);

    const formData = new FormData(parentForm);
    const formElements = formData.entries();

    for (const [name, value] of formElements) {
        if (name.trim() === '') {
            continue;
        }

        serializedForm[name] = value;
    }

    const formCheckboxes = parentForm.querySelectorAll('[type="checkbox"]');

    for (const checkbox of formCheckboxes) {
        if (checkbox.name.trim() === '') {
            continue;
        }

        serializedForm[checkbox.name] = checkbox.checked;
    }

    return serializedForm;
}
