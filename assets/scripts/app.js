document.addEventListener('DOMContentLoaded', () => {
    
    document.querySelectorAll('.room-booking-block').forEach(block => {

        const roomId = block.dataset.roomId;
        // const grid = block.querySelector('.calendar');
        const totalCost = block.querySelector('.total-cost');
        const arrivalInput = block.querySelector('input[name="arrival_date"]');
        const departureInput = block.querySelector('input[name="departure_date"]');
        
        if (!arrivalInput || !departureInput || !totalCost) return; 

        function calculatePrice() {
            const arrival = arrivalInput.value;
            const departure = departureInput.value;
            // const nights = checkOut - checkIn;
            if (!arrival || !departure) return;

            fetch('/app/src/functions.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json',},
                body: JSON.stringify({
                    action: 'calculateTotalCost',
                    room_id: roomId,
                    arrival_date: arrival,
                    departure_date: departure,
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    totalCost.innerHTML = `<strong>Error:</strong> Could not calculate price.`;
                    return;
                }
                totalCost.innerHTML =
                    `<strong>Total:</strong> $${data.totalCost} <br>
                    <small>($${data.nightlyRate} x ${data.nights} nights)</small>`;
            })
            .catch(error => {
                console.error('Error:', error);
                totalCost.innerHTML = `<strong>Error:</strong> Connection failed.`;
            });
            
        }

        arrivalInput.addEventListener('change', calculatePrice);
        departureInput.addEventListener('change', calculatePrice);

        // function draw() {
        //     grid.querySelectorAll('.day:not(.empty)').forEach((div, index) => {
        //         const d = index + 1;
        //         div.className = 'day';
        //         if (d === start || d === end) div.classList.add('selected');
        //         if (d > start && d < end) div.classList.add('range');
        //     });
        // }
    });

});