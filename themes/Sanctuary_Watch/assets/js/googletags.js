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

// function setupModalImagesLinkTracking(modalID) {
//   document.querySelectorAll('.accordion-body a').forEach(function(link) {
//     link.addEventListener('click', function(event) {
//       // Find the closest .accordion-item
//       const accordionItem = event.currentTarget.closest('.accordion-item');
//       const accordionButton = accordionItem ? accordionItem.querySelector('.accordion-header .accordion-button') : null;
//       const buttonText = accordionButton.textContent.trim();
//       // Check if the button has the class "More Info"
//       if (buttonText === 'Images') {
//         const linkTitle = link.textContent.trim();
//         const url = link.href;

//         // Get modal title from #modal-title
//         const modalTitleElement = document.getElementById('modal-title');
//         const modalTitle = modalTitleElement ? modalTitleElement.textContent.trim() : 'Unknown Title';

//         // Push to dataLayer
//         modalImagesLinkClicked(linkTitle, modalID, url, modalTitle, gaMeasurementID);
//       }
//     });
//   });
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
  //console.log('gaMeasurementID figureTimeseriesGraphLoaded', gaMeasurementID);
  //console.log(title, figureID, gaMeasurementID);
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
    //console.log('gaMeasurementID figureInternalImageLoaded', gaMeasurementID);
    //console.log(title, figureID, gaMeasurementID);
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
  //console.log('gaMeasurementID figureExternalImageLoaded', gaMeasurementID);
  //console.log(title, figureID, gaMeasurementID);
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
  //console.log('gaMeasurementID figureCodeDisplayLoaded', gaMeasurementID);
  //console.log(title, figureID, gaMeasurementID); 
  dataLayer.push({
      GA4_MeasurementID: gaMeasurementID,
      event: 'figureCodeDisplayLoaded',
      figureType: 'codeDisplay',
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
  //console.log('gaMeasurementID modalWindowLoaded', gaMeasurementID);
  //console.log(title, modal_id, gaMeasurementID);  
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
function modalTabLoaded(tab_label, modal_id, tab_id, gaMeasurementID) {
  //console.log('gaMeasurementID modalTabLoaded', gaMeasurementID);
  //console.log(tab_label, modal_id, tab_id, gaMeasurementID);    
  dataLayer.push({
      GA4_MeasurementID: gaMeasurementID,
      event: 'modalTabLoaded', 
      pageSection: 'modal',
      title:  tab_label,
      modalID:  modal_id,
      tabID:  tab_id 
    });
}

/**
 * Tracks the "More Info" button click event within a modal and pushes the event data to the dataLayer.
 *
 */
function modalMoreInfoLinkClicked(linkTitle, modalID, url, modalTitle, gaMeasurementID) {
  //console.log(linkTitle, modalID, url, modalTitle, gaMeasurementID);
  dataLayer.push({
    GA4_MeasurementID: gaMeasurementID,
    event: 'modalMoreInfoClicked',
    pageSection: 'modal',
    linkTitle: linkTitle,
    modalID: modalID,
    modalTitle: modalTitle,
    url: url
  });
}

function setupModalMoreInfoLinkTracking(modalID) {
  document.querySelectorAll('.accordion-body a').forEach(function(link) {
    link.addEventListener('click', function(event) {
      // Find the closest .accordion-item
      const accordionItem = event.currentTarget.closest('.accordion-item');
      const accordionButton = accordionItem ? accordionItem.querySelector('.accordion-header .accordion-button') : null;
      const buttonText = accordionButton.textContent.trim();
      // Check if the button has the class "More Info"
      if (buttonText === 'More Info') {
        const linkTitle = link.textContent.trim();
        const url = link.href;

        // Get modal title from #modal-title
        const modalTitleElement = document.getElementById('modal-title');
        const modalTitle = modalTitleElement ? modalTitleElement.textContent.trim() : 'Unknown Title';

        // Push to dataLayer
        modalMoreInfoLinkClicked(linkTitle, modalID, url, modalTitle, gaMeasurementID);
      }
    });
  });
}


/**
 * Tracks the event when modal images are clicked and pushes relevant data to the dataLayer.
 *
 */
function modalImagesLinkClicked(linkTitle, modalID, url, modalTitle, gaMeasurementID) {
  console.log(linkTitle, modalID, url, modalTitle, gaMeasurementID);
  dataLayer.push({
    GA4_MeasurementID: gaMeasurementID,
    event: 'modalMoreInfoClicked',
    pageSection: 'modal',
    linkTitle: linkTitle,
    modalID: modalID,
    modalTitle: modalTitle,
    url: url
  });
}

function setupModalImagesLinkTracking(modalID) {
  document.querySelectorAll('.accordion-body a').forEach(function(link) {
    link.addEventListener('click', function(event) {
      // Find the closest .accordion-item
      const accordionItem = event.currentTarget.closest('.accordion-item');
      const accordionButton = accordionItem ? accordionItem.querySelector('.accordion-header .accordion-button') : null;
      const buttonText = accordionButton.textContent.trim();
      // Check if the button has the class "More Info"
      if (buttonText === 'Images') {
        const linkTitle = link.textContent.trim();
        const url = link.href;

        // Get modal title from #modal-title
        const modalTitleElement = document.getElementById('modal-title');
        const modalTitle = modalTitleElement ? modalTitleElement.textContent.trim() : 'Unknown Title';

        // Push to dataLayer
        modalImagesLinkClicked(linkTitle, modalID, url, modalTitle, gaMeasurementID);
      }
    });
  });
}


//SCENE TRACKING AND ANALYSIS_______________________________________________________________
/**
 * Pushes a 'sceneLoaded' event to the dataLayer with details about the scene.
 *
 * @param {string} title - The title of the scene being loaded.
 * @param {number} sceneID - The ID of the post associated with the scene.
 */
function sceneLoaded(title, sceneID, instance_overview_scene, gaMeasurementID) {
  //console.log(title, sceneID, instance_overview_scene, gaMeasurementID); 
  dataLayer.push({
    GA4_MeasurementID: gaMeasurementID,
    event: 'sceneLoaded', 
    pageSection: 'scene',
    title:  title,
    sceneID:  sceneID,
    instance: instance_overview_scene
  });
}


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