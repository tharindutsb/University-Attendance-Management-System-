document.addEventListener('DOMContentLoaded', function () {
    // Event listener for form submission
    document.getElementById('attendanceForm').addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent the default form submission

        // Collect form data
        const studentName = document.getElementById('studentName').value;
        const attendanceDate = document.getElementById('attendanceDate').value;

        // Validate form data (you can add more validation here)

        // Send attendance data to the server using Fetch API
        fetch('submitAttendance.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'studentName=' + encodeURIComponent(studentName) + '&attendanceDate=' + encodeURIComponent(attendanceDate),
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(data => {
            // Handle the response from the server (you can customize this part)
            alert('Attendance submitted successfully!\nServer response: ' + data);
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
            alert('An error occurred while submitting attendance. Please try again.');
        });
    });
});
