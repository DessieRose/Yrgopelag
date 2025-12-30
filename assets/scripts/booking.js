
const roomSelect = document.getElementById('room_id');
const arrivalInput = document.querySelector('input[name="arrival_date"]');
const departureInput = document.querySelector('input[name="departure_date"]');
const featureCheckboxes = document.querySelectorAll('.feature-checkbox');
const displayTotal = document.getElementById('display-total');

function calculateTotal() {
    let total = 0;

    // 1. Get Room Price
    const selectedRoom = roomSelect.options[roomSelect.selectedIndex];
    const roomPrice = parseFloat(selectedRoom.getAttribute('data-price')) || 0;

    // 2. Calculate Days
    const arrival = new Date(arrivalInput.value);
    const departure = new Date(departureInput.value);
    
    let days = 0;
    if (arrival && departure && departure > arrival) {
        const diffTime = Math.abs(departure - arrival);
        days = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    }

    // Room total (price * days)
    total += (roomPrice * days);

    // 3. Add Features (only if dates are valid, or add them once per stay)
    featureCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            total += parseFloat(checkbox.getAttribute('data-price'));
        }
    });

    // 4. Update Display
    displayTotal.textContent = `$${total}`;
}

// Add event listeners so it updates whenever something changes
roomSelect.addEventListener('change', calculateTotal);
arrivalInput.addEventListener('change', calculateTotal);
departureInput.addEventListener('change', calculateTotal);
featureCheckboxes.forEach(cb => cb.addEventListener('change', calculateTotal));

// Run once on load to set initial price
calculateTotal();