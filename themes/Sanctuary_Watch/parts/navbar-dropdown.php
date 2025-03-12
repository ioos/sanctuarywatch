<?php
/**
 * Navigation Dropdowns Template
 *
 * This section of the navigation bar template provides dropdown menus for quick access to various parts of the site, 
 * specifically focusing on 'WebCRs' (Web-enabled Condition Reporting Systems) for different locations and 'Conservation Issues'.
 * These dropdowns are designed to improve user navigation efficiency by categorizing content under common themes.
 * This implementation is essential for a user-friendly navigation setup that allows visitors to find relevant information
 * quickly and efficiently, categorized under intuitive groupings. 
 */
?>
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
<!-- List item for the navigation menu, specifically a dropdown for WebCRs -->
<!-- <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">WebCRs</a>
    <div class="dropdown-menu"> 
        <a class="dropdown-item" href="<?php echo get_permalink(10); ?>" target="_blank">Channel Islands</a>
        <a class="dropdown-item" href="<?php echo get_permalink(80); ?>" target="_blank">Florida Keys</a>
        <a class="dropdown-item" href="<?php echo get_permalink(60); ?>" target="_blank">Olympic Coast</a>
    </div>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Conservation Issues</a>
    <div class="dropdown-menu">
        <a class="dropdown-item" href="https://sanctsound.ioos.us">Sound</a>
    </div>
</li> -->

<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Condition tracking</a>
</li>

<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">New sanctuaries</a>
</li>

<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">System-wide topics</a>
</li>

<li class="nav-item ">
    <a class="nav-link "  href="/about" role="button" aria-haspopup="true" aria-expanded="false">About</a>
</li>
