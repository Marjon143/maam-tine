// JavaScript to handle filtering and View Info functionality

document.addEventListener('DOMContentLoaded', function () {
    // Get the dropdown for filtering by status
    const filterDropdown = document.getElementById('date');
    
    // Get all rows in the table
    const rows = document.querySelectorAll('.availability-table tbody tr');
    
    // Add event listener for the dropdown change
    filterDropdown.addEventListener('change', function () {
        const filterValue = filterDropdown.value;

        // Loop through all rows and filter them based on the selected option
        rows.forEach(row => {
            const statusCell = row.cells[2]; // Status cell
            const statusText = statusCell.textContent.trim().toLowerCase();

            if (filterValue === 'today' || filterValue === 'alltime') {
                row.style.display = ''; // Show all rows (remove filter)
            } else if (filterValue === 'free' && statusText === 'available') {
                row.style.display = ''; // Show row if Available
            } else if (filterValue === 'scheduled' && statusText === 'not available') {
                row.style.display = ''; // Show row if Not Available
            } else {
                row.style.display = 'none'; // Hide the row if it doesn't match
            }
        });
    });

    // Add event listeners to all "View Info" buttons to show more info
    const viewInfoButtons = document.querySelectorAll('.view-info');
    viewInfoButtons.forEach(button => {
        button.addEventListener('click', function () {
            const row = this.closest('tr');
            const riderName = row.cells[0].textContent;
            const vehicleType = row.cells[1].textContent;
            const status = row.cells[2].textContent;
            
            // You can replace this with a more sophisticated modal or detailed page
            alert(`Rider Info:\nName: ${riderName}\nVehicle: ${vehicleType}\nStatus: ${status}`);
        });
    });
});
