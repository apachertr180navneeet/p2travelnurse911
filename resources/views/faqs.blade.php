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
               <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab2" onclick="selectCategory('#tab2')">Recruitment – Traveler Questions</li>
               <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab3" onclick="selectCategory('#tab3')">Compliance</li>
               <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab4" onclick="selectCategory('#tab4')">Assignment</li>
               <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab5" onclick="selectCategory('#tab5')">Qualifications</li>
               <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab6" onclick="selectCategory('#tab6')">Assignment section</li>
              <!-- <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab7" onclick="selectCategory('#tab7')">Agreement section</li> -->
               <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab8" onclick="selectCategory('#tab8')">Scheduling section</li>
               <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab9" onclick="selectCategory('#tab9')">Orientation</li>
               <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab10" onclick="selectCategory('#tab10')">Facility & Unit</li>
               <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab11" onclick="selectCategory('#tab11')">Submission,Interview & Offer</li>
               <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab12" onclick="selectCategory('#tab12')">Payroll</li>
              <!-- <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab13" onclick="selectCategory('#tab13')">Housing</li> -->
               <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab14" onclick="selectCategory('#tab14')">Insurance</li>
               <!--<li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab14" onclick="selectCategory('#tab15')">Agecny Cinical support</li>-->
               <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab16" onclick="selectCategory('#tab16')">Extension</li>
               <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab17" onclick="selectCategory('#tab17')">Per DIEM/PRN</li>
               <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab18" onclick="selectCategory('#tab18')">Interview</li>
               <!-- <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab19" onclick="selectCategory('#tab19')">Rates & Stipend</li> -->
               <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab20" onclick="selectCategory('#tab20')">Bonuses</li>
             <!--  <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab21" onclick="selectCategory('#tab21')">About Benefits</li> -->
             <!--  <li class="tab-btn btn-block btn-lg py-3 mx-0" data-tab="#tab22" onclick="selectCategory('#tab22')">Recruitment – Recruiter Questions</li> -->
               
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
                              <p>A travel nurse is a skilled medical professional that is a (RN) Registered Nurse, (LPN/LVN) Licensed Practical Nurse or Vocational Nurse. These nurses are called Travel Nurses because they must travel 50 + miles from their permanent place of residence to work as a professional nurse.  Travel Nurses differ from local nurses due to the miles that are required to travel to be considered a travel nurse.  Nurses that live within 50 miles of the facility are often referred to as Local Nurses as defined by many travel nursing companies. Travel nurses work for a staffing agency rather than working directly for the hospital, doctor's office, or other healthcare institutions. However, since the pandemic, more and more hospitals are creating their own internal travel nurse programs. Travel Nurses accept short term assignments as short as 4 - 6 weeks and as long as 13 - 52 weeks at a time, if needed. However, the average travel nurse placements range from 8 - 26 weeks. Travel nurses often travel to various cities and facilities to find travel assignments. To be a good Travel Nurse you must be able to thrive in new environments, sometimes at a fast pace, all while integrating with a new healthcare team.</p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn">What is the mission of Travel Nurse 911?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>The mission of Travel Nurse 911 is to provide top tier travel opportunities to travel nurses nationwide with the highest pay and benefit packages. We identify these agencies and invite them to become a part of our platform. Our mission is to also to provide a seamless recruitment process that decreases the work of the Recruiter and Travel Nurse to allow for a smooth and efficient placement. </p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn"> Steps to finding a Travel Assignment <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>1. Go to TravelNurse911.com to find a position that matches your desires such as location and pay. <br>2. Connect with an agency. Often times the position you are seeking is being offered by several agencies. Select the agency that is best aligned with your desires, personality and goals.  <br>3. Complete Submission File - work with your agency to complete the documents that make up your submission file such as your resume (work history), skills checklist and references. Once these items are completed, you are ready to start interviewing for travel assignments.</p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn">How is Travel Nurse 911 different from similar sites?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>Travel Nurse 911 focuses solely on the travel nurse, travel agency and healthcare facility making the smoothest placement possible with the tools on our platform. Other sites mostly focus on getting as many jobs posted to their site as possible whereas Travel Nurse 911's focus is to help travel nurses and employers make the best connections possible. </p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn">How many Recruiters should I work with?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>You should work with at least 2 to 3 Recruiters from different agencies when you are seeking a travel assignment. Don't put all of your eggs in one basket. This allows you to learn about new opportunities that the other Recruiters may not have. However, if you are working with multiple Recruiters, only accept one position and once you accept a position, please notify the other Recruiters immediately so that they can retrack you from any jobs you may have been submitted to. Also, please be sure to let your Recruiter know if you are working with another Recruiter. This should give them the push to secure you a position as soon as possible and make you the best offer. </p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn">How long does it take to become a travel nurse? <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>2 - 4 years! This time incudes earning your nursing degree, getting licensed and at least one year of bedside experience. It takes approximately two years to earn an ADN - Associates Degree in Nursing which is the minimum requirement to become a Registered Nurse. A BSN- Bachelors of Science in Nursing could take approximately 4 years to obtain and if you take the BSN route, you could become a Travel Nurse in 4 - 5 years.  </p>
                           </div>
                        </div>
                     </li>

                     

                     


                  </ul>
               </div>

               <!--Recruitment – Traveler Questions Tab-->
               <div class="tab" id="tab2">
                  <ul class="accordion-box">
                     <!--Block-->
                     <li class="accordion block active-block">
                        <div class="acc-btn active">If I lose my Recruiter, what do I do? <span class="icon flaticon-add"></span></div>
                        <div class="acc-content current">
                           <div class="content">
                              <p>Firstly, don't panic! You will be assigned a new Recruiter. Imagine you being assigned to a patient and the next shift the patient notices that another nurse has been assigned to them, do they panic? Or are they reassured that the next nurse will provide them with the best care as well? Recruiters being reassigned is typical but not common. Losing your Recruiter can happen for a variety of reasons. The Recruiter could have been transferred to another department, received a promotion or no longer employed at the company. If this happens, the company should have introduced you to another Recruiter and if not, you should reach out to them immediately to be connected with another Recruiter.</p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn"> How do I know when I have found the right recruiter?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>Finding the right Recruiter can be likened to finding a best friend, business partner, job, church, or person you feel absolutely comfortable with. The same emotions that you feel when you have connected with something that feels right or at home, you will get these same feelings when meeting the right Recruiter. They will maintain effective communication with you during the submission and hiring process. A great Recruiter will also be in touch with you while on assignment to ensure everything is going well and address any concerns you may have. You should feel supported by your Recruiter and if you don't, you may not have found the right Recruiter!</p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn">If I am reassigned to another Recruiter, what should I do?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>If you're assigned a new Recruiter, please allow them the opportunity to demonstrate their abilities. If you are not meshing with them and would like to be assigned a new Recruiter, contact the Recruitment Manager, express your concerns and you will be assigned a new Recruiter. Expressing your concerns in the most calm way possible allows the Recruitment Manager to document them and address them in the most effective way possible.</p>
                           </div>
                        </div>
                     </li>

                     <li class="accordion block">
                        <div class="acc-btn">If I am traveling with a travel partner, can we work the same shift?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>Travelers are offered a position with a hospital or healthcare organization because they need you to fill it on a temporary basis until they hire someone permanently. If this is the case, traveling with a travel partner and expecting to work the same shift is not likely or encouraged. Although you are traveling together, this does not mean you will be working together. There is a great possibility you could work the same unit but most facilities can't guarantee that you will be working the same shift. Sometimes this is possible and should be discussed with your Recruiter before submission and during the interview. Please proceed with caution because if the hiring manager feels that this is a deal breaker, they may make an offer to another candidate that do not have these requirements. </p>

                              
                           </div>
                        </div>
                     </li>

                     <li class="accordion block">
                        <div class="acc-btn">Will I be required to work the holidays?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>In most cases you will be required to work on holidays. Remember, travelers are hired to supplement the permanent staff and will be scheduled accordingly. However, it is possible to not be scheduled or work all holidays. If you are traveling around Thanksgiving and Christmas holidays, the healthcare facility may let you have one of the holidays off but not all. Please check with your Recruiter on the holiday requirements. </p>

                              
                           </div>
                        </div>
                     </li>

                     <li class="accordion block">
                        <div class="acc-btn">Should I take time off between assignments as a travel nurse?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>It's up to you! You can go right into another assignment or you can take time off between assignments. This all depends on if you have any upcoming days you would like off or you just simply need a break. Additionally, you may also have to consider how soon the next assignment wants you to start. If they want you to start immediately, taking time off between assignments may not be doable. If this is the case, request that the start date be pushed back to accomodate the days you may need off. If you are still unsure, reach out to your Recruiter or give us a call here at Travel Nurse 911. </p>

                              
                           </div>
                        </div>
                     </li>

                     <li class="accordion block">
                        <div class="acc-btn">How soon will I need to provide my Requested Time Off (RTO)?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>It is best to provide your Requested Time Off (RTO) before you are submitted to an assignment. This way, your Recruiter can present this information to the hiring manager at the time you are submitted. Once your time off is approved, it should be confirmed in writing in your travel agreement. Please also discuss your time off requirements during your interview so that all bases are covered. </p>

                              
                           </div>
                        </div>
                     </li>

                     <li class="accordion block">
                        <div class="acc-btn">If I have worked at the facility before, will I need to orient again?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>This depends on the facility, and the time you last worked at the facility. Some facilities require that you re-orient if you take any break in employment while others may not require you to orient if it has been less than 30 days since you last worked at the facility. Most facilities will require that you re-orient if you have been away for more than 30 days. Please check with your Recruiter to confirm requirements. </p>

                              
                           </div>
                        </div>
                     </li>
                    
                    <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn"> How much are referral bonuses? <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>Referral bonuses can range from the low end of $500 to $1,500 depending on the type of referral, specialty, profession and many more factors. Each agency is different and we highlighy encourage you to reach out to agency personnel to confirm their referral bonus amounts. </p>
                           </div>
                        </div>
                     </li>
                     
                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn"> Will the agency assist me with housing? <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>If you decide to travel and accept the company paid housing, your Recruiter or their assistant will most definitely assist you with all of your housing needs. If you decide to find your own housing, this is where the agency’s assistance is limited to referring you to potential housing leads or referring you to a housing website. Since the advent of sites like Airbnb, finding your own housing has never been easier. </p>
                           </div>
                        </div>
                     </li>
                     
                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn"> Can I switch my assignment from my current agency to another agency? <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>If your current agency is just not cutting it for you, not honoring their commitment, or you simply want a change, there is a possibility you can switch your assignment to another agency. <b>This must be done at the end of your current assignment.</b> The hospital wants to retain great travelers and just wants them to be happy and most will do whatever it takes to accommodate such requests. I would avoid agencies that have it written in their agreements that you can’t do this because this is not traveler friendly and encourage you to look for traveler friendly agencies and hospitals to ensure a great experience. </p>
                           </div>
                        </div>
                     </li>
                     
                     
                     
                    
                    
                    
                  </ul>
               </div>

               <!--Compliance Tab-->
               <div class="tab" id="tab3">
                  <ul class="accordion-box">
                     <!--Block-->
                     <li class="accordion block active-block">
                        <div class="acc-btn active">How long does compliance take to complete? <span class="icon flaticon-add"></span></div>
                        <div class="acc-content current">
                           <div class="content">
                              <p>The time it takes you to complete your compliance documents largely depends on you and on other factors as well. The faster you send your documents, the faster the compliance process will be. Once you receive the compliance requirements, begin sending in documents. You can also save your documents to your Credential Safe and have them ready to send or share as soon as you receive the offer and/or compliance requirements.</p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block active-block">
                        <div class="acc-btn active">What is required for the certifications on file in my Document Safe?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>The certifications you have on file should be current and up to date. Please delete or remove all expired certifications and upload new certifications in their place. This will ensure a smooth compliance process when you are onboarding for a new position. </p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block active-block">
                        <div class="acc-btn active">Why do I have to send my compliance docs to so many companies?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>Compliance documents should only be sent to the company you are onboarding with for a new assignment. We recommend that you do not give access to any company requesting your documents until you have signed a travel agreement and/or agreed to an assignment with that company. Your Document Safe allows you to give and restrict access to any company requesting your compliance documents on file. </p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block active-block">
                        <div class="acc-btn active">Why do I have to complete so many skills checklists?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>Travel Nurses are required to complete several checklists if they are working with several agencies. This is because each agency has their own skills checklists and if you are applying to multiple positions, you will be required to complete a skills checklist for each of those positions, unless your Recruiter is willing to accept another skills checklist from another agency. Once you complete a skills checklist on Travel Nurse 911, you can use this one checklist and send to several companies because our checklists are non-branded and approved by healthcare facilities nationwide. </p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block active-block">
                        <div class="acc-btn active">Why do I have to complete so many exams?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>Travel Nurses are required to complete several exams if they are working with several agencies. These exams are used to qualify you for the position as per the agency's compliane requirements. Exams are normally not issued until after you have been offered a position, however, some companies require that the exams are completed before you can be submitted to a position. </p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block active-block">
                        <div class="acc-btn active">What is the Compliance Deadline?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>The compliance deadline is normally set the Wednesday prior to your start date. All compliance items will be needed on file so that your agency can send to the facility by the compliance deadline date. If compliance documents are not received by this date, your start date could potentially be pushed back to the next available orientation date. Please check with your Recruiter or Compliance Specialist to confirm. </p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block active-block">
                        <div class="acc-btn active">What compliance documents will I need to submit to complete onboarding?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>"You will be sent a list by your agency's Compliance Specialist, but a general list is listed below: <br>
                                  Health Records (Physical, PPD, Immunization Record) <br>
                                  Certifications, if applicable, to include BLS/CPR, PALS, NIHSS, NRP or ACLS <br>
                                  Drug Screen consent <br>
                                  Background Check consent <br>
                                  Any facility required documents to e-sign"</p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn"> Once I am Offered & Accept the assignment, how will I send in my credentials and to whom?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>Once you have been offered the assignment and accepted, you will be immediately contacted by your agency's Compliance Specialist via email with onboarding instructions. These instructions will include a list of compliance documents that will be needed for your assignment. The Compliance Specialist will work with your directly to ensure all of your documents are submitted to the facility before the compliance deadline. You can also upload your documents to your Travel Nurse 911 credential safe and share with your Recruiter to expedite the onboarding process.</p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn">How long do I have to submit the required compliance documents?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>Generally, your compliance documents are due on the Wednesday before your assignment start date. The quicker your assignment starts, the quicker you will need to complete the compliance process. No worries, because your Compliance Specialist will be there to help you each step of the way.</p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn">What if I miss the deadline to submit all required compliance documents?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>If you miss the compliance deadline to have all of your documents submitted to your Compliance Specialist, your start date will be pushed back until the next orientation. In most cases, you will be pushed back one week and others up to two weeks.</p>

                              
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn">When will I receive First Day Reporting instructions?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>You will receive your First Day Instructions (FDI) once you have been cleared to start your assignment. This typically happens once you have submitted all of your required compliance documents. As soon as you are cleared, you will receive a welcome email from your Compliance Specialist along with the FDIs and timesheet.</p>

                              
                           </div>
                        </div>
                     </li>

                     

                     

                     

                  </ul>
               </div>
               
               <!--Assignment Tab-->
               <div class="tab" id="tab4">
                  <ul class="accordion-box">
                     <!--Block-->
                     <li class="accordion block active-block">
                        <div class="acc-btn active">What is my assignment is canceled? <span class="icon flaticon-add"></span></div>
                        <div class="acc-content current">
                           <div class="content">
                              <p>Several factors such as: <br>
o	Low Census<br>
o	Filled internally<br>
o	Attendance issues<br>
o	Behavior issues<br>
o	Clinical issues<br>
o	Budgeting issues</p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn">How long does it take to get an assignment?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>It can take from 24 hours to 4 plus weeks to get an assignment depending on several factors. Many travelers assume that once they apply for a position that they will be submitted, however, this is almost never the case. Before you can be submitted to an assignment you must have a Submission File ready to be submitted to the facility at the time of submission. These submission files include your resume/work history, skills checklist, references and a license verification in most cases. Once the agency or healthcare facility has obtained these documents, your submission file is then submitted to the facility. Now it's up to your Recruiter to submit you in a timely manner to newly opened positions. If you have a Recruiter that immediately submits you to open positions, you can get a position in as little as 24 hours depending on the availability of positions. If you have a Recruiter that takes a bit more time to get you submitted, then you may experience a delay in getting a position. To ensure a timely submission, make sure you have all of your submissions documents on file and you are working with a Recruiter that is, "On it"! </p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn">Can I work 48 hours per week guaranteed?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>Yes, this is possible if your desire is discussed with your Recruiter, during your interview and written in your agreement. Working 48 hours per week is not always available as hospitals are watching their budgets and agency usage. However, if there is a need, the hiring manager may agree to this, but please don’t present this as a deal breaker because it may just break the deal. If there is not a 48 hour per week guarantee and OT is available, try to work as much OT as possible.</p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn">Can I get 48 hours guaranteed written in my agreement?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>Yes, if working 48 hours per week was confirmed with your Recruiter and/or during your interview, you can request that this is written into your agreement. </p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn"> What should I do if I need to end my assignment early? <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>This depends on why you need to end your assignment early, as it may put a great strain on the unit’s ability to have adequate staffing levels. If you need to end your assignment early for personal reasons, please discuss with your Recruiter first. The agency will notify the facility. Please check your agreement for any cancellation fees that may be occurred. </p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn">If I accept a position and then it is canceled what do I do? <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>Continue to work with your Recruiter to assess your options and secure another assignment. </p>
                           </div>
                        </div>
                     </li>
                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn">If I am offered a position when will the start date be?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>The start date depends on your availability and the facility’s orientation schedule. Your required compliance documents can also be a factor. If you are available when the facility has it’s next orientation, the start date should align depending on how quickly you can complete the compliance requirements. </p>

                              
                           </div>
                        </div>
                     </li>
                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn">What if my rate drops while I am on assignment?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>This is rare to almost never happens, but if it does occur you should speak with management at the agency as soon as possible to see why this occurred. If you signed an agreement, the rate that is listed in your agreement should be honored throughout the duration of your assignment. </p>

                              
                           </div>
                        </div>
                     </li>
                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn">If I don’t feel comfortable floating to unit I am not assigned, what do I do?  <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>You should contact your Recruiter as soon as possible or a Clinical Liaison at the agency. Let them know that you are uncomfortable and ask them to address these concerns directly with the hospital on your behalf. Please note that floating to like units are common in this industry and if you are being floated to a like unit, this means your skills meet the requirements of the unit and you will be required to float or risk having your assignment cancelled. </p>

                             
                           </div>
                        </div>
                     </li>
                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn">If I receive too many patients on a shift, what do I do? <span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>If your patient load exceeds expectations, please discuss directly with the Charge Nurse or Nurse Manager. All units have set expectations on nurse to patient ratios but this can fluctuate depending on the census of the hospital at that particular time. You should not expect to have a certain amount of patients and should be ready to assist when needed if the census goes above expected levels. </p>

                              
                           </div>
                        </div>
                     </li>

                    

                  </ul>
               </div>
               

               <!--Qualifications Tab-->
               <div class="tab" id="tab5">
                  <ul class="accordion-box">
                     <!--Block-->
                     <li class="accordion block active-block">
                        <div class="acc-btn active">How many years of experience do I need to travel? <span class="icon flaticon-add"></span></div>
                        <div class="acc-content current">
                           <div class="content">
                              <p>You will need a minimum of 1 year of “on the job experience” as staff to be considered for a travel position. Most facilities require a minimum of 2 years of experience, but if you only have 1 year, highlight your current experience, accomplishments and goals so that you can stand out amongst the more experienced candidates.</p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn"> Do I need to have travel experience?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>You are not required to have travel experience with all assignments, however, some facilities will require that you have travel experience to be considered for their positions. If you do not have the required travel experience, highlight your experience, accomplishments and goals as best you can on your resume so that you can stand out amongst the travelers with travel experience.</p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn">Will I receive higher pay for having more experience?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>Most agencies and facilities with an internal travel program pay their travelers equally and according to their specialty. As long as you meet the minimum requirements most companies will quote the same rate regardless of experience while some companies do reward those travelers with more experience a higher wage. While this is not common, it does exist. </p>
                           </div>
                        </div>
                     </li>
                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn">How many years of experience do I need to have to qualify for this position?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>You will need a minimum of 1 year of “on the job experience” as staff to be considered for a travel position as this is a requirement for most companies. Most facilities require a minimum of 2 years of experience but if you have 1 year of experience, make sure you highlight your current experience, accomplishments and goals so that you can stand out amongst the experienced candidates.</p>

                              
                           </div>
                        </div>
                     </li>
                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn">Will I receive a job description for this position?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>Yes, you should receive a job description from your agency Recruiter when you receive the initial details. If you do not receive this information and need a job description to have absolute clarity of the position, please reach out to your Recruiter. </p>

                              
                           </div>
                        </div>
                     </li>

                     


                  </ul>
               </div>

               <!--Assignment section Tab-->
               <div class="tab" id="tab6">
                  <ul class="accordion-box">
                     <!--Block-->
                     <li class="accordion block active-block">
                        <div class="acc-btn active">How many hours can I work each week? <span class="icon flaticon-add"></span></div>
                        <div class="acc-content current">
                           <div class="content">
                              <p>You are required to work the number of hours confirmed in your agreement. Most agreements will have a minimum of 36 hours you can work each week. However, you can work more than 36 hours per week, if needed. Contact your Manager, Charge Nurse or whoever is responsible for scheduling and let them know you are available to work more than 36 hours per week "if needed." This is how you get OT if your schedule and the facility's budget allows for it. </p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn"> Is there OT Available?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>If the facility is offering OT as a 48 hour guarantee then OT is absolutely available. If you are confirmed on 36 hours per week or 40 hours per week and available for OT,  please inform your Manager, Charge Nurse or whoever is responsible for making your weekly schedules. In some cases, OT must be approved with Management so please check with them first.</p>
                           </div>
                        </div>
                     </li>

                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn">How many weeks can I work on an assignment (Minimum & Maximum)?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>The number of weeks offered on an assignment depends on the needs of the facility. Most facilities post their positions for 12 weeks or more. During the pandemic,  more assignments were offered for 6 weeks and 8 weeks.  </p>
                           </div>
                        </div>
                     </li>
                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn">How long can I work on one assignment?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>Typically, you can work up to 1 year, but as a traveler you will need to take a break after being on assignment for a 1 year continuously. You will need to do this so you can maintain your permanent tax home residence. After you have taken a break after 1 year, you can return to the same assignment. Your break should be anywhere from 1 month – 3 months, enough time for you to go home to ensure everything is being maintained at your permanent tax home residence. </p>

                              
                           </div>
                        </div>
                     </li>
                     <!--Block-->
                     <li class="accordion block">
                        <div class="acc-btn">How do I request time off?<span class="icon flaticon-add"></span></div>
                        <div class="acc-content">
                           <div class="content">
                              <p>Time Off is requested during the submission process. Please let your Recruiter know what days you would like to request off prior to being submitted. Also, please mention your time off request during your phone interview. This will ensure it is approved upon offer & acceptance. The facility will chose the candidate based upon experience and availability in most cases and most Requested Time Off is normally approved unless it is excessive. You may be required to make up any time you requested off.</p>

                              
                           </div>
                        </div>
                     </li>

                     


                  </ul>
               </div>
               
               <!--Agreement section Tab-->
               <!--<div class="tab" id="tab7">
                  <ul class="accordion-box">
                      <!--Block--
                      <li class="accordion block active-block">
                          <div class="acc-btn active">How long does compliance take to complete? <span class="icon flaticon-add"></span></div>
                          <div class="acc-content current">
                              <div class="content">
                                  <p>The time it takes you to complete your compliance documents largely depends on you and on other factors as well. The faster you send your documents, the faster the compliance process will be. Once you receive
                                      the compliance requirements, begin sending in documents. You can also save your documents to your Credential Safe and have them ready to send or share as soon as you receive the offer and/or compliance requirements.</p>
                              </div>
                          </div>
                      </li>

                      <!--Block--
                      <li class="accordion block">
                          <div class="acc-btn"> Once I am Offered & Accept the assignment, how will I send in my credentials and to whom?<span class="icon flaticon-add"></span></div>
                          <div class="acc-content">
                              <div class="content">
                                  <p>Once you have been offered the assignment and accepted, you will be immediately contacted by your agency's Compliance Specialist via email with onboarding instructions. These instructions will include a list
                                      of compliance documents that will be needed for your assignment. The Compliance Specialist will work with your directly to ensure all of your documents are submitted to the facility before the compliance
                                      deadline. You can also upload your documents to your Travel Nurse 911 credential safe and share with your Recruiter to expedite the onboarding process.</p>
                              </div>
                          </div>
                      </li>

                      <!--Block--
                      <li class="accordion block">
                          <div class="acc-btn">How long do I have to submit the required compliance documents?<span class="icon flaticon-add"></span></div>
                          <div class="acc-content">
                              <div class="content">
                                  <p>Generally, your compliance documents are due on the Wednesday before your assignment start date. The quicker your assignment starts, the quicker you will need to complete the compliance process. No worries,
                                      because your Compliance Specialist will be there to help you each step of the way.</p>
                              </div>
                          </div>
                      </li>

                      <!--Block--
                      <li class="accordion block">
                          <div class="acc-btn">What if I miss the deadline to submit all required compliance documents?<span class="icon flaticon-add"></span></div>
                          <div class="acc-content">
                              <div class="content">
                                  <p>If you miss the compliance deadline to have all of your documents submitted to your Compliance Specialist, your start date will be pushed back until the next orientation. In most cases, you will be pushed back
                                      one week and others up to two weeks.</p>


                              </div>
                          </div>
                      </li>

                      <!--Block--
                      <li class="accordion block">
                          <div class="acc-btn">When will I receive First Day Reporting instructions?<span class="icon flaticon-add"></span></div>
                          <div class="acc-content">
                              <div class="content">
                                  <p>You will receive your First Day Instructions (FDI) once you have been cleared to start your assignment. This typically happens once you have submitted all of your required compliance documents. As soon as you
                                      are cleared, you will receive a welcome email from your Compliance Specialist along with the FDIs and timesheet.</p>


                              </div>
                          </div>
                      </li>



                  </ul>
              </div>-->

              <!--Scheduling section Tab-->
              <div class="tab" id="tab8">
               <ul class="accordion-box">
                   <!--Block-->
                   <li class="accordion block active-block">
                       <div class="acc-btn active">Can I make my own schedule?<span class="icon flaticon-add"></span></div>
                       <div class="acc-content current">
                           <div class="content">
                               <p>Generally you cannot make your own schedule, however in some cases the Manager is totally fine with that. Please ask your Recruiter if the assignment allows for self-scheduling. In most cases this is not allowed and you will be scheduled according to the unit’s needs. But you never know, there is nothing wrong with asking.</p>
                           </div>
                       </div>
                   </li>

                   <!--Block-->
                   <li class="accordion block">
                       <div class="acc-btn">What hours will I be required to work?<span class="icon flaticon-add"></span></div>
                       <div class="acc-content">
                           <div class="content">
                               <p>You will be required to work a minimum of 36 - 40 hours per week or according to the number of hours listed in your agreement. Overtime is available at the discretion of the facility and needs of the unit. </p>
                           </div>
                       </div>
                   </li>

                   <!--Block-->
                   <li class="accordion block">
                       <div class="acc-btn">What shifts will I be required to work?<span class="icon flaticon-add"></span></div>
                       <div class="acc-content">
                           <div class="content">
                               <p>You will be required to work either (Days) 6:45 am – 7:15 pm or (Nights) 6:45 pm – 7:15am. You will not be required to work both shifts unless you are confirmed for a FLEX shift in which you may be required to work some DAYS and some NIGHTS planned accordingly with your schedule. FLEX shifts are not common and generally don't happen unless you offer to work FLEX shifts. If you are working the ER, at some facilities there is a MID shift, these shifts are typically 12pm - 12am, 1pm - 1am, 2pm - 2am or 3pm - 3am. </p>
                           </div>
                       </div>
                   </li>

                   <!--Block-->
                   <li class="accordion block">
                       <div class="acc-btn">Is call required for this assignment?<span class="icon flaticon-add"></span></div>
                       <div class="acc-content">
                           <div class="content">
                               <p>Please check with your Recruiter. Most assignments don't require call while others such as Operating Room, Recovery and other assignment do. If call is required, it should be included in your compensation email, offer email and discussed during your interview. </p>


                           </div>
                       </div>
                   </li>

                   <!--Block-->
                   <li class="accordion block">
                       <div class="acc-btn">If call is required, do I get paid for call back?<span class="icon flaticon-add"></span></div>
                       <div class="acc-content">
                           <div class="content">
                               <p>Yes, you will be paid the call back rate outlined in your agreement. This rate is normally paid at your OT rate. Please check with your Recruiter or review your agreement for these details. (Please see On-Call Rate in the Payroll Section) </p>


                           </div>
                       </div>
                   </li>

                   <!--Block-->
                   <li class="accordion block">
                     <div class="acc-btn">Are my hours guaranteed each week?<span class="icon flaticon-add"></span></div>
                     <div class="acc-content">
                         <div class="content">
                             <p>With most assignments, your hours are guaranteed each week. You will have 36 or 40 hours guaranteed each week, however this varies facility to facility. Please check with your Recruiter or refer to your travel agreement for all details. Please note that although it is not common, some assignments do not guarantee hours each week so please double check your agreement and confirm with your Recruiter if you are unsure.  </p>
                         </div>
                     </div>
                 </li>

                 <!--Block-->
                 <li class="accordion block">
                     <div class="acc-btn">What is the shift cancellation policy?<span class="icon flaticon-add"></span></div>
                     <div class="acc-content">
                         <div class="content">
                             <p>The general shift cancellation policy is that travelers can be canceled up to 12 hours (1 shift) during a 2 week period for low census, however, you will be allowed to make up this time at the end of your assignment, if needed. Each facility’s cancellation policy is different so please check with your Recruiter or refer to your agreement for the facility’s cancellation policy. </p>
                         </div>
                     </div>
                 </li>



               </ul>
           </div>

           <!-- Orientation Tab-->
           <div class="tab" id="tab9">
            <ul class="accordion-box">
                <!--Block-->
                <li class="accordion block active-block">
                    <div class="acc-btn active">Will I receive an orientation?<span class="icon flaticon-add"></span></div>
                    <div class="acc-content current">
                        <div class="content">
                            <p>Yes, you should receive a classroom orientation and floor orientation. Smaller facilities may not offer a classroom orientation but will offer floor orientations that should cover what you would have learned in the classroom. Special classes are held for those that need orientation on the facility’s EMR system in most cases. </p>
                        </div>
                    </div>
                </li>

                <!--Block-->
                <li class="accordion block">
                    <div class="acc-btn">How will I be paid for orientation?<span class="icon flaticon-add"></span></div>
                    <div class="acc-content">
                        <div class="content">
                            <p>At most travel agencies, most travelers are paid for orientation. Some agencies may offer you an orientation rate equivalent to your hourly rate while others may offer you a lower classroom orientation rate. Please check your agreement and with your Recruiter if you have any questions regarding your orientation rate. (See Payroll section regarding Orientation Rates) </p>
                        </div>
                    </div>
                </li>

                <!--Block-->
                <li class="accordion block">
                    <div class="acc-btn">How many hours are required for orientation?<span class="icon flaticon-add"></span></div>
                    <div class="acc-content">
                        <div class="content">
                            <p>Some facilities require 8 hours of orientation while other facilities may require up to 24 hours. Smaller facilities may offer fewer hours of orientation while larger facilities may require more. Please check with your Recruiter to confirm your orientation schedule. </p>
                        </div>
                    </div>
                </li>

                <!--Block-->
                <li class="accordion block">
                    <div class="acc-btn">Will I be scheduled for 36 hours during orientation week?<span class="icon flaticon-add"></span></div>
                    <div class="acc-content">
                        <div class="content">
                            <p>Orientation week is the only week you may not get all of your hours. For example, your orientation lasts for 8 hours x 1 day and work the next 2 days x 12 hour shifts. If this was the case you will only be paid for 32 hours during orientation week, however, post pandemic travelers are working 40+ hours during orientation week. After orientation is complete, you will definitely work your minimum or guaranteed hours each week.</p>


                        </div>
                    </div>
                </li>

                <!--Block-->
                <li class="accordion block">
                    <div class="acc-btn">If I have worked at the facility before, will I need to orient again? <span class="icon flaticon-add"></span></div>
                    <div class="acc-content">
                        <div class="content">
                            <p>This depends on how long it has been before you last worked at the facility. If you worked there within the last 30 days and just took a few weeks off, you will more than likely not need to orient again. If it has been greater than 30 days, the hospitals like to reorient these travelers because of new policies or procedures that may have been introduced. Please check with your Recruiter to find if you are required to reorient due to you having worked at the facility before. </p>


                        </div>
                    </div>
                </li>



            </ul>
        </div>


        <!--Facility & Unit tab-->
        <div class="tab" id="tab10">
         <ul class="accordion-box">
             <!--Block-->
             <li class="accordion block active-block">
                 <div class="acc-btn active">What is the facilities floating policy?<span class="icon flaticon-add"></span></div>
                 <div class="acc-content current">
                     <div class="content">
                         <p>All travelers are expected to float between units to some extent, but the idea of floating can make some clinicians uncomfortable. Asking up-front about floating expectations can alleviate that concern. Travel clinicians can expect to be floated only to units where they have experience. For instance, an ICU nurse may be asked to staff a step-down unit or a Med-Surg nurse going to a Rehab unit. If you are ever asked to float to a unit you do not feel comfortable with due to your skill set, please reach out to your agency and/or refer to the travel manual provided to you by your agency. </p>
                     </div>
                 </div>
             </li>

             <!--Block-->
             <li class="accordion block">
                 <div class="acc-btn">What does the facility expect from their travel nurses at this facility?<span class="icon flaticon-add"></span></div>
                 <div class="acc-content">
                     <div class="content">
                         <p>Most facilities that are using travelers expect competence, availability, and for you to exhibit team work by being a team player.</p>
                     </div>
                 </div>
             </li>

             <!--Block-->
             <li class="accordion block">
                 <div class="acc-btn">What EMR does this facility use?<span class="icon flaticon-add"></span></div>
                 <div class="acc-content">
                     <div class="content">
                         <p>This information will generally be included in the job posting details, when you are quoted and given the details of the job, or during the time of your Recruiter screening. Please check with your Recruiter for this information as it varies from facility to facility. You can also ask during your interview! </p>
                     </div>
                 </div>
             </li>

             <!--Block-->
             <li class="accordion block">
                 <div class="acc-btn">What are the scrub colors for this facility/unit?<span class="icon flaticon-add"></span></div>
                 <div class="acc-content">
                     <div class="content">
                         <p>This information will be given once you are cleared to start the assignment, however, in most cases Recruiters are able to give you this information as soon as you have been offered and accepted the assignment. Please check with your Recruiter and/or compliance representative for these details. You can also ask during your interview! </p>


                     </div>
                 </div>
             </li>

             <!--Block-->
             <li class="accordion block">
                 <div class="acc-btn">What is the patient to nurse ratio for this unit?<span class="icon flaticon-add"></span></div>
                 <div class="acc-content">
                     <div class="content">
                         <p>Please ask the Manager during your interview and you can also ask your Recruiter when you initially inquire about the position. Some agencies have this information readily available while others may not. This is a great interview question, please be sure to ask this question during your interview. </p>


                     </div>
                 </div>
             </li>

             <!--Block-->
             <li class="accordion block">
               <div class="acc-btn">How many beds does the unit have?<span class="icon flaticon-add"></span></div>
               <div class="acc-content">
                   <div class="content">
                       <p>Please ask the Manager during your interview and you can also ask your Recruiter when you initially inquire about the position. Some agencies have this information readily available, while others may not. This is a great interview question, please be sure to ask this question during your interview. </p>


                   </div>
               </div>
           </li>



         </ul>
     </div>
        
            
     <!--Submission,Interview & Offer tab-->
     <div class="tab" id="tab11">
      <ul class="accordion-box">
          <!--Block-->
          <li class="accordion block active-block">
              <div class="acc-btn active">When will I be submitted to the position?<span class="icon flaticon-add"></span></div>
              <div class="acc-content current">
                  <div class="content">
                      <p>You will be submitted to the position once you have expressed interested, prequalified and the following documents are collected. Prior to submitting, your Recruiter will need your Resume, Skills Checklist and References (Charge Nurse, Supervisor or Manager). This information makes up your submission file.</p>
                  </div>
              </div>
          </li>

          <!--Block-->
          <li class="accordion block">
              <div class="acc-btn">After I am submitted, how long does it take to be interviewed?<span class="icon flaticon-add"></span></div>
              <div class="acc-content">
                  <div class="content">
                      <p>Most agencies work around the clock to ensure you are interviewed as soon as possible. Once submitted, you will be interviewed within 24 – 48 hours of submission if the Manager is interested. If you have not received a call within the first 24 – 48 hours please don’t panic, the Managers are very busy. If this is the case, The agency Account Manager will reach out for an update and push them to call you as soon as possible. Please reach out to your Recruiter if you have not been contacted by the hiring Manager within 24 – 48 hours.</p>
                  </div>
              </div>
          </li>

          <!--Block-->
          <li class="accordion block">
              <div class="acc-btn">Who will call me for the interview?<span class="icon flaticon-add"></span></div>
              <div class="acc-content">
                  <div class="content">
                      <p>The Hiring Manager will call you for the interview. Please look out for the area code of the region you are looking to travel. This will probably be the Unit Manager or hiring Manager calling to interview. Good Luck! </p>
                  </div>
              </div>
          </li>

          <!--Block-->
          <li class="accordion block">
              <div class="acc-btn">Will I receive an offer after the interview?<span class="icon flaticon-add"></span></div>
              <div class="acc-content">
                  <div class="content">
                      <p>After your interview you should receive an offer if the interview went well. The hiring Manager will extend an offer after your interview OR will notify HR to send your agency an offer for you.  After your interview please notify your Recruiter immediately so that they can follow up to get your official offer.</p>


                  </div>
              </div>
          </li>

          <!--Block-->
          <li class="accordion block">
              <div class="acc-btn">How soon will I need to provide my Requested Time Off (RTO)?<span class="icon flaticon-add"></span></div>
              <div class="acc-content">
                  <div class="content">
                      <p>You are encouraged to provide any potential RTO prior to submission. This way the hiring Manager will know if your RTO can be honored prior to making you an offer. If you provide RTO after the offer, the hiring manager reserves the right to offer to another candidate due to this not being disclosed prior to submission. </p>


                  </div>
              </div>
          </li>



      </ul>
  </div>

      <!--Payroll Tab-->
      <div class="tab" id="tab12">
         <ul class="accordion-box">
             <!--Block-->
             <li class="accordion block active-block">
                 <div class="acc-btn active">When is Pay Day?<span class="icon flaticon-add"></span></div>
                 <div class="acc-content current">
                     <div class="content">
                         <p>Pay day is weekly, every Friday at most companies. Please check with your Recruiter or Payroll Coordinator to confirm your agency's pay day. </p>
                     </div>
                 </div>
             </li>

             <!--Block-->
             <li class="accordion block">
                 <div class="acc-btn">How do I view my paycheck stubs?<span class="icon flaticon-add"></span></div>
                 <div class="acc-content">
                     <div class="content">
                         <p> Most companies have links they can send to their travelers with access to their payroll portal. Please check with your Recruiter or Payroll Coordinator for this information.</p>
                     </div>
                 </div>
             </li>

             <!--Block-->
             <li class="accordion block">
                 <div class="acc-btn">When will I be paid the travel reimbursement?<span class="icon flaticon-add"></span></div>
                 <div class="acc-content">
                     <div class="content">
                         <p> Please check with your Recruiter or Payroll Coordinator to confirm these details.</p>
                     </div>
                 </div>
             </li>

             <!--Block-->
             <li class="accordion block">
                 <div class="acc-btn">How will my car/auto allowances be paid?<span class="icon flaticon-add"></span></div>
                 <div class="acc-content">
                     <div class="content">
                         <p> Please check with your Recruiter or Payroll Coordinator to confirm these details.</p>


                     </div>
                 </div>
             </li>

             <!--Block-->
             <li class="accordion block">
                 <div class="acc-btn">Should I receive mileage reimbursement?<span class="icon flaticon-add"></span></div>
                 <div class="acc-content">
                     <div class="content">
                         <p> Please check with your Recruiter or Payroll Coordinator to confirm these details.</p>


                     </div>
                 </div>
             </li>

             <!--Block-->
             <li class="accordion block">
               <div class="acc-btn">How do I submit my timesheets each week?<span class="icon flaticon-add"></span></div>
               <div class="acc-content">
                   <div class="content">
                       <p> Please check with your Recruiter or Payroll Coordinator to confirm these details. </p>
                   </div>
               </div>
           </li>

           <!--Block-->
           <li class="accordion block">
               <div class="acc-btn"> What is the deadline to submit timesheets each week?<span class="icon flaticon-add"></span></div>
               <div class="acc-content">
                   <div class="content">
                       <p> Please check with your Recruiter or Payroll Coordinator to confirm these details. </p>
                   </div>
               </div>
           </li>

           <!--Block-->
           <li class="accordion block">
               <div class="acc-btn">Are timesheets required to be signed each week?<span class="icon flaticon-add"></span></div>
               <div class="acc-content">
                   <div class="content">
                       <p>Some facilities require that you complete a weekly paper timesheet while others require that you use thier electronic timekeeping system. If you are required to complete a weekly timesheet, please do your best to get it signed each week. Example of acceptable signatures are from Charge Nurses, Managers, or Supervisors. Please check with your Manager on the appropriate person to sign your weekly timesheets. If you are working at a facility that is using an electronic clocking in and out system you may not be required to get your timesheets signed each week. Your time will automatically be submitted to your agency by the facility.  Please check with your Recruiter or Payroll Coordinator to confirm these details. </p>
                   </div>
               </div>
           </li>

           <!--Block-->
           <li class="accordion block">
               <div class="acc-btn">What is an On-Call Rate?<span class="icon flaticon-add"></span></div>
               <div class="acc-content">
                   <div class="content">
                       <p>The on-call rate is the rate that you are given to take call for positions such as Operating Room, Recovery Room, Endoscopy and similar positions. Taking call requires you to leave the facility and if you are called in during your call hours, this will be considered call back and you should be entitled to call back pay. The on-call rate varies from agency to agency and from assignment to assignment. Please check with your Recruiter or Payroll coordinator to confirm these details. </p>


                   </div>
               </div>
           </li>

           <!--Block-->
           <li class="accordion block">
               <div class="acc-btn">What is a Call Back Rate?<span class="icon flaticon-add"></span></div>
               <div class="acc-content">
                   <div class="content">
                       <p>The call back rate is the rate that you are given if you are called back in from working an on-call shift. This rate is normally paid at time and a half of your regular rate. Please check with your Recruiter or Payroll coordinator to confirm these details.</p>


                   </div>
               </div>
           </li>

           <!--Block-->
           <li class="accordion block">
            <div class="acc-btn">What is my Orientation Rate? <span class="icon flaticon-add"></span></div>
            <div class="acc-content">
                <div class="content">
                    <p>Your orientation rate is the rate some agencies provide while being oriented. This orientation normally consists of classroom orientation (non-patient care). For classroom orientation, some agencies  pay a lower rate. However, since COVID, many agencies did away with the orientation rate and paid regular rates no matter what. Please check with your Recruiter or Payroll coordinator to confirm these details.</p>
                </div>
            </div>
        </li>

        <!--Block-->
        <li class="accordion block">
            <div class="acc-btn">Am I reimbursed for scrubs on the assignment?<span class="icon flaticon-add"></span></div>
            <div class="acc-content">
                <div class="content">
                    <p>If a facility requires a specific color that you do not have, it is your responsibility as a traveler to obtain the appropriate uniform to meet the requirements. However, some agencies offer reimbursements on scrubs. Please check with your agency to confirm if you will or can be reimbursed for purchasing scrubs for the assignment. </p>
                </div>
            </div>
        </li>

        <!--Block-->
        <li class="accordion block">
            <div class="acc-btn">Will I receive a W2 at the end of the year?<span class="icon flaticon-add"></span></div>
            <div class="acc-content">
                <div class="content">
                    <p>Yes! Please check with your Recruiter or agency Payroll Coordinator. </p>
                </div>
            </div>
        </li>

        <!--Block-->
        <li class="accordion block">
            <div class="acc-btn">When will I receive my W2?<span class="icon flaticon-add"></span></div>
            <div class="acc-content">
                <div class="content">
                    <p>These are normally sent at the beginning of each calendar year, usually by the end of January. Please check with your Recruiter or agency Payroll Coordinator. </p>


                </div>
            </div>
        </li>

        <!--Block-->
        <li class="accordion block">
            <div class="acc-btn">Will my W2 come to my temporary or permanent address?<span class="icon flaticon-add"></span></div>
            <div class="acc-content">
                <div class="content">
                    <p>These will more than likely be mailed to your permanent address on file as this signifies your permanent address. Please check with your Recruiter or agency Payroll Coordinator to confirm and see if you can use your temporary address. </p>


                </div>
            </div>
        </li>

        <!--Block-->
        <li class="accordion block">
         <div class="acc-btn">How can I change my address?<span class="icon flaticon-add"></span></div>
         <div class="acc-content">
             <div class="content">
                 <p>Please check with your Recruiter or agency Payroll Coordinator. </p>


             </div>
         </div>
     </li>



         </ul>
     </div>


      <!--Insurance Tab-->
      <div class="tab" id="tab14">
         <ul class="accordion-box">
             <!--Block-->
             <li class="accordion block active-block">
                 <div class="acc-btn active">Do agencies offer medical, dental and vision insurance?<span class="icon flaticon-add"></span></div>
                 <div class="acc-content current">
                     <div class="content">
                         <p>As a matter of fact, the majority of agencies do offer medical, dental and insurance benefits. Please check with your Recruiter or Benefits Coordinator for more information on their medical benefits.</p>
                     </div>
                 </div>
             </li>



         </ul>
     </div>
     

     <!--Agecny Cinical support Tab-->
     <div class="tab" id="tab15">
       <ul class="accordion-box">
          <!--Block-->
          <li class="accordion block active-block">
              <div class="acc-btn active">Do agencies offer their travelers Clinical support?<span class="icon flaticon-add"></span></div>
              <div class="acc-content current">
                  <div class="content">
                      <p>Many agencies have a Clinical Resource available to speak with you 24
                        hours a day/7 days a week. If you have any clinical concerns or concerns at all with patient safety, your safety, any concerns with the facility or staff please notify your Recruiter as soon as possible and the agency's Clinical Resource will give you a call as soon as possible. Please refer to your welcome letter or travel agency's guide if one is available.</p>
                      </div>
                 </div>
            </li>



         </ul>
      </div>
  
  <!--Extension Tab-->
  <div class="tab" id="tab16">
    <ul class="accordion-box">

       <!--Block-->
       <li class="accordion block active-block">
           <div class="acc-btn active">Can I extend my current assignment?<span class="icon flaticon-add"></span></div>
           <div class="acc-content current">
               <div class="content">
                   <p>Yes, you sure can! If you are interested in extending your current assignment, [lease let your Manager know and notify your Recruiter as well. Tell your Manager that you have enjoyed the assignment and you would love to extend. Once you notify your Recruiter, they will notify the facility and hopefully receive an official offer for you to extend your assignment. Congrats!</p>
                  </div>
              </div>
         </li>

       <!--Block-->
        <li class="accordion block">
         <div class="acc-btn">Can I extend my current assignment for a shorter or longer term?<span class="icon flaticon-add"></span></div>
          <div class="acc-content">
             <div class="content">
                 <p>Yes, you can extend your assignment for 1 – 13 weeks depending on your availability and what the facility is willing to approve. Just let your Manager and Recruiter know and they will work it out and keep you posted on all the details. </p>
             </div>
          </div>
       </li>

      </ul>
   </div>


<!--Per DIEM/PRN Tab-->
<div class="tab" id="tab17">
   <ul class="accordion-box">
       <!--Block-->
       <li class="accordion block active-block">
           <div class="acc-btn active">What if my shift is canceled?<span class="icon flaticon-add"></span></div>
           <div class="acc-content current">
               <div class="content">
                   <p>If you shift is canceled, your scheduler or coordinator will do their best to get you rebooked or another shift later in the week. If your shift is cancelled, please do not report to work and if you are unsure, please check with your agency or hospital as soon as possible to receive clarification on what you should do. </p>
               </div>
           </div>
       </li>



   </ul>
</div>


<!--Interview Tab-->
<div class="tab" id="tab18">
   <ul class="accordion-box">
       <!--Block-->
       <li class="accordion block active-block">
           <div class="acc-btn active">Can I decline the position after interviewed and offered?<span class="icon flaticon-add"></span></div>
           <div class="acc-content current">
               <div class="content">
                   <p>Yes! The interview is to learn what is expected of you if you accept the position and a chance to ask any questions. If you learn of something that makes you feel uncomfortable or gives you pause during this process, please ask additional questions for clarity or decline the position immediately. You want to be as sure and confident as possible when accepting an offer and being uncomfortable may be a signal that this position may not be for you.</p>
               </div>
           </div>
       </li>



   </ul>
</div>


   <!--Bonuses Tab-->
  <div class="tab" id="tab20">
   <ul class="accordion-box">

       <!--Block-->
       <li class="accordion block active-block">
           <div class="acc-btn active">How much are sign on & extension bonuses?<span class="icon flaticon-add"></span></div>
           <div class="acc-content current">
               <div class="content">
                   <p>Bonuses can range from $100 to $1,000 plus for travel assignments. Bonuses are paid from the budget of the travel assignment. Bonuses come directly from your wage potential. For example, if an agency has a budget of $10,000 to pay for a travel assignment and you are requesting a $1,000 sign on or extension bonus, then the agency will only have $9,000 of the budget remaining to pay your wages. If you don't get a sign- on bonus, you could possibly get a higher wage. However, some bonuses may be needed to assist in paying for travel expenses. If you need it please request it, but don't expect the highest pay package. </p>
               </div>
           </div>
       </li>

       <!--Block-->
       <li class="accordion block">
         <div class="acc-btn">Do I receive a bonus for doing my compliance?<span class="icon flaticon-add"></span></div>
         <div class="acc-content">
             <div class="content">
                 <p>Some agencies offer bonuses for completing your compliance. This is the hiring process, and like any other job, it is the traveler's responsibility to complete all compliance requirements whether a bonus is offered or not. Please check with your Compliance Specialist to see if your agency offers a compliance bonus.</p>
             </div>
         </div>
     </li>

     <!--Block-->
     <li class="accordion block">
      <div class="acc-btn">How much are referral bonuses?<span class="icon flaticon-add"></span></div>
      <div class="acc-content">
          <div class="content">
              <p>Referral bonuses can range from $100 - $2,000 plus depending on the agency that is offering the bonus. </p>
          </div>
      </div>
  </li>

  <!--Block-->
  <li class="accordion block">
    <div class="acc-btn">What is a referral bonus?<span class="icon flaticon-add"></span></div>
    <div class="acc-content">
        <div class="content">
            <p>A referral bonus is a bonus provided by the agency for referring a traveler to the agency. </p>
         </div>
       </div>
   </li>

<!--Block-->
      <li class="accordion block">
         <div class="acc-btn">When will I receive my referral bonus? <span class="icon flaticon-add"></span></div>
           <div class="acc-content">
             <div class="content">
           <p>Referral bonuses are paid once the traveler you referred satisfies a certain number of hours worked or completes a certain number of weeks on assignment. Some agencies pay after 30 days of being on assignment, while others may pay these referral bonuses at the end of the assignment.</p>
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