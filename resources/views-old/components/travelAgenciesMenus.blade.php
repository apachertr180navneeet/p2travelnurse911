@php
$currentPage = $currentPage ?? 'default';
@endphp

<ul class="job-other-info d-sm-flex justify-content-center mt-4">
  <li class="{{ $currentPage === 'jobPosting' ? 'active' : '' }}">
    <a href="{{ route('agency-job-posting') }}">Job Postings</a>
  </li>
  <li class="{{ $currentPage === 'applicantTrackingSystem' ? 'active' : '' }}">
    <a href="{{ route('agency-applicant-tracking-system') }}">Applicant Tracking</a>
  </li>
  <li class="{{ $currentPage === 'submissionFiles' ? 'active' : '' }}">
    <a href="{{ route('agency-submission-files') }}">Submission Files</a>
  </li>
  <li class="{{ $currentPage === 'travelNurseManagement' ? 'active' : '' }}">
    <a href="{{ route('agency-travel-nurse-management') }}">Travel Nurse Management</a>
  </li>
  <li class="{{ $currentPage === 'complianceFiles' ? 'active' : '' }}">
    <a href="{{ route('agency-compliance-files') }}">Compliance Files</a>
  </li>
  <li class="{{ $currentPage === 'followUpScheduling' ? 'active' : '' }}">
    <a href="{{ route('agency-follow-up-scheduling') }}">Follow Up Scheduling</a>
  </li>
  <li class="{{ $currentPage === 'taskManagement' ? 'active' : '' }}">
    <a href="{{ route('agency-task-management') }}">Task Management</a>
  </li>
</ul>