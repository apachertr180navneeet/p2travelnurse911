<?php

// Get the domain, with fallback to SERVER_NAME
$domain = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');

$mainDomainUrls = [
    'app_url' => 'https://app.travelnurse911.com/',
    'login_url' => 'https://app.travelnurse911.com/',
    'register_url' => 'https://app.travelnurse911.com/register',
    'user_job_url' => 'https://app.travelnurse911.com/',
    'user_profile_url' => 'https://app.travelnurse911.com/',
    'user_documents_url' => 'https://app.travelnurse911.com/',
    'client_login_url' => 'https://app.travelnurse911.com/client/login',
    'admin_login_url' => 'https://app.travelnurse911.com/admin/login',
    'staff_login_url' => 'https://app.travelnurse911.com/staff/login',
];

$subDomainUrls = [
    'app_url' => 'https://staging.travelnurse911.com/',
    'login_url' => 'https://staging.travelnurse911.com/',
    'register_url' => 'https://staging.travelnurse911.com/register',
    'user_job_url' => 'https://staging.travelnurse911.com/',
    'user_profile_url' => 'https://staging.travelnurse911.com/',
    'user_documents_url' => 'https://staging.travelnurse911.com/',
    'client_login_url' => 'https://staging.travelnurse911.com/client/login',
    'admin_login_url' => 'https://staging.travelnurse911.com/client/login',
    'staff_login_url' => 'https://staging.travelnurse911.com/staff/login',
];

return [
    'email_delivery_to' => 'admin@travelnurse911.com',
    'email' => 'support@travelnurse911.com',
    'phone' => '1-800-485-7911',
    
    /* === Upload Directories === */
    'upload_folder' => 'public/uploads/',
    'user_folder' => 'public/uploads/users/',
    'job_folder' => 'public/uploads/jobs/',
    'doc_folder' => 'public/uploads/documents/',
    'resume_folder' => 'public/uploads/resumes/',
    'job_attchment_folder' => 'public/uploads/jobs/attachments/',
    'compliance_file_folder' => 'public/uploads/compliance-files/',
    'blog_folder' => 'public/uploads/blogs/',

    /* === Email === */
    'verify_subject' => 'Email Verification',
    'reset_password' => 'Reset Password',
    'share_document' => 'Share Document',
    'job_message' => 'has sent you a message',
    'document_access_request_sent' => 'New Document Access Request',
    'document_access_request_response' => 'Response to Your Document Access Request',
    'job_application' => 'New Job Application',
    'admin_candidate_register' => 'New Registration',
    'new_message' => 'New Message from',
    'office_admin_register' => "Welcome to :app_name - Office Admin Login Credentials",
    'assign_skill_checklist' => "New Skill Checklist Assigned by",
    
    /* === URLs === */
    'app_url' => in_array($domain, ['travelnurse911.com', 'www.travelnurse911.com']) ? $mainDomainUrls['app_url'] : $subDomainUrls['app_url'],
    'login_url' => in_array($domain, ['travelnurse911.com', 'www.travelnurse911.com']) ? $mainDomainUrls['login_url'] : $subDomainUrls['login_url'],
    'register_url' => in_array($domain, ['travelnurse911.com', 'www.travelnurse911.com']) ? $mainDomainUrls['register_url'] : $subDomainUrls['register_url'],
    'user_job_url' => in_array($domain, ['travelnurse911.com', 'www.travelnurse911.com']) ? $mainDomainUrls['user_job_url'] : $subDomainUrls['user_job_url'],
    'user_profile_url' => in_array($domain, ['travelnurse911.com', 'www.travelnurse911.com']) ? $mainDomainUrls['user_profile_url'] : $subDomainUrls['user_profile_url'],
    'user_documents_url' => in_array($domain, ['travelnurse911.com', 'www.travelnurse911.com']) ? $mainDomainUrls['user_documents_url'] : $subDomainUrls['user_documents_url'],
    'client_login_url' => in_array($domain, ['travelnurse911.com', 'www.travelnurse911.com']) ? $mainDomainUrls['client_login_url'] : $subDomainUrls['client_login_url'],
    'admin_login_url' => in_array($domain, ['travelnurse911.com', 'www.travelnurse911.com']) ? $mainDomainUrls['admin_login_url'] : $subDomainUrls['admin_login_url'],
    'staff_login_url' => in_array($domain, ['travelnurse911.com', 'www.travelnurse911.com']) ? $mainDomainUrls['staff_login_url'] : $subDomainUrls['staff_login_url'],
];
