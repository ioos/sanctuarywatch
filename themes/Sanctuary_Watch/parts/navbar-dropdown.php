<!--
TODO: WebCrs Dropdown, need to grab all locations and put in dropdown
TEMP SOLUTION - HARDCODE LOCATION
TODO: NEED TO FIND WAY TO DYNAMICALLY QUERY DATABASE FOR LOCATION

POST IDS:
channel islands: 56 
florida keys: 80
olypmic coast: 82
-->
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">WebCRs</a>
    <div class="dropdown-menu">
        <!--
        <a class="dropdown-item" href="/webcr-channelislands/overview/">Channel Islands</a>
        <a class="dropdown-item" href="/webcr-floridakeys/overview/">Florida Keys</a>
        <a class="dropdown-item" href="/webcr-olympiccoast/overview/">Olympic Coast</a>
        -->
        <a class="dropdown-item" href="<?php echo get_permalink(56); ?>">Channel Islands</a>
        <a class="dropdown-item" href="<?php echo get_permalink(80); ?>">Florida Keys</a>
        <a class="dropdown-item" href="<?php echo get_permalink(82); ?>">Olympic Coast</a>
    </div>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Conservation Issues</a>
    <div class="dropdown-menu">
        <a class="dropdown-item" href="https://sanctsound.ioos.us">Sound</a>
    </div>
</li>