**WPPOOL Plugin**

<div style="color:indianred; font-size: 20px; text-align: center;">Please go through the docs to get started. </div>
<blockquote> 
WPPOOL Plugin is a tiny SDK for only the plugins developed by WPPOOL to handle safe sync operations, promotional popup and other micro-tasks. The SDK will be only used by WPPOOL developer for official 
plugins.  
</blockquote>
<br>


# **Get Started**
THE SDK has ONE (**1**) required file, and optional image as a set.
```
/- 
- class-plugin.php
- background-image.png
```
You just need to load only the **class-plugin.php**. **background-image.png** is optional, and 'class-plugin.php' can work on Standalone Mode, means anywhere in a project.


We're using Singletone architecture for this SDK. No matter how many plugins do we have, it will be loaded ony ONCE. Easy 2 steps to work with the SDK.

## **Step 1**: Installation
### Download SDK
Method 1:

> Go to your project directory and run the command
```bash
git clone https://github.com/WPPOOL/plugin.git wppool
```
Method 2:

[Download Latest ZIP](https://github.com/WPPOOL/plugin/archive/refs/heads/master.zip) from master branch. Extract, copy and paste to to project.

I prefer pasting the SDK files at `/includes/wppool/*` location, so that we can add further future libraries. 

### Initialize SDK
> Send user-allowed tracking data from appsero in simplest way
```php
// Include class-plugin.php to your project.
require_once '/path/to/class-plugin.php';

// Init SDK.
wppool_plugin_init('wp_dark_mode');
```

### Installation complete!


---

## **Step 2**: Show Popup
```js
// Just put the snippet where/when you need to popup to be OPENED.
WPPOOL.Popup("wp_dark_mode").show(); 

/**
 * It will open the popup instantly for WP Dark Mode, with Live Google Sheet Data!
 * 
 * Works on
 * 
 * VanillaJS
 * jQuery
 * VueJS
 * React
 * AlpineJS
 * Gutenberg Block
 * Elementor
 * Divi
 * Angular (and I don't know angular)
 */

```


-------
# Advanced [Basically No Need]

## **Advanced JavaScript**
Playing with the events and hooks

### **Open and close**
```js
// Declare only once.
const wpdm = WPPOOL.Popup('wp_dark_mode');

// open.
wpdm.show();

// and close.
wpdm.hide();
```


### **Available Events**
```js 
// Our Events.
wpdm.on("show", (data) => {
    console.log("The modal for WP Dark Mode has just opened");
})

wpdm.on("hide", (data) => {
    console.log("The modal for WP Dark Mode has just closed");
})

wpdm.on("click", (data) => {
    console.log("The modal for WP Dark Mode has got a click");
})

wpdm.on("overlayClick", (data) => {
    console.log("The modal for WP Dark Mode has got a click on overlay");
})

wpdm.on("closeClick", (data) => {
    console.log("The modal for WP Dark Mode has got a click on Clock button");
})

wpdm.on("buttonClick", (data) => {
    console.log("The modal for WP Dark Mode has got a click on main button");
})

wpdm.on("countdownStart", (data) => {
    console.log("The modal for WP Dark Mode has started countdown");
})

wpdm.on("countdownFinish", (data) => {
    console.log("The modal for WP Dark Mode has ended countdown");
})  
``` 
<br>
<br>


<br>
<br>

## Advanced PHP [Basically NO NEED]

### **Sync with Fluent CRM**
> Sync client data with WPPOOL CRM 

#### **First thing first**
```php
/**
 * Required
 *
 * @var String $plugin_id The plugin ID.
 */
$plugin_id = 'wp_dark_mode';

/**
 * User data payload
 *
 * @var Array $user_data User data
 * Only email is required, rest of the data are optional and flexible. If you pass these, will be updated. Unless these will not be asked. 
 */
$user_data = [
    'first_name' => 'John',
    'last_name'  => 'Doe',
    'phone'      => '01700000000',
    'email'      => 'johndoe@wppool.dev' # required
];

/**
 * Get an instance of the Plugin class
 */
$wp_dark_mode = new \WPPOOL\Plugin($plugin_id, $user_data);

// or 
use \WPPOOL\Plugin as Plugin;

$wp_dark_mode = new Plugin($plugin_id, $user_data);
```

## **Subscribe contact**
### **Subscribe to current plugin**
```php

/**
 * @array data 
 *  
 */ 

// subscribe to WP Dark Mode list with free tag 
$wp_dark_mode->subscribe();



/**
 * More options
 */

// Similar
$wp_dark_mode->subscribe("free");

// Subscribe to pro tag
$wp_dark_mode->subscribe("pro");

// Subscribe to cancelled tag
$wp_dark_mode->subscribe("cancelled");
```


### **Unsubscribe from the plugin**
> User will be removed from the list of the current plugin, not fully unsubscribed from the system. 
```php
$wp_dark_mode->unsubscribe_plugin(); 
```

### **Unsubscribe from the tag**
> User will be removed from the list of the current tag.
```php
$wp_dark_mode->unsubscribe_tag(); 
```


### **Unsubscribe from the CRM**
> User will be fully unsubscribed from the CRM. 
```php
$wp_dark_mode->unsubscribe(); 
```



## Update SDK
> If you need to update the SDK from Git. simply pull it in your project directory. 
```bash
cd includes/wppool
git pull origin master
```
> Rebase if you changed something in library without commits. 
 
<br>
<br>

## Available IDs 
<ul>
<li>wp_dark_mode</li>
<li>easy_video_reviews </li>
<li>jitsi_meet </li>
<li>sheets_to_wp_table_live_sync </li>
<li>stock_notifier_for_woocommerce </li>
<li>stock_sync_with_google_sheet_for_woocommerce </li>
<li>social_contact_form </li>
<li>chat_widgets_for_multivendor_marketplaces </li>
<li>zero_bs_accounting </li>
<li>wp_markdown_editor </li>
<li>elementor_speed_optimizer </li>
<li>easy_email_integraion </li>
<li>easy_cloudflare_turnstile </li>
</ul> 