<?php

use Carbon\Carbon;
use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('title', 'Our Story | Travel Nurse 911')

@section('content')
<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>{{ $title }}</h1>
      </div>
   </div>
</section>
<!--End Page Title-->

<!-- Contact Section -->
<section class="about-section-three">
   <div class="auto-container">
      <div class="text-box">
         <p>
            Travel Nurse 911 was founded by a Travel Nurse, Shirley and two retired Nurse Recruiters, Pete and Alex. The name Travel Nurse 911 reflects the urgency and care involved in travel nursing. We created this platform to provide service to nurses by showing compassion and ensuring a quick match between the best travel nurses and the right jobs with suitable companies.
         </p>
         <p class="mb-3">
            This partnership emerged from the necessity of Shirley, the Travel Nurse seeking an assignment and the Recruiter, Pete looking to make a placement to fill an urgent need for a client. 
         </p>

         <p class="mb-3">
            Shirley experienced challenges while completing numerous skills checklists with various agencies. Each time she applied for a job, a different Recruiter would request yet another skills checklist. She had completed four of these checklists, which could take over 20 minutes each. Working 12-hour shifts, five days a week, she had to search for her next assignment during her days off. Additionally, Pete asked for two references, which she had already provided to other agencies that had contacted them multiple times within a two-week span.
         </p>

         <p class="mb-3">
            Pete explained to Shirley that he needed these details to compile her submission file, which is essentially her resume, showcasing her skills and references.
         </p>

         <p class="mb-3">
            Shirley shared her concerns with Pete, emphasizing that the process needed to change. She highlighted her frustrations, which included:
         </p>

         <ul class="mb-3 list-style-four">
            <li> Completing applications with multiple agencies</li>
            <li> Filling out several skills checklists for various agencies</li>
            <li> Having her references contacted by numerous agencies</li>
            <li> Sending documents to multiple agencies</li>
         </ul>

         <p class="mb-3">
            She pointed out that this situation was unfair to travel nurses and advocated for a more efficient process.
         </p>

         <p class="mb-3">
            In response, Pete reached out to a more experienced colleague, Alex, for advice on addressing the Travel Nurse’s frustrations. However, Alex agreed with the nurse's perspective and suggested they speak with her to discuss improvements for Travel Nurses and Recruiters.
         </p>

         <p class="mb-4">
            Several years later, Travel Nurse 911 was born. By Shirley advocating for travel nurses, we created an automated system that allows travel nurses to complete one submission file where they can securely store their documents. This system enables nurses to find a job, create a submission file, and send it to their Recruiter all in one place. Gone are the days of travel nurses having to complete multiple applications, skills checklists, and references contacted by several recruiters.
         </p>

         <h5 class="mb-2">How have we solved the problem for Recruiters?</h5>
         <p>
            Two of our founders were recruiters and understand the daily challenges of the role. As Recruiters, our goal is to build meaningful connections with qualified travel nurses, leading to successful placements. Travel Nurse 911 facilitates these connections and provides the tools necessary for quick placements.
         </p>

         <h5 class="mb-2">How have we made it easier to be a Travel Nurse?</h5>
         <p>
            One of our founders has personally worked multiple assignments as a Travel Nurse. Together with the Recruiters help, we have reimagined the process to simplify it. New travelers often duplicate their efforts by completing multiple skills checklists, repeatedly sending the same resume, and submitting documents to several agencies. Travel Nurse 911 solves this issue by allowing nurses to create one submission packet that includes their resume, skills checklist, and references for submission to Recruiters. We also offer a secure document safe for sending requested documents to Recruiters.
         </p>
      </div>
   </div>
   </div>
</section>

@endsection