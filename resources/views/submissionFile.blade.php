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

    <link rel="shortcut icon" href="{{ asset('public/assets/images/fav.png') }}" />

    <link rel="stylesheet" href="{{ asset('public/assets/css/bootstrap.css') }}" />

    <link rel="stylesheet" href="{{ asset('public/assets/css/style.css') }}" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">

    <style>

        .page-break {

            page-break-before: always;

        }



        table,

        img,

        content {

            page-break-inside: avoid;

        }



        body {

            height: auto !important;

            overflow: visible !important;

            font-family: 'Arial', sans-serif;

        }



        html,

        body {

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

        .contact-infos {

            list-style: none;

            padding: 0;

            margin: 0;

            display: flex;

            flex-direction: column;

            gap: 8px; /* Adds spacing between items */

        }

        

        .contact-infos li {

            display: flex;

            align-items: center;

            font-size: 16px;

            color: #333;

        }

        

        .contact-infos i {

            font-size: 18px;

            color: #1a1aff;

            margin-right: 10px;

            min-width: 24px;

            text-align: center;

        }

        

        .contact-infos .text {

            flex: 1; /* Ensures text aligns properly */

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

                <div class="col" style="width: 50%">



                    <div class="profile-header d-flex justify-content-center align-items-center py-4">

                        <h4 class="text-dark text-left">PROFILE TEMPLATE</h4>

                    </div>

                    <div class="section-title">Contact Information</div>

                    <div class="contact-infos py-3 px-4">

                        <ul>

                            @php

                                $phone = isset($userRecord) ? preg_replace('/\D/', '', $userRecord->phone) : '';

                                $formattedPhone =

                                    strlen($phone) == 10

                                        ? substr($phone, 0, 3) . '-' . substr($phone, 3, 3) . '-' . substr($phone, 6)

                                        : $userRecord->phone;

                            @endphp



                            <li>

                                <i class="fa fa-phone"></i>

                                <span class="text">{{ $formattedPhone }}</span>

                            </li>

                            <li>

                                <i class="fas fa-envelope"></i>

                                <span class="text">{{ isset($userRecord) ? $userRecord->email : '' }}</span>

                            </li>

                            <li>

                                <i class="fas fa-map-marker-alt"></i>

                                <span class="text">

                                    {{ isset($userRecord)

                                        ? trim(

                                            implode(

                                                ', ',

                                                array_filter([

                                                    $userRecord->address_line1,

                                                    $userRecord->address_line2,

                                                    $userRecord->city_name,

                                                    $userRecord->state_name,

                                                ]),

                                            ),

                                        )

                                        : '' }}

                                </span>

                            </li>

                            <li>

                                <i class="far fa-clock"></i>

                                <span class="text">Monday-Friday | 9am-6pm EST</span>

                            </li>

                        </ul>

                    </div>



                    <div class="section-title">Active Certifications</div>

                    <div class="certifications-infos py-3 px-4">

                        <ul>

                            @if (!empty($userActiveCertificate) && count($userActiveCertificate) > 0)

                                @foreach ($userActiveCertificate as $certificate)

                                    <li><span class="blue-bullet">&#8226;</span> <strong>

                                            {{ $certificate->certificate_name }}</strong> | <strong>Expires : </strong>

                                        {{ !empty($certificate->certificate_expiry_date) ? date('m/d/Y', strtotime($certificate->certificate_expiry_date)) : '-' }}

                                    </li>

                                @endforeach

                            @else

                                <li><span class="blue-bullet">-</span>

                            @endif

                        </ul>

                    </div>



                    <div class="section-title">Education</div>

                    <div class="education-infos py-3 px-4">

                        <?php

							if (!empty($educationalDetails)) {

						?>

                        <ul>

                            <?php foreach ($educationalDetails as $edu) { ?>

                            <li><span class="blue-bullet">&#8226;</span>

                                <?php

                                $edu_string = [];

                                

                                if (!empty($edu->school_college)) {

                                    $edu_string[] = $edu->school_college;

                                }

                                

                                if (!empty($edu->location)) {

                                    $edu_string[] = $edu->location;

                                }

                                

                                if (!empty($edu->degree)) {

                                    $edu_string[] = $edu->degree;

                                }

                                

                                if (!empty($edu->currently_attending)) {

                                    $edu_string[] = 'Currently Attending';

                                } elseif (!empty($edu->end_month) && !empty($edu->end_year)) {

                                    $edu_string[] = $edu->end_month . '/' . $edu->end_year;

                                }

                                

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

                <div class="col" style="width: 50%">



                    <div class=" profile-header text-left pt-5 pb-3 px-4">

                        <h1 class="mb-2"><strong>{{ isset($userRecord) ? $userRecord->name : '' }}</strong></h1>

                        <h3><i>{{ isset($userRecord) ? $userRecord->profession : '' }}</i></h3>

                        <h6><i>{{ isset($userRecord) ? $userRecord->specialty : '' }}</i></h6>

                    </div>



                    <div class="section-title">Active State License</div>

                    <div class="license-infos py-3 px-4">

                        <ul>

                            @if (!empty($userStateLicense) && count($userStateLicense) > 0)

                                @foreach ($userStateLicense as $license)

                                    <li><span class="blue-bullet">&#8226;</span> {{ $license->license_name }} <br>

                                        {{ $license->license_name }} | Expires:

                                        {{ date('m/d/Y', strtotime($license->license_expiry_date)) }}</li>

                                @endforeach

                            @else

                                <li><span class="blue-bullet">-</span>

                            @endif

                        </ul>

                    </div>



                    <div class="section-title">Summary</div>

                    <div class="summary-infos py-3 px-4">

                        <ul>

                            <li><span class="blue-bullet">&#8226;</span> <strong>Desired Shift</strong>:

                                {{ isset($desired_shifts) && !empty($desired_shifts) ? implode(', ', $desired_shifts) : '-' }}

                            </li>

                            <li><span class="blue-bullet">&#8226;</span> <strong>Available Start Date</strong>:

                                {{ !empty($userRecord->available_start_date) ? date('m/d/Y', strtotime($userRecord->available_start_date)) : '-' }}

                            </li>

                            <li><span class="blue-bullet">&#8226;</span> <strong>Years of Experience</strong>:

                                {{ isset($userRecord) && !empty($userRecord->total_experience) ? $userRecord->total_experience . ' year(s)' : '-' }}

                            </li>

                            <li><span class="blue-bullet">&#8226;</span> <strong>Experience in Specialty</strong>:

                                {{ !empty($userRecord->specialty_experience) ? $userRecord->specialty_experience . 'year(s)' : '-' }}

                            </li>







                            <li><span class="blue-bullet">&#8226;</span> <strong>RTO</strong>:

                                @php

                                    if (!empty($user_rto_details) && count($user_rto_details) > 0) {

                                        $dates = [];

                                        foreach ($user_rto_details as $key => $value) {

                                            // Assuming 'start_date' and 'end_date' are keys in each entry

                                            $dates[] = $value->rto_start_date . '-' . $value->rto_end_date;

                                        }

                                        // Join all date ranges with a comma, ensuring there's no trailing comma

    echo implode(', ', $dates);

} else {

    echo '-';

                                    }

                                @endphp

                            </li>

                            <li><span class="blue-bullet">&#8226;</span> <strong>EMR Experience</strong>:

                                {{ $userRecord->EMR_experience ?? '-' }}</li>

                            <li><span class="blue-bullet">&#8226;</span> <strong>Teaching Hospital Experience</strong>:

                                {{ $userRecord->teaching_hospital_experience ?? '-' }}</li>

                            <li><span class="blue-bullet">&#8226;</span> <strong>Travel Experience</strong>:

                                {{ !empty($userRecord->travel_experience) ? $userRecord->travel_experience . ' year(s)' : '-' }}

                            </li>

                            <li><span class="blue-bullet">&#8226;</span> <strong>Fully Vaccinated</strong>:

                                {{ $userRecord->fully_vaccinated ?? '-' }}</li>

                        </ul>

                    </div>

                </div>



                <div class="col-sm-12 col-xs-12 col-md-12">

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

                <div class="page-break"></div>



                <div class="col-sm-12 col-xs-12 col-md-12">

                    <div class="section-title">Skill Checklists</div>

                    <div class="education-infos py-3 px-4">

                        @if (!empty($completed_checklists))

                            <ul>

                                @foreach ($completed_checklists as $chKey => $chklist)

                                    <li><span class="blue-bullet">&#8226;</span> {{ $chklist->title }} |

                                        {{ date('m/d/Y', strtotime($chklist->submitted_on)) }}

                                    </li>



                                    @php

                                        $checkListMetaData = json_decode($chklist->checklist_meta, true);

                                        $checklist_answer = json_decode($chklist->checklist_answer, true);

                                        if (!is_array($checklist_answer)) {

                                            $checklist_answer = [];

                                        }

                                    @endphp

                                    <div class="meta-data-section m-2">

                                        @foreach ($checkListMetaData as $meta => $metaData)

                                            <span>{{ $metaData['sectionTitle'] }}</span>

                                            <table class="table table-bordered mb-2 table-sm">

                                                <thead>

                                                    <tr>

                                                        <th style="width: 33%;"></th>

                                                        <th class="text-center" style="width: 33%;">Proficiency</th>

                                                        <th class="text-center" style="width: 33%;">Frequency</th>

                                                    </tr>

                                                </thead>

                                                <tbody>



                                                    @foreach ($metaData['skills'] as $skey => $skills)

                                                        <tr class="mt-2">

                                                            <th>

                                                                <p class="m-0">{{ $skills['skillTitle'] }}</p>

                                                            </th>

                                                            @foreach ($skills['options'] as $opKey => $options)

                                                                @php

                                                                    if (

                                                                        $opKey != 'proficiency' &&

                                                                        $opKey != 'frequency'

                                                                    ) {

                                                                        break;

                                                                    }

                                                                @endphp

                                                                <td class="text-center">

                                                                    @foreach ($options as $op)

                                                                        @php

                                                                            $anwserValue = '';

                                                                            $idSkillTitle = strtolower(

                                                                                trim(

                                                                                    str_replace(

                                                                                        ' ',

                                                                                        '-',

                                                                                        $skills['skillTitle'],

                                                                                    ),

                                                                                ),

                                                                            );

                                                                        @endphp



                                                                        @if (array_key_exists($metaData['sectionTitle'], $checklist_answer) &&

                                                                                array_key_exists($skills['skillTitle'], $checklist_answer[$metaData['sectionTitle']]))

                                                                            @if (array_key_exists($opKey, $checklist_answer[$metaData['sectionTitle']][$skills['skillTitle']]))

                                                                                @php

                                                                                    $anwserValue =

                                                                                        $checklist_answer[

                                                                                            $metaData['sectionTitle']

                                                                                        ][$skills['skillTitle']][

                                                                                            $opKey

                                                                                        ];

                                                                                @endphp

                                                                            @endif

                                                                        @endif





                                                                        <label class="mx-1">

                                                                            <input type="radio"

                                                                                name="skill-{{ $chKey }}-{{ $idSkillTitle }}-{{ $opKey }}"

                                                                                class="me-1"

                                                                                id="skill-{{ $chKey }}-{{ $idSkillTitle }}-{{ $opKey }}-{{ $op['title'] }}"

                                                                                value="{{ $op['value'] }}"

                                                                                @if ($anwserValue == $op['value']) checked 

																			@else

																				disabled @endif>

                                                                            {{ $op['title'] }}

                                                                        </label>

                                                                    @endforeach

                                                                </td>

                                                            @endforeach

                                                        </tr>

                                                    @endforeach

                                                </tbody>

                                            </table>



                                        @endforeach

                                    </div>

                                    <div class="page-break"></div>

                                @endforeach

                            </ul>



                        @else

                            <p>No Skill Checklist Currently Available</p>

                        @endif

                    </div>

                </div>





                <div class="col-sm-12 col-xs-12 col-md-12" style="margin-top: 5%">

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

                                            {{ !empty($referenceDetails->dates_of_employment)

                                                ? date('m/d/Y', strtotime($referenceDetails->dates_of_employment))

                                                : '-' }}

                                        </p>

                                    </div>

                                    <div class="col mb-2" style="width: 33%">

                                        <label class="mb-0">End Dates of Employment</label>

                                        <p class="m-0">

                                            {{ !empty($referenceDetails->end_date_of_employment)

                                                ? date('m/d/Y', strtotime($referenceDetails->end_date_of_employment))

                                                : '-' }}

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

                                                        @if(!empty($referenceDetails->worked_with))

                                                            {{ $referenceDetails->worked_with }} Years

                                                        @endif

                                                    </td>

                                                    <td class="text-center">

                                                        @if(empty($referenceDetails->worked_with))

                                                           &#10003;

                                                        @endif

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

                                    {{ !empty($referenceDetails->additional_comments) ? $referenceDetails->additional_comments : '-' }}

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

                                        <div style="font-family: 'Dancing Script', cursive; font-size: 19px;">

                                            {{ isset($referenceDetails->reference_by_signature) ? $referenceDetails->reference_by_signature : '-' }}

                                        </div>

                                    </div>



                                    <div class="col-sm-3 col-xs-3 col-md-3 mb-2" style="width: 25%">

                                        <label class="mb-0">Date</label>

                                        <p class="m-0">

                                            {{ !empty($referenceDetails->reference_by_signature_date)

                                                ? date('m/d/Y', strtotime($referenceDetails->reference_by_signature_date))

                                                : '-' }}

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

                                            {{ !empty($referenceDetails->reference_by_verified_by_date)

                                                ? date('m/d/Y', strtotime($referenceDetails->reference_by_verified_by_date))

                                                : '-' }}

                                        </p>

                                    </div>



                                </div>



                            </div>

                        </div>

                        <div class="page-break"></div>

                        <?php } ?>



                        <?php } else { ?>

                        <p>No References Currently Available</p>

                        <?php } ?>

                    </div>

                </div>



                <div class="col-sm-12 col-xs-12 col-md-12">

                    <div class="section-title">Documents</div>

                    <div class="text-left" style="margin-top:2%">

                        <?php if (!empty($documents)) { ?>

                        <ul>

                            <?php                         

                            foreach ($documents as $doc) { 

                                $fileUrl = "https://staging.travelnurse911.com/frontend/uploads/documents/" . $doc->file_name;

                                $fileExtension = pathinfo($doc->file_name, PATHINFO_EXTENSION);

                                $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']);

                                ?>

                            <li style="margin-left:10px;">

                                <span class="blue-bullet">&#8226;</span>

                                <a href="{{ $fileUrl }}" download="{{ $doc->file_name }}" target="_blank">

                                    <?php if ($isImage): ?>

                                    <i class="fas fa-2x fa-image"></i>&nbsp; <!-- Icon for image files -->

                                    <?php elseif (strtolower($fileExtension) === 'pdf'): ?>

                                    <i class="fa fa-2x fa-file-pdf"></i>&nbsp;&nbsp;&nbsp;

                                    <?php elseif (in_array(strtolower($fileExtension), ['doc', 'docx'])): ?>

                                    <i class="fas fa-2x fa-file"></i>&nbsp;&nbsp;&nbsp;

                                    <?php else: ?>

                                    <i class="fas fa-2x fa-file"></i>

                                    <?php endif; ?>

                                    <span>

                                        @php

                                            $fields = [];

                                            if (!empty($doc->title)) {

                                                $fields[] = $doc->title;

                                            }

                                            if (!empty($doc->doc_type_name)) {

                                                $fields[] = $doc->doc_type_name;

                                            }

                                            if (!empty($doc->expiry_date)) {

                                                $fields[] = date('m/d/Y', strtotime($doc->expiry_date));

                                            }

                                        @endphp

                                        @if (count($fields) > 0)

                                            {{ implode(' | ', $fields) }}

                                        @endif



                                    </span>

                                </a>

                            </li>

                            <?php 

                            } 

                            ?>

                        </ul>

                        <?php } else { ?>

                        <p>No Documents Currently Available</p>

                        <?php } ?>

                    </div>

                </div>



            </div>

        </div>

    </div>



    <!-- Bootstrap 4 and FontAwesome 5 -->

    <script src="{{ asset('public/assets/js/jquery.js') }}"></script>

    <script src="{{ asset('public/assets/js/popper.min.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>



</body>



</html>