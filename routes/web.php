<?php



use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;

use App\Http\Controllers\TestController;

use App\Http\Controllers\ClassifiedController;



use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\UserLoginController;

use App\Http\Controllers\Auth\LogoutController;

use App\Http\Controllers\Auth\SignupController;

use App\Http\Controllers\Dashboard\CategoryController as DashboardCategoryController;

use App\Http\Controllers\Dashboard\CommentController as DashboardCommentController;

use App\Http\Controllers\Dashboard\HomeController as DashboardHomeController;

use App\Http\Controllers\Dashboard\MediaController;

use App\Http\Controllers\Dashboard\MenuController;

use App\Http\Controllers\Dashboard\PageController as DashboardPageController;

use App\Http\Controllers\Dashboard\PostController as DashboardPostController;

use App\Http\Controllers\Dashboard\ProfileController;

use App\Http\Controllers\Dashboard\SiteSettingController;

use App\Http\Controllers\Dashboard\SocialMediaController;

use App\Http\Controllers\Dashboard\TagController as DashboardTagController;

use App\Http\Controllers\Dashboard\UserController as DashboardUserController;

use App\Http\Controllers\Dashboard\MarketplaceController as DashboardMarketplaceController;

use App\Http\Controllers\Dashboard\SubscribeController as DashboardSubscribeController;

use App\Http\Controllers\Dashboard\ClassifiedController as DashboardClassifiedController;

use App\Http\Controllers\Dashboard\ServiceController as DashboardServiceController;

use App\Http\Controllers\Dashboard\VendorCategoryController;

use App\Http\Controllers\Dashboard\VendorSubCategoryController;

use App\Http\Controllers\VendorAgencyController;

use App\Http\Controllers\VendorController;

// Create user Login Controller
use App\Http\Controllers\User\UserSignupController;





/*

|--------------------------------------------------------------------------

| Web Routes

|--------------------------------------------------------------------------

|

| Here is where you can register web routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which

| contains the "web" middleware group. Now create something great!

|

*/



Route::get('/', [HomeController::class, 'index'])->name('home');



Route::get('for-travel-nurses', [HomeController::class, 'forTravelNurses'])->name('for-travel-nurses');

Route::get('for-employers', [HomeController::class, 'forEmployers'])->name('for-employers');

Route::get('for-travel-agencies', [HomeController::class, 'forTravelAgencies'])->name('for-travel-agencies');

Route::get('for-healthcare-facilities', [HomeController::class, 'forHealthcareFacilities'])->name('for-healthcare-facilities');



/*Route::get('candidate-tracking-system', [HomeController::class, 'candidateTrackingSystem'])->name('candidate-tracking-system');*/

Route::get('applicant-tracking-system', [HomeController::class, 'applicantTrackingSystem'])->name('applicant-tracking-system');



Route::get('agency-job-posting', [HomeController::class, 'agencyJobPosting'])->name('agency-job-posting');

Route::get('agency-travel-nurse-management', [HomeController::class, 'agencyTravelNurseManagement'])->name('agency-travel-nurse-management');

Route::get('agency-compliance-files', [HomeController::class, 'agencyComplianceFiles'])->name('agency-compliance-files');

Route::get('agency-follow-up-scheduling', [HomeController::class, 'agencyFollowUpScheduling'])->name('agency-follow-up-scheduling');

Route::get('agency-task-management', [HomeController::class, 'agencyTaskManagement'])->name('agency-task-management');

Route::get('agency-applicant-tracking-system', [HomeController::class, 'agencyApplicantTrackingSystem'])->name('agency-applicant-tracking-system');

Route::get('agency-submission-files', [HomeController::class, 'agencySubmissionFiles'])->name('agency-submission-files');



Route::get('facility-job-posting', [HomeController::class, 'facilityJobPosting'])->name('facility-job-posting');

Route::get('facility-travel-nurse-management', [HomeController::class, 'facilityTravelNurseManagement'])->name('facility-travel-nurse-management');

Route::get('facility-compliance-files', [HomeController::class, 'facilityComplianceFiles'])->name('facility-compliance-files');

Route::get('facility-follow-up-scheduling', [HomeController::class, 'facilityFollowUpScheduling'])->name('facility-follow-up-scheduling');

Route::get('facility-task-management', [HomeController::class, 'facilityTaskManagement'])->name('facility-task-management');



Route::get('document-safe', [HomeController::class, 'documentSafe'])->name('document-safe');

Route::get('messaging-sms', [HomeController::class, 'messagingSMS'])->name('messaging-sms');

Route::get('shortlisted-jobs', [HomeController::class, 'shortlistedJobs'])->name('shortlisted-jobs');



Route::get('compliance-files', [HomeController::class, 'complianceFiles'])->name('compliance-files');

Route::get('application-status-tracking', [HomeController::class, 'applicationStatusTracking'])->name('application-status-tracking');

Route::get('professional-profile', [HomeController::class, 'professionalProfile'])->name('professional-profile');

/*Route::get('resume-uploading', [HomeController::class, 'resumeUploading'])->name('resume-uploading');*/

Route::get('free-job-application', [HomeController::class, 'freeJobApplication'])->name('free-job-application');

Route::get('bookmark-job', [HomeController::class, 'bookmarkJob'])->name('bookmark-job');

/*Route::get('messaging-notification', [HomeController::class, 'messagingNotification'])->name('messaging-notification');*/

Route::get('email-notification', [HomeController::class, 'emailNotification'])->name('email-notification');



Route::get('locations', [HomeController::class, 'locations'])->name('locations');

Route::get('company', [HomeController::class, 'company'])->name('company');

Route::get('travel-nurse-benefits', [HomeController::class, 'travelNurseBenefits'])->name('travel-nurse-benefits');

Route::get('faqs', [HomeController::class, 'faqs'])->name('faqs');

Route::get('nursing-ceus', [HomeController::class, 'nursingCeus'])->name('nursing-ceus');

Route::get('nursing-compact-states', [HomeController::class, 'nursingCompactStates'])->name('nursing-compact-states');

Route::get('travel-nurse-housing', [HomeController::class, 'travelNurseHousing'])->name('travel-nurse-housing');

Route::get('travel-nurse-blogs', [HomeController::class, 'travelNurseBlogs'])->name('travel-nurse-blogs');

Route::get('blogs', [HomeController::class, 'blogs'])->name('blogs');

Route::get('blog/{bid}', [HomeController::class, 'blog'])->name('blog');



Route::get('term-conditions', [HomeController::class, 'termConditions'])->name('term-conditions');

Route::get('privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy-policy');



Route::get('job-categories', [HomeController::class, 'jobCategories'])->name('job-categories');



Route::get('pilot-partner-program', [HomeController::class, 'pilotPartnerProgram'])->name('pilot-partner-program');

Route::get('pilot-partner-signup', [HomeController::class, 'pilotPartnerSignup'])->name('pilot-partner-signup');

Route::post('pilot-signup-submit', [HomeController::class, 'pilotSignupSubmit'])->name('pilot-signup-submit');



Route::get('contact-us', [HomeController::class, 'contactUs'])->name('contact-us');

Route::post('contact-us-submit', [HomeController::class, 'contactUsSubmit'])->name('contact-us-submit');

Route::get('our-story', [HomeController::class, 'ourStory'])->name('our-story');



/*

Route::get('jobs/search', [HomeController::class, 'search'])->name('jobs-search');

Route::get('jobs', [HomeController::class, 'jobs'])->name('jobs');

*/

Route::get('jobs', [HomeController::class, 'search'])->name('jobs-search');



Route::get('jobs/{pid}', [HomeController::class, 'jobs'])->name('job-category');



Route::get('job/{id}', [HomeController::class, 'job'])->name('job');



Route::get('test-email-page', [TestController::class, 'testMailPage'])->name('testMailPage');

Route::post('send-test-email', [TestController::class, 'sendTestEmail'])->name('sendTestEmail');

Route::get('resource', [HomeController::class, 'resources'])->name('resources');

Route::get('resource/get-resource-feedback', [HomeController::class, 'getResourceFeedback'])->name('resource.feedback');

Route::post('/submit-review', [HomeController::class, 'submitReview'])->name('resource.submit.review');

/****

 * 

 * News Feed route

 * 

 * 

 *  

 ****/

Route::get('vendorcategory', [VendorController::class, 'vendorcategory'])->name('vendorcategory');

Route::get('vendorcategory/{vendor_category_slug}/{vendor_subcategory_slug}', [VendorController::class, 'vendorList'])->name('vendorList');

Route::get('vendorcategory/{vendor_category_slug}', [VendorController::class, 'vendorSubList'])->name('vendorSubList');

Route::get('vendor/{id}', [VendorController::class, 'vendorDetails'])->name('vendorDetails');



Route::get('vendor/{id}/products', [VendorController::class, 'vendorProducts'])->name('vendorProducts');

Route::get('vendor/{id}/blogs', [VendorController::class, 'vendorBlogs'])->name('vendorBlogs');

Route::get('vendor/{id}/press-releases', [VendorController::class, 'vendorPressReleases'])->name('vendorPressReleases');

// Vendor product details page

Route::get('vendor/products/{id}', [VendorController::class, 'vendorProductDetails'])->name('vendorProducts.details');

Route::get( 'vendor/blogs/{id}', [VendorController::class, 'vendorBlogDetails'])->name('vendorBlogs.details');

Route::get('vendor/news/{id}', [VendorController::class, 'vendorPressReleaseDetails'])->name('vendorNews.details');

//Vendor agency reviews and feedback page

Route::post('vendor/agency/review-store', [VendorController::class, 'vendorAgencyReviewStore'])->name('vendor.agency.review-store');
Route::post('vendor/agency/register', [VendorController::class, 'vendorAgencyRegister'])->name('vendor.agency.register');

Route::get('vendor/agency/review-list', [VendorController::class, 'vendorAgencyReviewList'])->name('vendor.agency.review-list');

//Mail send 

Route::post('vendorcategory/contact-mail-send', [VendorController::class, 'contactMailSend'])->name('vendorcategory.mail.send');

Route::post('vendorcategory/store-company-details', [VendorController::class, 'storeCompanyDetails'])->name('vendorcategory.submit-company');





Route::get('reference/{id}', [HomeController::class, 'references'])->name('reference');

Route::post('reference-form-submit', [HomeController::class, 'referenceFormSubmit'])->name('reference-form-submit');

Route::get('submission-file/{id}', [HomeController::class, 'submissionFile'])->name('submission-file');



/****
 * 
 * Classified Ads
 * 
 * 
 *
 ****/


 Route::get('/classifieds', [ClassifiedController::class, 'index'])->name('classified.index');
 Route::match(['get', 'post'], '/classifieds/save', [ClassifiedController::class, 'save'])->name('classified.save');
 Route::match(['get', 'post'], '/classifieds/add', [ClassifiedController::class, 'add'])->name('classified.add');
 Route::match(['get', 'post'], '/classifieds/{id}/edit', [ClassifiedController::class, 'edit'])->name('classified.edit');
 Route::put('/classifieds/{id}/update', [ClassifiedController::class, 'update'])->name('classified.update');
 Route::delete('/classifieds/{id}', [ClassifiedController::class, 'destroy'])->name('classified.delete');
 Route::post('/classifieds/search', [ClassifiedController::class, 'search'])->name('classified.search');
 Route::get('/classifieds/search', [ClassifiedController::class, 'search'])->name('classified.search');
 
 Route::get('/get-cities-by-state', [ClassifiedController::class, 'getCitiesByState'])->name('getCitiesByState');
 Route::match(['get', 'post'], '/classifieds/contact-seller', [ClassifiedController::class, 'contact_seller'])->name('classified.contact_seller');
 Route::get('/singledetail/{slug}', [ClassifiedController::class, 'singledetail'])->name('classified.singledetail');



/****

 * 

 * News Feed route

 * 

 * 

 *

****/

Route::get('news', [HomeController::class, 'news'])->name('news');

Route::post('news', [HomeController::class, 'news'])->name('news');

Route::get('news-detail/{slug}', [HomeController::class, 'newsDetail'])->name('news-detail');

Route::get('news/subscribe', [HomeController::class, 'newssubscribe'])->name('newssubscribe');

Route::post('news/subscribe', [HomeController::class, 'newssubscribe'])->name('newssubscribe');

// Route for displaying posts of a specific category

Route::get('/{slug}', [HomeController::class, 'particularcat'])->name('particularcat');

Route::get('/news/track-news-clicks', [HomeController::class, 'trackNewsClicks'])->name('track.news.clicks');



Route::name("auth.")->group(function() {

    Route::get("/signup", [SignupController::class, "index"])->name("signup");

    Route::post("/signup", [SignupController::class, "signup"])->name("signup.submit");

    Route::get("/admin/newslogin", [LoginController::class, "index"])->name("login");

    Route::post("/admin/newslogin", [LoginController::class, "login"])->name("login.submit");
    
    Route::get("/user/login", [UserLoginController::class, "user"])->name("user.login");
    Route::post("/user/login", [UserLoginController::class, "userLogin"])->name("userlogin.submit");

    Route::post("/logout", [LogoutController::class, "index"])->name("logout");

});



Route::name("dashboard.")->prefix("/dashboard")->middleware(["auth"])->group(function() {

    // dashboard home

    Route::get("/admin", [DashboardHomeController::class, "index"])->name("home");



    // posts

    Route::prefix("/posts")->name("posts.")->controller(DashboardPostController::class)->group(function() {

        Route::get("/{id}/status", "status")->name("status");

        Route::get("/{id}/featured", "featured")->name("featured");

        Route::get("/{id}/comment", "comment")->name("comment");

        Route::get("/trashed", "trashed")->name("trashed");

        Route::get("/{id}/restore", "restore")->name("restore");

        Route::delete("/{id}/delete", "delete")->name("delete");

    });

    Route::resource("/posts", DashboardPostController::class)->except(["show"]);



    // media

    Route::resource("/media", MediaController::class)->except(["show", "edit", "update"]);







    // categories

    Route::prefix("/categories")->name("categories.")->controller(DashboardCategoryController::class)->group(function() {

        Route::get("/{id}/status", "status")->name("status");

        Route::get("/trashed", "trashed")->name("trashed");

        Route::get("/{id}/restore", "restore")->name("restore");

        Route::delete("/{id}/delete", "delete")->name("delete");

    

        Route::post('/sort', "sort")->name('sort');



    });

    Route::resource("/categories", DashboardCategoryController::class);





    // marketplaces

    Route::prefix("/marketplaces")->name("marketplaces.")->controller(DashboardMarketplaceController::class)->group(function() {

        Route::get("/{id}/status", "status")->name("status");

        Route::get("/trashed", "trashed")->name("trashed");

        Route::get("/{id}/restore", "restore")->name("restore");

        Route::delete("/{id}/delete", "delete")->name("delete");

    });

    Route::resource("/marketplaces", DashboardMarketplaceController::class);



     // classifieds
     Route::prefix("/classifieds")->name("classifieds.")->controller(DashboardClassifiedController::class)->group(function() {
        Route::put("/{id}/approve", "approve")->name("approve");
        Route::get("/contactseller", "contactseller")->name('contactseller');
        Route::delete("/{id}/contactselller-delete", "contactsellerDelete")->name('contactseller.delete');
        Route::get("/trashed", "trashed")->name("trashed");
        Route::get("/{id}/restore", "restore")->name("restore");
        Route::delete("/{id}/delete", "delete")->name("delete");   
        Route::get('/{id}/show',  'show')->name('show');  
        Route::put('/{id}/update-status','updateStatus');  
    });

    // Service
    Route::prefix("/services")->name("services.")->controller(DashboardServiceController::class)->group(function() {
        Route::get("list", "index")->name("index");
        Route::post("store", "store")->name("store");
        Route::post("delete", "delete")->name("delete");
        Route::post("status", "status")->name("status");  
    });

    //Routes for Showing and Updating Status
   

    Route::resource("/classifieds", DashboardClassifiedController::class);


  

     //subscribes  

     Route::resource("/subscribes", DashboardSubscribeController::class);





     //tags

     Route::prefix("/tags")->name("tags.")->controller(DashboardTagController::class)->group(function() {

        Route::get("/index", "index")->name("index");

        Route::delete("/{id}/destroy", "destroy")->name("destroy");

    });



      // users

      Route::prefix("/users")->name("users.")->controller(DashboardUserController::class)->group(function() {

        Route::get("/{id}/status", "status")->name("status");

    });





    // users

    Route::prefix("/users")->name("users.")->controller(DashboardUserController::class)->group(function() {

        Route::get("/{id}/status", "status")->name("status");

    });

    Route::resource("/users", DashboardUserController::class);



    // pages

    Route::prefix("/pages")->name("pages.")->controller(DashboardPageController::class)->group(function() {

        Route::get("/{id}/status", "status")->name("status");

        Route::get("/trashed", "trashed")->name("trashed");

        Route::get("/{id}/restore", "restore")->name("restore");

        Route::delete("/{id}/delete", "delete")->name("delete");

    });

    Route::resource("/pages", DashboardPageController::class)->except(["show"]);


    // Vendor Categories

    Route::prefix("/vendorcategories")->name("vendorcategories.")->controller(VendorCategoryController::class)->group(function () {

        Route::get("/{id}/status", "status")->name("status");

        Route::get("/trashed", "trashed")->name("trashed");

        Route::get("/{id}/restore", "restore")->name("restore");

        Route::delete("/{id}/delete", "delete")->name("delete");

    });

    Route::resource("/vendorcategories", VendorCategoryController::class);



    // Vendor Subcategories

    Route::prefix("/vendorsubcategories")->name("vendorsubcategories.")->controller(VendorSubCategoryController::class)->group(function () {

        Route::get("/{id}/status", "status")->name("status");

        Route::get("/trashed", "trashed")->name("trashed");

        Route::get("/{id}/restore", "restore")->name("restore");

        Route::delete("/{id}/delete", "delete")->name("delete");

    });

    Route::resource("/vendorsubcategories", VendorSubCategoryController::class);

    

    // Vendor Agencies

    Route::prefix("/vendor_agencies")->name("vendor_agencies.")->controller(VendorAgencyController::class)->group(function () {

        Route::get("/{id}/status", "status")->name("status");

        Route::get("/trashed", "trashed")->name("trashed");

        Route::get('/get-subcategories', "getSubcategories")->name('getSubcategories');

    });

    Route::resource('/vendor_agencies', VendorAgencyController::class);

    Route::get('/vendor_agency/email-list', [VendorAgencyController::class,"getEmailList"])->name('vendor_agency.getEmailList');

    Route::get('/vendor_agency/delete-email-list', [VendorAgencyController::class, "deleteEmailList"])->name('vendor_agency.delete_email_list');

    Route::get('/vendor_agency/reviews-feedback', [VendorAgencyController::class,"getReviewsFeedback"])->name('vendor_agency.getReviewsFeedback');

    Route::get('vendor_agency/reviews-update', [VendorAgencyController::class, 'vendorAgencyReviewsUpdate'])->name('vendor_agency.review-update');

    Route::get('/vendor_agency/company-list', [VendorAgencyController::class, "getContactList"])->name('vendor_agency.getContactList');

    Route::get('/vendor_agency/delete-list', [VendorAgencyController::class, "deleteCompanyList"])->name('vendor_agency.delete_company_list');



    // settings

    Route::prefix("/settings")->name("settings.")->group(function() {

        // site settings

        Route::get("/site-settings", [SiteSettingController::class, "index"])->name("site");

        Route::post("/site-settings", [SiteSettingController::class, "update"])->name("site.update");

        // profile update

        Route::get("/profile", [ProfileController::class, "index"])->withoutMiddleware(["admin"])->name("profile");

        Route::post("/profile", [ProfileController::class, "update"])->withoutMiddleware(["admin"])->name("profile.update");

        // password change

        Route::get("/change-password", [ProfileController::class, "password"])->withoutMiddleware(["admin"])->name("password");

        Route::post("/change-password", [ProfileController::class, "passwordUpdate"])->withoutMiddleware(["admin"])->name("password.update");

        // social media

        Route::get("/social-media", [SocialMediaController::class, "index"])->name("social.media");

        Route::post("/social-media", [SocialMediaController::class, "add"])->name("social.media.add");

        Route::get("/social-media/{id}/status", [SocialMediaController::class, "status"])->name("social.media.status");

        Route::delete("/social-media/{id}/delete", [SocialMediaController::class, "delete"])->name("social.media.delete");

        // site menu

        Route::get("/menus/header", [MenuController::class, "header"])->name("menus.header");

        Route::post("/menus/header", [MenuController::class, "headerUpdate"])->name("menus.header.update");

        Route::get("/menus/footer", [MenuController::class, "footer"])->name("menus.footer");

        Route::post("/menus/footer", [MenuController::class, "footerUpdate"])->name("menus.footer.update");

    });



});


//Users create accounts

Route::name("auth.user")->group(function() {

    Route::get("user/signup", [UserSignupController::class, "index"])->name("signup");

    Route::post("user/signup", [UserSignupController::class, "signup"])->name("signup.submit");

    Route::get("/user/login", [LoginController::class, "index"])->name("login");

    Route::post("/user/login", [LoginController::class, "login"])->name("login.submit");
    

    Route::post("/logout", [LogoutController::class, "index"])->name("logout");

});



