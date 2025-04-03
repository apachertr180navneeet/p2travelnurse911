@php
$currentPage = $currentPage ?? 'default';
@endphp


<ul class="job-other-info d-sm-flex justify-content-center mt-4">
  <li class="{{ $currentPage === 'jobPosting' ? 'active' : '' }}">
    <a href="{{ route('facility-job-posting') }}">Free Job Postings</a>
  </li>
  <li class="{{ $currentPage === 'applicantTrackingSystem' ? 'active' : '' }}">
    <a href="{{ route('applicant-tracking-system') }}">Applicant Tracking System</a>
  </li>
  <li class="{{ $currentPage === 'travelNurseManagement' ? 'active' : '' }}">
    <a href="{{ route('facility-travel-nurse-management') }}">Travel Nurse Management</a>
  </li>
  <li class="{{ $currentPage === 'complianceFileMangement' ? 'active' : '' }}">
    <a href="{{ route('facility-compliance-files') }}">Compliance File Management</a>
  </li>
  <li class="{{ $currentPage === 'followUpScheduling' ? 'active' : '' }}">
    <a href="{{ route('facility-follow-up-scheduling') }}">Follow Up Scheduling</a>
  </li>
  <li class="{{ $currentPage === 'taskManagement' ? 'active' : '' }}">
    <a href="{{ route('facility-task-management') }}">Task Management</a>
  </li>
</ul>