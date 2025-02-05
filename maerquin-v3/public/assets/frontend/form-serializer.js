function serializeForm(formElementId) {
    const serializedForm = {};

    const parentForm = document.getElementById(formElementId);

    const formData = new FormData(parentForm);
    const formElements = formData.entries();

    for (const [name, value] of formElements) {
        serializedForm[name] = value;
    }

    const formCheckboxes = parentForm.querySelectorAll('[type="checkbox"]');

    for (const checkbox of formCheckboxes) {
        serializedForm[checkbox.name] = checkbox.checked;
    }

    return serializedForm;
}
