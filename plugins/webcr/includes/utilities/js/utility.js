//utility functions used in lots of places
export function loadExternalScript(url) {
    return new Promise((resolve, reject) => {
      // Check if script is already loaded
      if (document.querySelector(`script[src="${url}"]`)) {
          resolve();
          return;
      }
  
      const script = document.createElement('script');
      script.type = 'text/javascript';
      script.src = url;
      script.async = true;
  
      script.onload = () => {
          resolve();
      };
  
      script.onerror = () => {
          reject(new Error(`Failed to load script: ${url}`));
      };
  
      document.head.appendChild(script);
    });
  }
  