<?php if (isset($cur_page) && ($cur_page == 'homepage')) { ?>
   <header class="main-header">
      @include('layouts.menu')
   </header>
<?php } else { ?>
   <span class="header-span"></span>
   <header class="main-header header-shaddow">
      <div class="container-fluid">
         @include('layouts.menu')
      </div>
   </header>
<?php } ?>