<?php
?>
<div class="col-lg-12">
	<div class="card">
		<div class="card-body">
			<form action="" id="manage_user">
				<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
				<div class="row">
					<div class="col-md-6 border-right">
						<b class="text-muted">Información Personal</b>
						<div class="form-group">
							<label for="" class="control-label">Nombre</label>
							<input type="text" name="firstname" class="form-control form-control-sm" required value="<?php echo isset($firstname) ? $firstname : '' ?>">
						</div>
						<div class="form-group">
							<label for="" class="control-label">Segundo Nombre</label>
							<input type="text" name="middlename" class="form-control form-control-sm" value="<?php echo isset($middlename) ? $middlename : '' ?>">
						</div>
						<div class="form-group">
							<label for="" class="control-label">Apellido</label>
							<input type="text" name="lastname" class="form-control form-control-sm" required value="<?php echo isset($lastname) ? $lastname : '' ?>">
						</div>
						<div class="form-group">
							<label for="" class="control-label">Contacto</label>
							<input type="text" name="contact" class="form-control form-control-sm" required value="<?php echo isset($contact) ? $contact : '' ?>">
						</div>
						<div class="form-group">
							<label class="control-label">Dirección</label>
							<textarea name="address" id="" cols="30" rows="4" class="form-control" required><?php echo isset($address) ? $address : '' ?></textarea>
						</div>
					</div>
					<div class="col-md-6">
						<b class="text-muted">Credenciales del Sistema</b>
						<?php if ($_SESSION['login_type'] == 1) : ?>
							<div class="form-group">
								<label for="" class="control-label">Rol de Usuario</label>
								<select name="type" id="type" class="custom-select custom-select-sm">
									<option value="3" <?php echo isset($type) && $type == 3 ? 'selected' : '' ?>>Suscriptor</option>
									<option value="2" <?php echo isset($type) && $type == 2 ? 'selected' : '' ?>>Staff</option>
									<option value="1" <?php echo isset($type) && $type == 1 ? 'selected' : '' ?>>Administrador</option>
								</select>
							</div>
						<?php else : ?>
							<input type="hidden" name="type" value="3">
						<?php endif; ?>
						<div class="form-group">
							<label class="control-label">Correo</label>
							<input type="email" class="form-control form-control-sm" name="email" required value="<?php echo isset($email) ? $email : '' ?>">
							<small id="#msg"></small>
						</div>
						<div class="form-group">
							<label class="control-label">Contraseña</label>
							<input type="password" class="form-control form-control-sm" name="password" <?php echo isset($id) ? "" : 'required' ?>>
							<small><i><?php echo isset($id) ? "Deje esto en blanco si no desea cambiar su contraseña" : '' ?></i></small>
						</div>
						<div class="form-group">
							<label class="label control-label">Confirmar Contraseña</label>
							<input type="password" class="form-control form-control-sm" name="cpass" <?php echo isset($id) ? 'required' : '' ?>>
							<small id="pass_match" data-status=''></small>
						</div>
					</div>
				</div>
				<hr>
				<div class="col-lg-12 text-right justify-content-center d-flex">
					<button class="btn btn-primary mr-2">Guardar</button>
					<button class="btn btn-secondary" type="button" onclick="location.href = 'index.php?page=user_list'">Cancelar</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	$('[name="password"],[name="cpass"]').keyup(function() {
		var pass = $('[name="password"]').val()
		var cpass = $('[name="cpass"]').val()
		if (cpass == '' || pass == '') {
			$('#pass_match').attr('data-status', '')
		} else {
			if (cpass == pass) {
				$('#pass_match').attr('data-status', '1').html('<i class="text-success">Contraseñas Coinciden</i>')
			} else {
				$('#pass_match').attr('data-status', '2').html('<i class="text-danger">Contraseñas no Coinciden</i>')
			}
		}
	})

	function displayImg(input, _this) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				$('#cimg').attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}
	$('#manage_user').submit(function(e) {
		e.preventDefault()
		$('input').removeClass("border-danger")
		start_load()
		$('#msg').html('')
		if ($('#pass_match').attr('data-status') != 1) {
			if ($("[name='password']").val() != '') {
				$('[name="password"],[name="cpass"]').addClass("border-danger")
				end_load()
				return false;
			}
		}
		$.ajax({
			url: 'ajax.php?action=save_user',
			data: new FormData($(this)[0]),
			cache: false,
			contentType: false,
			processData: false,
			method: 'POST',
			type: 'POST',
			success: function(resp) {
				if (resp == 1) {
					alert_toast('Datos guardados correctamente', "success");
					setTimeout(function() {
						location.replace('index.php?page=user_list')
					}, 750)
				} else if (resp == 2) {
					$('#msg').html("<div class='alert alert-danger'>Correo ya existe en el sistema</div>");
					$('[name="email"]').addClass("border-danger")
					end_load()
				}
			}
		})
	})
</script>