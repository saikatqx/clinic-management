/**
 * Appointment Slot Fetcher
 * Integrates with /appointment/slots endpoint to dynamically load available slots
 */

document.addEventListener('DOMContentLoaded', function () {
    const doctorSelect = document.getElementById('doctor_id');
    const dateInput = document.getElementById('appointment_date');
    const slotsContainer = document.getElementById('slots_container');
    const timeSelect = document.getElementById('appointment_time');

    if (!doctorSelect || !dateInput || !slotsContainer) {
        return; // Elements not present on page
    }

    async function fetchSlots() {
        const doctorId = doctorSelect.value;
        const date = dateInput.value;

        if (!doctorId || !date) {
            slotsContainer.innerHTML = '<p class="text-muted">Select a doctor and date</p>';
            return;
        }

        try {
            const response = await fetch('/appointment/slots?doctor_id=' + doctorId + '&date=' + date);
            const data = await response.json();

            if (!data.slots || data.slots.length === 0) {
                slotsContainer.innerHTML = '<p class="alert alert-warning">No available slots for this date.</p>';
                return;
            }

            let html = '<label for="appointment_time">Select Time:</label><select id="appointment_time" class="form-control" name="appointment_time" required>';
            html += '<option value="">-- Choose a time --</option>';

            data.slots.forEach(slot => {
                html += '<option value="' + slot.datetime + '">' + slot.time + '</option>';
            });

            html += '</select>';
            slotsContainer.innerHTML = html;

            // Update the hidden datetime field when user selects a slot
            document.getElementById('appointment_time').addEventListener('change', function () {
                if (document.getElementById('appointment_date_time')) {
                    document.getElementById('appointment_date_time').value = this.value;
                }
            });
        } catch (error) {
            console.error('Error fetching slots:', error);
            slotsContainer.innerHTML = '<p class="alert alert-danger">Error loading slots. Please try again.</p>';
        }
    }

    // Fetch slots when doctor or date changes
    doctorSelect.addEventListener('change', fetchSlots);
    dateInput.addEventListener('change', fetchSlots);

    // Initial load
    fetchSlots();
});
