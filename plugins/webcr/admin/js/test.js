console.log("hello");
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('editinline')) {
            // Hide all fields in the Quick Edit form
            document.querySelectorAll('.inline-edit-post .inline-edit-group').forEach(function(group) {
                group.style.display = 'none';
            });

            // Show the Title field
            var titleField = document.querySelector('.inline-edit-post #title');
            if (titleField) {
                titleField.parentElement.style.display = 'block';
            }

            // Show the Slug field
            var slugField = document.querySelector('.inline-edit-post #editable-post-name');
            if (slugField) {
                slugField.parentElement.style.display = '';
            }
        }
    });
});