<?php ob_start(); ?>

<div class="card">
    <h2>Submit Leave</h2>
</div>

<div class="card">

    <form method="POST" action="/leave-system/public/leave-store">

        <div class="form-group">
            <label>Leave Type</label>
            <select name="leave_type_id" required>
                <option value="1">Annual Leave</option>
            </select>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Start Date</label>
                <input type="date" name="start_date" required>
            </div>

            <div class="form-group">
                <label>End Date</label>
                <input type="date" name="end_date" required>
            </div>
        </div>

        <div class="form-group">
            <label>Duration</label>
            <select name="duration_type" required>
                <option value="full">Full Day</option>
                <option value="half">Half Day</option>
            </select>
        </div>

        <div class="form-group">
            <label>Reason</label>
            <textarea name="reason" rows="4"></textarea>
        </div>

        <br>

        <button class="btn btn-primary">
            Submit Request
        </button>

    </form>

</div>

<style>

.form-group {
    margin-bottom: 20px;
    display: flex;
    flex-direction: column;
}

.form-row {
    display: flex;
    gap: 20px;
}

.form-row .form-group {
    flex: 1;
}

label {
    font-size: 13px;
    margin-bottom: 6px;
    color: #64748b;
}

input, select, textarea {
    padding: 12px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    font-size: 14px;
    transition: all 0.25s ease;
}

input:focus,
select:focus,
textarea:focus {
    outline: none;
    border: 1px solid #f97316;
    box-shadow: 0 0 0 3px rgba(249,115,22,0.15);
}

</style>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>