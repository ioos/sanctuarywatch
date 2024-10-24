<?php
/**
 * Register class that defines the Figure custom content type as well as associated Modal functions 
 * 
 */
include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-webcr-utility.php';
class Webcr_Export_Figures {

    public function __construct( $plugin_name ) {
		$this->plugin_name = $plugin_name;
	}

    /**
	 * Function to add the "Export Figures" submenu under Tools
	 *
	 * @since    1.0.0
	 */
    public function add_export_figures_menu() {
        add_submenu_page(
            'tools.php',              // Parent slug - adding it under 'Tools'
            'Export Figures',         // Page title
            'Export Figures',         // Menu title
            'manage_options',         // Capability required to see the option
            'export-figures',         // Slug (used in the URL)
            [$this, 'export_figures_page']     // Callback function to output the page content
        );
    }

    // Callback function to display the content of the "Export Figures" page
    public function export_figures_page() {
        ?>
        <div class="wrap">
            <h1>Export Figures</h1>
            <p>Select an Instance for figure export:</p>
            <p>
            <?php
                // get list of locations
                $function_utilities = new Webcr_Utility();
                $locations = $function_utilities -> returnAllInstances();

                echo '<select id="location" name="location">'; // Opening the <select> tag
                foreach ($locations as $key => $value) {
                    echo '<option value="' . $key . '">' . $value . '</option>'; // Dynamically generating options
                }
                echo '</select>'; // Closing the <select> tag
            ?></p>
            <p><button id="testbutton">Download Test RTF</button></p>
            <p><button id="chooseInstance">Choose Instance</button></p>
            <!-- Form to trigger export -->
            <form method="post" action="">
                <input type="hidden" name="export_figures_action" value="export" />
                <?php submit_button('Export Figures'); ?>
            </form>
        </div>
        <?php
        
        // Handle export logic when the form is submitted
        if (isset($_POST['export_figures_action']) && $_POST['export_figures_action'] === 'export') {
            $this -> export_figures_data();
        }
    }

    // Function to handle exporting the data (CSV example)
    function export_figures_data() {
        // Example data to export (can be dynamic from your database)
        $data = [
            ['ID', 'Figure', 'Value'],
            [1, 'Revenue', '10000'],
            [2, 'Profit', '5000'],
            [3, 'Expenses', '2000']
        ];

        // Set headers to force download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="export_figures.csv"');

        // Open output stream to write CSV
        $output = fopen('php://output', 'w');

        // Loop through data and write to CSV
        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }
}