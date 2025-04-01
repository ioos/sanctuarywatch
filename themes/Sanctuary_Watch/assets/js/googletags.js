window.dataLayer = window.dataLayer || [];

const gaMeasurementID = window.webcrSettings.googleAnalyticsMeasurementId;

//FIGURE TRACKING AND ANALYSIS_______________________________________________________________
/**
 * Tracks a "figureSourceLinkClick" event by pushing data to the dataLayer whenclicked.
 *
 * @param {string} title - The title of the figure or source being tracked.
 * @param {number} postID - The ID of the post associated with the figure or source.
 *
 */
// function figureSourceLinkClick(title, postID) {
//     dataLayer.push({
//       event: 'figureSourceLinkClick',
//       pageSection: 'figure',
//       title:  title,
//       postID:  postID
//     });
// }

/**
 * Pushes a custom event to the dataLayer for tracking figure data link interactions when clicked.
 *
 * @function figureDataLinkClick
 * @param {string} title - The title of the figure or content being tracked.
 * @param {number} postID - The unique identifier of the post associated with the figure.
 * @description This function is used to send a custom event named 'figureDataLink' to the 
 *              Google Tag Manager dataLayer. It includes metadata such as the page section, 
 *              title, and post ID for analytics purposes.
 */
// function figureDataLinkClick(title, postID) {
//     dataLayer.push({
//         event: 'figureDataLink',
//         pageSection: 'figure',
//         title:  title,
//         postID:  postID
//     });
// }

/**
 * Tracks the loading of an interactive timeseries graph by pushing an event to the dataLayer.
 * @param {string} title - The title of the figure.
 * @param {number} figureID - The ID of the post associated with the figure.
 * @description
 * This function is used to log the loading of an interactive timeseries graph by pushing
 * an event to the `dataLayer` object. The event includes metadata such as
 * the figure type and the page section where the image is displayed.
 * It is typically used for analytics purposes.
 */
function figureTimeseriesGraphLoaded(title, figureID, gaMeasurementID) {
  dataLayer.push({
    GA4_MeasurementID: gaMeasurementID,
    event: 'figureTimeseriesGraphLoaded',
    figureType: "lineChart",
    pageSection: 'figure',
    title:  title,
    figureID:  figureID
  });
}


/**
 * Tracks the loading of an internal image and pushes an event to the dataLayer.
 * @param {string} title - The title of the figure.
 * @param {number} figureID - The ID of the post associated with the figure.
 * @description
 * This function is used to log the loading of an internal image by pushing
 * an event to the `dataLayer` object. The event includes metadata such as
 * the figure type and the page section where the image is displayed.
 * It is typically used for analytics purposes.
 */
function figureInternalImageLoaded(title, figureID, gaMeasurementID) {
    dataLayer.push({
      GA4_MeasurementID: gaMeasurementID,
      event: 'figureInternalImageLoaded',
      figureType: "internalImage",
      pageSection: 'figure',
      title:  title,
      figureID:  figureID
    });
}

/**
 * Tracks the loading of an external image and pushes an event to the dataLayer.
 * @param {string} title - The title of the figure.
 * @param {number} figureID - The ID of the post associated with the figure.
 * @description
 * This function is used to log the loading of an external image by pushing
 * an event to the `dataLayer` object. The event includes metadata such as
 * the figure type and the page section  where the image is displayed.
 * It is typically used for analytics purposes.
 */
function figureExternalImageLoaded(title, figureID, gaMeasurementID) {
    dataLayer.push({
      GA4_MeasurementID: gaMeasurementID,
      event: 'figureExternalImageLoaded',
      figureType: "externalImage",
      pageSection: 'figure',
      title:  title,
      figureID:  figureID
    });
}


/**
 * Pushes an event to the dataLayer indicating that a code display has been loaded.
 *
 * @param {string} title - The title of the figure.
 * @param {number} figureID - The ID of the post associated with the figure.
 */
function figureCodeDisplayLoaded(title, figureID, gaMeasurementID) {
    dataLayer.push({
      GA4_MeasurementID: gaMeasurementID,
      event: 'figureCodeDisplayLoaded',
      figureType: "codeDisplay",
      pageSection: 'figure',
      title:  title,
      figureID:  figureID
    });
}



//MODAL & TAB TRACKING AND ANALYSIS_______________________________________________________________
/**
 * Tracks the loading of a modal window by pushing an event to the dataLayer.
 *
 * @param {string} title - The title of the modal window.
 * @param {string|number} modal_id - The ID of the post associated with the modal window.
 */
function modalWindowLoaded(title, modal_id, gaMeasurementID) {
    dataLayer.push({
      GA4_MeasurementID: gaMeasurementID,
      event: 'modalWindowLoaded', 
      pageSection: 'modal',
      title:  title,
      modalID:  modal_id  
    });
}

/**
 * Tracks the loading of a modal tab by pushing an event to the dataLayer.
 *
 * @param {string} title - The title of the modal tab being loaded.
 * @param {string|number} modal_id - The ID of the post associated with the modal tab.
 */
function modalTabLoaded(title, modal_id, gaMeasurementID) {
    dataLayer.push({
      GA4_MeasurementID: gaMeasurementID,
      event: 'modalTabLoaded', 
      pageSection: 'modal',
      title:  title,
      modalID:  modal_id  
    });
}

/**
 * Tracks the "More Info" button click event within a modal and pushes the event data to the dataLayer.
 *
 */
function modalMoreInfoClicked(title, modalID, url, modalTitle, gaMeasurementID) {

  dataLayer.push({
    GA4_MeasurementID: gaMeasurementID,
    event: 'modalMoreInfoClicked',
    pageSection: 'modal',
    title: title,
    modalID: modalID,
    modalTitle: modalTitle,
    url: url
  });
}

// On click modalMoreInfoClicked triggered
document.querySelectorAll('.accordion-body a').forEach(function(link) {
  link.addEventListener('click', function(event) {
    // Find the closest .accordion-item and check if the .accordion-button has 'Images' in the class
    const accordionItem = event.currentTarget.closest('.accordion-item');
    const accordionButton = accordionItem ? accordionItem.querySelector('.accordion-button') : null;

    // Check if the class list contains the word 'Images'
    if (accordionButton && accordionButton.classList.contains('accordion-button') && accordionButton.classList.contains('More Info')) {
      const title = link.textContent.trim();
      const url = link.href;

      // Get the modal ID (either from data attribute or ID)
      const modalID = accordionItem ? (accordionItem.dataset.postId || accordionItem.id.split('-').pop()) : 'unknown-post-id';

      // Get the modal title from the element with id="modal-title"
      const modalTitleElement = document.getElementById('modal-title');
      const modalTitle = modalTitleElement ? modalTitleElement.textContent.trim() : 'Unknown Title';

      // Push to dataLayer
      modalMoreInfoClicked(title, modalID, url, modalTitle);
    }
  });
});


/**
 * Tracks the event when modal images are clicked and pushes relevant data to the dataLayer.
 *
 */
function modalImagesClicked(title, modalID, url, modalTitle, gaMeasurementID) {
  dataLayer.push({
    GA4_MeasurementID: gaMeasurementID,
    event: 'modalImagesClicked',
    pageSection: 'modal',
    title: title,
    modalID: modalID,
    modalTitle: modalTitle,
    modalTitle: url
  });
}

// On click modalImagesClicked triggered
document.querySelectorAll('.accordion-body a').forEach(function(link) {
  link.addEventListener('click', function(event) {
    // Find the closest .accordion-item and check if the .accordion-button has 'Images' in the class
    const accordionItem = event.currentTarget.closest('.accordion-item');
    const accordionButton = accordionItem ? accordionItem.querySelector('.accordion-button') : null;

    // Check if the class list contains the word 'Images'
    if (accordionButton && accordionButton.classList.contains('accordion-button') && accordionButton.classList.contains('Images')) {
      const title = link.textContent.trim();
      const url = link.href;

      // Get the modal ID (either from data attribute or ID)
      const modalID = accordionItem ? (accordionItem.dataset.postId || accordionItem.id.split('-').pop()) : 'unknown-post-id';

      // Get the modal title from the element with id="modal-title"
      const modalTitleElement = document.getElementById('modal-title');
      const modalTitle = modalTitleElement ? modalTitleElement.textContent.trim() : 'Unknown Title';

      // Push to dataLayer
      modalImagesClicked(title, modalID, url, modalTitle);
    }
  });
});



//SCENE TRACKING AND ANALYSIS_______________________________________________________________
/**
 * Pushes a 'sceneLoaded' event to the dataLayer with details about the scene.
 *
 * @param {string} title - The title of the scene being loaded.
 * @param {number} sceneID - The ID of the post associated with the scene.
 */
function sceneLoaded(title, sceneID, gaMeasurementID) {
    dataLayer.push({
      GA4_MeasurementID: gaMeasurementID,
      event: 'sceneLoaded', 
      pageSection: 'scene',
      title:  title,
      title:  sceneID  
    });
}


/**
 * Tracks a click event on a scene graphic and pushes the event data to the dataLayer.
 *
 * @param {string} title - The title of the scene graphic that was clicked.
 * @param {number} sceneID - The ID of the post associated with the scene graphic.
 */
// function sceneGraphicClick(title, sceneID) {
//     dataLayer.push({
//       event: 'sceneGraphicClick', 
//       pageSection: 'scene',
//       title:  title,
//       sceneID:  sceneID  
//     });
// }

/**
 * Tracks the "More Info" click event for a scene and pushes the event data to the dataLayer.
 *
 * @param {string} title - The title of the scene being clicked.
 * @param {number} sceneID - The unique identifier of the post associated with the scene.
 */
// function sceneMoreInfoClicked(title, sceneID) {
//     dataLayer.push({
//       event: 'sceneMoreInfoClicked', 
//       pageSection: 'scene',
//       title:  title,
//       sceneID:  sceneID  
//     });
// }


/**
 * Tracks when a scene image is clicked and pushes the event data to the dataLayer.
 *
 * @param {string} title - The title of the scene image that was clicked.
 * @param {number} sceneID - The ID of the post associated with the scene image.
 */
// function sceneImagesClicked(title, sceneID) {
//     dataLayer.push({
//       event: 'sceneImagesClicked', 
//       pageSection: 'scene',
//       title:  title,
//       sceneID:  sceneID  
//     });
// }


//INSTANCE TRACKING AND ANALYSIS_______________________________________________________________

/**
 * Tracks the selection of an instance and pushes relevant data to the dataLayer.
 *
 * @param {string} title - The title of the selected instance.
 * @param {number} instanceID - The unique identifier of the selected instance.
 */
// function instanceSelected(title, instanceID) {
//     dataLayer.push({
//       event: 'instanceSelected', 
//       pageSection: 'instance',
//       title:  title,
//       instanceID:  instanceID  
//     });
// }


//ABOUT TRACKING AND ANALYSIS_______________________________________________________________
//Not sure if needed
/**
 * Tracks the selection of an "about" section item by pushing an event to the dataLayer.
 *
 */
// function aboutSelected() {
//     dataLayer.push({
//       event: 'aboutSelected', 
//       pageSection: 'about',
//     });
// }