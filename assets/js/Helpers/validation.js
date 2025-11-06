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
