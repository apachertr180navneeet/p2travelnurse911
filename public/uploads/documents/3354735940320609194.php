<?php
$user_type_id = $this->session->userdata('user_type_id');

if (isset($jobInfo) && $jobInfo->num_rows() > 0) {
	$urow = $jobInfo->row();

	foreach ($urow as $k => $v) {
		${$k} = $v;
	}
	if (isset($salary_range) && !empty($salary_range)) {

		$sr_exp = explode('~', $salary_range);
		$salary_range_start = $sr_exp[0];
		$salary_range_end = $sr_exp[1];
	}

	$btnText = 'Update';
	$pageHeading = 'Edit';
	$image = $urow->image;
	$icon = 'icon-user';
} else {
	$job_status = '1';
	$btnText = 'Save';
	$pageHeading = 'Add New';
	$image = '';
	$icon = 'icon-plus';
	$submission_type = 'normal';
}
?>
<script>
	function get_speciality_list(prof_id) {
		$('.alert').remove();
		$('.page-loader').show();
		$('#specialty').attr('disabled', true);
		$('#specialty').html('<option value="">Loading...</option>');
		$.ajax({
			url: '<?php echo base_url(); ?>ajax/fetch_specialty_list_by_profession_callback_func',
			type: 'POST',
			success: function(res) {
				$('#specialty').attr('disabled', false);

				$('#specialty').html(res);
				$('.page-loader').hide();
				$('.alert').delay(5000).fadeOut('slow');
			},
			data: {
				prof_id: prof_id
			},
			cache: false
		});
	}

	function get_city_list(state_id) {
		$('.alert').remove();
		$('.page-loader').show();
		$('#city').attr('disabled', true);
		$('#city').html('<option value="">Loading...</option>');
		$.ajax({
			url: '<?php echo base_url(); ?>admin/ajax/fetch_city_list_by_statee_callback_func',
			type: 'POST',
			success: function(res) {
				$('#city').attr('disabled', false);

				$('#city').html(res);
				$('.page-loader').hide();
				$('.alert').delay(5000).fadeOut('slow');
			},
			data: {
				state_id: state_id
			},
			cache: false
		});
	}

	function checkPassword(str) {
		var re = /^(?=.*\d)(?=.*[!@#$%^&*])(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
		return re.test(str);
	}

	function check_submisstion(slug) {
		$('.smart_submission_block #taxed_hourly_rate').attr('required', false);
		$('.smart_submission_block #hourly_per_diems').attr('required', false);
		$('.smart_submission_block #hourly_blended_rate').attr('required', false);
		$('.smart_submission_block #gross_weekly_pay').attr('required', false);
		$('.smart_submission_block #ot_rate').attr('required', false);
		$('.smart_submission_block #ot_hourly_rate').attr('required', false);
		if (slug) {
			$('.smart_submission_block #taxed_hourly_rate').attr('required', true);
			$('.smart_submission_block #hourly_per_diems').attr('required', true);
			$('.smart_submission_block #hourly_blended_rate').attr('required', true);
			$('.smart_submission_block #gross_weekly_pay').attr('required', true);
			$('.smart_submission_block #ot_rate').attr('required', true);
			$('.smart_submission_block #ot_hourly_rate').attr('required', true);
		}
	}

	$(document).ready(function() {
		$('.smart_submission_block').hide();


		if ($('input[type="radio"][name="submission_type"]:checked').val() == 'smart') {
			check_submisstion(true);
			$('.smart_submission_block').show();
		} else {
			check_submisstion(false);
		}

		$('input[type="radio"][name="submission_type"]').change(function() {
			$('.smart_submission_block').hide();
			check_submisstion(false);
			if ($(this).val() == 'smart') {
				check_submisstion(true);
				$('.smart_submission_block').show();
			}

		});
		$("#u_password").keyup(function() {
			'use strict';
			if (this.value != '') {
				/*
				var validated =  true;
				if(this.value.length < 8)
					validated = false;
				if(!/\d/.test(this.value))
					validated = false;
				if(!/[a-z]/.test(this.value))
					validated = false;
				if(!/[A-Z]/.test(this.value))
					validated = false;
				if(/[^0-9a-zA-Z]/.test(this.value))
					validated = false;
				*/
				if (checkPassword(this.value)) {
					this.setCustomValidity('');
				} else {
					this.setCustomValidity('Password Must Have atlease 8 character in Length and Contain Atlease 1 Uppercase Later, 1 Lowercase Later, 1 Number And 1 Special Character');
				}
			}
		});

		$("#c_password").keyup(function() {
			'use strict';
			if (this.value != '') {
				/*
				var validated =  true;
				if(this.value.length < 8)
					validated = false;
				if(!/\d/.test(this.value))
					validated = false;
				if(!/[a-z]/.test(this.value))
					validated = false;
				if(!/[A-Z]/.test(this.value))
					validated = false;
				if(/[^0-9a-zA-Z]/.test(this.value))
					validated = false;
				*/
				if (checkPassword(this.value)) {
					this.setCustomValidity('');
				} else {
					this.setCustomValidity('Password Must Have atlease 8 character in Length and Contain Atlease 1 Uppercase Later, 1 Lowercase Later, 1 Number And 1 Special Character');
				}
			}
		});
	});
</script>
<div class="page-content-wrapper">
	<div class="page-content">

		<?php $this->load->view('include/alerts'); ?>

		<div class="row">
			<!--Form Start-->
			<div class="col-md-12">
				<div class="portlet light sideForm">
					<div class="portlet-title">
						<div class="caption font-green">
							<i class="font-green <?php echo $icon ?>" id="pagetitleIcon"></i>
							<span class="caption-subject bold uppercase" id="pagetitle">
								<?php echo $pageHeading . ' Job'; ?>
							</span>
						</div>
					</div>
					<div class="portlet-body form">
						<form role="form" method="post" enctype="multipart/form-data">
							<input type="hidden" id="id" value="<?php if (isset($id) && !empty($id)) echo $myHelpers->EncryptId($id);  ?>" name="id">

							<div class="row">

								<div class="col-md-6">
									<div class="form-group">
										<label for="job_no">Job ID <span class="mandatory">*</span></label>
										<input type="text" required="" readonly value="<?php if (isset($job_id) && !empty($job_id)) echo $job_id;
																																		else echo $myHelpers->generate_random_job_id(); ?>" name="job_no" id="job_no" class="form-control">
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label for="job_title">Job Title <span class="mandatory">*</span></label>
										<input type="text" required="" value="<?php if (isset($job_title)) echo $job_title ?>" name="job_title" id="job_title" class="form-control">
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group">
										<label for="description">Description <span class="mandatory">*</span></label>
										<textarea name="description" required id="description" class="form-control ckeditor"><?php if (isset($description)) echo $description; ?></textarea>
									</div>
								</div>

								<div class="col-md-12">
									<div class="form-group">
										<label for="qualification">Qualification <span class="mandatory">*</span></label>
										<textarea name="qualification" required id="qualification" class="form-control ckeditor"><?php if (isset($qualification)) echo $qualification; ?></textarea>
									</div>
								</div>

								<div class="col-md-12">
									<div class="form-group">
										<label for="responsibilities">Responsibilities</label>
										<textarea name="responsibilities" id="responsibilities" class="form-control ckeditor"><?php if (isset($responsibilities)) echo $responsibilities; ?></textarea>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="location">State <span class="mandatory">*</span></label>
										<span class="input-form-group-addon"></span>
										<select name="location" id="location" class="form-control" required onchange="get_city_list(this.value)">
											<option value="">Select State</option>
											<?php
											if (isset($state_list) && $state_list->num_rows() > 0) {
												foreach ($state_list->result() as $state_row) {
													echo '<option value="' . $myHelpers->EncryptId($state_row->id) . '"';
													if (isset($location) && $location == $state_row->id)
														echo ' selected="selected" ';
													echo '>' . ucfirst($state_row->name) . '</option>';
												}
											}
											?>
										</select>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="city">City</label>
										<span class="input-form-group-addon"></span>
										<select name="city" id="city" class="form-control">
											<option value="<?php echo $myHelpers->EncryptClientId(0); ?>">Select City</option>
											<?php
											if (isset($city_list) && $city_list->num_rows() > 0) {
												foreach ($city_list->result() as $row) {
													echo  '<option value="' . $myHelpers->EncryptClientId($row->id) . '"';
													if (isset($city) && $city == $row->id)
														echo ' selected="selected" ';
													echo '>' . ucfirst($row->city_name) . '</option>';
												}
											}
											?>
										</select>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="profession">Profession <span class="mandatory">*</span></label>
										<select name="profession" id="profession" class="form-control" required onchange="get_speciality_list(this.value)">
											<option value="">Select Profession</option>
											<?php if (isset($profession_list) && $profession_list->num_rows() > 0) {
												foreach ($profession_list->result() as $pRow) {
													echo '<option value="' . $myHelpers->EncryptId($pRow->id) . '"';
													if (isset($profession_id) && $profession_id == $pRow->id)
														echo ' selected="selected" ';
													echo '>' . ucfirst($pRow->profession) . '</option>';

													$sql = "select * from professions where status = '1' and parent_id = $pRow->id order by order_by ASC, profession ASC";
													$sub_profession_list = $myHelpers->Main_model->__callMasterquery($sql);
													if ($sub_profession_list->num_rows() > 0) {
														foreach ($sub_profession_list->result() as $spRow) {
															echo '<option value="' . $myHelpers->EncryptId($spRow->id) . '"';
															if (isset($profession_id) && $profession_id == $spRow->id)
																echo ' selected="selected" ';
															echo '> - ' . ucfirst($spRow->profession) . '</option>';
														}
													}
												}
											} ?>
										</select>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="specialty">Specialty <span class="mandatory">*</span></label>
										<select class="selectize-select2 form-control" required name="specialty" id="specialty">
											<option value="">Select Specialty</option>
											<?php
											if (isset($profession_id) && !empty($profession_id)) {
												$sql = "select * from specialities where profession_id = '$profession_id' order by specialty ASC";
												$specialities_res = $myHelpers->Main_model->__callMasterquery($sql);
												if (isset($specialities_res) && $specialities_res->num_rows() > 0) {
													foreach ($specialities_res->result() as $row) {
														echo '<option value="' . $myHelpers->EncryptId($row->id) . '"';
														if (isset($specialty_id) && $specialty_id == $row->id)
															echo ' selected="selected" ';
														echo '>' . ucfirst($row->specialty) . '</option>';
													}
												}
											}
											?>
										</select>
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label for="start_date">Start Date <span class="mandatory">*</span></label>
										<input type="date" required="" name="start_date" value="<?php if (isset($start_date)) echo $start_date; ?>" id="start_date" class="form-control">
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label for="end_date">End Date <span class="mandatory">*</span></label>
										<input type="date" required="" name="end_date" value="<?php if (isset($end_date)) echo $end_date; ?>" id="end_date" class="form-control">
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label for="shift">Shift <span class="mandatory">*</span></label>
										<input type="text" required="" value="<?php if (isset($shift)) echo $shift;  ?>" name="shift" id="shift" class="form-control">
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label for="time_of_day">Job Note<span class="mandatory">*</span></label>
										<input type="text" required="" value="<?php if (isset($time_of_day)) echo $time_of_day;  ?>" name="time_of_day" id="time_of_day" class="form-control">
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label for="total_opening">Total Opening<span class="mandatory">*</span></label>
										<input type="number" min="0" step="1" required="" value="<?php if (isset($total_opening)) echo $total_opening;  ?>" name="total_opening" id="total_opening" class="form-control">
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label for="duration_weeks">Assignment Week Duration (in Weeks)<span class="mandatory">*</span></label>
										<input type="number" min="0" step="1" required value="<?php if (isset($duration_weeks)) echo $duration_weeks;  ?>" name="duration_weeks" id="duration_weeks" class="form-control">
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label for="salary_range_start">Salary Range<span class="mandatory">*</span></label>


										<div class="input-group">
											<div class="input-group-addon" style="border-radius:50px !important;border-top-right-radius: 0px !important;border-bottom-right-radius: 0px !important;">From ($)</div>
											<input type="number" min="0" step=".001" required value="<?php if (isset($salary_range_start)) echo $salary_range_start;  ?>" name="salary_range_start" id="salary_range_start" class="form-control" style="border-top-left-radius: 0px !important;border-bottom-left-radius: 0px !important;">
											<div class="input-group-addon" style="border-radius:50px !important;border-top-right-radius: 0px !important;border-bottom-right-radius: 0px !important;">To ($)</div>
											<input type="number" min="0" step=".001" required value="<?php if (isset($salary_range_end)) echo $salary_range_end;  ?>" name="salary_range_end" id="salary_range_end" class="form-control" style="border-top-left-radius: 0px !important;border-bottom-left-radius: 0px !important;">
										</div>

									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label for="employment_type">Employment Type <span class="mandatory">*</span></label>

										<select class="selectize-select2 form-control" required name="employment_type" id="employment_type">
											<option value="">Select Employment Type</option>
											<option value="Permanent" <?php if (isset($employment_type) && $employment_type == 'Permanent') echo 'selected';  ?>>Permanent</option>
											<option value="Part Time" <?php if (isset($employment_type) && $employment_type == 'Part Time') echo 'selected';  ?>>Part Time</option>
											<option value="Contract" <?php if (isset($employment_type) && $employment_type == 'Contract') echo 'selected';  ?>>Contract</option>
											<option value="Travel Or Contract" <?php if (isset($employment_type) && $employment_type == 'Travel Or Contract') echo 'selected';  ?>>Travel Or Contract</option>
										</select>

									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="cirsis_response">Crisis Response </label>
										<input type="text" value="<?php if (isset($cirsis_response)) echo $cirsis_response;  ?>" name="cirsis_response" id="cirsis_response" class="form-control">
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label>Job Image</label>
										<input type="hidden" name="old_file" id="old_file" value="<?php echo $image ?>">
										<input type="file" name="feat_img" id="feat_img" class="form-control" accept="image/*">

										<img class="imgPreview" src="<?php if ($image != '' && file_exists('uploads/jobs/' . $image)) {
																										echo base_url() . 'uploads/jobs/' . $image;
																									} ?>" style="<?php if ($image != '' && file_exists('uploads/jobs/' . $image)) {
																																	echo 'display:block;';
																																} ?> top: inherit;bottom: 15px;right: 15px;">
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label for="job_status">Job Status <span class="mandatory">*</span></label>

										<select class="selectize-select2 form-control" required name="job_status" id="job_status">
											<option value="">Select Employment Type</option>
											<option value="0" <?php if (isset($job_status) && $job_status == '0') echo 'selected';  ?>>In-Active</option>
											<option value="1" <?php if (isset($job_status) && $job_status == '1') echo 'selected';  ?>>Active</option>
											<option value="2" <?php if (isset($job_status) && $job_status == '2') echo 'selected';  ?>>On-hold</option>
											<option value="3" <?php if (isset($job_status) && $job_status == '3') echo 'selected';  ?>>Closed</option>
										</select>

									</div>
								</div>

								<div class="clearfix"></div>

								
									<div class="col-md-4">
										<div class="form-group">
											<label for="submission_type_normal">Submission Type <span class="mandatory">*</span></label>
											<div class="md-radio">
												<input type="radio" required="" id="submission_type_normal" name="submission_type" value="normal" class="md-radiobtn" <?php if (($submission_type == 'normal') || $submission_type == '') echo 'checked="checked"' ?>>
												<label for="submission_type_normal">Normal Submission
													<span></span>
													<span class="check"></span>
													<span class="box"></span></label>
											</div>
											<div class="md-radio">
												<input type="radio" required="" id="submission_type_smart" name="submission_type" value="smart" class="md-radiobtn" <?php if ($submission_type == 'smart') echo 'checked="checked"' ?>>
												<label for="submission_type_smart">Chris Smart Submission
													<span></span>
													<span class="check"></span>
													<span class="box"></span></label>
											</div>
										</div>
									</div>

									<div class="clearfix smart_submission_block"></div>
								<?php if ($user_type_id == 1) { ?>
									<div class="col-md-4 smart_submission_block">
										<div class="form-group">
											<label for="compensation">Compensation</label>
											<input type="text" value="<?php if (isset($compensation)) echo $compensation ?>" name="compensation" id="compensation" class="form-control">
										</div>
									</div>

									<div class="col-md-4 smart_submission_block">
										<div class="form-group">
											<label for="year_of_experience">Years of Experience Required</label>
											<input type="number" value="<?php if (isset($year_of_experience)) echo $year_of_experience ?>" name="year_of_experience" id="year_of_experience" class="form-control" min="0" step="1">
										</div>
									</div>

									<div class="clearfix smart_submission_block"></div>

									<div class="col-md-4 smart_submission_block">
										<div class="form-group">
											<label for="taxed_hourly_rate">Taxed Hourly Rate</label>
											<div class="input-group">
												<div class="input-group-addon" style="border-radius:50px !important;border-top-right-radius: 0px !important;border-bottom-right-radius: 0px !important;">$</div>
												<input type="number" min="0" step=".001" required value="<?php if (isset($taxed_hourly_rate)) echo $taxed_hourly_rate;  ?>" name="taxed_hourly_rate" id="taxed_hourly_rate" class="form-control" style="border-radius: 0px !important;">
												<div class="input-group-addon" style="border-radius:50px !important;border-top-left-radius: 0px !important;border-bottom-left-radius: 0px !important;">Per Hour</div>
											</div>
										</div>
									</div>

									<div class="col-md-4 smart_submission_block">
										<div class="form-group">
											<label for="hourly_per_diems">Stipends</label>
											<div class="input-group">
												<div class="input-group-addon" style="border-radius:50px !important;border-top-right-radius: 0px !important;border-bottom-right-radius: 0px !important;">$</div>
												<input type="number" min="0" step=".001" required value="<?php if (isset($hourly_per_diems)) echo $hourly_per_diems;  ?>" name="hourly_per_diems" id="hourly_per_diems" class="form-control" style="border-radius: 0px !important;">
												<div class="input-group-addon" style="border-radius:50px !important;border-top-left-radius: 0px !important;border-bottom-left-radius: 0px !important;">Per Week</div>
											</div>
										</div>
									</div>

									<div class="col-md-4 smart_submission_block">
										<div class="form-group">
											<label for="hourly_blended_rate">Hourly Blended Rate</label>
											<div class="input-group">
												<div class="input-group-addon" style="border-radius:50px !important;border-top-right-radius: 0px !important;border-bottom-right-radius: 0px !important;">$</div>
												<input type="number" min="0" step=".001" required value="<?php if (isset($hourly_blended_rate)) echo $hourly_blended_rate;  ?>" name="hourly_blended_rate" id="hourly_blended_rate" class="form-control" style="border-radius: 0px !important;">
												<div class="input-group-addon" style="border-radius:50px !important;border-top-left-radius: 0px !important;border-bottom-left-radius: 0px !important;">Per Hour</div>
											</div>
										</div>
									</div>

									<div class="col-md-4 smart_submission_block">
										<div class="form-group">
											<label for="gross_weekly_pay">Gross Weekly Pay (36 Hours)</label>
											<div class="input-group">
												<div class="input-group-addon" style="border-radius:50px !important;border-top-right-radius: 0px !important;border-bottom-right-radius: 0px !important;">$</div>
												<input type="number" min="0" step=".001" required value="<?php if (isset($gross_weekly_pay)) echo $gross_weekly_pay;  ?>" name="gross_weekly_pay" id="gross_weekly_pay" class="form-control" style="border-radius: 0px !important;">
												<div class="input-group-addon" style="border-radius:50px !important;border-top-left-radius: 0px !important;border-bottom-left-radius: 0px !important;">Per Week</div>
											</div>
										</div>
									</div>

									<div class="col-md-4 smart_submission_block">
										<div class="form-group">
											<label for="ot_rate">Gross Weekly pay for 48 Hours</label>
											<div class="input-group">
												<div class="input-group-addon" style="border-radius:50px !important;border-top-right-radius: 0px !important;border-bottom-right-radius: 0px !important;">$</div>
												<input type="number" min="0" step=".001" required value="<?php if (isset($ot_rate)) echo $ot_rate;  ?>" name="ot_rate" id="ot_rate" class="form-control" style="border-radius: 0px !important;">
												<div class="input-group-addon" style="border-radius:50px !important;border-top-left-radius: 0px !important;border-bottom-left-radius: 0px !important;">Per Week</div>
											</div>
										</div>
									</div>

									<div class="col-md-4 smart_submission_block">
										<div class="form-group">
											<label for="ot_hourly_rate">OT Hourly Rate (after 40 hours)</label>
											<div class="input-group">
												<div class="input-group-addon" style="border-radius:50px !important;border-top-right-radius: 0px !important;border-bottom-right-radius: 0px !important;">$</div>
												<input type="number" min="0" step=".001" required value="<?php if (isset($ot_hourly_rate)) echo $ot_hourly_rate;  ?>" name="ot_hourly_rate" id="ot_hourly_rate" class="form-control" style="border-radius: 0px !important;">
												<div class="input-group-addon" style="border-radius:50px !important;border-top-left-radius: 0px !important;border-bottom-left-radius: 0px !important;">Per Hour</div>
											</div>
										</div>
									</div>

								<?php } ?>
							</div>

							<div class="clearfix"></div>

							<div class="form-actions noborder">
								<button type="submit" name="submit" id="submit" class="btn blue"><?php echo $btnText ?></button>
								<?php if (isset($job_id) && $job_id == 0) { ?>
									<button type="reset" id="reset" class="btn default">Reset</button>
								<?php } else { ?>
									<button class="btn default" id="cancel" type="button" onclick="history.go(-1)">Cancel</button>
								<?php } ?>
							</div>

							<div class="clearfix"></div>
							<div id="regAlert"></div>
						</form>
					</div>
				</div>
			</div>
			<!--Form End-->
		</div>
	</div>
</div>
<!-- END CONTENT -->