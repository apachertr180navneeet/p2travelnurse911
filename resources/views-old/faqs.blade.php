<?php

use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')

<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>Frequently Asked Questions</h1>
         <ul class="page-breadcrumb">
            <li>Your Guide to Navigating Our Travel Nurse Platform</li>
         </ul>
      </div>
   </div>
</section>
<!--End Page Title-->

<script>
   document.addEventListener('DOMContentLoaded', function() {
      const searchInput = document.getElementById('faq-search');
      const clearSearchButton = document.getElementById('clear-search');
      const tabButtons = document.querySelectorAll('.tab-btn');
      const tabsContent = document.querySelectorAll('.tabs-content .tab');

      searchInput.addEventListener('input', function() {
         filterFAQs(searchInput.value);
      });

      clearSearchButton.addEventListener('click', function() {
         searchInput.value = '';
         filterFAQs('');
      });

      tabButtons.forEach(button => {
         button.addEventListener('click', function() {
            resetSearch();
            selectCategory(button.getAttribute('data-tab'));
         });
      });

      function filterFAQs(query) {
         const activeTab = document.querySelector('.tab.active-tab');
         const faqs = activeTab.querySelectorAll('.accordion.block');

         faqs.forEach(faq => {
            const question = faq.querySelector('.acc-btn').textContent.toLowerCase();
            const answer = faq.querySelector('.acc-content').textContent.toLowerCase();
            if (question.includes(query.toLowerCase()) || answer.includes(query.toLowerCase())) {
               faq.style.display = '';
            } else {
               faq.style.display = 'none';
            }
         });
      }

      function resetSearch() {
         searchInput.value = '';
         filterFAQs('');
      }

      window.selectCategory = function(tabId) {
         tabsContent.forEach(tab => {
            if (tab.id === tabId.substring(1)) {
               tab.classList.add('active-tab');
            } else {
               tab.classList.remove('active-tab');
            }
         });

         tabButtons.forEach(button => {
            if (button.getAttribute('data-tab') === tabId) {
               button.classList.add('active-btn');
            } else {
               button.classList.remove('active-btn');
            }
         });
      };
   });
</script>

<section class="steps-section">
   <div class="container">

      <div class="row mb-3">
         <div class="col-md-6 offset-md-3">
            <div class="search-box-one">
               <div class="form-group">
                  <span class="icon flaticon-search-1"></span>
                  <input type="search" id="faq-search" name="search-field" value="" placeholder="Search" class="w-100">

                  <!--<button id="clear-search">Clear</button>-->
               </div>
            </div>
         </div>
      </div>

      <div class="row default-tabs tabs-box">

         <div class="col-md-3">
            <ul class="tab-buttons clearfix">
               <li class="tab-btn active-btn btn-block btn-lg py-3 mx-0" data-tab="#tab1" onclick="selectCategory('#tab1')">General</li>
               <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab2" onclick="selectCategory('#tab2')">Recruitment</li>
               <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab3" onclick="selectCategory('#tab3')">Job Posting</li>
               <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab1" onclick="selectCategory('#tab1')">Licensing and Certification</li>
               <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab2" onclick="selectCategory('#tab2')">Assignments</li>
               <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab3" onclick="selectCategory('#tab3')">Benefits and Compensation</li>
               <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab1" onclick="selectCategory('#tab1')">Compliance and Documentation</li>
               <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab2" onclick="selectCategory('#tab2')">Onboarding and Training</li>
               <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab3" onclick="selectCategory('#tab3')">Agency Policies</li>
            </ul>
         </div>
         <div class="col-md-9">
            <div class="tabs-content pt-0">
               <div class="tab active-tab" id="tab1">
                  <ul class="accordion-box">
                     <!--Block-->
                     <li class="accordion block active-block">
                        <div class="acc-btn active">What is a Travel Nurse? <span class="icon flaticon-add"></span></div>
                        <div class="acc-content current">
                           <div class="content">
                              <p>A travel nurse is a registered nurse who is employed to work in a specific location for a limited period of time, often to fill in temporary staffing shortages.</p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn"> How Does Your Platform Work? <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>Our platform connects travel nurses with healthcare facilities that need temporary staffing. Nurses can browse job listings, apply for positions, and manage their assignments all in one place.</p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn">What Are the Benefits of Being a Travel Nurse? <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>Travel nurses enjoy higher pay, flexible schedules, and the opportunity to explore new locations and work in diverse healthcare settings.</p>
                           </div>
                        </div>
                     </li>

                     <li class="accordion block">
                        <div class="acc-btn">Lorem Ipsum is simply dummy text of the printing and typesetting industry <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English.</p>

                              <p>Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. </p>
                           </div>
                        </div>
                     </li>

                     <li class="accordion block">
                        <div class="acc-btn">Lorem Ipsum is simply dummy text of the printing and typesetting industry <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English.</p>

                              <p>Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. </p>
                           </div>
                        </div>
                     </li>

                     <li class="accordion block">
                        <div class="acc-btn">Lorem Ipsum is simply dummy text of the printing and typesetting industry <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English.</p>

                              <p>Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. </p>
                           </div>
                        </div>
                     </li>

                     <li class="accordion block">
                        <div class="acc-btn">Lorem Ipsum is simply dummy text of the printing and typesetting industry <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English.</p>

                              <p>Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. </p>
                           </div>
                        </div>
                     </li>

                     <li class="accordion block">
                        <div class="acc-btn">Lorem Ipsum is simply dummy text of the printing and typesetting industry <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English.</p>

                              <p>Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. </p>
                           </div>
                        </div>
                     </li>


                  </ul>
               </div>

               <!--Tab-->
               <div class="tab" id="tab2">
                  <ul class="accordion-box">
                     <!--Block-->
                     <li class="accordion block active-block">
                        <div class="acc-btn active">How Do I Apply for a Job? <span class="icon flaticon-add"></span></div>
                        <div class="acc-content current">
                           <div class="content">
                              <p>To apply for a job, first create a profile on our platform. Then, browse available job listings and click 'Apply' on the positions that interest you.</p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn"> What Documents Do I Need to Submit? <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>You will need to submit your nursing license, resume, compliance documents, and any certifications relevant to the position you are applying for.</p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn">How Are Job Assignments Made? <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>Job assignments are made based on your qualifications, preferences, and the needs of the healthcare facilities. Our system matches you with the best available positions.</p>
                           </div>
                        </div>
                     </li>

                     <li class="accordion block">
                        <div class="acc-btn">Lorem Ipsum is simply dummy text of the printing and typesetting industry <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English.</p>

                              <p>Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. </p>
                           </div>
                        </div>
                     </li>

                     <li class="accordion block">
                        <div class="acc-btn">Lorem Ipsum is simply dummy text of the printing and typesetting industry <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English.</p>

                              <p>Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. </p>
                           </div>
                        </div>
                     </li>

                     <li class="accordion block">
                        <div class="acc-btn">Lorem Ipsum is simply dummy text of the printing and typesetting industry <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English.</p>

                              <p>Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. </p>
                           </div>
                        </div>
                     </li>

                     <li class="accordion block">
                        <div class="acc-btn">Lorem Ipsum is simply dummy text of the printing and typesetting industry <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English.</p>

                              <p>Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. </p>
                           </div>
                        </div>
                     </li>

                     <li class="accordion block">
                        <div class="acc-btn">Lorem Ipsum is simply dummy text of the printing and typesetting industry <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English.</p>

                              <p>Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. </p>
                           </div>
                        </div>
                     </li>

                  </ul>
               </div>

               <!--Tab-->
               <div class="tab" id="tab3">
                  <ul class="accordion-box">
                     <!--Block-->
                     <li class="accordion block active-block">
                        <div class="acc-btn active">How Do I Post a Job? <span class="icon flaticon-add"></span></div>
                        <div class="acc-content current">
                           <div class="content">
                              <p>Employers can post a job by logging into their account, navigating to the 'Job Posting' section, and filling out the required details about the position.</p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn"> How Long Does It Take for My Job Posting to Be Approved? <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>Job postings are typically reviewed and approved within 24-48 hours.</p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn">Can I Edit My Job Posting After It’s Published? <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>Yes, you can edit your job posting at any time by going to the 'My Postings' section and selecting the job you wish to update.</p>
                           </div>
                        </div>
                     </li>

                     <li class="accordion block">
                        <div class="acc-btn">Lorem Ipsum is simply dummy text of the printing and typesetting industry <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English.</p>

                              <p>Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. </p>
                           </div>
                        </div>
                     </li>

                     <li class="accordion block">
                        <div class="acc-btn">Lorem Ipsum is simply dummy text of the printing and typesetting industry <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English.</p>

                              <p>Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. </p>
                           </div>
                        </div>
                     </li>

                     <li class="accordion block">
                        <div class="acc-btn">Lorem Ipsum is simply dummy text of the printing and typesetting industry <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English.</p>

                              <p>Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. </p>
                           </div>
                        </div>
                     </li>

                     <li class="accordion block">
                        <div class="acc-btn">Lorem Ipsum is simply dummy text of the printing and typesetting industry <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English.</p>

                              <p>Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. </p>
                           </div>
                        </div>
                     </li>

                     <li class="accordion block">
                        <div class="acc-btn">Lorem Ipsum is simply dummy text of the printing and typesetting industry <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English.</p>

                              <p>Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. </p>
                           </div>
                        </div>
                     </li>

                  </ul>
               </div>
            </div>
         </div>
      </div>


   </div>
</section>


@endsection