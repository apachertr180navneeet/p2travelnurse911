<!DOCTYPE html>

<html lang="en">



<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSRF Token -->

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="description" content="{{ $metadescription ?? '-' }}" />

    <meta name="keywords" content="" />

    <meta name="author" content="" />

    <title>

        @if (isset($title))

            {{ $title }}

            @endif @if (!isset($home))

                | {{ config('app.name') }}

            @endif

    </title>

    <link rel="shortcut icon" href="{{ asset('public/assets/images/fav.png') }}" />

    <link rel="stylesheet" href="{{ asset('public/assets/css/bootstrap.css') }}" />

    <link rel="stylesheet" href="{{ asset('public/assets/css/style.css') }}" />



    <style>

        select {

            padding: 2px;

            word-wrap: normal;

            border: 2px solid;

            border-radius: 0.1rem;

            background: none;

        }

        .section-header {

            background-color: #1a2eb9;

            color: white;

            padding: 10px;

            font-weight: bold;

        }



        .form-group {

            margin-bottom: 1rem;

        }



        .static-label {

            font-weight: bold;

        }



        .static-text {

            font-size: 14px;

            color: #000;

        }



        .rating-label {

            margin-right: 10px;

        }



        .static-section {

            padding: 10px;

            border: 1px solid #ccc;

            margin-bottom: 20px;

        }



        .candidate-info-box {

            background-color: #f8f8f8;

            /* Light gray background */

            border-radius: 20px;

            /* Rounded corners */

            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);

            /* Shadow effect */

        }



        .icon {

            font-size: 2rem;

            color: #0A00FF;

            /* Blue color for the icons */

        }



        h5 {

            font-size: 1rem;

            /* Adjust the font size */

            font-weight: 600;

            color: #333;

            /* Darker color for text */

        }



        p {

            font-size: 0.9rem;

            color: #666;

            /* Slightly lighter color for secondary text */

        }



        .ml-3 {

            margin-left: 1rem;

            /* Adjust left margin */

        }



        .underline-input {

            border: none;

            /* Remove default borders */

            border-bottom: 1px solid #000;

            /* Add a thin bottom border */

            border-radius: 0;

            /* Ensure no rounded corners */

            outline: none;

            /* Remove outline on focus */

            width: 100%;

            font-size: 1rem;

            background-color: revert !important;

            /* Adjust font size */

            padding-top: 15px;

        }



        .underline-input:focus {

            border-bottom: 2px solid #0A00FF;

            box-shadow: none;

            /* Thicker blue border on focus */

        }



        label {

            font-weight: 600;

            font-size: 1rem;

            color: #333;

            margin-top: 10px;

            /* Darker label color */

        }



        .form-group {

            margin-bottom: 15px;

            /* Space between fields */

        }



        .emp_info label {

            white-space: nowrap;

            margin-right: 10px;

        }



        /* Container and layout styles */

        .candidate-info-section {

            background-color: #f7f5f5;

            border-radius: 10px;

            padding: 20px;

            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);

        }



        /* Styling for icons */

        .icon {

            font-size: 35px;

            /* Adjust icon size */

            color: #0A00FF;

            /* Set the icon color */

        }



        /* Spacing for text */

        .ml-3 {

            margin-left: 1rem;

            /* Adjust margin to position text next to the icon */

        }



        /* Styling for Name and Job Title */

        h5 {

            font-size: 1rem;

            font-weight: 600;

            color: #333;

        }



        p {

            font-size: 1rem;

            color: #666;

        }



        /* Adjust layout for mobile view */

        @media (max-width: 768px) {

            .candidate-info-section .row {

                flex-direction: column;

                text-align: left;

            }

        }



        input[type="radio"] {

            display: none;

            /* Hide the default radio button */

        }



        label.form-check-inline {

            display: inline-block;

            width: 20px;

            /* Set the width of the square */

            height: 20px;

            /* Set the height of the square */

            border: 2px solid #000000;

            /* Border color */

            border-radius: 0;

            /* Make it square */

            cursor: pointer;

            /* Change cursor on hover */

            position: relative;

            /* For positioning the checked state */

            margin-right: 10px;

            /* Space between square and label */

            margin-bottom: 0;

            vertical-align: text-bottom;

        }



        input[type="radio"]:checked+label {

            background-color: white;

            /* Change background color when checked */

            color: white;

            /* Change text color (if applicable) */

        }



        input[type="radio"]:checked+label::after {

            content: "";

            position: absolute;

            top: 1px;

            /* Position the check mark */

            left: 1px;

            /* Position the check mark */

            width: 16px;

            /* Size of the check mark */

            height: 16px;

            /* Size of the check mark */

            background: #000000;

            /* Color of the check mark */

            clip-path: polygon(0 50%, 40% 100%, 100% 0, 80% 0, 40% 60%);

            /* Create a check mark shape */

        }



        .required {

            color: red;

        }

        .custom-checkbox {         

            width: 25px; /* Adjust width */

            cursor: pointer; /* Add pointer cursor for better UX */

        }

        

    </style>

</head>



<body>

    <form method="POST" action="#" id="email-form">

        @csrf



        <input type="hidden" name="reference_id" value="{{ $referenceRecord->ref_id }}">

        <input type="hidden" name="user_id" value="{{ $referenceRecord->user_id }}">



        <div class="container mt-4 mb-4 shadow py-5">



            <div class="form-group col-lg-12 col-md-12 col-sm-12">

                <div class="response text-center"></div>

                <div class="success-response bg-success text-white text-center"></div>

            </div>



            <h4 class="text-center mb-4 text-uppercase font-weight-bold">Candidate Reference Information</h4>

            <div class="section-header">Candidate Information</div>

            <div class="container candidate-info-section p-3">

                <div class="row">

                    <!-- Line 1: Name -->

                    <div class="col-md-6 d-flex align-items-center">

                        <span class="icon flaticon-user"></span> <!-- Name Icon -->

                        <div class="ml-3">

                            <p class="mb-0">{{ $referenceRecord->name }}</p>

                        </div>

                    </div>

                    <!-- Line 2: Job Title -->

                    <div class="col-md-6 d-flex align-items-center">

                        <span class="icon flaticon-phone-call"></span> <!-- Phone Icon -->

                        <div class="ml-3">

                            @php 

                                $formatted = '';

                                if(!empty($referenceRecord->phone)) {

                                    $formatted = preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1-$2-$3', $referenceRecord->phone);

                                }

                            @endphp 



                            <p class="mb-0">{{ $formatted }}</p>

                        </div>

                    </div>



                </div>



                <div class="row mt-3">

                    <!-- Line 3: Phone -->

                    <div class="col-md-6 d-flex align-items-center">

                        <span class="icon flaticon-briefcase"></span> <!-- Job Title Icon -->

                        <div class="ml-3">

                            <p class="mb-0">{{ $referenceRecord->profession }}</p>

                        </div>

                    </div>

                    <!-- Line 4: Email -->

                    <div class="col-md-6 d-flex align-items-center">

                        <span class="icon flaticon-envelope"></span> <!-- Email Icon -->

                        <div class="ml-3">

                            <p class="mb-0">{{ $referenceRecord->email }}</p>

                        </div>

                    </div>

                </div>

            </div>





            <!-- Employment Information Section -->

            <div class="section-header">Employment Information</div>

            <div class="form-row mt-2 emp_info">

                <div class="form-group col-md-6 d-flex">

                    <label>Facility:</label>

                    <input type="text" class="form-control underline-input" name="facility"

                        value="{{ isset($referenceRecord) && isset($referenceRecord->reference_name) ? $referenceRecord->reference_name : '' }}" readonly>

                </div>

                <div class="form-group col-md-6 d-flex">

                    <label>Dates of Employment:</label>

                    @php

                        $dateEmp = !empty($referenceDetails->dates_of_employment)

                            ? date('m/d/Y',strtotime($referenceDetails->dates_of_employment))

                            : '';

                    @endphp

                    <input type="text" class="form-control underline-input" name="dates_of_employment"

                        value="{{ $dateEmp }}" readonly>

                </div>
                <div class="form-group col-md-6 d-flex">

                    <label>End Date of Employment:</label>

                    @php

                        $dateEmp = !empty($referenceDetails->end_date_of_employment)

                            ? date('m/d/Y',strtotime($referenceDetails->end_date_of_employment))

                            : '';

                    @endphp

                    <input type="text" class="form-control underline-input" name="dates_of_employment"

                        value="{{ $dateEmp }}" readonly>

                </div>

                <div class="form-group col-md-6 d-flex">

                    <label>Address:</label>

                    <input type="text" class="form-control underline-input" name="address"

                        value="{{ isset($referenceDetails) && isset($referenceDetails->address) ? $referenceDetails->address : '' }}" readonly>

                </div>

                <div class="form-group col-md-6 d-flex">

                    <label>Title while Employed:</label>

                    <input type="text" class="form-control underline-input" name="title_while_employed"

                        value="{{ isset($referenceDetails) && isset($referenceDetails->title_while_employed) ? $referenceDetails->title_while_employed : '' }}" readonly>

                </div>

                <div class="form-group col-md-6 d-flex">

                    <label>Phone:</label>

                    @php 

                        $formattedPhone = '';

                        if(!empty($referenceRecord->reference_phone)) {

                            $formattedPhone = preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1-$2-$3', $referenceRecord->reference_phone);

                        }

                    @endphp 

                    <input type="text" class="form-control underline-input" name="phone"

                        value="{{ $formattedPhone }}" readonly> 

                </div>

                <div class="form-group col-md-6 d-flex">

                    <label>Specialty Worked:</label>

                    <input type="text" class="form-control underline-input" name="specialty_worked"

                        value="{{ isset($referenceDetails) && isset($referenceDetails->specialty_worked) ? $referenceDetails->specialty_worked : '' }}">

                </div>

            </div>



            <!-- Employment Questions -->

            <div class="form-row">

                <div class="form-group col-md-12">

                    <label>1. How long have you worked with this candidate?</label>&nbsp;&nbsp;                    

                    <select class="form-select" name="worked_with" id="worked_with" required>

                        <option value="" disabled selected>Select years</option>

                        @for ($i = 1; $i <= 10; $i++)

                            @php 

                                if($i == 10) {

                                    $i = "10+";

                                }

                            @endphp

                            <option value="{{ $i }}" 

                                {{ isset($referenceDetails) && $referenceDetails->worked_with == $i ? 'selected' : '' }}>

                                {{ $i }} year{{ $i > 1 ? 's' : '' }}

                            </option>

                        @endfor

                    </select>



                </div>

                <div class="form-group col-md-12">

                    <label>2. Is this person honest, reliable, and trustworthy?</label>&nbsp;&nbsp;



                    <input class="form-check-input" type="radio" name="honest_reliable" value="Yes"

                        id="honest_reliable_yes"

                        {{ isset($referenceDetails) && $referenceDetails->honest_reliable == 'Yes' ? 'checked' : '' }}>

                    <label class="form-check form-check-inline" for="honest_reliable_yes"></label>Yes



                    <input class="form-check-input" type="radio" name="honest_reliable" value="No"

                        id="honest_reliable_no"

                        {{ isset($referenceDetails) && $referenceDetails->honest_reliable == 'No' ? 'checked' : '' }}>

                    <label class="form-check form-check-inline" for="honest_reliable_no"></label>No



                </div>

                <div class="form-group col-md-12">

                    <label>3. Was this candidate on a travel assignment?</label>&nbsp;&nbsp;



                    <input class="form-check-input" type="radio" name="travel_assignment" value="Yes"

                        id="travel_assignment_yes"

                        {{ isset($referenceDetails) && $referenceDetails->travel_assignment == 'Yes' ? 'checked' : '' }}>

                    <label class="form-check form-check-inline" for="travel_assignment_yes"></label>Yes



                    <input class="form-check-input" type="radio" name="travel_assignment" value="No"

                        id="travel_assignment_no"

                        {{ isset($referenceDetails) && $referenceDetails->travel_assignment == 'No' ? 'checked' : '' }}>

                    <label class="form-check form-check-inline" for="travel_assignment_no"></label>No

                </div>

                <div class="form-group col-md-12">

                    <label>4. Is this candidate eligible for rehire?</label>&nbsp;&nbsp;

                    <input class="form-check-input" type="radio" name="eligible_rehire" value="Yes"

                        id="eligible_rehire_yes"

                        {{ isset($referenceDetails) && $referenceDetails->eligible_rehire == 'Yes' ? 'checked' : '' }}>

                    <label class="form-check form-check-inline" for="eligible_rehire_yes"></label>Yes



                    <input class="form-check-input" type="radio" name="eligible_rehire" value="No"

                        id="eligible_rehire_no"

                        {{ isset($referenceDetails) && $referenceDetails->eligible_rehire == 'No' ? 'checked' : '' }}>

                    <label class="form-check form-check-inline" for="eligible_rehire_no"></label>No



                </div>

            </div>



            <!-- Candidate Employment Evaluation Section -->

            <div class="section-header">Candidate Employment Evaluation</div>

            <div class="table-responsive mt-3">

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

                            <td>Quality of Work</td>

                            <td class="text-center">

                                <input type="radio" name="quality_of_work" value="Poor"

                                    id="quality_of_work_poor"

                                    {{ isset($referenceDetails) && $referenceDetails->quality_of_work == 'Poor' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline" for="quality_of_work_poor"></label>

                            </td>

                            <td class="text-center">

                                <input type="radio" name="quality_of_work" value="Fair"

                                    id="quality_of_work_fair"

                                    {{ isset($referenceDetails) && $referenceDetails->quality_of_work == 'Fair' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline" for="quality_of_work_fair"></label>

                            </td>

                            <td class="text-center">

                                <input type="radio" name="quality_of_work" value="Average"

                                    id="quality_of_work_average"

                                    {{ isset($referenceDetails) && $referenceDetails->quality_of_work == 'Average' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline" for="quality_of_work_average"></label>

                            </td>

                            <td class="text-center">

                                <input type="radio" name="quality_of_work" value="Good"

                                    id="quality_of_work_good"

                                    {{ isset($referenceDetails) && $referenceDetails->quality_of_work == 'Good' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline" for="quality_of_work_good"></label>

                            </td>

                            <td class="text-center">

                                <input type="radio" name="quality_of_work" value="Excellent"

                                    id="quality_of_work_excellent"

                                    {{ isset($referenceDetails) && $referenceDetails->quality_of_work == 'Excellent' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline" for="quality_of_work_excellent"></label>

                            </td>

                        </tr>

                        <tr>

                            <td>Clinical Knowledge/Skills</td>

                            <td class="text-center">

                                <input type="radio" name="clinical_knowledge" value="Poor"

                                    id="clinical_knowledge_poor"

                                    {{ isset($referenceDetails) && $referenceDetails->clinical_knowledge == 'Poor' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline" for="clinical_knowledge_poor"></label>

                            </td>

                            <td class="text-center">

                                <input type="radio" name="clinical_knowledge" value="Fair"

                                    id="clinical_knowledge_fair"

                                    {{ isset($referenceDetails) && $referenceDetails->clinical_knowledge == 'Fair' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline" for="clinical_knowledge_fair"></label>

                            </td>

                            <td class="text-center">

                                <input type="radio" name="clinical_knowledge" value="Average"

                                    id="clinical_knowledge_average"

                                    {{ isset($referenceDetails) && $referenceDetails->clinical_knowledge == 'Average' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline" for="clinical_knowledge_average"></label>

                            </td>

                            <td class="text-center">

                                <input type="radio" name="clinical_knowledge" value="Good"

                                    id="clinical_knowledge_good"

                                    {{ isset($referenceDetails) && $referenceDetails->clinical_knowledge == 'Good' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline" for="clinical_knowledge_good"></label>

                            </td>

                            <td class="text-center">

                                <input type="radio" name="clinical_knowledge" value="Excellent"

                                    id="clinical_knowledge_excellent"

                                    {{ isset($referenceDetails) && $referenceDetails->clinical_knowledge == 'Excellent' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline"

                                    for="clinical_knowledge_excellent"></label>

                            </td>

                        </tr>

                        <tr>

                            <td>Attendance/Dependability</td>

                            <td class="text-center">

                                <input type="radio" name="attendance_dependability" value="Poor"

                                    id="attendance_dependability_poor"

                                    {{ isset($referenceDetails) && $referenceDetails->attendance_dependability == 'Poor' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline"

                                    for="attendance_dependability_poor"></label>

                            </td>

                            <td class="text-center">

                                <input type="radio" name="attendance_dependability" value="Fair"

                                    id="attendance_dependability_fair"

                                    {{ isset($referenceDetails) && $referenceDetails->attendance_dependability == 'Fair' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline"

                                    for="attendance_dependability_fair"></label>

                            </td>

                            <td class="text-center">

                                <input type="radio" name="attendance_dependability" value="Average"

                                    id="attendance_dependability_average"

                                    {{ isset($referenceDetails) && $referenceDetails->attendance_dependability == 'Average' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline"

                                    for="attendance_dependability_average"></label>

                            </td>

                            <td class="text-center">

                                <input type="radio" name="attendance_dependability" value="Good"

                                    id="attendance_dependability_good"

                                    {{ isset($referenceDetails) && $referenceDetails->attendance_dependability == 'Good' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline"

                                    for="attendance_dependability_good"></label>

                            </td>

                            <td class="text-center">

                                <input type="radio" name="attendance_dependability" value="Excellent"

                                    id="attendance_dependability_excellent"

                                    {{ isset($referenceDetails) && $referenceDetails->attendance_dependability == 'Excellent' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline"

                                    for="attendance_dependability_excellent"></label>

                            </td>

                        </tr>

                        <tr>

                            <td>Interpersonal/Communication Skills</td>

                            <td class="text-center">

                                <input type="radio" name="communication_skills" value="Poor"

                                    id="communication_skills_poor"

                                    {{ isset($referenceDetails) && $referenceDetails->communication_skills == 'Poor' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline" for="communication_skills_poor"></label>

                            </td>

                            <td class="text-center">

                                <input type="radio" name="communication_skills" value="Fair"

                                    id="communication_skills_fair"

                                    {{ isset($referenceDetails) && $referenceDetails->communication_skills == 'Fair' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline" for="communication_skills_fair"></label>

                            </td>

                            <td class="text-center">

                                <input type="radio" name="communication_skills" value="Average"

                                    id="communication_skills_average"

                                    {{ isset($referenceDetails) && $referenceDetails->communication_skills == 'Average' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline"

                                    for="communication_skills_average"></label>

                            </td>

                            <td class="text-center">

                                <input type="radio" name="communication_skills" value="Good"

                                    id="communication_skills_good"

                                    {{ isset($referenceDetails) && $referenceDetails->communication_skills == 'Good' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline" for="communication_skills_good"></label>

                            </td>

                            <td class="text-center">

                                <input type="radio" name="communication_skills" value="Excellent"

                                    id="communication_skills_excellent"

                                    {{ isset($referenceDetails) && $referenceDetails->communication_skills == 'Excellent' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline"

                                    for="communication_skills_excellent"></label>

                            </td>

                        </tr>

                        <tr>

                            <td>Relationship with Others</td>

                            <td class="text-center">

                                <input type="radio" name="relationship_others" value="Poor"

                                    id="relationship_others_poor"

                                    {{ isset($referenceDetails) && $referenceDetails->relationship_others == 'Poor' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline" for="relationship_others_poor"></label>

                            </td>

                            <td class="text-center">

                                <input type="radio" name="relationship_others" value="Fair"

                                    id="relationship_others_fair"

                                    {{ isset($referenceDetails) && $referenceDetails->relationship_others == 'Fair' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline" for="relationship_others_fair"></label>

                            </td>

                            <td class="text-center">

                                <input type="radio" name="relationship_others" value="Average"

                                    id="relationship_others_average"

                                    {{ isset($referenceDetails) && $referenceDetails->relationship_others == 'Average' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline" for="relationship_others_average"></label>

                            </td>

                            <td class="text-center">

                                <input type="radio" name="relationship_others" value="Good"

                                    id="relationship_others_good"

                                    {{ isset($referenceDetails) && $referenceDetails->relationship_others == 'Good' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline" for="relationship_others_good"></label>

                            </td>

                            <td class="text-center">

                                <input type="radio" name="relationship_others" value="Excellent"

                                    id="relationship_others_excellent"

                                    {{ isset($referenceDetails) && $referenceDetails->relationship_others == 'Excellent' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline"

                                    for="relationship_others_excellent"></label>

                            </td>

                        </tr>

                        <tr>

                            <td>Acceptance of Supervision</td>

                            <td class="text-center">

                                <input type="radio" name="acceptance_supervision" value="Poor"

                                    id="acceptance_supervision_poor"

                                    {{ isset($referenceDetails) && $referenceDetails->acceptance_supervision == 'Poor' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline" for="acceptance_supervision_poor"></label>

                            </td>

                            <td class="text-center">

                                <input type="radio" name="acceptance_supervision" value="Fair"

                                    id="acceptance_supervision_fair"

                                    {{ isset($referenceDetails) && $referenceDetails->acceptance_supervision == 'Fair' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline" for="acceptance_supervision_fair"></label>

                            </td>

                            <td class="text-center">

                                <input type="radio" name="acceptance_supervision" value="Average"

                                    id="acceptance_supervision_average"

                                    {{ isset($referenceDetails) && $referenceDetails->acceptance_supervision == 'Average' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline"

                                    for="acceptance_supervision_average"></label>

                            </td>

                            <td class="text-center">

                                <input type="radio" name="acceptance_supervision" value="Good"

                                    id="acceptance_supervision_good"

                                    {{ isset($referenceDetails) && $referenceDetails->acceptance_supervision == 'Good' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline" for="acceptance_supervision_good"></label>

                            </td>

                            <td class="text-center">

                                <input type="radio" name="acceptance_supervision" value="Excellent"

                                    id="acceptance_supervision_excellent"

                                    {{ isset($referenceDetails) && $referenceDetails->acceptance_supervision == 'Excellent' ? 'checked' : '' }}>

                                <label class="form-check form-check-inline"

                                    for="acceptance_supervision_excellent"></label>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>



            <div class="form-group d-flex emp_info">

                <label for="comments">Additional Comments:</label>

                <input type="text" class="form-control underline-input" name="additional_comments"

                    value="{{ isset($referenceDetails) && $referenceDetails->additional_comments ? $referenceDetails->additional_comments : '' }}">

            </div>



            <!-- Employment Information Section -->

            <div class="section-header">Reference Completed By <small>(All Information must be completed below)</small>

            </div>

            <div class="form-row mt-2 emp_info">

                <div class="form-group col-md-6 d-flex">

                    <label>Name:</label>

                    <input type="text" class="form-control underline-input" id="reference_by_name" name="reference_by_name" required

                        value="{{ isset($referenceDetails) && $referenceDetails->reference_by_name ? $referenceDetails->reference_by_name : '' }}">

                </div>

                <div class="form-group col-md-6 d-flex">

                    <label>Title:</label>

                    <input type="text" class="form-control underline-input" name="reference_by_title" required

                        value="{{ isset($referenceDetails) && $referenceDetails->reference_by_title ? $referenceDetails->reference_by_title : '' }}">

                </div>

                <div class="form-group col-md-6 d-flex">

                    <label>Phone:</label>

                    @php 

                        $formatted = '';

                        if(!empty($referenceDetails->reference_by_phone)) {

                            $formatted = preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1-$2-$3', $referenceDetails->reference_by_phone);

                        }

                    @endphp

                    <input type="text" id="reference_by_phone" class="form-control underline-input" name="reference_by_phone" required

                        value="{{$formatted }}">

                </div>

                <div class="form-group col-md-6 d-flex">

                    <label>Email:</label>

                    <input type="email" class="form-control underline-input" id="reference_by_email"

                        name="reference_by_email" required

                        value="{{ isset($referenceDetails) && $referenceDetails->reference_by_email ? $referenceDetails->reference_by_email : '' }}">

                </div>

                <div class="form-group col-md-6 d-flex">

                </div>

                <div class="form-group col-md-6 d-flex">

                    <span class="form-group required" id="reference_by_email_error"></span>

                </div>



                <div class="form-group col-md-6 d-flex" style="display: none !important;">

                    <label>Signature:</label>

                    <input type="hidden" class="form-control underline-input" id="reference_by_signature" name="reference_by_signature"

                        value="{{ isset($referenceDetails) && $referenceDetails->reference_by_signature ? $referenceDetails->reference_by_signature : '' }}">

                </div>



                <div class="form-group col-md-6 d-flex">

                    <input type="checkbox" id="consent_signature" class="custom-checkbox mr-2">

                    <label for="consent_signature" class="consent_signature">Consent to Adapt Signature</label>                        

                </div>                

                <div class="form-group col-md-6 d-flex">

                    <label>Date:</label>

                    @php

                        $refDate = !empty($referenceDetails->reference_by_signature_date)

                            ? \Carbon\Carbon::createFromFormat(

                                'Y-m-d',

                                $referenceDetails->reference_by_signature_date,

                            )->format('m/d/Y')

                            : '';

                    @endphp

                    <input type="text" class="form-control underline-input" id="reference_by_signature_date"

                        name="reference_by_signature_date" value="{{ $refDate }}">

                </div>

                <div class="form-group col-md-6 d-flex">

                    <span class="form-group required" id="reference_by_signature_error"></span>

                </div>

            </div>



            <div class="text-center">

                <?php if (isset($referenceDetails)) { ?>

                <button type="submit" class="theme-btn btn-style-one">Update</button>

                <?php } else { ?>

                <button type="submit" class="theme-btn btn-style-one">Submit</button>

                <?php } ?>

            </div>

        </div>

    </form>



    <script src="{{ asset('public/assets/js/jquery.js') }}"></script>

    <script src="{{ asset('public/assets/js/popper.min.js') }}"></script>

    <script src="{{ asset('public/assets/js/chosen.min.js') }}"></script>

    <script src="{{ asset('public/assets/js/bootstrap.min.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <script>

        $(document).ready(function() {



            $("#reference_by_signature_date").datepicker({

                dateFormat: "mm/dd/yy", // Format for month/day/year

            });

                    

            // Prevent keyboard input (disable typing)

            $("#reference_by_signature_date").keydown(function (e) {

                e.preventDefault(); // This will block any key press

            });

            



            $('#reference_by_phone').on('input keyup', function () {

                let value = $(this).val().replace(/\D/g, ''); // Remove non-numeric characters



                // Format as XXX-XXX-XXXX

                if (value.length > 3 && value.length <= 6) {

                    value = value.slice(0, 3) + '-' + value.slice(3);

                } else if (value.length > 6) {

                    value = value.slice(0, 3) + '-' + value.slice(3, 6) + '-' + value.slice(6, 10);

                }



                // Update the input value

                $(this).val(value);

            });



            $('#consent_signature').on('change', function () {

                const printName = $('#reference_by_name').val(); // Get the value of Print Name field

                const $signatureField = $('#reference_by_signature'); // Reference the Signature field

                if ($(this).is(':checked')) {

                    $signatureField.val(printName || ''); // Set the Signature field value

                    $('#reference_by_signature_error').text("");

                } else {

                    $signatureField.val(''); // Clear the Signature field

                }

            });

            



            //Contact Form Validation

            if ($("#email-form").length) {

                $("#email-form").submit(function(e) {

                    e.stopPropagation();



                    $("#email-form .response, #email-form .success-response").html('');



                    var o = new Object();

                    var form = "#email-form";



                    var reference_by_name = $("#email-form .reference_by_name").val();

                    var reference_by_title = $("#email-form .reference_by_title").val();

                    var reference_by_phone = $("#email-form .reference_by_phone").val();

                    var reference_by_email = $("#reference_by_email").val();

                    var reference_by_email = $("#email-form .message").val();





                    // Regular expression to prevent script/php injection

                    var regex =

                        /<\s*script.*?>.*?<\s*\/\s*script\s*>|<\s*\?php.*?\?>|<.*?>/i;



                    if (

                        reference_by_name == "" ||

                        reference_by_title == "" ||

                        reference_by_phone == "" ||

                        reference_by_email == ""

                    ) {

                        $(form + " .response").html(

                            '<div class="failed">Please fill the required fields.</div>'

                        );

                        return false;

                    }



                    if (

                        regex.test(reference_by_name) ||

                        regex.test(reference_by_title) ||

                        regex.test(reference_by_phone) ||

                        regex.test(reference_by_email)

                    ) {

                        $(form + " .response").html(

                            '<div class="failed">Invalid input detected. Please avoid using special characters or code.</div>'

                        );

                        return false;

                    }





                    $.ajax({

                        url: "{{ route('reference-form-submit') }}",

                        method: "POST",

                        data: $(form).serialize(),

                        beforeSend: function() {

                            $("#email-form .response").html(

                                '<div class="text-info"><img src="{{ asset('public/assets/images/icons/preloader.gif') }}"> Loading...</div>'

                            );

                        },

                        success: function(res) {

                            if (res.errors && res.errors != "") {

                                for (var error in res.errors) {

                                    $(form + " .response ul").append(

                                        "<li>" + res.errors[error] + "</li>"

                                    );

                                    if (error == "reference_by_email") {

                                        $('#reference_by_email_error').text(res.errors[error]);

                                    }

                                    if (error == "reference_by_signature") {

                                        $('#reference_by_signature_error').text("Please check the box to confirm your consent before proceeding.!");

                                    }



                                }

                            } else {

                                if (res.status) {

                                    $('#reference_by_email_error').text('');



                                    $("#email-form .response").hide();

                                    /*$("form").trigger("reset");*/

                                    $("#email-form .success-response").fadeIn().html(res

                                        .message);



                                    swal({

                                        title: "Good Job!",

                                        text: "Your reference detail has been submitted successfully",

                                        icon: "success",

                                        button: "OK",

                                    }).then(() => {

                                        // Redirect to home route

                                        window.location.href = "{{ route('home') }}";

                                    });

                                    setTimeout(function() {

                                        $("#email-form .success-response").fadeOut(

                                            "slow");

                                    }, 5000);

                                } else {

                                    $(form + " .response").html(

                                        '<div class="failed">' + res.message + '</div>'

                                    );

                                }

                            }



                        },

                        error: function(data) {

                            $("#email-form .response").fadeIn().html(data);

                        },

                    });



                    return false;

                });

            }

        });

    </script>

</body>



</html>

