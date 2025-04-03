<?php

## User Controllers

use Illuminate\Support\Facades\Route;



use App\Http\Controllers\API\CommonController;



use App\Http\Controllers\API\User\AuthController;

use App\Http\Controllers\API\User\DashboardController;

use App\Http\Controllers\API\User\CalendarController;

use App\Http\Controllers\API\User\ProfileController;

use App\Http\Controllers\API\User\DocumentController;

use App\Http\Controllers\API\User\JobController;

use App\Http\Controllers\API\User\ChecklistController;

use App\Http\Controllers\API\User\SettingsController;

use App\Http\Controllers\API\User\SubmissionFileController;

use App\Http\Controllers\API\User\JobRequestController;



## Clients Controllers

use App\Http\Controllers\API\Client\DashboardController as ClientDashboardController;

use App\Http\Controllers\API\Client\AuthController as ClientAuthController;

use App\Http\Controllers\API\Client\ProfileController as ClientProfileController;

use App\Http\Controllers\API\Client\JobController as ClientJobController;

use App\Http\Controllers\API\Client\DocumentController as ClientDocumentController;

use App\Http\Controllers\API\Client\ComplianceFileController as ClientComplianceFileController;

use App\Http\Controllers\API\Client\UserController as ClientUserController;

use App\Http\Controllers\API\Client\FacilityController as ClientFacilityController;

use App\Http\Controllers\API\Client\SubmissionController as ClientSubmissionController;

use App\Http\Controllers\API\Client\RedirectController as ClientRedirectController;

use App\Http\Controllers\API\Client\MessageController as ClientMessageController;

use App\Http\Controllers\API\Client\SchedulingController as ClientSchedulingController;

use App\Http\Controllers\API\Client\TaskController as ClientTaskController;

use App\Http\Controllers\API\Client\CalendarController as ClientCalendarController;

use App\Http\Controllers\API\Client\JobRequestController as ClientJobRequestController;

use App\Http\Controllers\API\Client\AgencyController as ClientAgencyController;





## Admin Controllers

use App\Http\Controllers\API\Admin\AuthController as AdminAuthController;

use App\Http\Controllers\API\Admin\DashboardController as AdminDashboardController;

use App\Http\Controllers\API\Admin\JobController as AdminJobController;

use App\Http\Controllers\API\Admin\UserController as AdminUserController;

use App\Http\Controllers\API\Admin\ComplianceFileController as AdminComplianceFileController;

use App\Http\Controllers\API\Admin\ChecklistController as AdminChecklistController;

use App\Http\Controllers\API\Admin\DocumentController as AdminDocumentController;

use App\Http\Controllers\API\Admin\ProfessionController as AdminProfessionController;

use App\Http\Controllers\API\Admin\SkillController as AdminSkillController;

use App\Http\Controllers\API\Admin\SubAdminController as AdminSubAdminController;

use App\Http\Controllers\API\Admin\RecruitmentComplianceTeamController as AdminRecruitmentComplianceTeamController;

use App\Http\Controllers\API\Admin\FacilityController as AdminFacilityController;

use App\Http\Controllers\API\Admin\SubmissionController as AdminSubmissionController;

use App\Http\Controllers\API\Admin\RedirectController as AdminRedirectController;

use App\Http\Controllers\API\Admin\UserActivityController;

use App\Http\Controllers\API\Admin\UserRoleController;

use App\Http\Controllers\API\Admin\MessageController;

use App\Http\Controllers\API\Admin\AnnouncementController;

use App\Http\Controllers\API\Admin\SchedulingController as AdminSchedulingController;

use App\Http\Controllers\API\Admin\TaskController as AdminTaskController;

use App\Http\Controllers\API\Admin\CalendarController as AdminCalendarController;

use App\Http\Controllers\API\Admin\BlogController as AdminBlogController;

use App\Http\Controllers\API\Admin\JobRequestController as AdminJobRequestController;

use App\Http\Controllers\API\Admin\AgencyController as AdminAgencyController;





/*

|--------------------------------------------------------------------------

| API Routes

|--------------------------------------------------------------------------

|

| Here is where you can register API routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which

| is assigned the "api" middleware group. Enjoy building your API!

|

*/



/*

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {

    return $request->user();

});

*/



## ==== User Group ==== ##

Route::group(['prefix' => 'user'], function () {

    ## Auth Routes

    Route::post('login', [AuthController::class, 'login']);

    Route::post('register', [AuthController::class, 'register']);

    Route::post('resend-verify-email', [AuthController::class, 'resendVerifyEmail']);

    Route::post('verify-email', [AuthController::class, 'verifyEmail']);

    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);

    Route::post('reset-password', [AuthController::class, 'resetPassword']);

});



Route::middleware(['api.auth'])->prefix('user')->group(function () {



    Route::post('change-password', [AuthController::class, 'changePassword']);

    Route::post('logout', [AuthController::class, 'logout']);



    ## Dashhboard Routes

    Route::post('get-dashboard-data', [DashboardController::class, 'getDashboardData']);

    Route::post('get-resumes', [DashboardController::class, 'getResumes']);

    Route::post('upload-resume', [DashboardController::class, 'uploadResume']);

    Route::post('get-sidebar-data', [DashboardController::class, 'getSidebarData']);



    Route::post('get-submission-file-data', [SubmissionFileController::class, 'getSubmissionFileData']);



    ## Calendar Routes

    Route::post('get-calendar-data', [CalendarController::class, 'getCalendarData']);



    ## Profile Routes

    Route::post('get-profile-data', [ProfileController::class, 'getProfileData']);

    Route::post('update-profile', [ProfileController::class, 'updateProfile']);

    Route::post('get-profile', [ProfileController::class, 'getUserProfile']);

    Route::post('update-work-history', [ProfileController::class, 'updateWorkHistory']);

    Route::post('get-work-history', [ProfileController::class, 'getWorkHistory']);

    Route::post('delete-work-history', [ProfileController::class, 'deleteWorkHistory']);

    Route::post('update-educational-info', [ProfileController::class, 'updateEducationalInfo']);

    Route::post('get-educational-info', [ProfileController::class, 'getEducationalInfo']);

    Route::post('delete-educational-info', [ProfileController::class, 'deleteEducationalInfo']);

    Route::post('update-skills', [ProfileController::class, 'updateSkills']);

    Route::post('get-skills', [ProfileController::class, 'getUserSkills']);

    Route::post('delete-skill', [ProfileController::class, 'deleteUserSkills']);

    Route::post('update-job-preference', [ProfileController::class, 'updateJobPreference']);

    Route::post('get-job-preference', [ProfileController::class, 'getJobPreference']);

    Route::post('update-user-certificates-licences', [ProfileController::class, 'updateCertificatesLicense']);

    Route::post('store-submission-document', [ProfileController::class, 'storeSubmissionFileDocs']);



    Route::post('update-references', [ProfileController::class, 'updateReferences']);

    Route::post('get-references', [ProfileController::class, 'getReferences']);

    Route::post('send-reference-request', [ProfileController::class, 'sendReferenceRequest']);



    ## Setting Routes

    Route::post('get-settings', [SettingsController::class, 'getSettings']);

    Route::post('update-settings', [SettingsController::class, 'updateSettings']);



    ## Job Routes

    Route::post('get-jobs', [JobController::class, 'getJobs']);

    Route::post('get-job-detail', [JobController::class, 'getJobDetail']);

    Route::post('get-bookmarked-jobs', [JobController::class, 'getBookmarkedJobs']);

    Route::post('update-job-bookmark', [JobController::class, 'updateJobBookmark']);

    Route::post('apply-job', [JobController::class, 'applyJob']);

    Route::post('get-applied-jobs', [JobController::class, 'getAppliedJobs']);

    Route::post('insert-job-message', [JobController::class, 'insertJobMessage']);

    Route::post('get-unique-job-messages', [JobController::class, 'getUniqueJobMessages']);

    Route::post('get-job-messages', [JobController::class, 'getJobMessages']);

    Route::post('get-company-details', [JobController::class, 'getCompanyDetails']);





    ## Checklist Routes

    Route::post('get-assigned-checklists', [ChecklistController::class, 'getAssignedChecklists']);

    Route::post('get-checklist-detail', [ChecklistController::class, 'getChecklistDetail']);

    Route::post('update-checklist-detail', [ChecklistController::class, 'updateChecklistDetail']);

    Route::post('get-user-checklist-detail', [ChecklistController::class, 'getUserChecklistDetail']);

    Route::post('get-compliances', [ChecklistController::class, 'getCompliances']);

    Route::post('user-completed-checklist',[ChecklistController::class, 'getCompletedChecklist']);





    ##Document Routes

    Route::post('update-document', [DocumentController::class, 'updateDocument']);

    Route::post('get-documents', [DocumentController::class, 'getDocument']);

    Route::post('delete-document', [DocumentController::class, 'deleteDocument']);

    Route::post('get-document-share-history', [DocumentController::class, 'getDocumentShareHistory']);

    Route::post('share-document', [DocumentController::class, 'shareDocument']);

    Route::post('get-document-access-requests', [DocumentController::class, 'getDocumentAccessRequests']);

    Route::post('respond-document-access-request', [DocumentController::class, 'respondDocumentAccessRequest']);



    ## Job Request Routes

    Route::post('job-requests/store', [JobRequestController::class, 'store']);

    Route::post('job-requests/store-state-city', [JobRequestController::class, 'storeStateCity']);

    Route::post('job-requests/get-active-request', [JobRequestController::class, 'jobRequestList']);

    Route::post('job-requests/get-received-job-opportunities', [JobRequestController::class, 'getReceivedJobOpportunites']);

    Route::post('job-requests/get-job-opportunity-details', [JobRequestController::class, 'getJobOpportinityDetails']);

    Route::post('job-requests/delete-job-opportunity', [JobRequestController::class, 'deleteJobOpportunity']);

    Route::post('job-requests/respond-job-opportunity', [JobRequestController::class, 'updateJobOpportunityResponse']);

});





## ==== Client Group ==== ##

Route::group(['prefix' => 'client'], function () {

    Route::post('login', [ClientAuthController::class, 'login']);

    Route::post('register', [ClientAuthController::class, 'register']);

    Route::post('resend-verify-email', [ClientAuthController::class, 'resendVerifyEmail']);

    Route::post('verify-email', [ClientAuthController::class, 'verifyEmail']);

    Route::post('forgot-password', [ClientAuthController::class, 'forgotPassword']);

    Route::post('reset-password', [ClientAuthController::class, 'resetPassword']);

});



Route::middleware(['api.auth'])->prefix('client')->group(function () {



    Route::post('get-dashboard-data', [ClientDashboardController::class, 'getDashboardData']);

    Route::post('get-sidebar-data', [ClientDashboardController::class, 'getSidebarData']);



    Route::post('change-password', [ClientAuthController::class, 'changePassword']);

    Route::post('logout', [ClientAuthController::class, 'logout']);



    ## Profile Routes

    Route::post('update-profile', [ClientProfileController::class, 'updateProfile']);

    Route::post('get-profile', [ClientProfileController::class, 'getProfile']);



    ## Job Routes

    Route::post('get-jobs', [ClientJobController::class, 'getJobs']);

    Route::post('get-boosted-jobs', [ClientJobController::class, 'getBoostedJobs']);

    Route::post('delete-job', [ClientJobController::class, 'deleteJob']);

    Route::post('update-job-status', [ClientJobController::class, 'updateJobStatus']);

    Route::post('post-job', [ClientJobController::class, 'postJob']);

    Route::post('get-draft-job', [ClientJobController::class, 'getDraftJob']);

    Route::post('get-job-attatchments', [ClientJobController::class, 'getJobAttatchments']);

    Route::post('upload-job-attachments', [ClientJobController::class, 'uploadJobAttachments']);

    Route::post('get-job-applications', [ClientJobController::class, 'getJobApplications']);

    Route::post('get-rejected-job-applications', [ClientJobController::class, 'getRejectedJobApplications']);

    Route::post('get-user-job-applications', [ClientJobController::class, 'getUserJobApplications']);

    Route::post('update-user-job-application-status', [ClientJobController::class, 'updateUserJobApplicationStatus']);

    Route::post('update-user-job-interview', [ClientJobController::class, 'updateUserJobInterview']);

    Route::post('get-job-interviews', [ClientJobController::class, 'getJobInterviews']);

    Route::post('get-job-employees', [ClientJobController::class, 'getJobEmployees']);

    Route::post('delete-job-interview', [ClientJobController::class, 'deleteJobInterview']);

    Route::post('delete-job-application', [ClientJobController::class, 'deleteJobApplication']);

    Route::post('toggle-starred-job', [ClientJobController::class, 'toggleStarredJob']);

    Route::post('update-boosted-job', [ClientJobController::class, 'updateBoostedJob']);

    Route::post('restore-job-application', [ClientJobController::class, 'restoreJobApplication']);

    Route::post('reject-job-application', [ClientJobController::class, 'rejectJobApplication']);

    Route::post('job-bulk-actions', [ClientJobController::class, 'jobBulkActions']);

    Route::post('job-application-bulk-actions', [ClientJobController::class, 'jobApplicationBulkActions']);

    Route::post('job-application-bulk-actions-with-added', [ClientJobController::class, 'jobApplicationBulkActionsWithAdded']);



    Route::post('insert-job-message', [ClientJobController::class, 'insertJobMessage']);

    Route::post('get-unique-job-messages', [ClientJobController::class, 'getUniqueJobMessages']);

    Route::post('get-job-messages', [ClientJobController::class, 'getJobMessages']);

    Route::post('clone-job', [ClientJobController::class, 'cloneJob']);



    ##Document Routes

    Route::post('get-documents', [ClientDocumentController::class, 'getDocument']);

    Route::post('get-document-share-history', [ClientDocumentController::class, 'getDocumentShareHistory']);

    Route::post('get-document-types', [ClientDocumentController::class, 'getDocumentTypes']);

    Route::post('update-document-types', [ClientDocumentController::class, 'updateDocumentTypes']);

    Route::post('delete-document-type', [ClientDocumentController::class, 'deleteDocumentType']);

    Route::post('get-user-documents', [ClientDocumentController::class, 'getUserDocuments']);

    Route::post('get-user-shared-documents', [ClientDocumentController::class, 'getUserSharedDocuments']);

    ##Compliance File Routes



    Route::post('get-compliance-files', [ClientComplianceFileController::class, 'getComplianceFiles']);

    Route::post('update-compliance-file', [ClientComplianceFileController::class, 'updateComplianceFile']);

    Route::post('delete-compliance-file', [ClientComplianceFileController::class, 'deleteComplianceFile']);

    Route::post('compliance-files-bulk-actions', [ClientComplianceFileController::class, 'complianceFileBulkActions']);

    Route::post('update-compliance-file-status', [ClientComplianceFileController::class, 'updateComplianceFileStatus']);





    Route::post('get-facility-compliances', [ClientComplianceFileController::class, 'getFacilityCompliances']);

    Route::post('update-facility-compliance', [ClientComplianceFileController::class, 'updateFacilityCompliance']);

    Route::post('delete-facility-compliance', [ClientComplianceFileController::class, 'deleteFacilityCompliance']);

    Route::post('get-assigned-facility-compliance', [ClientComplianceFileController::class, 'getAssignedFacilityCompliance']);

    Route::post('update-assigned-facility-compliance', [ClientComplianceFileController::class, 'updateAssignedFacilityCompliance']);

    Route::post('delete-assigned-facility-compliance', [ClientComplianceFileController::class, 'deleteAssignedFacilityCompliance']);

    Route::post('update-facility-compliance-status', [ClientComplianceFileController::class, 'updateFacilityCompliancesStatus']);

    Route::post('clone-facility-compliance', [ClientComplianceFileController::class, 'cloneFacilityCompliance']);



    Route::post('get-compliance-file-types', [ClientComplianceFileController::class, 'getComplianceFileTypes']);

    Route::post('update-compliance-file-types', [ClientComplianceFileController::class, 'updateComplianceFileType']);

    Route::post('delete-compliance-file-type', [ClientComplianceFileController::class, 'deleteComplianceFileType']);



    Route::post('get-compliance-file-categories', [ClientComplianceFileController::class, 'getComplianceFileCategories']);

    Route::post('update-compliance-file-category', [ClientComplianceFileController::class, 'updateComplianceFileCategory']);

    Route::post('delete-compliance-file-category', [ClientComplianceFileController::class, 'deleteComplianceFileCategory']);



    Route::post('get-compliance-checklists', [ClientComplianceFileController::class, 'getComplianceChecklists']);

    Route::post('get-compliance-submitted-checklists', [ClientComplianceFileController::class, 'getComplianceSubmittedChecklists']);

    Route::post('get-user-submitted-checklists', [ClientComplianceFileController::class, 'getUserSubmittedChecklists']);

    Route::post('update-compliance-checklist', [ClientComplianceFileController::class, 'updateComplianceChecklist']);

    Route::post('get-compliance-assigned-checklist', [ClientComplianceFileController::class, 'getComplianceAssignedChecklists']);

    Route::post('update-compliance-checklist-assigned-users', [ClientComplianceFileController::class, 'updateComplianceChecklistAssignedUsers']);

    Route::post('delete-compliance-assigned-checklist', [ClientComplianceFileController::class, 'deleteComplianceAssignedChecklist']);

    Route::post('update-compliance-checklist-status', [ClientComplianceFileController::class, 'updateComplianceChecklistStatus']);

    Route::post('clone-compliance-checklist', [ClientComplianceFileController::class, 'cloneComplianceChecklist']);

    Route::post('get-compliance-checklist-details', [ClientComplianceFileController::class, 'getComplianceChecklistDetails']);

    Route::post('delete-compliance-checklist', [ClientComplianceFileController::class, 'deletedComplianceChecklist']);

    Route::post('get-checklist-assigned-users', [ClientComplianceFileController::class, 'getChecklistAssignedUsers']);



    ## Client Routes

    Route::post('get-client-types', [ClientFacilityController::class, 'getClientTypes']);

    Route::post('get-clients', [ClientFacilityController::class, 'getClients']);

    Route::post('update-client', [ClientFacilityController::class, 'updateClient']);

    Route::post('delete-client', [ClientFacilityController::class, 'deleteClient']);

    Route::post('update-client-status', [ClientFacilityController::class, 'updateClientStatus']);



    ## Assignment Routes

    Route::post('get-assignments', [ClientFacilityController::class, 'getAssignments']);

    Route::post('update-assignment', [ClientFacilityController::class, 'updateAssignment']);

    Route::post('delete-assignment', [ClientFacilityController::class, 'deleteAssignment']);

    Route::post('update-assignment-status', [ClientFacilityController::class, 'updateAssignmentStatus']);



    ##Submission Routes

    Route::post('get-submissions', [ClientSubmissionController::class, 'getSubmissions']);

    Route::post('update-submission', [ClientSubmissionController::class, 'updateSubmission']);

    Route::post('delete-submission', [ClientSubmissionController::class, 'deleteSubmission']);

    Route::post('get-specialities', [ClientSubmissionController::class, 'getSpecialities']);

    Route::post('update-submission-field', [ClientSubmissionController::class, 'updateSubmissionField']);



    ## Redirects Routes

    Route::post('get-redirects', [ClientRedirectController::class, 'getRedirects']);

    Route::post('update-redirect', [ClientRedirectController::class, 'updateRedirect']);

    Route::post('delete-redirect', [ClientRedirectController::class, 'deleteRedirect']);

    Route::post('update-redirect-field', [ClientRedirectController::class, 'updateRedirectField']);



    ## User Routes

    Route::post('get-users', [ClientUserController::class, 'getUsers']);

    Route::post('get-public-job-applications', [ClientUserController::class, 'getPublicJobApplications']);

    Route::post('get-all-users', [ClientUserController::class, 'getAllUsers']);

    Route::post('update-user', [ClientUserController::class, 'updateUser']);

    Route::post('add-candidate', [ClientUserController::class, 'addCandidate']);

    Route::post('get-user-details', [ClientUserController::class, 'getUserDetails']);

    Route::post('update-user-status', [ClientUserController::class, 'updateUserStatus']);

    Route::post('delete-user', [ClientUserController::class, 'deleteUser']);

    Route::post('user-bulk-actions', [ClientUserController::class, 'userBulkActions']);

    Route::post('update-user-field', [ClientUserController::class, 'updateUserField']);

    Route::post('get-user-applications', [ClientUserController::class, 'getUserJobApplications']);

    Route::post('get-user-connections', [ClientUserController::class, 'getUserConnections']);

    Route::post('get-user-references', [ClientUserController::class, 'getUserReferences']);

    Route::post('get-user-compliance-files', [ClientUserController::class, 'getUserComplianceFiles']);

    Route::post('get-all-users-list', [ClientUserController::class, 'getAllUserList']);

    Route::post('send-document-access-request', [ClientUserController::class, 'sendDocumentAccessRequest']);

    Route::post('get-user-job-preferences', [ClientUserController::class, 'getUserJobPreferences']);





    Route::post('get-interviwers', [ClientUserController::class, 'getInterviwers']);



    Route::post('update-candidate-job-preference', [ClientUserController::class, 'updateJobPreference']);

    Route::post('get-candidate-job-preference', [ClientUserController::class, 'getJobPreference']);

    

    Route::post('get-candidate-submission-files', [ClientUserController::class, 'getCandidateSubmissionFiles']);



    ## Message Routes

    Route::post('send-message', [ClientMessageController::class, 'sendMessage']);

    Route::post('get-messages', [ClientMessageController::class, 'getMessages']);



    ## Scheduling Routes

    Route::post('get-schedules', [ClientSchedulingController::class, 'getSchedules']);

    Route::post('update-schedule', [ClientSchedulingController::class, 'updateSchedule']);

    Route::post('update-schedule-status', [ClientSchedulingController::class, 'updateScheduleStatus']);

    Route::post('delete-schedule', [ClientSchedulingController::class, 'deleteSchedule']);

    Route::post('upload-schedule-attatchment', [ClientSchedulingController::class, 'uploadScheduleAttatchment']);

    Route::post('get-schedule-attatchments', [ClientSchedulingController::class, 'getScheduleAttatchments']);



    ## Task Routes

    Route::post('get-tasks', [ClientTaskController::class, 'getTasks']);

    Route::post('update-task', [ClientTaskController::class, 'updateTask']);

    Route::post('update-task-status', [ClientTaskController::class, 'updateTaskStatus']);

    Route::post('delete-task', [ClientTaskController::class, 'deleteTask']);

    Route::post('upload-task-attatchment', [ClientTaskController::class, 'uploadTaskAttatchment']);

    Route::post('get-task-attatchments', [ClientTaskController::class, 'getTaskAttatchments']);

    Route::post('task-bulk-actions', [ClientTaskController::class, 'taskBulkActions']);



    ## Calendar/Events Routes

    Route::post('get-events', [ClientCalendarController::class, 'getEvents']);

    Route::post('update-event', [ClientCalendarController::class, 'updateEvent']);

    Route::post('update-event-status', [ClientCalendarController::class, 'updateEventStatus']);

    Route::post('delete-event', [ClientCalendarController::class, 'deleteEvent']);



    ## Message Routes

    Route::post('send-message', [ClientMessageController::class, 'sendMessage']);

    Route::post('get-messages', [ClientMessageController::class, 'getMessages']);



    ## Job Request Routes

    Route::post('job-requests/get-matched-job-request', [ClientJobRequestController::class, 'getMatchedJobRequestList']);

    Route::post('job-requests/get-all-job-request', [ClientJobRequestController::class,'getAllJobRequest']);

    Route::post('job-requests/get-job-request-details', [ClientJobRequestController::class,'getJobRequestDetails']);

    Route::post('job-requests/create-job-opportunity', [ClientJobRequestController::class, 'createJobOpportunity']);

    Route::post('job-requests/delete-job-opportunity', [ClientJobRequestController::class, 'deleteJobOpportunity']);

    Route::post('job-requests/send-job-opportunity', [ClientJobRequestController::class,'sendJobOpportunity']);

    Route::post('job-requests/job-opportunity-list', [ClientJobRequestController::class,'jobOpportunityList']);

    Route::post('job-requests/hide-job-request', [ClientJobRequestController::class, 'hideJobRequest']);

    Route::post('job-requests/send-job-request-message', [ClientJobRequestController::class, 'sendJobRequestMsg']);

    



    ## Agency

    Route::post('get-agencyfeedbacks', [ClientAgencyController::class, 'getAgencyFeedbacks']);

    Route::post('delete-feedback-request', [ClientAgencyController::class, 'deleteFeedbackRequest']);

    Route::post('get-declined-feedback-request', [ClientAgencyController::class, 'getDeclinedFeedbackRequest']);

});



## ==== Admin Group ==== ##

Route::group(['prefix' => 'admin'], function () {

    ## Auth Routes

    Route::post('login', [AdminAuthController::class, 'login']);

    Route::post('register', [AdminAuthController::class, 'register']);

    Route::post('forgot-password', [AdminAuthController::class, 'forgotPassword']);

    Route::post('reset-password', [AdminAuthController::class, 'resetPassword']);

});



Route::middleware(['api.auth'])->prefix('admin')->group(function () {



    Route::post('change-password', [AdminAuthController::class, 'changePassword']);

    Route::post('logout', [AdminAuthController::class, 'logout']);

    Route::post('get-user', [AdminAuthController::class, 'GetUser']);

    Route::post('verify-user', [AdminAuthController::class, 'VerifyUser']);



    ## Dashboard Routes

    Route::post('get-dashboard-data', [AdminDashboardController::class, 'getDashboardData']);



    ## Job Routes

    Route::post('get-job-detail', [AdminJobController::class, 'getJobDetail']);

    Route::post('get-jobs', [AdminJobController::class, 'getJobs']);

    Route::post('get-boosted-jobs', [AdminJobController::class, 'getBoostedJobs']);

    Route::post('update-job-status', [AdminJobController::class, 'updateJobStatus']);

    Route::post('delete-job', [AdminJobController::class, 'deleteJob']);

    Route::post('post-job', [AdminJobController::class, 'postJob']);

    Route::post('get-draft-job', [AdminJobController::class, 'getDraftJob']);

    Route::post('get-job-attatchments', [AdminJobController::class, 'getJobAttatchments']);

    Route::post('upload-job-attachments', [AdminJobController::class, 'uploadJobAttachments']);

    Route::post('get-job-applications', [AdminJobController::class, 'getJobApplications']);

    Route::post('get-rejected-job-applications', [AdminJobController::class, 'getRejectedJobApplications']);

    Route::post('get-user-job-applications', [AdminJobController::class, 'getUserJobApplications']);

    Route::post('update-user-job-application-status', [AdminJobController::class, 'updateUserJobApplicationStatus']);

    Route::post('update-user-job-interview', [AdminJobController::class, 'updateUserJobInterview']);

    Route::post('get-job-interviews', [AdminJobController::class, 'getJobInterviews']);

    Route::post('get-job-employees', [AdminJobController::class, 'getJobEmployees']);

    Route::post('delete-job-interview', [AdminJobController::class, 'deleteJobInterview']);

    Route::post('delete-job-application', [AdminJobController::class, 'deleteJobApplication']);

    Route::post('toggle-starred-job', [AdminJobController::class, 'toggleStarredJob']);

    Route::post('restore-job-application', [AdminJobController::class, 'restoreJobApplication']);

    Route::post('reject-job-application', [AdminJobController::class, 'rejectJobApplication']);

    Route::post('job-bulk-actions', [AdminJobController::class, 'jobBulkActions']);

    Route::post('job-application-bulk-actions', [AdminJobController::class, 'jobApplicationBulkActions']);

    Route::post('clone-job', [AdminJobController::class, 'cloneJob']);





    ##Compliance File Routes

    Route::post('get-compliance-files', [AdminComplianceFileController::class, 'getComplianceFiles']);

    Route::post('update-compliance-file', [AdminComplianceFileController::class, 'updateComplianceFile']);

    Route::post('delete-compliance-file', [AdminComplianceFileController::class, 'deleteComplianceFile']);

    Route::post('compliance-files-bulk-actions', [AdminComplianceFileController::class, 'complianceFileBulkActions']);

    Route::post('update-compliance-file-status', [AdminComplianceFileController::class, 'updateComplianceFileStatus']);



    Route::post('get-compliance-file-types', [AdminComplianceFileController::class, 'getComplianceFileTypes']);

    Route::post('update-compliance-file-types', [AdminComplianceFileController::class, 'updateComplianceFileType']);

    Route::post('delete-compliance-file-type', [AdminComplianceFileController::class, 'deleteComplianceFileType']);

    Route::post('update-compliance-file-type-status', [AdminComplianceFileController::class, 'updateComplianceFileTypeStatus']);

    Route::post('compliance-files-type-bulk-actions', [AdminComplianceFileController::class, 'ComplianceFileTypeBulkActions']);



    Route::post('get-compliance-file-categories', [AdminComplianceFileController::class, 'getComplianceFileCategories']);

    Route::post('update-compliance-file-category', [AdminComplianceFileController::class, 'updateComplianceFileCategory']);

    Route::post('delete-compliance-file-category', [AdminComplianceFileController::class, 'deleteComplianceFileCategory']);

    Route::post('update-compliance-file-category-status', [AdminComplianceFileController::class, 'updateComplianceFileCategoryStatus']);

    Route::post('compliance-files-category-bulk-actions', [AdminComplianceFileController::class, 'ComplianceFileCategoryBulkActions']);



    Route::post('get-compliance-checklists', [AdminChecklistController::class, 'getComplianceChecklists']);

    Route::post('get-compliance-submitted-checklists', [AdminChecklistController::class, 'getComplianceSubmittedChecklists']);

    Route::post('get-user-submitted-checklists', [AdminChecklistController::class, 'getUserSubmittedChecklists']);

    Route::post('update-compliance-checklist', [AdminChecklistController::class, 'updateComplianceChecklist']);

    Route::post('get-compliance-assigned-checklist', [AdminChecklistController::class, 'getComplianceAssignedChecklists']);

    Route::post('update-compliance-checklist-assigned-users', [AdminChecklistController::class, 'updateComplianceChecklistAssignedUsers']);

    Route::post('delete-compliance-assigned-checklist', [AdminChecklistController::class, 'deleteComplianceAssignedChecklist']);

    Route::post('update-compliance-checklist-status', [AdminChecklistController::class, 'updateComplianceChecklistStatus']);

    Route::post('clone-compliance-checklist', [AdminChecklistController::class, 'cloneComplianceChecklist']);

    Route::post('get-compliance-checklist-details', [AdminChecklistController::class, 'getComplianceChecklistDetails']);

    Route::post('delete-compliance-checklist', [AdminChecklistController::class, 'deletedComplianceChecklist']);

    Route::post('get-checklist-assigned-users', [AdminChecklistController::class, 'getChecklistAssignedUsers']);



    ##Document Routes

    Route::post('get-documents', [AdminDocumentController::class, 'getDocument']);

    Route::post('get-document-share-history', [AdminDocumentController::class, 'getDocumentShareHistory']);

    Route::post('get-document-types', [AdminDocumentController::class, 'getDocumentTypes']);

    Route::post('update-document-types', [AdminDocumentController::class, 'updateDocumentTypes']);

    Route::post('delete-document-type', [AdminDocumentController::class, 'deleteDocumentType']);

    Route::post('update-document-type-status', [AdminDocumentController::class, 'updateDocumentTypeStatus']);

    Route::post('document-type-bulk-actions', [AdminDocumentController::class, 'documentTypeBulkActions']);



    ## Profession Routes

    Route::post('get-professions', [AdminProfessionController::class, 'getProfessions']);

    Route::post('update-professions', [AdminProfessionController::class, 'updateProfessions']);

    Route::post('delete-profession', [AdminProfessionController::class, 'deleteProfession']);

    Route::post('update-profession-status', [AdminProfessionController::class, 'updateProfessionStatus']);

    Route::post('profession-bulk-actions', [AdminProfessionController::class, 'professionBulkActions']);



    ## Specialities Routes

    Route::post('get-specialties', [AdminProfessionController::class, 'getSpecialties']);

    Route::post('update-specialities', [AdminProfessionController::class, 'updateSpecialities']);

    Route::post('delete-speciality', [AdminProfessionController::class, 'deleteSpeciality']);

    Route::post('update-speciality-status', [AdminProfessionController::class, 'updateSpecialityStatus']);

    Route::post('speciality-bulk-actions', [AdminProfessionController::class, 'specialityBulkActions']);



    ## Sub Admin Routes

    Route::post('get-sub-admins', [AdminSubAdminController::class, 'getSubAdmins']);

    Route::post('update-sub-admin', [AdminSubAdminController::class, 'updateSubAdmin']);

    Route::post('delete-sub-admin', [AdminSubAdminController::class, 'deleteSubAdmin']);

    Route::post('update-sub-admin-status', [AdminSubAdminController::class, 'updateSubAdminStatus']);



    ## Recruitment/Compliance Teams Routes

    Route::post('get-recruitment-compliance-teams', [AdminRecruitmentComplianceTeamController::class, 'getRecruitmentComplianceTeams']);

    Route::post('update-recruitment-compliance-team', [AdminRecruitmentComplianceTeamController::class, 'updateRecruitmentComplianceTeam']);

    Route::post('delete-recruitment-compliance-team', [AdminRecruitmentComplianceTeamController::class, 'deleteRecruitmentComplianceTeam']);

    Route::post('update-recruitment-compliance-team-status', [AdminRecruitmentComplianceTeamController::class, 'updateRecruitmentComplianceTeamStatus']);

    Route::post('recruitment-compliance-team-bulk-actions', [AdminRecruitmentComplianceTeamController::class, 'recruitmentComplianceTeamBulkActions']);



    ## Skill Routes

    Route::post('get-skills', [AdminSkillController::class, 'getSkills']);

    Route::post('update-skill', [AdminSkillController::class, 'updateSkill']);

    Route::post('delete-skill', [AdminSkillController::class, 'deleteSkill']);

    Route::post('update-skill-status', [AdminSkillController::class, 'updateSkillStatus']);

    Route::post('skill-bulk-actions', [AdminSkillController::class, 'skillBulkActions']);



    Route::post('get-interviwers', [AdminUserController::class, 'getInterviwers']);



    Route::post('get-users', [AdminUserController::class, 'getUsers']);

    Route::post('get-public-job-applications', [AdminUserController::class, 'getPublicJobApplications']);

    Route::post('get-all-users', [AdminUserController::class, 'getAllUsers']);

    Route::post('update-user', [AdminUserController::class, 'updateUser']);

    Route::post('add-candidate', [AdminUserController::class, 'addCandidate']);

    Route::post('update-user-status', [AdminUserController::class, 'updateUserStatus']);

    Route::post('get-user-details', [AdminUserController::class, 'getUserDetails']);

    Route::post('user-bulk-actions', [AdminUserController::class, 'userBulkActions']);

    Route::post('update-user-field', [AdminUserController::class, 'updateUserField']);

    Route::post('delete-user', [AdminUserController::class, 'deleteUser']);

    Route::post('get-user-applications', [AdminUserController::class, 'getUserJobApplications']);

    Route::post('get-user-references', [AdminUserController::class, 'getUserReferences']);

    Route::post('get-user-compliance-files', [AdminUserController::class, 'getUserComplianceFiles']);

    Route::post('get-all-users-list', [AdminUserController::class, 'getAllUserList']);

    Route::post('reference-verified-by-admin', [AdminUserController::class, 'verifiedReference']);





    Route::post('get-agencies', [AdminUserController::class, 'getAgencies']);

    Route::post('update-agency', [AdminUserController::class, 'updateAgency']);



    Route::post('update-candidate-job-preference', [AdminUserController::class, 'updateJobPreference']);

    Route::post('get-candidate-job-preference', [AdminUserController::class, 'getJobPreference']);



    #Agency Routes

    Route::post('get-agency-details', [AdminUserController::class, 'getAgencyDetails']);



    #Facility Routes

    Route::post('get-facility-details', [AdminUserController::class, 'getFacilityDetails']);



    ## Client Routes

    Route::post('get-client-types', [AdminFacilityController::class, 'getClientTypes']);

    Route::post('get-clients', [AdminFacilityController::class, 'getClients']);

    Route::post('update-client', [AdminFacilityController::class, 'updateClient']);

    Route::post('delete-client', [AdminFacilityController::class, 'deleteClient']);

    Route::post('update-client-status', [AdminFacilityController::class, 'updateClientStatus']);



    ## Assignment Routes

    Route::post('get-assignments', [AdminFacilityController::class, 'getAssignments']);

    Route::post('update-assignment', [AdminFacilityController::class, 'updateAssignment']);

    Route::post('delete-assignment', [AdminFacilityController::class, 'deleteAssignment']);

    Route::post('update-assignment-status', [AdminFacilityController::class, 'updateAssignmentStatus']);



    ##Submission Routes

    Route::post('get-submissions', [AdminSubmissionController::class, 'getSubmissions']);

    Route::post('update-submission', [AdminSubmissionController::class, 'updateSubmission']);

    Route::post('delete-submission', [AdminSubmissionController::class, 'deleteSubmission']);

    Route::post('get-specialities', [AdminSubmissionController::class, 'getSpecialities']);

    Route::post('update-submission-field', [AdminSubmissionController::class, 'updateSubmissionField']);



    ## Redirects Routes

    Route::post('get-redirects', [AdminRedirectController::class, 'getRedirects']);

    Route::post('update-redirect', [AdminRedirectController::class, 'updateRedirect']);

    Route::post('delete-redirect', [AdminRedirectController::class, 'deleteRedirect']);

    Route::post('update-redirect-field', [AdminRedirectController::class, 'updateRedirectField']);



    ## User Activities Routes

    Route::post('get-user-activities', [UserActivityController::class, 'getUserActivities']);

    Route::post('get-contact-enquiries', [UserActivityController::class, 'getContactEnquiries']);

    Route::post('get-pilot-partner-enquiries', [UserActivityController::class, 'getPilotPartnerEnquiries']);





    ## User Roles Routes

    Route::post('get-user-roles', [UserRoleController::class, 'getUserRoles']);

    Route::post('add-user-role', [UserRoleController::class, 'addEditRole']);

    Route::post('update-user-role', [UserRoleController::class, 'addEditRole']);



    ## Message Routes

    Route::post('send-message', [MessageController::class, 'sendMessage']);

    Route::post('get-messages', [MessageController::class, 'getMessages']);



    ## Announcement Routes

    Route::post('add-edit-announcement', [AnnouncementController::class, 'addEditAnnouncement']);

    Route::post('get-announcements', [AnnouncementController::class, 'getAnnouncements']);



    ## Scheduling Routes

    Route::post('get-schedules', [AdminSchedulingController::class, 'getSchedules']);

    Route::post('update-schedule', [AdminSchedulingController::class, 'updateSchedule']);

    Route::post('update-schedule-status', [AdminSchedulingController::class, 'updateScheduleStatus']);

    Route::post('delete-schedule', [AdminSchedulingController::class, 'deleteSchedule']);

    Route::post('upload-schedule-attatchment', [AdminSchedulingController::class, 'uploadScheduleAttatchment']);

    Route::post('get-schedule-attatchments', [AdminSchedulingController::class, 'getScheduleAttatchments']);



    ## Task Routes

    Route::post('get-tasks', [AdminTaskController::class, 'getTasks']);

    Route::post('update-task', [AdminTaskController::class, 'updateTask']);

    Route::post('update-task-status', [AdminTaskController::class, 'updateTaskStatus']);

    Route::post('delete-task', [AdminTaskController::class, 'deleteTask']);

    Route::post('upload-task-attatchment', [AdminTaskController::class, 'uploadTaskAttatchment']);

    Route::post('get-task-attatchments', [AdminTaskController::class, 'getTaskAttatchments']);

    Route::post('task-bulk-actions', [AdminTaskController::class, 'taskBulkActions']);



    ## Calendar/Events Routes

    Route::post('get-events', [AdminCalendarController::class, 'getEvents']);

    Route::post('update-event', [AdminCalendarController::class, 'updateEvent']);

    Route::post('update-event-status', [AdminCalendarController::class, 'updateEventStatus']);

    Route::post('delete-event', [AdminCalendarController::class, 'deleteEvent']);



    ## Blog Routes

    Route::post('get-blogs', [AdminBlogController::class, 'getBlogs']);

    Route::post('update-blog', [AdminBlogController::class, 'updateBlog']);

    Route::post('update-blog-status', [AdminBlogController::class, 'updateBlogStatus']);

    Route::post('delete-blog', [AdminBlogController::class, 'deleteBlog']);





    ## Job Request Routes

    Route::post('job-requests/get-all-job-request', [AdminJobRequestController::class, 'getAllJobRequest']);

    Route::post('job-requests/get-job-request-details', [AdminJobRequestController::class, 'getJobRequestDetails']);

    Route::post('job-requests/create-job-opportunity', [AdminJobRequestController::class, 'createJobOpportunity']);

    Route::post('job-requests/delete-job-opportunity', [AdminJobRequestController::class, 'deleteJobOpportunity']);

    Route::post('job-requests/send-job-opportunity', [AdminJobRequestController::class, 'sendJobOpportunity']);

    Route::post('job-requests/job-opportunity-list', [AdminJobRequestController::class, 'jobOpportunityList']);



    #Agency

    Route::post('get-agency', [AdminAgencyController::class, 'getAgency']);

    Route::post('get-agencyfeedbacks', [AdminAgencyController::class, 'getAgencyFeedbacks']);

    Route::get('get-recentfeedbacks', [AdminAgencyController::class, 'getRecentFeedbacks']);

    Route::post('approve-feedback', [AdminAgencyController::class, 'approveFeedback']);

    Route::post('get-detele-request-feedbacks', [AdminAgencyController::class, 'getDeleteRequestFeedbacks']);

    Route::post('declined-feedback-request', [AdminAgencyController::class, 'declinedFeedbackRequest']);

    Route::post('detele-request-feedbacks', [AdminAgencyController::class, 'deleteRequestFeedbacks']);

    

    # Deleted Users

    Route::post('get-deleted-users', [AdminUserController::class, 'getDeletedUsersByRole']);

    Route::post('restore-deleted-users', [AdminUserController::class, 'restoreUser']);

});



## ==== Common APIs ==== ##

Route::get('get-skills', [CommonController::class, 'getSkills']);

Route::get('get-states', [CommonController::class, 'getStates']);

Route::get('get-job-application-status', [CommonController::class, 'getJobApplicationStatus']);

Route::get('get-cities/{state_id}', [CommonController::class, 'getCities']);

Route::get('get-professions', [CommonController::class, 'getProfessions']);

Route::get('get-specialties/{profession_id}', [CommonController::class, 'getSpecialties']);

Route::get('get-doc-types', [CommonController::class, 'getDocTypes']);

Route::get('get-employment-types', [CommonController::class, 'getEmploymentTypes']);

Route::get('get-shifts', [CommonController::class, 'getShifts']);

Route::get('get-users/{role_id}', [CommonController::class, 'getUsers']);

Route::get('get-announcement/{role_id}', [CommonController::class, 'getAnnouncements']);



Route::get('submission-file/{id}', [CommonController::class, 'submissionFile']);

Route::get('submission-file-data/{id}', [CommonController::class, 'submissionFileData']);

Route::post('upload-submission-file', [CommonController::class, 'uploadSubmissionFile']);

Route::post('get-applicants-list', [CommonController::class, 'getApplicantsList']);