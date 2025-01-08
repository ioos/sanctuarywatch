
# Chapter 3 Creating a Java-Script Actionable Image Vector (JAI Vector) #

Java-Script Actionable Image Vectors (JAI Vectors) are vector-based images in an .svg or .ai format that are set up specifically to allow the individual components of the image to be responsive objects that are highlighted when a mouse hovers over them. If clicked, they provide access to either other JAI Vectors, or windows with informative interactive graphs, charts, or other content.

Here we provide instructions for two vector graphics packages: 
- [Adobe Illustrator (.ai)](https://www.adobe.com/products/illustrator.html) (requires a paid subscription)
- [Inkscape (.svg)](https://inkscape.org/) (free)

For this tutorial, you will need the example folder for the JAI Vectors, which provides a full working example that you can fiddle around with. We will be referring to this folder extensively in this guide and you can download a compressed version [here](https://github.com/ioos/sanctuarywatch/blob/robbiebranch/docs/training_images/JAI_Vectors_chapter_3_files.zip). Just unzip the folder after it has been downloaded.

You can see a working example in action as a finished product [here](https://marinebon.github.io/infographiqJS/demo.html).

## 3.1 Adobe Illustrator ##

This guide assumes you have a basic knowledge of using Illustrator ([here is a great series of tutorials](https://www.adobe.com/learn/illustrator)). In particular, you’ll need to understand how "layers" work in Illustrator ([and here’s a tutorial for that](https://helpx.adobe.com/illustrator/using/layers.html)).

**NOTE: The methods of implementation vary slightly for desktop view vs mobile view when using "layers". For the sake of continuity, this documentation will consider both as part of a singular workflow for optimizing a .svg graphic for use with JAI Vector in both desktop and mobile settings.**

In the JAI Vector example folder, you will find a file called **"test_image1.ai"**. Open this file in Illustrator and you will see the following:

 - <img src="updated_images/image1-illustrator.png" alt="Alt Text" width="80%" height="80%">

If you check out the "Layers" tab for the image above, you’ll see that the image is composed of four layers: mobile, text, icons, and background.

If you don't see "Layers", use this access path: *Top Navigation Bar > Windows > Layers*

 - When optimizing a graphic for desktop view only, the required layer order for a JAI Vector is listed below. You'll notice it's missing the "mobile" section as shown in the image. All the following steps will still apply, just skip the sections about "mobile" in the following sections. 
    1. text
    2. icons
    3. background

 - When optimizing a graphic for desktop and mobile, the required layer order of a JAI Vector image is shown in the image below.
    1. mobile
    2. text
    3. icons
    4. background

     - <img src="updated_images/image2-illustrator.png" alt="Alt Text" width="80%" height="80%">

### 3.1.1 Layer organization

1. **mobile**: 
     This layer is optional. However, when viewing the same webpage on your desktop (or laptop) computer compared to a mobile device, you may have noticed that the area and orientation of the screen shifts from landscape (like your TV) to portrait (like your mobile device). This shift causes the elements and items on web pages to be displayed very differently. When creating a vector for JAI Vectors, it is good practice to account for the change of display because society ubiquitously uses both device types when surfing the waves of information on the world wide web. 
     
     We will put a pin in this section for the time being and revisit at the end of this section because we will need to use some layers from "3. icons" to complete it correctly.

2. **text**: 
    
    **This layer is required** and must be called lower case “text”. It contains additional explanatory text and graphics for the image that the JAI Vector can toggle on and off.

     - **When creating text, be sure to use a [web-safe font](https://www.w3schools.com/cssref/css_websafe_fonts.php) to ensure that your text displays correctly in a web browser.** 

    In the Layers panel, after we click ">" to expand the "text" layer, we can see that this layer contains several elements denoted by <Group>:
     - <img src="updated_images/image3-illustrator.png" alt="Alt Text" width="80%" height="80%">

    There are three essential things here:

    A. All of the elements are vector-based (nothing raster-based, raster means image such as .tif, .jpg, or .png). If you happen to have raster-based elements in your image (or are not sure), we strongly recommend that you find a vector version or attempt to convert the raster item to a vector using the [image trace tool](https://helpx.adobe.com/illustrator/using/image-trace.html). Rasters will break the functionality of JAI Vectors and are not meant to be used in these workflows. 

    B. None of the elements within this layer can be named “text”. The following (where one of the elements is named text) is not allowed:
     - <img src="updated_images/image4-illustrator.png" alt="Alt Text" width="80%" height="80%">

    C. Double check that the font you select for the text elements displays well in a browser. The default font for Illustrator is often “Myriad Pro”, which does not display well. There are many great alternatives, with one being “Arial”.

3. **icons**: 

    **This layer is required** and contains all of the clickable elements in the image. This layer can be named anything, except for “text” or the name of any other clickable element in the image. We recommend the name "icons" though.

    If you check out the "Layers" panel for “icons”, you’ll see that it contains four sub-layers; octopus, bird, boat, & shark.
     - <img src="updated_images/image5-illustrator.png" alt="Alt Text" width="70%" height="70%">

    Each of these sub-layers defines a single clickable component of the image.

     - The names of these sub-layers should not contain spaces, commas, or be called “text”.

     - All elements within the sub-layers should be vector-based and not raster-based. If you happen to have raster-based elements in your image (or are not sure), we you will need to convert them to vector  objects using the [image trace tool](https://helpx.adobe.com/illustrator/using/image-trace.html).

     - Nothing should overlap on top of anything you want to be clickable.

     - Each sub-layer contains all of the elements for a single clickable icon.

     - The elements in the sub-layer, if named, should not have the same name as the sub-layer itself. So, for example, the following won’t work :
         - <img src="updated_images/image6-illustrator.png" alt="Alt Text" width="70%" height="70%">

    **Adding a background to complex objects to make them easy to highlight:**
     It might be necessary to add a transparent rectangle or ellipse to the behind complicated objects to ensure that they are able to be locked on to when the mouse hovers over them. This might apply to long slender objects or objects with multiple items, appendages, or branches. In our example, we will use an added layer called "sardines" which is located in layers under "icons > sardines". 

     - Step 1: In the main ArtBoard, we have selected all of the "sardines" (2). You will see the shape tool on the left-side toolbar (1), and the "sardines" layer selected on the right-side Layers panel (3). 
         - <img src="updated_images/image20-1-illustrator.png" alt="Alt Text" width="80%" height="80%">

     - Step 2: Select the "Ellipse tool" from the left-side bar
         - <img src="updated_images/image20-2-illustrator.png" alt="Alt Text" width="40%" height="40%">
     
     - Step 3: While holding right-click with your mouse, drag an ellipse from the top-left of the sardines to the bottom-right. When you first draw the ellipse it may cover your sardines, this is ok and we will fix it in a second. You can edit the size and rotation of the ellipse by hovering your mouse over the anchor points (little squares that are connected by lines) surrounding the ellipse to adjust these settings accordingly to fit completely over the sardines.
         - <img src="updated_images/image20-3-illustrator.png" alt="Alt Text" width="50%" height="50%">
    
     - Step 4: The rectangle you created may cover your icon because it is filled in. If this happens, right click on the rectangle, hover over "arrange" in the expanded menu, and then click on "Send to Back". This will make it the bottom layer of your icon. Be sure to check the layers panel to ensure that you sent it to the back of the "sardines" layer. If your ellipse appears in a different layer, you can simply drag it into the sardines layer and repeat the process of sending it to the back. 
         - <img src="updated_images/image20-4-illustrator.png" alt="Alt Text" width="50%" height="50%">

     - Step 5: To make your rectangle transparent, be sure to click on both the "fill" and "outline" portions of the color indicator on the bottom of your left side tool bar. Once you select either "fill" (#1.1) or "outline" (#1.2) in the top section, click on the white box with the red strike-through in the bottom right (#2) to make it transparent. The "fill" or "outline" will also have this red strike-through when it is applied to them as shown below. 
         - <img src="updated_images/image20-5-illustrator.png" alt="Alt Text" width="20%" height="20%">

    - Step 6: Everything should look like this when you're complete.
        - <img src="updated_images/image20-6-illustrator.png" alt="Alt Text" width="90%" height="90%">

4. **background**:

    This is an optional layer that contains all non-responsive elements of the image. This layer can be called anything (other than “text” or the name of a clickable sub-layer). It is ignored by the JAI Vectors Javascript. We recommend calling it "background" though.

5. **mobile** continued: 
    
    Step 1: Expand the layers for "icons" using the ">" toggle next to the word. Then, select the four sub-layers; octopus, bird, boat, & shark under by clicking on the top option "octopus", then while holding the "shift" key, click on the bottom option "shark". You should see all four sub-layers highlighted in light blue. You can release the shift key. 
    
    Step 2: In the top right corner of the "Layers" window, click on the three stacked bars to expand the options menu and select "Duplicate Selection".
    <img src="updated_images/image7-illustrator.png" alt="Alt Text" width="80%" height="80%">

    Step 3: Next, with the four original sub-layers still selected, hover the mouse on top of them then click and hold them, then "drag and drop" them directly over the "mobile" layer. You will see that they will be moved to the "mobile" layer section.
    <img src="updated_images/image8-illustrator.png" alt="Alt Text" width="80%" height="80%"> 

    **Considerations:**
    - Icons might need to be adjusted to fit into the example format shown in the example image below. If your icons are too wide or long, you will need to adjust them to make them fit properly. Or, use a different icon to represent the same subject. Below is an example of what you icons might look like when displayed in mobile view.
    - <img src="updated_images/image9-illustrator.png" alt="Alt Text" width="30%" height="30%">

### 3.1.2 Dealing with raster-based elements

How do I tell if something is raster or vector-based?

One dead give-away that an image is raster-based is if the file is saved in a raster-based file format. You can determine the file format of a file by checking out the last few characters of the file name (the file extension). Some common raster-based file formats are (with their extensions):

    .gif (Graphic Interchange Format)

    .jpg or .jpeg (Joint Photographic Experts Group)

    .png (Portable Network Graphics)

    .psd (PhotoShop Document)

    .tiff (Tag Image File Format)

Checking the file extension isn’t a fool-proof system though. Just because an image is saved in some other format than those above doesn’t mean that it isn’t a raster-based image. So, how can you know for sure? Well, open the image up in Illustrator and take a close look at the Layer panel. If the image is raster-based, it will say <Image> under the appropriate layer (be sure to click the arrow just to the left of the layer name to see what it contains). See below for an example:

<img src="updated_images/image10-illustrator.png" alt="Alt Text" width="80%" height="80%">

**Converting a raster to a vector**

If you have some raster-based elements that you’d like to include in your JAI Vectors image, Illustrator has got you covered. You’ll just need to convert those elements into vectors and here’s how to do that:

1. Select image (from Layers; or Select All from menu).

<img src="updated_images/image11-illustrator.png" alt="Alt Text" width="60%" height="60%">

2. From the menu, select Object > Image Trace > Make and Expand.

<img src="updated_images/image12-illustrator.png" alt="Alt Text" width="60%" height="60%">

3. Delete (trash icon in Layers menu) or Unite/Merge (in Pathfinder menu) the selected layers until you achieve the desired simplified icon result.

<img src="updated_images/image13-illustrator.png" alt="Alt Text" width="60%" height="60%">

### 3.1.3 Saving the image

To be used by JAI Vectors, the file must be exported in svg format. To do so:

1. From the menu, click: File > Export > Export As.
2. In the following screen, select svg format. Be sure to click “Use ArtBoards”.
3. In the final screen that pops up, be sure to set Object IDs to “Layer Names”, as follows:

<img src="updated_images/image14-illustrator.png" alt="Alt Text" width="50%" height="50%">

4. The default for Illustrator is to add an “01” to your svg file name (so, “example.svg” becomes “example01.svg”. Change the file name back to your desired choice.

## 3.2 Inkscape

This guide assumes you have a basic knowledge of using Inkscape ([here is a great series of tutorials](https://inkscape.org/learn/tutorials/)). In particular, you’ll need to understand how layers work in Inkscape ([and here’s a tutorial for that](https://inkscape.org/~JurgenG/%E2%98%85layers-objects-and-paths)).

In the JAI Vectors example folder, you’ll find a file called test-image1.svg. Open this file in Inkscape and you’ll see the following:

- You can open up the "Layers" panel by clicking on the stacked icon in the top navigation bar. It's highlighted in red. 

<img src="updated_images/image15-inkscape.png" alt="Alt Text" width="80%" height="80%">

If you check out the Layers panel for the image, you’ll see the following:

<img src="updated_images/image16-inkscape.png" alt="Alt Text" width="50%" height="50%">

This is the required layer order of an Inkscape image.

### 3.2.1 Layer organization

1. **mobile**:

     This layer is optional. However, when viewing the same webpage on your desktop (or laptop) computer compared to a mobile device, you may have noticed that the area and orientation of the screen shifts from landscape (like your TV) to portrait (like your mobile device). This shift causes the elements and items on web pages to be displayed very differently. When creating a vector for JAI Vectors, it is good practice to account for the change of display because society ubiquitously uses both device types when surfing the waves of information on the world wide web. 
     
     We will put a pin in this section for the time being and revisit at the end of this section because we will need to use some layers from "3. icons" to complete it correctly.

2. **text**:

    **This layer is required** and must be called lower case “text”. It contains additional explanatory text and graphics for the image that the JAI Vector can toggle on and off.

     - **When creating text, be sure to use a [web-safe font](https://www.w3schools.com/cssref/css_websafe_fonts.php) to ensure that your text displays correctly in a web browser.** 

    There are three essential things here:

    A. All of the elements are vector-based (nothing raster-based, raster means image such as .tif, .jpg, or .png). If you happen to have raster-based elements in your image (or are not sure), we strongly recommend that you find a vector version or attempt to convert the raster item to a vector. Rasters will break the functionality of JAI Vectors and are not meant to be used in these workflows. 

    B. None of the elements within this layer can be named “text”. In this case, it doesn't really apply because there are no sub-layers for "text". but, if you did have them this would be the case.

    C. Double check that the font you select for the text elements displays well in a browser. The default font for Illustrator is often “Myriad Pro”, which does not display well. There are many great alternatives, with one being “Arial”.

3. **icons**:

    This layer is required and contains all of the clickable elements in the image. This layer can be named anything, except for “text” or the name of any clickable element in the image. If you check out the Layers panel for “icons” (see image just above), you’ll see that it contains four sub-layers (chart examples 1 through 6). Each of these sub-layers defines a single clickable component of the image.

    - The names of these sub-layers should not contain spaces, commas, or be called “text”.

    - All elements within the sub-layers should be vector-based and not raster-based. If you happen to have raster-based elements in your image (or are not sure), they will need to be converted to vector objects using the method linked [here](https://inkscape.org/doc/tutorials/tracing/tutorial-tracing.html) in Inkscape. 

    - Nothing should overlap on top of anything you want to be clickable.

    - Each sub-layer contains all of the elements for a single clickable icon.

    **Adding a background to complex objects to make them easy to highlight:**
    It might be necessary to add a transparent white rectangle or ellipse to the behind complicated objects to ensure that they are able to be locked on to when the mouse hovers over them. This might apply to long slender objects or objects with multiple items, appendages, or branches.

    - See this section for the Illustrator instructions for "Icons > Adding a background to complex objects to make them easy to highlight" above for a general outline of what needs to be done in Inkscape as well. The exact method will differ.

4. **background**:

    Another optional layer that contains all non-responsive elements of the image. This layer can be called anything (other than “text” or the name of a clickable layer). It is ignored by the JAI Vectors Javascript.

5. **mobile** continued: 

    Once again, as in the example for Adobe Illustrator above, the four sub-layers; octopus, bird, boat, & shark need to be copied and placed into the mobile folder for icons. If you expand "mobile" layer, you will see the sub-layers inside of it. Below is an example of what you icons might look like when displayed in mobile view.

    <img src="updated_images/image19-inkscape.png" alt="Alt Text" width="40%" height="40%">

    **Considerations**:
    - Icons might need to be adjusted to fit into the example format shown in the example image below. If your icons are too wide or long, you will need to adjust them to make them fit properly. Or, use a different icon to represent the same subject.
    - <img src="updated_images/image9-illustrator.png" alt="Alt Text" width="30%" height="30%">

### 3.2.2 Editing the layer XML

In order for the JAI Vectors image to behave properly, you will need to edit the XML for the image. In order to do so, you’ll need to have two panels visible:

1. Layers (to show from Menu, Layer > Layers…)

2. XML Editor (to show from Menu, Edit > XML Editor…)

Let’s zoom in on these panels for the image inkscape_example.svg:

<img src="updated_images/image17-inkscape.png" alt="Alt Text" width="50%" height="50%">

For each of your layers:

1. Click the associated entry in the XML Editor (hint: the “inkscape:label” will match the layer name).
2. In the box to the right, change the id to match the layer name.
3. Select the “text” layer in the XML Editor. In the box to the right, click the red X by “display: inline” (thereby deleting that row). Note: in the example file inkscape_example.svg, the “display: inline” line has already been deleted.

### 3.2.3 Dealing with raster-based elements

How do I tell if something is raster or vector-based?

One dead give-away that an image is raster-based is if the file is saved in a raster-based file format. You can determine the file format of a file by checking out the last few characters of the file name (the file extension). Some common raster-based file formats are (with their extensions):

    - .gif (Graphic Interchange Format)

    - .jpg or .jpeg (Joint Photographic Experts Group)

    - .png (Portable Network Graphics)

    - .psd (PhotoShop Document)

    - .tiff (Tag Image File Format)

Checking the file extension isn’t a fool-proof system though. Just because an image is saved in some other format than those above doesn’t mean that it isn’t a raster-based image. So, how can you know for sure? Well, open the image up in Inkscape and right click on it. If in the options you see “Image Properties…”, you’ve got a raster-based image on your hands (see image below).

<img src="updated_images/image18-inkscape.png" alt="Alt Text" width="50%" height="50%">

Converting a raster to a vector

If you have some raster-based elements that you’d like to include in your JAI Vectors image, Inkscape has got you covered. You’ll just need to convert those elements into vectors and [here’s a tutorial on how to do that](https://inkscape.org/doc/tutorials/tracing/tutorial-tracing.html).

### 3.2.4 Saving the image

To be used by JAI Vectors, the file must be exported in svg format. Good news! Inkscape’s native file format is already svg. When you save the image, just be sure to save it in the format “Inkscape SVG (#.svg)”.
