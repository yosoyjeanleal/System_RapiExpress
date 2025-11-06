const validationRules = {
    username: {
        pattern: /^[a-zA-Z0-9_]{3,20}$/,
        message: 'invalid_username'
    },
    email: {
        pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
        message: 'invalid_email'
    },
    notEmpty: {
        pattern: /.*\S.*/,
        message: 'field_not_empty'
    }
};

function validateField(value, rule) {
    if (!rule || !validationRules[rule]) {
        return true;
    }
    return validationRules[rule].pattern.test(value);
}

/**
 * Valida una contraseña según los requisitos definidos.
 *
 * @param {string} password - La contraseña a validar.
 * @returns {object} - Un objeto que indica qué requisitos se cumplen.
 */
function validatePassword(password) {
    return {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)
    };
}
