<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<title>Mi Perfil - RapiExpress</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="assets/img/logo-rapi.ico" type="image/x-icon">
	<?php include __DIR__ . '/../partels/barras.php'; ?>


	<style>
		.profile-photo {
			text-align: center;
			margin-bottom: 20px;
			position: relative;
		}

		.profile-photo .avatar-photo {
			width: 150px;
			height: 150px;
			border-radius: 50%;
			object-fit: cover;
			border: 3px solid #e0e0e0;
			transition: all 0.3s ease;
		}

		.profile-photo .avatar-photo:hover {
			border-color: #1b00ff;
			box-shadow: 0 0 15px rgba(27, 0, 255, 0.3);
		}

		.profile-photo .edit-avatar {
			position: absolute;
			bottom: 10px;
			right: 50%;
			transform: translateX(75px);
			background: #1b00ff;
			color: white;
			width: 35px;
			height: 35px;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			cursor: pointer;
			transition: all 0.3s;
			box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
		}

		.profile-photo .edit-avatar:hover {
			background: #0056b3;
			transform: translateX(75px) scale(1.1);
			box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
		}

		.profile-info ul {
			list-style: none;
			padding: 0;
		}

		.profile-info ul li {
			padding: 10px 0;
			border-bottom: 1px solid #f1f1f1;
		}

		.profile-info ul li span {
			font-weight: 600;
			color: #1b00ff;
			display: inline-block;
			width: 140px;
		}

		.hidden {
			display: none !important;
		}

		/* Estilos para validación */
		.form-control.is-invalid {
			border-color: #dc3545;
		}

		.form-control.is-valid {
			border-color: #28a745;
		}

		.invalid-feedback {
			display: none;
			color: #dc3545;
			font-size: 0.875rem;
			margin-top: 0.25rem;
		}

		.form-control.is-invalid~.invalid-feedback {
			display: block;
		}

		/* Overlay de carga */
		.loading-overlay {
			display: none;
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(0, 0, 0, 0.5);
			z-index: 9999;
			justify-content: center;
			align-items: center;
		}

		.loading-overlay.active {
			display: flex;
		}

		.loading-spinner {
			text-align: center;
			color: white;
		}

		.loading-spinner i {
			font-size: 3rem;
			margin-bottom: 1rem;
		}

		/* Botón deshabilitado */
		.btn-primary:disabled {
			opacity: 0.6;
			cursor: not-allowed;
		}
	</style>
</head>

<body>
	<?php include __DIR__ . '/../partels/navegacion.php'; ?>

	<!-- Loading Overlay -->
	<div class="loading-overlay" id="loadingOverlay">
		<div class="loading-spinner">
			<i class="fa fa-spinner fa-spin"></i>
			<p>Actualizando perfil...</p>
		</div>
	</div>

	<div class="main-container">
		<div class="pd-ltr-20 xs-pd-20-10">
			<div class="min-height-200px">
				<div class="page-header">
					<div class="row">
						<div class="col-md-12 col-sm-12">
							<div class="title">
								<h4>Perfil</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item">
										<a href="index.php?c=dashboard&a=index">RapiExpress</a>
									</li>
									<li class="breadcrumb-item active" aria-current="page">
										Perfil
									</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>

				<div class="row">
					<!-- Columna Izquierda - Info del Usuario -->
					<div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-30">
						<div class="pd-20 card-box height-100-p">
							<div class="profile-photo">
								<a href="#" onclick="document.getElementById('imagenInput').click(); return false;" class="edit-avatar" title="Cambiar foto">
									<i class="fa fa-pencil"></i>
								</a>
								<img id="previewImage" src="uploads/<?= htmlspecialchars($usuario['imagen_archivo'] ?? 'default.png') ?>" alt="Foto de perfil" class="avatar-photo" />
							</div>

							<h5 class="text-center h5 mb-0" id="nombreCompleto">
								<?= htmlspecialchars($usuario['Nombres_Usuario'] . ' ' . $usuario['Apellidos_Usuario']) ?>
							</h5>
							<p class="text-center text-muted font-14">
								@<?= htmlspecialchars($usuario['Username']) ?>
							</p>

							<div class="profile-info">
								<h5 class="mb-20 h5 text-blue">Información de Contacto</h5>
								<ul>
									<li>
										<span>Cédula:</span>
										<?= htmlspecialchars($usuario['Cedula_Identidad']) ?>
									</li>
									<li>
										<span>Email:</span>
										<span id="emailDisplay"><?= htmlspecialchars($usuario['Correo_Usuario'] ?? 'No especificado') ?></span>
									</li>
									<li>
										<span>Teléfono:</span>
										<span id="telefonoDisplay"><?= htmlspecialchars($usuario['Telefono_Usuario'] ?? 'No especificado') ?></span>
									</li>
									<li>
										<span>Dirección:</span>
										<span id="direccionDisplay"><?= htmlspecialchars($usuario['Direccion_Usuario'] ?? 'No especificada') ?></span>
									</li>
									<li>
										<span>Sucursal:</span>
										<?= htmlspecialchars($usuario['Sucursal_Nombre'] ?? 'No asignada') ?>
									</li>
									<li>
										<span>Cargo:</span>
										<?= htmlspecialchars($usuario['Cargo_Nombre'] ?? 'No asignado') ?>
									</li>
									<li>
										<span>Fecha Registro:</span>
										<?= date('d/m/Y', strtotime($usuario['Fecha_Registro'])) ?>
									</li>
								</ul>
							</div>
						</div>
					</div>

					<!-- Columna Derecha - Formulario -->
					<div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 mb-30">
						<div class="card-box height-100-p overflow-hidden">
							<div class="profile-tab height-100-p">
								<div class="tab height-100-p">
									<ul class="nav nav-tabs customtab" role="tablist">
										<li class="nav-item">
											<a class="nav-link active" data-toggle="tab" href="#setting" role="tab">
												<i class="icon-copy dw dw-settings2"></i> Editar Perfil
											</a>
										</li>
									</ul>

									<div class="tab-content">
										<div class="tab-pane fade show active height-100-p" id="setting" role="tabpanel">
											<div class="profile-setting">
												<div class="pd-20">
													<form id="formPerfil" novalidate>
														<!-- Input file oculto -->
														<input type="file" id="imagenInput" accept="image/jpeg,image/png,image/gif,image/webp" class="hidden">

														<ul class="profile-edit-list row">
															<li class="weight-500 col-md-12">
																<h4 class="text-blue h5 mb-20">
																	Editar Información Personal
																</h4>

																<div class="row">
																	<div class="col-md-6">
																		<div class="form-group">
																			<label>Cédula de Identidad</label>
																			<input class="form-control form-control-lg" type="text" value="<?= htmlspecialchars($usuario['Cedula_Identidad']) ?>" disabled />
																		</div>
																	</div>
																	<div class="col-md-6">
																		<div class="form-group">
																			<label>Nombre de Usuario</label>
																			<input class="form-control form-control-lg" type="text" value="<?= htmlspecialchars($usuario['Username']) ?>" disabled />
																		</div>
																	</div>
																</div>

																<div class="row">
																	<div class="col-md-6">
																		<div class="form-group">
																			<label>Nombres <span class="text-danger">*</span></label>
																			<input class="form-control form-control-lg" type="text" name="Nombres_Usuario" id="Nombres_Usuario" value="<?= htmlspecialchars($usuario['Nombres_Usuario']) ?>" required maxlength="50" data-original="<?= htmlspecialchars($usuario['Nombres_Usuario']) ?>" />
																			<div class="invalid-feedback">Los nombres solo pueden contener letras y espacios (2-50 caracteres)</div>
																		</div>
																	</div>
																	<div class="col-md-6">
																		<div class="form-group">
																			<label>Apellidos <span class="text-danger">*</span></label>
																			<input class="form-control form-control-lg" type="text" name="Apellidos_Usuario" id="Apellidos_Usuario" value="<?= htmlspecialchars($usuario['Apellidos_Usuario']) ?>" required maxlength="50" data-original="<?= htmlspecialchars($usuario['Apellidos_Usuario']) ?>" />
																			<div class="invalid-feedback">Los apellidos solo pueden contener letras y espacios (2-50 caracteres)</div>
																		</div>
																	</div>
																</div>

																<div class="row">
																	<div class="col-md-6">
																		<div class="form-group">
																			<label>Correo Electrónico</label>
																			<input class="form-control form-control-lg" type="email" name="Correo_Usuario" id="Correo_Usuario" value="<?= htmlspecialchars($usuario['Correo_Usuario'] ?? '') ?>" maxlength="100" data-original="<?= htmlspecialchars($usuario['Correo_Usuario'] ?? '') ?>" />
																			<div class="invalid-feedback">Ingrese un correo electrónico válido</div>
																		</div>
																	</div>
																	<div class="col-md-6">
																		<div class="form-group">
																			<label>Teléfono</label>
																			<input class="form-control form-control-lg" type="tel" name="Telefono_Usuario" id="Telefono_Usuario" value="<?= htmlspecialchars($usuario['Telefono_Usuario'] ?? '') ?>" maxlength="15" placeholder="Ej: 8095551234" data-original="<?= htmlspecialchars($usuario['Telefono_Usuario'] ?? '') ?>" />
																			<div class="invalid-feedback">El teléfono debe contener 7-15 dígitos numéricos</div>
																		</div>
																	</div>
																</div>

																<div class="form-group">
																	<label>Dirección</label>
																	<textarea class="form-control" name="Direccion_Usuario" id="Direccion_Usuario" rows="3" maxlength="255" data-original="<?= htmlspecialchars($usuario['Direccion_Usuario'] ?? '') ?>"><?= htmlspecialchars($usuario['Direccion_Usuario'] ?? '') ?></textarea>
																	<small class="form-text text-muted">Máximo 255 caracteres</small>
																</div>

																<div class="form-group mb-0">
																	<button type="submit" class="btn btn-primary" id="btnGuardar" disabled>
																		<i class="icon-copy dw dw-diskette"></i> Actualizar Información
																	</button>
																	<a href="index.php?c=dashboard&a=index" class="btn btn-secondary ml-2">
																		<i class="icon-copy dw dw-left-arrow"></i> Cancelar
																	</a>
																	<small class="text-muted ml-2" id="estadoCambios">No hay cambios pendientes</small>
																</div>
															</li>
														</ul>
													</form>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


	<script>
		document.addEventListener('DOMContentLoaded', () => {
			const form = document.getElementById('formPerfil');
			const imagenInput = document.getElementById('imagenInput');
			const previewImage = document.getElementById('previewImage');
			const loadingOverlay = document.getElementById('loadingOverlay');
			const btnGuardar = document.getElementById('btnGuardar');
			const estadoCambios = document.getElementById('estadoCambios');

			let archivoImagen = null;
			const imagenOriginal = previewImage.src;
			let hayImagenNueva = false;

			// ========== DETECTAR CAMBIOS ==========
			function detectarCambios() {
				let hayCambios = hayImagenNueva;

				// Verificar cada campo del formulario
				const campos = form.querySelectorAll('[data-original]');
				campos.forEach(campo => {
					const valorActual = campo.value.trim();
					const valorOriginal = campo.getAttribute('data-original').trim();

					if (valorActual !== valorOriginal) {
						hayCambios = true;
					}
				});

				// Actualizar estado del botón y mensaje
				if (hayCambios) {
					btnGuardar.disabled = false;
					estadoCambios.textContent = '✓ Hay cambios sin guardar';
					estadoCambios.className = 'text-warning ml-2';
				} else {
					btnGuardar.disabled = true;
					estadoCambios.textContent = 'No hay cambios pendientes';
					estadoCambios.className = 'text-muted ml-2';
				}

				return hayCambios;
			}

			// ========== MANEJO DE IMAGEN ==========
			imagenInput.addEventListener('change', function(e) {
				const file = this.files[0];

				if (!file) {
					archivoImagen = null;
					hayImagenNueva = false;
					previewImage.src = imagenOriginal;
					detectarCambios();
					return;
				}

				// Validar tipo de archivo
				const tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
				if (!tiposPermitidos.includes(file.type)) {
					Swal.fire({
						icon: 'error',
						title: 'Tipo de archivo no válido',
						text: 'Solo se permiten archivos JPG, PNG, GIF o WebP',
						confirmButtonColor: '#1b00ff'
					});
					this.value = '';
					archivoImagen = null;
					hayImagenNueva = false;
					previewImage.src = imagenOriginal;
					detectarCambios();
					return;
				}

				// Validar tamaño (5MB)
				if (file.size > 5_000_000) {
					Swal.fire({
						icon: 'error',
						title: 'Archivo muy grande',
						text: 'El archivo no debe superar 5MB',
						confirmButtonColor: '#1b00ff'
					});
					this.value = '';
					archivoImagen = null;
					hayImagenNueva = false;
					previewImage.src = imagenOriginal;
					detectarCambios();
					return;
				}

				// Preview de la imagen
				const reader = new FileReader();
				reader.onload = function(e) {
					previewImage.src = e.target.result;
					archivoImagen = file;
					hayImagenNueva = true;
					detectarCambios();
				};
				reader.readAsDataURL(file);
			});

			// ========== VALIDACIONES EN TIEMPO REAL ==========
			const validaciones = {
				Nombres_Usuario: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,50}$/,
				Apellidos_Usuario: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,50}$/,
				Telefono_Usuario: /^\d{7,15}$/,
				Correo_Usuario: /^[^\s@]+@[^\s@]+\.[^\s@]+$/
			};

			// Validar campos en tiempo real
			Object.keys(validaciones).forEach(campo => {
				const input = document.getElementById(campo);
				if (!input) return;

				input.addEventListener('input', function() {
					validarCampo(this, validaciones[campo]);
					detectarCambios();
				});

				input.addEventListener('blur', function() {
					validarCampo(this, validaciones[campo]);
				});
			});

			// Detectar cambios en dirección
			const direccionInput = document.getElementById('Direccion_Usuario');
			if (direccionInput) {
				direccionInput.addEventListener('input', detectarCambios);
			}

			function validarCampo(input, regex) {
				const valor = input.value.trim();

				// Campos opcionales vacíos son válidos
				if (!input.required && valor === '') {
					input.classList.remove('is-invalid', 'is-valid');
					return true;
				}

				// Campos obligatorios no pueden estar vacíos
				if (input.required && valor === '') {
					input.classList.add('is-invalid');
					input.classList.remove('is-valid');
					return false;
				}

				// Validar con regex
				if (regex && !regex.test(valor)) {
					input.classList.add('is-invalid');
					input.classList.remove('is-valid');
					return false;
				}

				input.classList.add('is-valid');
				input.classList.remove('is-invalid');
				return true;
			}

			// ========== ENVÍO DEL FORMULARIO ==========
			form.addEventListener('submit', async (e) => {
				e.preventDefault();
				e.stopPropagation();

				// Verificar si hay cambios
				if (!detectarCambios()) {
					Swal.fire({
						icon: 'info',
						title: 'Sin cambios',
						text: 'No hay cambios para guardar',
						confirmButtonColor: '#1b00ff'
					});
					return;
				}

				// Validar todos los campos antes de enviar
				let formularioValido = true;
				Object.keys(validaciones).forEach(campo => {
					const input = document.getElementById(campo);
					if (input && !validarCampo(input, validaciones[campo])) {
						formularioValido = false;
					}
				});

				if (!formularioValido) {
					Swal.fire({
						icon: 'warning',
						title: 'Campos inválidos',
						text: 'Por favor, corrija los errores en el formulario',
						confirmButtonColor: '#1b00ff'
					});
					return;
				}

				// Mostrar loading
				loadingOverlay.classList.add('active');
				btnGuardar.disabled = true;

				try {
					// Crear FormData
					const formData = new FormData(form);

					// Agregar imagen si hay una nueva
					if (archivoImagen) {
						formData.append('imagen', archivoImagen);
					}

					// Enviar datos
					const response = await fetch('index.php?c=perfil&a=actualizar', {
						method: 'POST',
						body: formData
					});

					if (!response.ok) {
						throw new Error(`Error HTTP: ${response.status}`);
					}

					const data = await response.json();

					// Ocultar loading
					loadingOverlay.classList.remove('active');

					if (data.estado === 'success') {
						// Mostrar notificación de éxito
						await Swal.fire({
							icon: 'success',
							title: '¡Perfil actualizado!',
							text: data.mensaje || 'Los cambios se han guardado correctamente',
							confirmButtonColor: '#1b00ff',
							confirmButtonText: 'Entendido',
							allowOutsideClick: false,
							allowEscapeKey: false
						});

						// Actualizar los valores originales sin recargar la página
						const campos = form.querySelectorAll('[data-original]');
						campos.forEach(campo => {
							campo.setAttribute('data-original', campo.value.trim());
						});

						// Actualizar displays en el panel izquierdo
						document.getElementById('nombreCompleto').textContent =
							document.getElementById('Nombres_Usuario').value + ' ' +
							document.getElementById('Apellidos_Usuario').value;
						// === ACTUALIZAR NOMBRE EN LA BARRA SUPERIOR ===
						const nuevoNombre = document.getElementById('Nombres_Usuario').value.trim() + ' ' +
							document.getElementById('Apellidos_Usuario').value.trim();

						// Actualiza la sesión por AJAX para mantener coherencia
						await fetch('index.php?c=perfil&a=actualizarSesion', {
							method: 'POST',
							headers: {
								'Content-Type': 'application/x-www-form-urlencoded'
							},
							body: `nombre_completo=${encodeURIComponent(nuevoNombre)}`
						});

						// Actualiza visualmente el nombre en el menú superior (sin recargar)
						const userNameSpan = document.querySelector('.user-name');
						if (userNameSpan) {
							userNameSpan.textContent = nuevoNombre;
						}


						const emailValue = document.getElementById('Correo_Usuario').value.trim();
						document.getElementById('emailDisplay').textContent = emailValue || 'No especificado';

						const telValue = document.getElementById('Telefono_Usuario').value.trim();
						document.getElementById('telefonoDisplay').textContent = telValue || 'No especificado';

						const dirValue = document.getElementById('Direccion_Usuario').value.trim();
						document.getElementById('direccionDisplay').textContent = dirValue || 'No especificada';

						// Limpiar estado de imagen nueva
						hayImagenNueva = false;
						archivoImagen = null;
						imagenInput.value = '';

						// Limpiar validaciones visuales
						form.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
							el.classList.remove('is-valid', 'is-invalid');
						});

						// Actualizar estado
						detectarCambios();

					} else {
						btnGuardar.disabled = false;
						Swal.fire({
							icon: 'error',
							title: 'Error al actualizar',
							html: data.mensaje || 'No se pudo actualizar el perfil',
							confirmButtonColor: '#1b00ff'
						});
					}

				} catch (error) {
					console.error('Error:', error);
					loadingOverlay.classList.remove('active');
					btnGuardar.disabled = false;

					Swal.fire({
						icon: 'error',
						title: 'Error de conexión',
						text: 'No se pudo conectar con el servidor. Por favor, intente nuevamente.',
						confirmButtonColor: '#1b00ff'
					});
				}
			});

			// Limpiar validaciones al hacer focus
			form.querySelectorAll('input, textarea').forEach(input => {
				input.addEventListener('focus', function() {
					this.classList.remove('is-invalid');
				});
			});

			// Detectar cambios iniciales
			detectarCambios();
		});
	</script>
</body>

</html>