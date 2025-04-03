<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if (isset($title))
            {{ $title }}
            @endif @if (!isset($home))
                | {{ config('app.name') }}
            @endif
    </title>
    <link rel="shortcut icon" href="{{ asset('assets/images/fav.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />
    <style>
        body {
            height: auto !important;
            overflow: visible !important;
            font-family: 'Arial', sans-serif;
        }

        html, body {
            width: 100%;
            margin: 0;
            padding: 0;
        }        

        .section-title {
            background-color: #1a1aff;
            color: white;
            padding: 10px;
            font-weight: bold;
        }

        .profile-header {
            text-align: right;
            font-weight: bold;
        }

        .profile-header h1 {
            font-size: 32px;
            margin: 0;
        }

        .profile-header h3 {
            font-size: 18px;
            margin: 0;
            font-weight: normal;
            color: gray;
        }

        .icon {
            color: #1a1aff;
            font-size: 20px;
            margin-right: 10px;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            margin-bottom: 10px;
        }

        .contact-info,
        .license-info,
        .certifications-info,
        .summary-info,
        .education-info {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .blue-bullet {
            color: #1a1aff;
        }

        @media print {
            .no-print {
                display: none
            }

            .shadow {
                box-shadow: none !important;
            }

            .container {
                margin: 0 !important;
            }
        }

        #pageLoader {
            display: none;
            /* Hide loader by default */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            /* Semi-transparent background */
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            /* Make sure it appears on top */
        }

        /* Loader spinner */
        .loader {
            width: 50px;
            height: 50px;
            border: 6px solid transparent;
            border-top-color: #3498db;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        /* Spinner animation */
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <!-- Header Section -->
    <div id="pageLoader" style="display: none;">
        <div class="loader"></div>
    </div>
    <div class="container my-4" style="
    width: 100%;
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;">
        <div class="shadowss">
            <div class="row">

                <div class="col-md-6" style="width: 50%; float: left; padding-right: 15px; box-sizing: border-box;">

                    <div class="profile-header" style="text-align: center; padding: 20px 0;">
                    <h4 class="text-dark" style="color: #333; text-align: left;">PROFILE TEMPLATE</h4>
                    </div>
                    
                    <div class="section-title" style="font-weight: bold; margin-top: 20px;">Contact Information</div>
                    <div class="contact-infos" style="padding: 15px; border: 1px solid #ddd;">
                    <ul style="list-style-type: none; padding: 0; margin: 0;">
                    <li><span class="icon">&#9742;</span>{{ isset($userRecord) ? $userRecord->phone : '' }}</li>
                    <li><span class="icon">&#9993;</span>{{ isset($userRecord) ? $userRecord->email : '' }}</li>
                    <li><span class="icon">&#127968;</span>{{ isset($userRecord) ? $userRecord->address_line1 . ', ' . $userRecord->address_line2 . ', ' . $userRecord->city_name . ', ' . $userRecord->state_name : '' }}</li>
                    <li><span class="icon">&#128337;</span>Monday-Friday | 9am-6pm EST</li>
                    </ul>
                    </div>

                    <div class="section-title" style="font-weight: bold; margin-top: 20px;">Active Certifications</div>
                    <div class="certifications-infos" style="padding: 15px; border: 1px solid #ddd;">
                    <ul style="list-style-type: none; padding: 0; margin: 0;">
                    @if (!empty($userActiveCertificate) && count($userActiveCertificate) > 0)
                    @foreach ($userActiveCertificate as $certificate)
                        <li><span class="blue-bullet" style="color: #00f;">&#8226;</span> <strong>{{ $certificate->certificate_name }}</strong> | <strong>Expires: </strong>{{ date('m/d/Y', strtotime($certificate->certificate_expiry_date)) }}</li>
                    @endforeach
                    @else
                    <li><span class="blue-bullet" style="color: #00f;">&#8226;</span> No Active Certifications</li>
                    @endif
                    </ul>
                    </div>

                    <div class="section-title" style="font-weight: bold; margin-top: 20px;">Education</div>
                    <div class="education-infos" style="padding: 15px; border: 1px solid #ddd;">
                    <?php if (!empty($educationalDetails)) { ?>
                    <ul style="list-style-type: none; padding: 0; margin: 0;">
                    <?php foreach ($educationalDetails as $edu) { ?>
                        <li><span class="blue-bullet" style="color: #00f;">&#8226;</span>
                            <?php
                                $edu_string = [];
                                if (!empty($edu->school_college)) { $edu_string[] = $edu->school_college; }
                                if (!empty($edu->location)) { $edu_string[] = $edu->location; }
                                if (!empty($edu->degree)) { $edu_string[] = $edu->degree; }
                                if (!empty($edu->currently_attending)) { $edu_string[] = 'Currently Attending'; }
                                elseif (!empty($edu->end_month) && !empty($edu->end_year)) { $edu_string[] = $edu->end_month . '/' . $edu->end_year; }
                                echo implode(' | ', $edu_string);
                            ?>
                        </li>
                    <?php } ?>
                    </ul>
                    <?php } else { ?>
                    <p>No Educational Information Currently Available</p>
                    <?php } ?>
                    </div>

                </div>

                <!-- Active State License Section -->
                <div class="col-md-6" style="width: 50%; float: left; padding-left: 15px; box-sizing: border-box;">

                    <div class="profile-header" style="text-align: left; padding-top: 20px; padding-bottom: 10px;">
                    <h1 style="margin-bottom: 10px;"><strong>{{ isset($userRecord) ? $userRecord->name : '' }}</strong></h1>
                    <h3><i>{{ isset($userRecord) ? $userRecord->profession : '' }}</i></h3>
                    <h6><i>{{ isset($userRecord) ? $userRecord->specialty : '' }}</i></h6>
                    </div>

                    <div class="section-title" style="font-weight: bold; margin-top: 20px;">Active State License</div>
                    <div class="license-infos" style="padding: 15px; border: 1px solid #ddd;">
                    <ul style="list-style-type: none; padding: 0; margin: 0;">
                    @if (!empty($userStateLicense) && count($userStateLicense) > 0)
                    @foreach ($userStateLicense as $license)
                        <li><span class="blue-bullet" style="color: #00f;">&#8226;</span> {{ $license->license_name }} | Expires: {{ date('m/d/Y', strtotime($license->license_name)) }}</li>
                    @endforeach
                    @else
                    <li><span class="blue-bullet" style="color: #00f;">&#8226;</span> No Active State License</li>
                    @endif
                    </ul>
                    </div>

                    <div class="section-title" style="font-weight: bold; margin-top: 20px;">Summary</div>
                    <div class="summary-infos" style="padding: 15px; border: 1px solid #ddd;">
                        <ul style="list-style-type: none; padding: 0; margin: 0;">
                            <li><span class="blue-bullet" style="color: #00f;">&#8226;</span> <strong>Desired Shift:</strong> {{ isset($desired_shifts) && !empty($desired_shifts) ? implode(', ', $desired_shifts) : '-' }}</li>
                            <li><span class="blue-bullet" style="color: #00f;">&#8226;</span> <strong>Available Start Date:</strong> {{ isset($userRecord) && !empty($userRecord->available_start_date) ? $userRecord->available_start_date : '-' }}</li>
                            <li><span class="blue-bullet" style="color: #00f;">&#8226;</span> <strong>Years of Experience:</strong> {{ isset($userRecord) && !empty($userRecord->total_experience) ? $userRecord->total_experience . ' year(s)' : '-' }}</li>
                            <li><span class="blue-bullet" style="color: #00f;">&#8226;</span> <strong>Experience in Specialty:</strong> {{ !empty($userRecord->specialty_experience) ? $userRecord->specialty_experience . ' year(s)' : '-' }}</li>

                            @php
                            $rtoStart = !empty($userRecord->RTO_start_date) ? date('m/d/Y', strtotime($userRecord->RTO_start_date)) : '';
                            $rtoEnd = !empty($userRecord->RTO_end_date) ? date('m/d/Y', strtotime($userRecord->RTO_start_date)) : '';
                            @endphp

                            <li><span class="blue-bullet" style="color: #00f;">&#8226;</span> <strong>RTO:</strong> {{ !empty($rtoStart) ? $rtoStart : '' }} - {{ !empty($rtoEnd) ? $rtoEnd : '' }}</li>
                            <li><span class="blue-bullet" style="color: #00f;">&#8226;</span> <strong>EMR Experience:</strong> {{ $userRecord->EMR_experience ?? '-' }}</li>
                            <li><span class="blue-bullet" style="color: #00f;">&#8226;</span> <strong>Teaching Hospital Experience:</strong> {{ $userRecord->teaching_hospital_experience ?? '-' }}</li>
                            <li><span class="blue-bullet" style="color: #00f;">&#8226;</span> <strong>Travel Experience:</strong> {{ !empty($userRecord->travel_experience) ? $userRecord->travel_experience . ' year(s)' : '-' }}</li>
                            <li><span class="blue-bullet" style="color: #00f;">&#8226;</span> <strong>Fully Vaccinated:</strong> {{ $userRecord->fully_vaccinated ?? '-' }}</li>
                        </ul>
                    </div>
                </div>

                <!-- Active State License Section -->
                <div class="col-md-6" style="width: 50%">
                </div>

                <div class="col-md-12" style="width: 100%; float: left; padding-left: 15px; box-sizing: border-box;">
                    <div class="section-title">Work Experience</div>
                    <div class="text-left pt-3 pb-3 px-4">
                        <?php
							if (!empty($work_histories)) {
						?>
                        <ul>
                            <?php foreach ($work_histories as $edu) { ?>
                            <li><span class="blue-bullet">&#8226;</span>
                                <?php
                                $wh_string = [];
                                
                                if (!empty($edu->profession)) {
                                    $wh_string[] = $edu->profession;
                                }
                                
                                if (!empty($edu->company_name)) {
                                    $wh_string[] = $edu->company_name;
                                }
                                
                                if (!empty($edu->employment_type_title)) {
                                    $wh_string[] = $edu->employment_type_title;
                                }
                                
                                if (!empty($edu->profession)) {
                                    $wh_string[] = $edu->profession;
                                }
                                
                                if (!empty($edu->currently_attending)) {
                                    if (!empty($edu->start_month) && !empty($edu->start_year)) {
                                        $wh_string[] = $edu->start_month . '/' . $edu->start_year . ' - ' . 'Currently Attending';
                                    } else {
                                        $wh_string[] = 'Currently Attending';
                                    }
                                } elseif (!empty($edu->end_month) && !empty($edu->end_year)) {
                                    if (!empty($edu->start_month) && !empty($edu->start_year)) {
                                        $wh_string[] = $edu->start_month . '/' . $edu->start_year . ' - ' . $edu->end_month . '/' . $edu->end_year;
                                    } else {
                                        $wh_string[] = $edu->end_month . '/' . $edu->end_year;
                                    }
                                }
                                
                                if (!empty($edu->city_name)) {
                                    $wh_string[] = $edu->city_name;
                                }
                                
                                if (!empty($edu->state_name)) {
                                    $wh_string[] = $edu->state_name;
                                }
                                
                                if (!empty($edu->specialty)) {
                                    $wh_string[] = $edu->specialty;
                                }
                                
                                echo implode(' | ', $wh_string);
                                ?>
                            </li>
                            <?php } ?>
                        </ul>
                        <?php } else { ?>
                        <p>No Work History Currently Available</p>
                        <?php } ?>
                    </div>
                </div>

                <div class="col-md-12" style="width: 100%; float: left; padding-left: 15px; box-sizing: border-box;">
                    <div class="section-title">Skill Checklists</div>
                    <div class="education-infos py-3 px-4">
                        @if (!empty($completed_checklists))
                            <ul>
                                @foreach ($completed_checklists as $chklist)
                                    <li><span class="blue-bullet">&#8226;</span> {{ $chklist->title }} |
                                        {{ date('m/d/Y', strtotime($chklist->submitted_on)) }}
                                    </li>

                                    @php
                                        $checkListMetaData = json_decode($chklist->checklist_meta, true);
										$checklist_answer = json_decode($chklist->checklist_answer, true);
                                    @endphp
                                    <div class="meta-data-section m-2">
                                        @foreach ($checkListMetaData as $meta => $metaData)
                                            <div style="page-break-before: always">											
											<span>{{ $metaData['sectionTitle'] }}</span>
											</div>
                                            <table class="table table-bordered mb-2 table-sm">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 33%;"></th>
                                                        <th class="text-center" style="width: 33%;">Proficiency</th>
                                                        <th class="text-center" style="width: 33%;">Frequency</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													
													@foreach($metaData['skills'] as $skey => $skills)
                                                    <tr class="mt-2">
                                                        <th>
                                                            <p class="m-0">{{ $skills['skillTitle'] }}</p>
                                                        </th>
														@foreach($skills['options'] as $opKey => $options)
															@php
																if($opKey != "proficiency" && $opKey != "frequency")
																{
																	break;
																}				
															@endphp
															<td class="text-center">
																@foreach($options as $op)
																	
																	@php
																		$anwserValue = '';
																	@endphp

																	@if (array_key_exists($metaData['sectionTitle'], $checklist_answer) && 
																		array_key_exists($skills['skillTitle'],
																		$checklist_answer[$metaData['sectionTitle']])
																		)
																		@if(array_key_exists($opKey,
																		$checklist_answer[$metaData['sectionTitle']][$skills['skillTitle']]))
																			@php
																				$anwserValue = $checklist_answer[$metaData['sectionTitle']][$skills['skillTitle']][$opKey];
																			@endphp
																		@endif
																	@endif


																	<label class="mx-1">
																		<input type="radio"
																			name="{{$skills['skillTitle']}}"
																			class="me-1" value="{{$op['value']}}" 
																			@if($anwserValue==$op['value'])
																				checked 
																			@else
																				disabled
																			@endif
																			> {{$op['value']}}																	
																	</label>
																@endforeach
															</td>
														@endforeach
                                                    </tr>
													@endforeach
                                                </tbody>
                                            </table>
                                        @endforeach
                                @endforeach
                            </ul>
                        @else
                            <p>No Skill Checklist Currently Available</p>
                        @endif
                    </div>
                </div>


                <div class="col-md-12" style="width: 100%; float: left; padding-left: 15px; box-sizing: border-box;">
                    <div class="section-title">References</div>
                    <div class="text-left pt-3 pb-3 px-4">
                        <?php
                            if (!empty($references)) {
                            ?>

                        <?php
                            $n = 0;
                            foreach ($references as $referenceDetails) {
                            $n++;
                        ?>
                        <div class="border">
                            <div class="px-3 py-2 border-bottom bg-light">
                                Reference {{ $n }}
                            </div>
                            <div class="p-3">
                                <h6 class="border-bottom pb-2 mb-2">Employment Information</h6>

                                <div class="row">
                                    <div class="col mb-2" style="width: 33%">
                                        <label class="mb-0">Facility</label>
                                        <p class="m-0">
                                            {{ isset($referenceDetails->facility) ? $referenceDetails->facility : '-' }}
                                        </p>
                                    </div>

                                    <div class="col mb-2" style="width: 33%">
                                        <label class="mb-0">Dates of Employment</label>
                                        <p class="m-0">
                                            {{ isset($referenceDetails->dates_of_employment) ? $referenceDetails->dates_of_employment : '-' }}
                                        </p>
                                    </div>

                                    <div class="col mb-2" style="width: 33%">
                                        <label class="mb-0">Address</label>
                                        <p class="m-0">
                                            {{ isset($referenceDetails->address) ? $referenceDetails->address : '-' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col mb-2" style="width: 33%">
                                        <label class="mb-0">Title while Employed</label>
                                        <p class="m-0">
                                            {{ isset($referenceDetails->title_while_employed) ? $referenceDetails->title_while_employed : '-' }}
                                        </p>
                                    </div>

                                    <div class="col mb-2" style="width: 33%">
                                        <label class="mb-0">Phone</label>
                                        <p class="m-0">
                                            {{ isset($referenceDetails->phone) ? $referenceDetails->phone : '-' }}</p>
                                    </div>

                                    <div class="col mb-2" style="width: 33%">
                                        <label class="mb-0">Specialty Worked</label>
                                        <p class="m-0">
                                            {{ isset($referenceDetails->specialty_worked) ? $referenceDetails->specialty_worked : '-' }}
                                        </p>
                                    </div>

                                    <div class="col-sm-12 col-xs-12 col-md-12 mb-2">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>&nbsp;</th>
                                                    <th class="text-center">Yes</th>
                                                    <th class="text-center">No</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1. How long have you worked with this candidate?</td>
                                                    <td class="text-center">
                                                        {!! $referenceDetails->worked_with == 'Yes' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! $referenceDetails->worked_with == 'No' ? '&#10003;' : '' !!}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>2. Is this person honest, reliable, and trustworthy?</td>
                                                    <td class="text-center">
                                                        {!! $referenceDetails->honest_reliable == 'Yes' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! $referenceDetails->honest_reliable == 'No' ? '&#10003;' : '' !!}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>3. Was this candidate on a travel assignment? </td>
                                                    <td class="text-center">
                                                        {!! $referenceDetails->travel_assignment == 'Yes' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! $referenceDetails->travel_assignment == 'No' ? '&#10003;' : '' !!}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>4. Is this candidate eligible for rehire?</td>
                                                    <td class="text-center">
                                                        {!! $referenceDetails->eligible_rehire == 'Yes' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! $referenceDetails->eligible_rehire == 'No' ? '&#10003;' : '' !!}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <h6 class="border-bottom pb-2 mb-2">Candidate Employment Evaluation</h6>
                                <div class="row">
                                    <div class="col-sm-12 col-xs-12 col-md-12 mb-2">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Criteria</th>
                                                    <th class="text-center">Poor</th>
                                                    <th class="text-center">Fair</th>
                                                    <th class="text-center">Average</th>
                                                    <th class="text-center">Good</th>
                                                    <th class="text-center">Excellent</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1. Quality of Work</td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->quality_of_work == 'Poor' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->quality_of_work == 'Fair' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->quality_of_work == 'Average' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->quality_of_work == 'Good' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->quality_of_work == 'Excellent' ? '&#10003;' : '' !!}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>2. Clinical Knowledge/Skills</td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->clinical_knowledge == 'Poor' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->clinical_knowledge == 'Fair' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->clinical_knowledge == 'Average' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->clinical_knowledge == 'Good' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->clinical_knowledge == 'Excellent' ? '&#10003;' : '' !!}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>3. Attendance/Dependability</td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->attendance_dependability == 'Poor' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->attendance_dependability == 'Fair' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->attendance_dependability == 'Average' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->attendance_dependability == 'Good' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->attendance_dependability == 'Excellent' ? '&#10003;' : '' !!}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>4. Interpersonal/Communication Skills</td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->communication_skills == 'Poor' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->communication_skills == 'Fair' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->communication_skills == 'Average' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->communication_skills == 'Good' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->communication_skills == 'Excellent' ? '&#10003;' : '' !!}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>5. Relationship with Others</td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->relationship_others == 'Poor' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->relationship_others == 'Fair' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->relationship_others == 'Average' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->relationship_others == 'Good' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->relationship_others == 'Excellent' ? '&#10003;' : '' !!}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>6. Acceptance of Supervision</td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->acceptance_supervision == 'Poor' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->acceptance_supervision == 'Fair' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->acceptance_supervision == 'Average' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->acceptance_supervision == 'Good' ? '&#10003;' : '' !!}
                                                    </td>
                                                    <td class="text-center">
                                                        {!! isset($referenceDetails) && $referenceDetails->acceptance_supervision == 'Excellent' ? '&#10003;' : '' !!}
                                                    </td>
                                                </tr>


                                            </tbody>
                                        </table>
                                    </div>


                                </div>

                                <h6 class="border-bottom pb-2 mb-2">Additional Comments</h6>
                                <p class="mb-3">
                                    {{ isset($referenceDetails->additional_comments) ? $referenceDetails->additional_comments : '-' }}
                                </p>

                                <h6 class="border-bottom pb-2 mb-2">Reference Completed By</h6>

                                <div class="row">
                                    <div class="col-sm-3 col-xs-3 col-md-3 mb-2" style="width: 25%">
                                        <label class="mb-0">Name</label>
                                        <p class="m-0">
                                            {{ isset($referenceDetails->reference_by_name) ? $referenceDetails->reference_by_name : '-' }}
                                        </p>
                                    </div>

                                    <div class="col-sm-3 col-xs-3 col-md-3 mb-2" style="width: 25%">
                                        <label class="mb-0">Title</label>
                                        <p class="m-0">
                                            {{ isset($referenceDetails->reference_by_title) ? $referenceDetails->reference_by_title : '-' }}
                                        </p>
                                    </div>

                                    <div class="col-sm-3 col-xs-3 col-md-3 mb-2" style="width: 25%">
                                        <label class="mb-0">Phone</label>
                                        <p class="m-0">
                                            {{ isset($referenceDetails->reference_by_phone) ? $referenceDetails->reference_by_phone : '-' }}
                                        </p>
                                    </div>

                                    <div class="col-sm-3 col-xs-3 col-md-3 mb-2" style="width: 25%">
                                        <label class="mb-0">Email</label>
                                        <p class="m-0">
                                            {{ isset($referenceDetails->reference_by_email) ? $referenceDetails->reference_by_email : '-' }}
                                        </p>
                                    </div>

                                    <div class="col-sm-3 col-xs-3 col-md-3 mb-2" style="width: 25%">
                                        <label class="mb-0">Signature</label>
                                        <p class="m-0">
                                            {{ isset($referenceDetails->reference_by_signature) ? $referenceDetails->reference_by_signature : '-' }}
                                        </p>
                                    </div>

                                    <div class="col-sm-3 col-xs-3 col-md-3 mb-2" style="width: 25%">
                                        <label class="mb-0">Date</label>
                                        <p class="m-0">
                                            {{ isset($referenceDetails->reference_by_signature_date) ? $referenceDetails->reference_by_signature_date : '-' }}
                                        </p>
                                    </div>

                                    <div class="col-sm-3 col-xs-3 col-md-3 mb-2" style="width: 25%">
                                        <label class="mb-0">Reference Information Verified By</label>
                                        <p class="m-0">
                                            {{ isset($referenceDetails->reference_by_verified_by) ? $referenceDetails->reference_by_verified_by : '-' }}
                                        </p>
                                    </div>

                                    <div class="col-sm-3 col-xs-3 col-md-3 mb-2" style="width: 25%">
                                        <label class="mb-0">Date</label>
                                        <p class="m-0">
                                            {{ isset($referenceDetails->reference_by_verified_by_date) ? $referenceDetails->reference_by_verified_by_date : '-' }}
                                        </p>
                                    </div>

                                </div>

                            </div>
                        </div>
                        <?php } ?>

                        <?php } else { ?>
                        <p>No References Currently Available</p>
                        <?php } ?>
                    </div>
                </div>

                <div class="col-md-12" style="width: 100%; float: left; padding-left: 15px; box-sizing: border-box;">
                    <div class="section-title">Documents</div>
                    <div class="text-left" style="margin-top:2%">
                        <?php if (!empty($documents)) { ?>
                        <ul>
                            <?php foreach ($documents as $doc) { 
                            $imageUrl = "https://staging.travelnurse911.com/frontend/uploads/documents/" . $doc->file_name;
                            // Check if image exists at URL
                            if (@getimagesize($imageUrl)) { ?>
                            <li>
                                <span class="blue-bullet">&#8226;</span>
                                <a href="{{ $imageUrl }}" download="{{ $doc->file_name }}">
                                    <img src="{{ $imageUrl }}" alt="Document Image" width="400"
                                        height="400" style="margin-right: 10px;height:250px;">
                                </a>
                                {{ $doc->title }} | {{ $doc->doc_type_name }} |
                                {{ date('m/d/Y', strtotime($doc->expiry_date)) }}
                            </li>
                            <?php } ?>
                            <?php } ?>
                        </ul>
                        <?php } else { ?>
                        <p>No Documents Currently Available</p>
                        <?php } ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @if(empty($hide_action))
    <div class="no-print text-center py-3">
        <button class="btn btn-primary " onclick="window.print()">Print</button>
        <button class="btn btn-success " id="save-submission-file">Save</button>
    </div>
    @endif

    <!-- Bootstrap 4 and FontAwesome 5 -->
    <script src="{{ asset('assets/js/jquery.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/chosen.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script>
        $(document).ready(function () {
            var userID = "{{ $userRecord->unique_id }}";
            $('#save-submission-file').on('click', function () {
                $("#pageLoader").css("display", "flex");

                var redirectURL = "https://staging.travelnurse911.com/user/documents";
                $.ajax({
                    url: '{{ route("submission-file", ":id") }}'.replace(':id', userID),
                    type: 'GET',
                    data: {
                        save : 1 
                    },
                    success: function (response) {
                        $("#pageLoader").css("display", "none");
                        if (response.status) {
                            swal({
                                title: "Success!",
                                text: "Your submission file has been saved successfully",
                                icon: "success",
                                button: "OK",
                            }).then(() => {
                                window.location.href = redirectURL;
                            });
                        }
                    },
                    error: function (xhr) {
                        $("#pageLoader").css("display", "none");
                        alert('Something went wrong!');
                    }
                });
            });
        });
    
    </script>
    
</body>

</html>
