<!--
TODO: WebCrs Dropdown, need to grab all locations and put in dropdown
TEMP SOLUTION - HARDCODE LOCATION
TODO: NEED TO FIND WAY TO DYNAMICALLY QUERY DATABASE FOR LOCATION

POST IDS:
channel islands: 56 
florida keys: 80
olypmic coast: 82

previous links used:
<a class="dropdown-item" href="/webcr-channelislands/overview/">Channel Islands</a>
<a class="dropdown-item" href="/webcr-floridakeys/overview/">Florida Keys</a>
<a class="dropdown-item" href="/webcr-olympiccoast/overview/">Olympic Coast</a>
-->
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">WebCRs</a>
    <div class="dropdown-menu">
        <a class="dropdown-item" href="<?php echo get_permalink(56); ?>" target="_blank">Channel Islands</a>
        <a class="dropdown-item" href="<?php echo get_permalink(80); ?>" target="_blank">Florida Keys</a>
        <a class="dropdown-item" href="<?php echo get_permalink(82); ?>" target="_blank">Olympic Coast</a>
    </div>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Conservation Issues</a>
    <div class="dropdown-menu">
        <a class="dropdown-item" href="https://sanctsound.ioos.us">Sound</a>
    </div>
</li>