document.querySelector('.booking-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const rider = document.querySelector('#rider').value;
    const vehicle = document.querySelector('#vehicle').value;
    const date = document.querySelector('#date').value;
    const time = document.querySelector('#time').value;
    const route = document.querySelector('#route').value;
    const comments = document.querySelector('#comments').value;

    console.log("Booking Details:", {
        rider,
        vehicle,
        date,
        time,
        route,
        comments
    });

    // Here, you can add code to send the data to a server or an API.
});
