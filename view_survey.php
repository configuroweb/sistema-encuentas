<?php include 'db_connect.php' ?>
<?php
$qry = $conn->query("SELECT * FROM survey_set where id = " . $_GET['id'])->fetch_array();
foreach ($qry as $k => $v) {
	if ($k == 'title')
		$k = 'stitle';
	$$k = $v;
}
$answers = $conn->query("SELECT distinct(user_id) from answers where survey_id ={$id}")->num_rows;
?>
<div class="col-lg-12">
	<div class="row">
		<div class="col-md-4">
			<div class="card card-outline card-primary">
				<div class="card-header">
					<h3 class="card-title">Información Encuesta</h3>
				</div>
				<div class="card-body p-0 py-2">
					<div class="container-fluid">
						<p>Título: <b><?php echo $stitle ?></b></p>
						<p class="mb-0">Descripción:</p>
						<small><?php echo $description; ?></small>
						<p>Inicio: <b><?php echo date("M d, Y", strtotime($start_date)) ?></b></p>
						<p>Fin: <b><?php echo date("M d, Y", strtotime($end_date)) ?></b></p>
						<p>Encuentas tomadas: <b><?php echo number_format($answers) ?></b></p>

					</div>
					<hr class="border-primary">
				</div>
			</div>
		</div>
		<div class="col-md-8">
			<div class="card card-outline card-success">
				<div class="card-header">
					<h3 class="card-title"><b>Preguntas de Encuesta</b></h3>
					<div class="card-tools">
						<button class="btn btn-block btn-sm btn-default btn-flat border-success new_question" type="button"><i class="fa fa-plus"></i> Agregar Nueva Pregunta</button>
					</div>
				</div>
				<form action="" id="manage-sort">
					<div class="card-body ui-sortable">
						<?php
						$question = $conn->query("SELECT * FROM questions where survey_id = $id order by abs(order_by) asc,abs(id) asc");
						while ($row = $question->fetch_assoc()) :
						?>
							<div class="callout callout-info">
								<div class="row">
									<div class="col-md-12">
										<span class="dropleft float-right">
											<a class="fa fa-ellipsis-v text-dark" href="javascript:void(0)" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></a>
											<div class="dropdown-menu" style="">
												<a class="dropdown-item edit_question text-dark" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Editar</a>
												<div class="dropdown-divider"></div>
												<a class="dropdown-item delete_question text-dark" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Eliminar</a>
											</div>
										</span>
									</div>
								</div>
								<h5><?php echo $row['question'] ?></h5>
								<div class="col-md-12">
									<input type="hidden" name="qid[]" value="<?php echo $row['id'] ?>">
									<?php
									if ($row['type'] == 'radio_opt') :
										foreach (json_decode($row['frm_option']) as $k => $v) :
									?>
											<div class="icheck-primary">
												<input type="radio" id="option_<?php echo $k ?>" name="answer[<?php echo $row['id'] ?>]" value="<?php echo $k ?>" checked="">
												<label for="option_<?php echo $k ?>"><?php echo $v ?></label>
											</div>
										<?php endforeach; ?>
										<?php elseif ($row['type'] == 'check_opt') :
										foreach (json_decode($row['frm_option']) as $k => $v) :
										?>
											<div class="icheck-primary">
												<input type="checkbox" id="option_<?php echo $k ?>" name="answer[<?php echo $row['id'] ?>][]" value="<?php echo $k ?>">
												<label for="option_<?php echo $k ?>"><?php echo $v ?></label>
											</div>
										<?php endforeach; ?>
									<?php else : ?>
										<div class="form-group">
											<textarea name="answer[<?php echo $row['id'] ?>]" id="" cols="30" rows="4" class="form-control" placeholder="Escriba su respuesta aquí..."></textarea>
										</div>
									<?php endif; ?>
								</div>
							</div>
						<?php endwhile; ?>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		$('.ui-sortable').sortable({
			placeholder: "ui-state-highlight",
			update: function() {
				alert_toast("Guardando el orden de clasificación de las preguntas.", "info")
				$.ajax({
					url: "ajax.php?action=action_update_qsort",
					method: 'POST',
					data: $('#manage-sort').serialize(),
					success: function(resp) {
						if (resp == 1) {
							alert_toast("El orden de las preguntas se guardó correctamente.", "success")
						}
					}
				})
			}
		})
	})
	$('.new_question').click(function() {
		uni_modal("Nueva Pregunta", "manage_question.php?sid=<?php echo $id ?>", "large")
	})
	$('.edit_question').click(function() {
		uni_modal("Nueva Pregunta", "manage_question.php?sid=<?php echo $id ?>&id=" + $(this).attr('data-id'), "large")
	})

	$('.delete_question').click(function() {
		_conf("Deseas eliminar esta pregunta?", "delete_question", [$(this).attr('data-id')])
	})

	function delete_question($id) {
		start_load()
		$.ajax({
			url: 'ajax.php?action=delete_question',
			method: 'POST',
			data: {
				id: $id
			},
			success: function(resp) {
				if (resp == 1) {
					alert_toast("Datos eliminados correctamente", 'success')
					setTimeout(function() {
						location.reload()
					}, 1500)

				}
			}
		})
	}
</script>