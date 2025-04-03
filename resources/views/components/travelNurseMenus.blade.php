@php
$currentPage = $currentPage ?? 'default';
@endphp

<ul class="job-other-info d-sm-flex justify-content-center mt-4">
  <li class="{{ $currentPage === 'travelNurseBenefits' ? 'active' : '' }}">
    <a href="{{ route('travel-nurse-benefits') }}">Travel Nurse Benefits</a>
  </li>
  <li class="{{ $currentPage === 'professionalProfile' ? 'active' : '' }}">
    <a href="{{ route('professional-profile') }}">Professional Profile</a>
  </li>
  <li class="{{ $currentPage === 'documentSafe' ? 'active' : '' }}">
    <a href="{{ route('document-safe') }}">Document Safe</a>
  </li>
  <li class="{{ $currentPage === 'applicationStatusTracking' ? 'active' : '' }}">
    <a href="{{ route('application-status-tracking') }}">Application Status Tracking</a>
  </li>
  <li class="{{ $currentPage === 'messagingSMS' ? 'active' : '' }}">
    <a href="{{ route('messaging-sms') }}">Messaging & SMS</a>
  </li>
  <li class="{{ $currentPage === 'shortlistedJobs' ? 'active' : '' }}">
    <a href="{{ route('shortlisted-jobs') }}">Shortlisted Jobs</a>
  </li>
</ul>