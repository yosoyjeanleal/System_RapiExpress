

            // ============================================================
            // HELPERS DE VALIDACIÓN
            // ============================================================
            function ensureFeedback($input) {
                let $fb = $input.siblings('.invalid-feedback').first();
                if ($fb.length === 0) {
                    $fb = $('<div class="invalid-feedback"></div>');
                    $input.after($fb);
                }
                return $fb;
            }

            function markInvalid($input, message) {
                const $fb = ensureFeedback($input);
                $input.addClass('is-invalid').removeClass('is-valid');
                $fb.text(message).show();
            }

            function markValid($input) {
                const $fb = ensureFeedback($input);
                $input.addClass('is-valid').removeClass('is-invalid');
                $fb.text('').hide();
            }

            function clearValidation($input) {
                const $fb = ensureFeedback($input);
                $input.removeClass('is-valid is-invalid');
                $fb.text('').hide();
            }

            function firstInvalidFocus($form) {
                const $first = $form.find('.is-invalid').first();
                if ($first.length) {
                    $first.focus();
                }
            }

            // ============================================================
            // REGLAS DE VALIDACIÓN CENTRALIZADAS
            // ============================================================
            const regexNombre = /^[A-Za-z0-9\sáéíóúÁÉÍÓÚñÑ.,\-()]{3,50}$/;

            function validarNombreCampo(value) {
                if (value === '') return { ok: false, msg: 'El nombre es obligatorio.' };
                if (value.length < 3) return { ok: false, msg: 'Mínimo 3 caracteres.' };
                if (value.length > 50) return { ok: false, msg: 'Máximo 50 caracteres.' };
                if (!regexNombre.test(value)) return { ok: false, msg: 'Solo letras, números y (,.-()). 3-50 caracteres.' };
                return { ok: true, msg: '' };
            }

            function validarDireccionCampo(value) {
                if (value === '') return { ok: false, msg: 'La dirección es obligatoria.' };
                if (value.length < 5) return { ok: false, msg: 'Mínimo 5 caracteres.' };
                if (value.length > 100) return { ok: false, msg: 'Máximo 100 caracteres.' };
                return { ok: true, msg: '' };
            }

            // ============================================================
            // VALIDACIÓN EN TIEMPO REAL
            // ============================================================
            $(document).on('input', 'input[name="Casillero_Nombre"]', function() {
                const $this = $(this);
                const v = $this.val().trim();
                const res = validarNombreCampo(v);
                if (!res.ok) markInvalid($this, res.msg);
                else markValid($this);
            });

            $(document).on('input', 'input[name="Direccion"]', function() {
                const $this = $(this);
                const v = $this.val().trim();
                const res = validarDireccionCampo(v);
                if (!res.ok) markInvalid($this, res.msg);
                else markValid($this);
            });
            