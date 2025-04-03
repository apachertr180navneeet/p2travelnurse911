<?php

use App\Helper\CommonFunction;
?>

@extends('layouts.app')

@section('description',
    'Discover the benefits of nursing compact states! Learn how a compact license can open doors to more travel nursing opportunities and simplify your career on the go.')

@section('content')

<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>Nursing License Compact State List</h1>
      </div>
   </div>
</section>
<!--End Page Title-->

<?php
$states = [
   "Alabama",
   "Arizona",
   "Arkansas",
   "Colorado",
   "Connecticut – NLC enacted: Awaiting implementation",
   "Delaware",
   "Florida",
   "Georgia",
   "Guam",
   "Idaho",
   "Indiana",
   "Iowa",
   "Kansas",
   "Kentucky",
   "Louisiana",
   "Maine",
   "Maryland",
   "Mississippi",
   "Missouri",
   "Montana",
   "Nebraska",
   "New Hampshire",
   "New Jersey",
   "New Mexico",
   "North Carolina",
   "North Dakota",
   "Ohio",
   "Oklahoma",
   "Pennsylvania",
   "Rhode Island",
   "South Carolina",
   "South Dakota",
   "Tennessee",
   "Texas",
   "Utah",
   "Vermont",
   "Virginia",
   "Virgin Islands – NLC enacted: Awaiting implementation",
   "Washington",
   "West Virginia",
   "Wisconsin",
   "Wyoming"
];

?>

<style>
   table {
      width: 100%;
      border-collapse: collapse;
   }

   th,
   td {
      padding: 8px;
      text-align: center;
      border: 1px solid #ddd;
      width: 33%;
      vertical-align: middle;
   }
</style>

<section class="job-categories border-bottom-0">
   <div class="small-container">
      <div class="sec-title text-center">
         <h2 class="color-style-1">Nursing License Compact State List</h2>
      </div>

      <?php
      echo "<table class='table table-bordered' >";

      for ($i = 0; $i < count($states); $i++) {
         if ($i % 3 == 0) {
            echo "<tr>";
         }

         echo "<td >" . $states[$i] . "</td>";

         if ($i % 3 == 2) {
            echo "</tr>";
         }
      }

      if ($i % 3 != 0) {
         echo "</tr>";
      }

      echo "</table>";
      ?>
   </div>
</section>

@endsection