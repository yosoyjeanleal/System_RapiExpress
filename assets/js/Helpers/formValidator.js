class FormValidator {
    constructor(form, fields) {
        this.form = form;
        this.fields = fields;
        this.errors = {};

        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            if (this.validate()) {
                this.form.submit();
            }
        });
    }

    validate() {
        this.errors = {};
        let isValid = true;

        this.fields.forEach(field => {
            const input = this.form.querySelector(`#${field.name}`);
            const value = input.value;
            const rules = field.rules;

            rules.forEach(rule => {
                if (!validateField(value, rule)) {
                    this.errors[field.name] = validationRules[rule].message;
                    isValid = false;
                }
            });
        });

        this.displayErrors();
        return isValid;
    }

    displayErrors() {
        this.clearErrors();
        for (const fieldName in this.errors) {
            const input = this.form.querySelector(`#${fieldName}`);
            const errorContainer = document.createElement('div');
            errorContainer.classList.add('invalid-feedback');
            errorContainer.innerText = Lang.get(this.errors[fieldName]);
            input.parentElement.appendChild(errorContainer);
            input.classList.add('is-invalid');
        }
    }

    clearErrors() {
        const errorMessages = this.form.querySelectorAll('.invalid-feedback');
        errorMessages.forEach(error => error.remove());
        const invalidFields = this.form.querySelectorAll('.is-invalid');
        invalidFields.forEach(field => field.classList.remove('is-invalid'));
    }
}
