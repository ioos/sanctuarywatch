<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Field: Upload
 *
 */
/**
 * Info about JavaScript uploaders
 *
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Field_upload' ) ) {

	class Exopite_Simple_Options_Framework_Field_upload extends Exopite_Simple_Options_Framework_Fields {

		public function __construct( $field, $value = '', $unique = '', $config = array() ) {

			parent::__construct( $field, $value, $unique, $config );

			$defaults = array(
				'attach'                   => false,
				'filecount'                => 1,
				'delete-enabled'           => true,
				'delete-force-confirm'     => true,
				'retry-enable-auto'        => true,
				'retry-max-auto-attempts'  => 1,
				'retry-auto-attempt-delay' => 2,
				'auto-upload'              => false,
			);

			$options = ( ! empty( $this->field['options'] ) ) ? $this->field['options'] : array();

			$this->field['options'] = wp_parse_args( $options, $defaults );

		}

		public function output() {
			echo $this->element_before();
			?>

			<?php		
			// Expoite array variables from the FILE UPLOAD ARRAY BOX field in class-webcr-figure.php
			//$maxsize = $this->field['options']['maxsize']; //not used

			// WP variables for post values in database
			$post_id = get_the_ID();
			$instance_id = get_post_meta( $post_id, 'location', true );
			$uploaded_path_csv = get_post_meta( $post_id, 'uploaded_path_csv', true );
			$uploaded_path_json = get_post_meta( $post_id, 'uploaded_path_json', true );

			// Check if a file exists in postmeta before rendering the file input button
			$existing_file = get_post_meta($post_id, 'uploaded_file', true);
			$file_label = $existing_file ? 'Current File: ' . basename($existing_file) : '';

			// Style for the .custom-div where the light grey text is located. 
			echo '<style>
			.custom-div {
				color: #aaa; /* Light grey text */
				font-size: 13px; /* Text size */
				text-align: left; /* Left justified */
			}
			</style>';

			?>
			<!-- Custom form elements for the file select, upload, and -->
			<form id="custom-file-upload" enctype="multipart/form-data">
				<input type="hidden" id="existing-file-name" value="<?php echo esc_attr(basename($existing_file)); ?>">
				<input type="hidden" name="post_id" value="<?php echo get_the_ID(); ?>"> 
				<label for="uploaded-file" id="file-label"><?php echo esc_html($file_label); ?></label>
				<?php if (!$existing_file): ?>
					<input type="file" name="uploaded_file" id="uploaded-file" accept=".json, .csv"><input type="hidden" name="post_id" value="<?php echo get_the_ID(); ?>">
					<button type="button" id="upload-btn">Upload</button>
				<?php endif; ?>
				<?php if ($existing_file): ?>
					<button type="button" id="delete-btn">Delete File</button>
				<?php endif; ?>
			</form>

			<div class="custom-div">
				<?php
				echo '<br>';
				echo '<strong>Upload Information:</strong><br>';
				echo esc_attr__( 'Max amount of files: ', 'exopite-sof' ) . $this->field['options']['filecount'] . '<br>';
				echo esc_attr__( 'Allowed file types: ', 'exopite-sof' ) . '.csv, .json'  . '<br><br>';

				// Output links to example files
				// Define the folder path inside wp-content
				$example_folder = get_site_url() . '/wp-content/data/example_files/';

				// Example file names
				$example_csv = 'example.csv';
				$example_json = 'example.json';

				echo '<strong>Example Files:</strong><br>';
				echo 'Please format your file as shown in the examples below. If they are not formatted properly, your file will be rejected.<br>';
				echo ' - For .csv and/or .json files, be sure none of your columns header names or row data values contain commas.<br>';
				echo ' - Please see the date examples in the example files for accepted date formats. <br>';
				echo ' - The date formats are best viewed in Notepad or a similar text editor, MS Excel may automatically change date formats. <br>';
				echo '<a href="' . esc_url($example_folder . $example_csv) . '" target="_blank">Download example.csv</a><br>';
				echo '<a href="' . esc_url($example_folder . $example_json) . '" target="_blank">Download example.json</a>';
				?>
			</div>


			<script>
			/**
			 * Converts a CSV string into a structured JSON object.
			 * 
			 * @param {string} csvString - The CSV input as a string.
			 * @returns {Object} - The converted JSON object with metadata and data.
			 */
			function csvToJson(csvString) {
				// Split the CSV string into an array of lines, trim whitespace, and remove empty lines
				const lines = csvString.split('\n').map(line => line.trim()).filter(line => line.length > 0);
				
				// Extract the first line as headers and trim whitespace from each header
				const headers = lines[0].split(',').map(header => header.trim());
				
				// Initialize an empty object to store the result
				const result = {};
				
				// Create an array for each header key in the result object
				headers.forEach(header => {
					result[header] = [];
				});
				
				// Iterate through the remaining lines (data rows)
				for (let i = 1; i < lines.length; i++) {
					// Split each line by commas and trim whitespace
					const values = lines[i].split(',').map(value => value.trim());
					
					// Assign values to corresponding headers
					headers.forEach((header, index) => {
						let parsedValue = values[index];
						
						// Convert numeric values to integers or floats, keep non-numeric as strings
						if (!isNaN(parsedValue) && parsedValue.trim() !== "") {
							parsedValue = parsedValue.includes('.') ? parseFloat(parsedValue) : parseInt(parsedValue, 10);
						}
						
						// Push the parsed value into the respective header array
						result[header].push(parsedValue);
						//result[header][i - 1] = parsedValue;
					});
				}
				
				// Initialize an empty metadata object
				const metadata_result = {};

				// Wrap the result object inside a "data" key and return it along with metadata
				return { metadata: metadata_result, data: result };
			}

			</script>

			
			<script>
			//JSON formatter______________________________________________________________________
			function formatJsonCompact(obj) {
				//let jsonStr = '{\n    "data": {\n';
				let jsonStr = '{\n    "metadata": {},\n    "data": {\n';
				const keys = Object.keys(obj.data);
				
				keys.forEach((key, index) => {
					const values = obj.data[key].map(value => 
						typeof value === "string" ? `"${value}"` : value // Keep string values quoted
					);
					
					jsonStr += `        "${key}": [${values.join(',')}]`;
					if (index < keys.length - 1) {
						jsonStr += ',\n'; // Add comma only between items
					}
				});
				
				jsonStr += '\n    }\n}';
				return jsonStr;
			}
			</script>




			<script>
			//JSON Validator______________________________________________________________________
			function validateJson(json) {
				if (typeof json !== 'object' || json === null) return false;
				if (!json.metadata || typeof json.metadata !== 'object') return false;
				if (!json.data || typeof json.data !== 'object') return false;
				
				const keys = Object.keys(json.data);
				//if (!keys.includes("Year")) return false;
				//if (!Array.isArray(json.data.Year) || json.data.Year.some(y => typeof y !== 'number')) return false;
				
				for (let key of keys) {
					if (!Array.isArray(json.data[key])) return false;
				}
				
				return true;
			}
			</script>


			<script>
			// Delete Function______________________________________________________________________
			function deleteUploadedFile() {

				//Select an existing uploaded file, or the file you just attempted to upload that is not formatted correctly for deletion. 
				try {
					var fileNameInput = document.getElementById('existing-file-name');
					if (!fileNameInput || !fileNameInput.value) {
						//alert("Error: No file to delete.");
						console.error("Filename input error:", error);
						return;
					}
					var fileName = fileNameInput.value;
				} catch (error) {
					var fileNameInput = document.getElementById('uploaded-file');
					var file = fileNameInput.files[0];
					var fileName = file.name.toLowerCase();
				}
				
				//Get the post ID
				var postIdInput = document.querySelector('[name="post_id"]');
				if (!postIdInput || !postIdInput.value) {
					alert("Error: Post ID is missing in the form!");
					return;
				}

				var postId = postIdInput.value;
				
				var formData = new FormData();
				formData.append('post_id', postId);
				formData.append('file_name', fileName); // Send only the stored filename
				formData.append('action', 'custom_file_delete'); // Match WordPress AJAX action

				console.log("Sending post_id:", postId, "file_name:", fileName); // Debugging

				fetch('<?php echo admin_url("admin-ajax.php"); ?>', { // Correct URL
					method: 'POST',
					body: formData,
					credentials: 'same-origin'
				})
				.then(response => response.json())
				.then(data => {
					console.log("Server response:", data); // Debugging
					if (data.success) {
						alert("Success: " + (data.message || "File deleted successfully."));
						location.reload(); // Refresh the page to reflect deletion
					} else {
						alert("Error: " + (data.message || "Delete failed."));
					}
				})
				.catch(error => {
					console.error("Delete error:", error);
					alert("Delete failed: " + error.message);
				});
			}
			</script>

			<script>
			// Trigger Delete button when clicked______________________________________________________________________
			document.getElementById('delete-btn').addEventListener('click', deleteUploadedFile);
				console.log("Delete button clicked!"); // Debugging
			</script>

			<script>
			// Do not allow access to the upload button if it is a new post. 
			document.addEventListener("DOMContentLoaded", function () {
				var uploadBtn = document.getElementById('upload-btn');
				var fileInput = document.getElementById('uploaded-file');

				// Check if URL contains "post-new.php?post_type=figure"
				if (window.location.href.includes("post-new.php?post_type=figure")) {
					if (uploadBtn) {
						uploadBtn.disabled = true; // Disable the button
						uploadBtn.style.opacity = "0.5"; // Make it look inactive
					}

					if (fileInput) {
						fileInput.disabled = true; // Disable file input as well
					}

					// Show a message above the button
					var message = document.createElement("p");
					message.textContent = "⚠️ You must save this post before uploading a file to create an interactive figure.";
					message.style.color = "red";
					message.style.fontWeight = "bold";
					uploadBtn.parentNode.insertBefore(message, uploadBtn);
				}
			});			
			</script>

			<script>
			// Upload button and .csv call to csvtojson converter and .json call to json validator______________________________________________________________________
			document.getElementById('upload-btn').addEventListener('click', function() {
				
				// Validation of variables form html and php
				var fileInput = document.getElementById('uploaded-file');
				var uploadBtn = document.getElementById('upload-btn');
				var uploadMessage = document.createElement("p");

				if (!fileInput.files.length) {
					alert("Please select a file before uploading.");
					return;
				}

				var formData = new FormData();
				var file = fileInput.files[0];
				var fileName = file.name.toLowerCase();

				// Append uploaded_file to form to send to AJAX
				formData.append('uploaded_file', file);

				var postIdInput = document.querySelector('[name="post_id"]');
				if (!postIdInput || !postIdInput.value) {
					alert("Error: Post ID is missing in the form!");
					return;
				}

				var postId = postIdInput.value;
				if (postId == '') {
					alert("Error: Post ID is missing in the form!");
					return;
				}
				
				// Append post_id and the action to trigger custom_file_upload to form to send to AJAX
				formData.append('post_id', postId);
				formData.append('action', 'custom_file_upload'); // Required for WordPress AJAX

				// AJAX processing request
				console.log("Sending post_id:", postId);
				fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
					method: 'POST',
					body: formData,
					credentials: 'same-origin'
				})
				.then(response => response.json())
				.then(data => {
					console.log("Server response:", data);
					// If the Ajax Request was successful
					if (data.success) {
						// If the file is a .csv file, trigger CSV-to-JSON conversion
						if (fileName.endsWith(".csv")) {
							var reader = new FileReader();
							reader.onload = function(event) {
								var csvData = event.target.result;
								var jsonData;

								try {
									jsonData = csvToJson(csvData); // Convert CSV to JSON
								} catch (error) {
									deleteUploadedFile();
									alert("CSV Conversion Failed: " + error.message);
									console.error("CSV Conversion Error:", error);
									return; // Stop execution if CSV is invalid
								}

								// Convert JSON object to a Blob
								//var jsonBlob = new Blob([JSON.stringify(jsonData, null, 0)], { type: "application/json" }); //standard JS way of precessing that doesn't give what Jai wants.
								var jsonBlob = new Blob([formatJsonCompact(jsonData)], { type: "application/json" });
								var json_fileName = fileName.replace('.csv', '.json');

								// Convert Blob to a File
								var jsonFile = new File([jsonBlob], json_fileName, { type: "application/json" });

								// Prepare FormData for AJAX request
								var formData_csvtojson = new FormData();
								formData_csvtojson.append('uploaded_file', jsonFile);
								formData_csvtojson.append('post_id', postId);
								formData_csvtojson.append('action', 'custom_file_upload');

								console.log("Sending JSON file to server:", json_fileName);

								fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
									method: 'POST',
									body: formData_csvtojson,
									credentials: 'same-origin'
								})
								.then(response => response.json())
								.then(data => {
									console.log("Server response:", data);
									if (data.success) {
										alert("Success: " + (data.message || "File Upload Successful.") +  "\n" +
											"\nClick 'Update' button in the top-right to save your changes or access the delete button.");

										// Hide the upload button and the file input
										uploadBtn.style.display = "none";
										fileInput.style.display = "none";

										// Display message after hiding the buttons
										uploadMessage.textContent = `Current File: ${fileName}, Click 'Update' button in the top-right to save your changes and/or access the delete button.`;
										uploadBtn.parentNode.insertBefore(uploadMessage, uploadBtn.nextSibling);
									} else {
										alert("Error: " + (data.message || "Something went wrong."));
									}
								})
								.catch(error => {
									console.error("Upload error:", error);
									alert("Upload failed: " + error.message);
								});
							};
							reader.readAsText(file);


						}
						// If the file is a .json file, trigger json validation script
						if (fileName.endsWith(".json")) {
							var reader = new FileReader();

							reader.onload = function(event) {
								try {
									var jsonData = JSON.parse(event.target.result); // Parse the file content into JSON
									var isValid = validateJson(jsonData); // Validate the JSON

									if (!isValid) {
										throw new Error("JSON validation failed.");
									}

									alert("Success: " + (data.message || "File Upload Successful.") + "\n\n" +
										"Click 'Update' button in the top-right to save your changes or access the delete button.");

									// Hide the upload button and the file input
									uploadBtn.style.display = "none";
									fileInput.style.display = "none";

									// Display message after hiding the buttons
									uploadMessage.textContent = `Current File: ${fileName}\n\nClick 'Update' button in the top-right to save your changes and/or access the delete button.`;
									uploadBtn.parentNode.insertBefore(uploadMessage, uploadBtn.nextSibling);
								} catch (error) {
									deleteUploadedFile();
									alert("JSON Validation Failed: " + error.message);
									console.error("JSON Validation Error:", error);
								}
							};
							reader.readAsText(file); // Read file content as text
											
						} 
						else {
							// If the file is NOT a CSV or a Json, proceed with the normal success message. This would be if you allowed for more file types.
// 							alert("Success: " + (data.message || "File Upload Successful.") + "\n" +
// 									"\nClick 'Update' button in the top-right to save your changes or access the delete button.");

// ``							// Hide the upload button and the file input
// 							uploadBtn.style.display = "none";
// 							fileInput.style.display = "none";

// 							// Display message after hiding the buttons
// 							uploadMessage.textContent = `Current File: ${fileName}, Click 'Update' button in the top-right to save your changes and/or access the delete button.`;
// 							uploadBtn.parentNode.insertBefore(uploadMessage, uploadBtn.nextSibling);``
						} 
					}
					// If the Ajax Request was not successful			
					if (!data.success) {
						console.error("Upload error:", error);
						alert("Upload failed: Data was not successfully Sent: " + error.message)
					}
				})
				.catch(error => {
					console.error("Upload error:", error);
					alert("Upload failed: " + error.message);
				});
			});
			</script>

			<?php

		}
	}
}

