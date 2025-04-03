<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@if(isset($title)) {{ $title }} @endif @if(!isset($home)) | {{ config('app.name') }} @endif</title>
  <link rel="shortcut icon" href="{{ asset('public/assets/images/fav.png') }}" />
  <link rel="stylesheet" href="{{ asset('public/assets/css/bootstrap.css') }}" />
  <link rel="stylesheet" href="{{ asset('public/assets/css/style.css') }}" />
  <style>
    body {
      font-family: 'Arial', sans-serif;
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
  </style>
</head>

<body>

  <!-- Header Section -->
  <div class="container my-4">
    <div class="shadow">


      <div class="row">
        <div class="col-sm-6 col-xs-6 col-md-6 ">

          <div class="profile-header d-flex justify-content-center align-items-center py-4">
            <h4 class="text-dark text-left">PROFILE TEMPLATE</h4>
          </div>
          <div class="section-title">Contact Information</div>
          <div class="contact-info w-75 m-auto">
            <ul>
              <li><span class="icon">&#9742;</span>{{ isset($userRecord)?$userRecord->phone:'' }}</li>
              <li><span class="icon">&#9993;</span>{{ isset($userRecord)?$userRecord->email:'' }}</li>
              <li><span class="icon">&#127968;</span>{{ isset($userRecord)?$userRecord->address_line1.', '.$userRecord->address_line2.', '.$userRecord->city_name.', '.$userRecord->state_name:''; }}</li>
              <li><span class="icon">&#128337;</span>Monday-Friday 9am-6pm EST</li>
            </ul>
          </div>

          <div class="section-title">Active Certifications</div>
          <div class="certifications-info w-75 m-auto">
            <ul>
              <li><span class="blue-bullet">&#8226;</span> BLS | Expires: 12/31/2024</li>
              <li><span class="blue-bullet">&#8226;</span> ACLS | Expires: 12/31/2024</li>
              <li><span class="blue-bullet">&#8226;</span> PALS | Expires: 12/31/2024</li>
              <li><span class="blue-bullet">&#8226;</span> TNCC | Expires: 12/31/2024</li>
            </ul>
          </div>

          <div class="section-title">Education</div>
          <div class="education-info w-75 m-auto">
            <?php
            if (!empty($educationalDetails)) {
            ?>
              <ul>
                <?php foreach ($educationalDetails as $edu) { ?>
                  <li><span class="blue-bullet">&#8226;</span> {{ $edu->school_college }} | {{ $edu->location }} | {{ $edu->degree }} | {{ $edu->currently_attending == 1?'Currently Attending':$edu->end_month.'/'.$edu->end_year}}</li>
                <?php } ?>
              </ul>
            <?php } else { ?>
              <p>No Educational Information Currently Available</p>
            <?php } ?>
          </div>

          <div class="section-title">Skill Checklists</div>
          <div class="education-info w-75 m-auto">
            <?php
            if (!empty($completed_checklists)) {
            ?>
              <ul>
                <?php foreach ($completed_checklists as $chklist) { ?>
                  <li><span class="blue-bullet">&#8226;</span> {{ $chklist->title }} | {{ date('m/d/Y',strtotime($chklist->submitted_on)) }}
                  </li>
                <?php } ?>
              </ul>
            <?php } else { ?>
              <p>No Skill Checklist Currently Available</p>
            <?php } ?>
          </div>
        </div>

        <!-- Active State License Section -->
        <div class="col-sm-6 col-xs-6 col-md-6 ">

          <div class=" profile-header text-left pt-5 pb-3 px-4">
            <h1 class="mb-2"><strong>{{ isset($userRecord)?$userRecord->name:'' }}</strong></h1>
            <h3><i>{{ isset($userRecord)?$userRecord->profession:'' }}</i></h3>
            <h6><i>{{ isset($userRecord)?$userRecord->specialty:'' }}</i></h6>
          </div>

          <div class="section-title">Active State License</div>
          <div class="license-info w-75 m-auto">
            <ul>
              <li><span class="blue-bullet">&#8226;</span> Registered Nurse <br> Georgia | 123456 | Expires: 12/31/2024</li>
              <li><span class="blue-bullet">&#8226;</span> Registered Nurse <br> New Mexico | 123456 | Expires: 12/31/2024</li>
              <li><span class="blue-bullet">&#8226;</span> Registered Nurse <br> California | 123456 | Expires: 12/31/2024</li>
            </ul>
          </div>

          <div class="section-title">Summary</div>
          <div class="summary-info w-75 m-auto">
            <ul>
              <li><span class="blue-bullet">&#8226;</span> Desired Shift: {{ (isset($desired_shifts) && !empty($desired_shifts))?implode(', ',$desired_shifts):'-'; }}</li>
              <li><span class="blue-bullet">&#8226;</span> Available Start Date: {{ (isset($userRecord) && !empty($userRecord->available_start_date))?$userRecord->available_start_date:'-' }}</li>
              <li><span class="blue-bullet">&#8226;</span> Years of Experience: {{ (isset($userRecord) && !empty($userRecord->total_experience))?$userRecord->total_experience.' year(s)':'-' }}</li>
              <li><span class="blue-bullet">&#8226;</span> Experience in Specialty: 5 years</li>
              <li><span class="blue-bullet">&#8226;</span> RTO: 12/25/2025-1/1/2026</li>
              <li><span class="blue-bullet">&#8226;</span> EMR Experience: EPIC, Cerner</li>
              <li><span class="blue-bullet">&#8226;</span> Teaching Hospital Experience: Yes</li>
              <li><span class="blue-bullet">&#8226;</span> Travel Experience: 7 years</li>
              <li><span class="blue-bullet">&#8226;</span> Fully Vaccinated: Yes</li>
            </ul>
          </div>


        </div>

        <div class="col-sm-12 col-xs-12 col-md-12">
          <div class="section-title">Expertise/Skills</div>
          <div class="text-left pt-3 pb-3 px-4">
            <ul>
              <li><span class="blue-bullet">&#8226;</span> Dedicated registered nurse with 8 years of experience in Med-Surg providing compassionate care to patients</li>
              <li><span class="blue-bullet">&#8226;</span> Proven ability to administer complex medications, manage patient care, collaborate with interdisciplinary teams while mantaining a high level of patient satisfaction and safety.</li>
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
                  <li><span class="blue-bullet">&#8226;</span> {{ $edu->profession }} | {{ $edu->company_name }} | {{ $edu->employment_type_title }} | {{ $edu->start_month.'/'.$edu->start_year }}-{{ $edu->currently_working == 1?'Currently Working':$edu->end_month.'/'.$edu->end_year}} | {{ $edu->city_name }} | {{ $edu->state_name }} | {{ $edu->specialty }}</li>
                <?php } ?>
              </ul>
            <?php } else { ?>
              <p>No Work History Currently Available</p>
            <?php } ?>
          </div>
        </div>

        <div class="col-sm-12 col-xs-12 col-md-12">
          <div class="section-title">Documents</div>
          <div class="text-left pt-3 pb-3 px-4">
            <?php
            if (!empty($documents)) {
            ?>
              <ul>
                <?php foreach ($documents as $doc) { ?>
                  <li><span class="blue-bullet">&#8226;</span> {{ $doc->title }} | {{ $doc->doc_type_name }} | {{ date('m/d/Y',strtotime($doc->expiry_date)) }}
                  </li>
                <?php } ?>
              </ul>
            <?php } else { ?>
              <p>No Documents Currently Available</p>
            <?php } ?>
          </div>
        </div>

        <div class="col-sm-12 col-xs-12 col-md-12">
          <div class="section-title">References</div>
          <div class="text-left pt-3 pb-3 px-4">
            <?php
            if (!empty($references)) {
            ?>

              <?php
              $n = 0;
              foreach ($references as $referenceDetails) {
                $n++; ?>
                <div class="border">
                  <div class="px-3 py-2 border-bottom bg-light">
                    Reference {{$n }}
                  </div>
                  <div class="p-3">
                    <h6 class="border-bottom pb-2 mb-2">Employment Information</h6>

                    <div class="row">
                      <div class="col-sm-4 col-xs-4 col-md-4 mb-2">
                        <label class="mb-0">Facility</label>
                        <p class="m-0">{{ (isset($referenceDetails->facility))?$referenceDetails->facility:'-' }}</p>
                      </div>

                      <div class="col-sm-4 col-xs-4 col-md-4 mb-2">
                        <label class="mb-0">Dates of Employment</label>
                        <p class="m-0">{{ (isset($referenceDetails->dates_of_employment))?$referenceDetails->dates_of_employment:'-' }}</p>
                      </div>

                      <div class="col-sm-4 col-xs-4 col-md-4 mb-2">
                        <label class="mb-0">Address</label>
                        <p class="m-0">{{ (isset($referenceDetails->address))?$referenceDetails->address:'-' }}</p>
                      </div>

                      <div class="col-sm-4 col-xs-4 col-md-4 mb-2">
                        <label class="mb-0">Title while Employed</label>
                        <p class="m-0">{{ (isset($referenceDetails->title_while_employed))?$referenceDetails->title_while_employed:'-' }}</p>
                      </div>

                      <div class="col-sm-4 col-xs-4 col-md-4 mb-2">
                        <label class="mb-0">Phone</label>
                        <p class="m-0">{{ (isset($referenceDetails->phone))?$referenceDetails->phone:'-' }}</p>
                      </div>

                      <div class="col-sm-4 col-xs-4 col-md-4 mb-2">
                        <label class="mb-0">Specialty Worked</label>
                        <p class="m-0">{{ (isset($referenceDetails->specialty_worked))?$referenceDetails->specialty_worked:'-' }}</p>
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
                                {!! ($referenceDetails->worked_with == 'Yes')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! ($referenceDetails->worked_with == 'No')?'&#10003;':'' !!}
                              </td>
                            </tr>
                            <tr>
                              <td>2. Is this person honest, reliable, and trustworthy?</td>
                              <td class="text-center">
                                {!! ($referenceDetails->honest_reliable == 'Yes')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! ($referenceDetails->honest_reliable == 'No')?'&#10003;':'' !!}
                              </td>
                            </tr>
                            <tr>
                              <td>3. Was this candidate on a travel assignment? </td>
                              <td class="text-center">
                                {!! ($referenceDetails->travel_assignment == 'Yes')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! ($referenceDetails->travel_assignment == 'No')?'&#10003;':'' !!}
                              </td>
                            </tr>
                            <tr>
                              <td>4. Is this candidate eligible for rehire?</td>
                              <td class="text-center">
                                {!! ($referenceDetails->eligible_rehire == 'Yes')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! ($referenceDetails->eligible_rehire == 'No')?'&#10003;':'' !!}
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
                                {!! (isset($referenceDetails) && $referenceDetails->quality_of_work == 'Poor')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->quality_of_work == 'Fair')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->quality_of_work == 'Average')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->quality_of_work == 'Good')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->quality_of_work == 'Excellent')?'&#10003;':'' !!}
                              </td>
                            </tr>

                            <tr>
                              <td>2. Clinical Knowledge/Skills</td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->clinical_knowledge == 'Poor')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->clinical_knowledge == 'Fair')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->clinical_knowledge == 'Average')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->clinical_knowledge == 'Good')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->clinical_knowledge == 'Excellent')?'&#10003;':'' !!}
                              </td>
                            </tr>

                            <tr>
                              <td>3. Attendance/Dependability</td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->attendance_dependability == 'Poor')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->attendance_dependability == 'Fair')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->attendance_dependability == 'Average')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->attendance_dependability == 'Good')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->attendance_dependability == 'Excellent')?'&#10003;':'' !!}
                              </td>
                            </tr>

                            <tr>
                              <td>4. Interpersonal/Communication Skills</td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->communication_skills == 'Poor')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->communication_skills == 'Fair')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->communication_skills == 'Average')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->communication_skills == 'Good')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->communication_skills == 'Excellent')?'&#10003;':'' !!}
                              </td>
                            </tr>

                            <tr>
                              <td>5. Relationship with Others</td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->relationship_others == 'Poor')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->relationship_others == 'Fair')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->relationship_others == 'Average')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->relationship_others == 'Good')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->relationship_others == 'Excellent')?'&#10003;':'' !!}
                              </td>
                            </tr>

                            <tr>
                              <td>6. Acceptance of Supervision</td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->acceptance_supervision == 'Poor')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->acceptance_supervision == 'Fair')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->acceptance_supervision == 'Average')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->acceptance_supervision == 'Good')?'&#10003;':'' !!}
                              </td>
                              <td class="text-center">
                                {!! (isset($referenceDetails) && $referenceDetails->acceptance_supervision == 'Excellent')?'&#10003;':'' !!}
                              </td>
                            </tr>


                          </tbody>
                        </table>
                      </div>


                    </div>

                    <h6 class="border-bottom pb-2 mb-2">Additional Comments</h6>
                    <p class="mb-3">{{ (isset($referenceDetails->additional_comments))?$referenceDetails->additional_comments:'-' }}</p>

                    <h6 class="border-bottom pb-2 mb-2">Reference Completed By</h6>

                    <div class="row">
                      <div class="col-sm-3 col-xs-3 col-md-3 mb-2">
                        <label class="mb-0">Name</label>
                        <p class="m-0">{{ (isset($referenceDetails->reference_by_name))?$referenceDetails->reference_by_name:'-' }}</p>
                      </div>

                      <div class="col-sm-3 col-xs-3 col-md-3 mb-2">
                        <label class="mb-0">Title</label>
                        <p class="m-0">{{ (isset($referenceDetails->reference_by_title))?$referenceDetails->reference_by_title:'-' }}</p>
                      </div>

                      <div class="col-sm-3 col-xs-3 col-md-3 mb-2">
                        <label class="mb-0">Phone</label>
                        <p class="m-0">{{ (isset($referenceDetails->reference_by_phone))?$referenceDetails->reference_by_phone:'-' }}</p>
                      </div>

                      <div class="col-sm-3 col-xs-3 col-md-3 mb-2">
                        <label class="mb-0">Email</label>
                        <p class="m-0">{{ (isset($referenceDetails->reference_by_email))?$referenceDetails->reference_by_email:'-' }}</p>
                      </div>

                      <div class="col-sm-3 col-xs-3 col-md-3 mb-2">
                        <label class="mb-0">Signature</label>
                        <p class="m-0">{{ (isset($referenceDetails->reference_by_signature))?$referenceDetails->reference_by_signature:'-' }}</p>
                      </div>

                      <div class="col-sm-3 col-xs-3 col-md-3 mb-2">
                        <label class="mb-0">Date</label>
                        <p class="m-0">{{ (isset($referenceDetails->reference_by_signature_date))?$referenceDetails->reference_by_signature_date:'-' }}</p>
                      </div>

                      <div class="col-sm-3 col-xs-3 col-md-3 mb-2">
                        <label class="mb-0">Reference Information Verified By</label>
                        <p class="m-0">{{ (isset($referenceDetails->reference_by_verified_by))?$referenceDetails->reference_by_verified_by:'-' }}</p>
                      </div>

                      <div class="col-sm-3 col-xs-3 col-md-3 mb-2">
                        <label class="mb-0">Date</label>
                        <p class="m-0">{{ (isset($referenceDetails->reference_by_verified_by_date))?$referenceDetails->reference_by_verified_by_date:'-' }}</p>
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

      </div>

    </div>
  </div>

  <div class="no-print text-center py-3">
    <button class="btn btn-primary " onclick="window.print()">Print</button>
  </div>

  <!-- Bootstrap 4 and FontAwesome 5 -->
  <script src="{{ asset('public/assets/js/jquery.js') }}"></script>
  <script src="{{ asset('public/assets/js/popper.min.js') }}"></script>
  <script src="{{ asset('public/assets/js/chosen.min.js') }}"></script>
  <script src="{{ asset('public/assets/js/bootstrap.min.js') }}"></script>
</body>

</html>