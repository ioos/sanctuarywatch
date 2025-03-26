window.dataLayer = window.dataLayer || [];


//FIGURE TRACKING AND ANALYSIS_______________________________________________________________
/**
 * Tracks a "figure source link" event by pushing data to the dataLayer.
 *
 * @param {string} title - The title of the figure or source being tracked.
 * @param {number} postID - The ID of the post associated with the figure or source.
 *
 * @fires dataLayer#figureSourceLink
 */
function figureSourceLink(title, postID) {
    dataLayer.push({
      event: 'figureSourceLink',
      pageSection: 'figure',
      title:  title,
      postID:  postID
    });
}

/**
 * Pushes a custom event to the dataLayer for tracking figure data link interactions.
 *
 * @function figureDataLink
 * @param {string} title - The title of the figure or content being tracked.
 * @param {number} postID - The unique identifier of the post associated with the figure.
 * @description This function is used to send a custom event named 'figureDataLink' to the 
 *              Google Tag Manager dataLayer. It includes metadata such as the page section, 
 *              title, and post ID for analytics purposes.
 */
function figureDataLink(title, postID) {
    dataLayer.push({
        event: 'figureDataLink',
        pageSection: 'figure',
        title:  title,
        postID:  postID
    });
}

/**
 * Tracks the loading of an interactive timeseries graph by pushing an event to the dataLayer.
 * @param {string} title - The title of the figure.
 * @param {number} postID - The ID of the post associated with the figure.
 * @description
 * This function is used to log the loading of an interactive timeseries graph by pushing
 * an event to the `dataLayer` object. The event includes metadata such as
 * the figure type and the page section where the image is displayed.
 * It is typically used for analytics purposes.
 */
function timeseriesGraphLoaded(title, postID) {
  dataLayer.push({
    event: 'timeseriesGraphLoaded',
    figureType: "lineChart",
    pageSection: 'figure',
    title:  title,
    postID:  postID
  });
}


/**
 * Tracks the loading of an internal image and pushes an event to the dataLayer.
 * @param {string} title - The title of the figure.
 * @param {number} postID - The ID of the post associated with the figure.
 * @description
 * This function is used to log the loading of an internal image by pushing
 * an event to the `dataLayer` object. The event includes metadata such as
 * the figure type and the page section where the image is displayed.
 * It is typically used for analytics purposes.
 */
function internalImageLoaded(title, postID) {
    dataLayer.push({
      event: 'internalImageLoaded',
      figureType: "internalImage",
      pageSection: 'figure',
      title:  title,
      postID:  postID
    });
}

/**
 * Tracks the loading of an external image and pushes an event to the dataLayer.
 * @param {string} title - The title of the figure.
 * @param {number} postID - The ID of the post associated with the figure.
 * @description
 * This function is used to log the loading of an external image by pushing
 * an event to the `dataLayer` object. The event includes metadata such as
 * the figure type and the page section  where the image is displayed.
 * It is typically used for analytics purposes.
 */
function externalImageLoaded(title, postID) {
    dataLayer.push({
      event: 'externalImageLoaded',
      figureType: "externalImage",
      pageSection: 'figure',
      title:  title,
      postID:  postID
    });
}


/**
 * Pushes an event to the dataLayer indicating that a code display has been loaded.
 *
 * @param {string} title - The title of the figure.
 * @param {number} postID - The ID of the post associated with the figure.
 */
function codeDisplayLoaded(title, postID) {
    dataLayer.push({
      event: 'codeDisplayLoaded',
      figureType: "codeDisplay",
      pageSection: 'figure',
      title:  title,
      postID:  postID
    });
}



//MODAL & TAB TRACKING AND ANALYSIS_______________________________________________________________
/**
 * Tracks the loading of a modal window by pushing an event to the dataLayer.
 *
 * @param {string} title - The title of the modal window.
 * @param {string|number} postID - The ID of the post associated with the modal window.
 */
function modalWindowLoaded(title, postID) {
    dataLayer.push({
      event: 'modalWindowLoaded', 
      pageSection: 'modal',
      title:  title,
      postID:  postID  
    });
}

/**
 * Tracks the loading of a modal tab by pushing an event to the dataLayer.
 *
 * @param {string} title - The title of the modal tab being loaded.
 * @param {string|number} postID - The ID of the post associated with the modal tab.
 */
function modalTabLoaded(title, postID) {
    dataLayer.push({
      event: 'modalTabLoaded', 
      pageSection: 'modal',
      title:  title,
      postID:  postID  
    });
}

/**
 * Tracks the "More Info" button click event within a modal and pushes the event data to the dataLayer.
 *
 * @param {string} title - The title of the modal or content being tracked.
 * @param {number} postID - The unique identifier of the post associated with the modal.
 */
function modalMoreInfoClicked(title, postID) {
    dataLayer.push({
      event: 'modalMoreInfoClicked', 
      pageSection: 'modal',
      title:  title,
      postID:  postID  
    });
}


/**
 * Tracks the event when modal images are clicked and pushes relevant data to the dataLayer.
 *
 * @param {string} title - The title of the modal or image being clicked.
 * @param {number} postID - The ID of the post associated with the modal or image.
 */
function modalImagesClicked(title, postID) {
    dataLayer.push({
      event: 'modalImagesClicked', 
      pageSection: 'modal',
      title:  title,
      postID:  postID  
    });
}


//SCENE TRACKING AND ANALYSIS_______________________________________________________________


/**
 * Pushes a 'sceneLoaded' event to the dataLayer with details about the scene.
 *
 * @param {string} title - The title of the scene being loaded.
 * @param {number} postID - The ID of the post associated with the scene.
 */
function sceneLoaded(title, postID) {
    dataLayer.push({
      event: 'sceneLoaded', 
      pageSection: 'scene',
      title:  title,
      postID:  postID  
    });
}


/**
 * Tracks a click event on a scene graphic and pushes the event data to the dataLayer.
 *
 * @param {string} title - The title of the scene graphic that was clicked.
 * @param {number} postID - The ID of the post associated with the scene graphic.
 */
function sceneGraphicClick(title, postID) {
    dataLayer.push({
      event: 'sceneGraphicClick', 
      pageSection: 'scene',
      title:  title,
      postID:  postID  
    });
}

/**
 * Tracks the "More Info" click event for a scene and pushes the event data to the dataLayer.
 *
 * @param {string} title - The title of the scene being clicked.
 * @param {number} postID - The unique identifier of the post associated with the scene.
 */
function sceneMoreInfoClicked(title, postID) {
    dataLayer.push({
      event: 'sceneMoreInfoClicked', 
      pageSection: 'scene',
      title:  title,
      postID:  postID  
    });
}


/**
 * Tracks when a scene image is clicked and pushes the event data to the dataLayer.
 *
 * @param {string} title - The title of the scene image that was clicked.
 * @param {number} postID - The ID of the post associated with the scene image.
 */
function sceneImagesClicked(title, postID) {
    dataLayer.push({
      event: 'sceneImagesClicked', 
      pageSection: 'scene',
      title:  title,
      postID:  postID  
    });
}


//INSTANCE TRACKING AND ANALYSIS_______________________________________________________________

/**
 * Tracks the selection of an instance and pushes relevant data to the dataLayer.
 *
 * @param {string} title - The title of the selected instance.
 * @param {number} postID - The unique identifier of the selected instance.
 */
function instanceSelected(title, postID) {
    dataLayer.push({
      event: 'instanceSelected', 
      pageSection: 'instance',
      title:  title,
      postID:  postID  
    });
}


//ABOUT TRACKING AND ANALYSIS_______________________________________________________________

/**
 * Tracks the selection of an "about" section item by pushing an event to the dataLayer.
 *
 * @param {string} title - The title of the selected item.
 * @param {number} postID - The ID of the selected post.
 */
function aboutSelected(title, postID) {
    dataLayer.push({
      event: 'aboutSelected', 
      pageSection: 'about',
      title:  title,
      postID:  postID  
    });
}