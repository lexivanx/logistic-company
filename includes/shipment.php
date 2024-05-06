<?php if (!empty($errors)): ?>
    <ul>
        <?php foreach ($errors as $error) { ?>
            <li class="error-message"><?= $error; ?></li>
        <?php } ?>
    </ul>
<?php endif; ?>
<div class="shipment-details">
<form method="post">
    <?php if ($shipment): ?>
            <p>Shipment ID: <?= $shipment['id']; ?></p>
    <?php endif; ?>
    <div>
        <label for="shipWeight">Ship Weight:</label>
        <input type="number" step="0.01" name="ship_weight" id="ship_weight" placeholder="Weight of the shipment" value="<?= htmlspecialchars($shipWeight, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div>
        <label for="passengerAmount">Passenger Amount:</label>
        <input type="number" name="passenger_amount" id="passenger_amount" placeholder="Passengers to transport" value="<?= htmlspecialchars($passengerAmount, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div>
        <label for="dateSent">Date sent:</label>
        <input type="datetime-local" name="date_sent" id="date_sent" value="<?= htmlspecialchars($dateSent, ENT_QUOTES, 'UTF-8'); ?>">
        <p>(Leave empty for current date and time)</p>
    </div>
    <div>
        <p>Date arrived: <?php if ($dateReceived) { echo htmlspecialchars($dateReceived, ENT_QUOTES, 'UTF-8'); } else { echo "Not received yet"; } ?> (can be completed on shipment page)</p>
    </div>
    <div>
        <label for="deliver_from_full_name">Sender name:</label>
        <input name="deliver_from_full_name" id="deliver_from_full_name" placeholder="Default user is you" value="<?= htmlspecialchars($deliver_from_full_name, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div>
        <label for="deliver_to_full_name">Recipient name:</label>
        <input name="deliver_to_full_name" id="deliver_to_full_name" placeholder="Not required" value="<?= htmlspecialchars($deliver_to_full_name, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div>
        <p>Registered by user ID: <?= htmlspecialchars($registered_by_full_name, ENT_QUOTES, 'UTF-8'); ?> (automatically asigned)</p>
    </div>
    <div>
        <p>Source address</p>
        <label for="from_country">Country:</label>
        <input name="from_country" id="from_country" value="<?= htmlspecialchars($from_country, ENT_QUOTES, 'UTF-8'); ?>">
        <label for="from_city">City:</label>
        <input name="from_city" id="from_city" value="<?= htmlspecialchars($from_city, ENT_QUOTES, 'UTF-8'); ?>">
        <label for="from_street">Street:</label>
        <input name="from_street" id="from_street" value="<?= htmlspecialchars($from_street, ENT_QUOTES, 'UTF-8'); ?>">
        <label for="from_street_number">Street number:</label>
        <input type="number" name="from_street_number" id="from_street_number" value="<?= htmlspecialchars($from_street_number, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div>
        <p>Destination address</p>
        <label for="to_country">Country:</label>
        <input name="to_country" id="to_country" value="<?= htmlspecialchars($to_country, ENT_QUOTES, 'UTF-8'); ?>">
        <label for="to_city">City:</label>
        <input name="to_city" id="to_city" value="<?= htmlspecialchars($to_city, ENT_QUOTES, 'UTF-8'); ?>">
        <label for="to_street">Street:</label>
        <input name="to_street" id="to_street" value="<?= htmlspecialchars($to_street, ENT_QUOTES, 'UTF-8'); ?>">
        <label for="to_street_number">Street number:</label>
        <input type="number" name="to_street_number" id="to_street_number" value="<?= htmlspecialchars($to_street_number, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div>
        <label for="deliveryContactInfo">Delivery Contact Info:</label>
        <input type="text" name="delivery_contact_info" id="delivery_contact_info" value="<?= htmlspecialchars($delivery_contact_info, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div>
        <p>Price: <?= htmlspecialchars($exactPrice, ENT_QUOTES, 'UTF-8'); ?> BGN</p>
    </div>
    <?php if ($shipment): ?>
    <div>
        <label for="statusShipment">Status:</label>
        <input name="status_shipment" id="status_shipment" placeholder="Enter shipment status" value="<?= htmlspecialchars($statusShipment, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div>
        <label for="deliverer_employee_name">Delivery employee name:</label>
        <input name="deliverer_employee_name" id="deliverer_employee_name" value="<?= htmlspecialchars($deliverer_employee_name, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div>
        <label for="isPaid">Is Paid:</label>
        <input type="checkbox" name="is_paid" id="is_paid" <?= $isPaid ? 'checked' : ''; ?>>
    </div>        
    <?php endif; ?>
    <button type="submit">Submit</button>
    <?php if ($shipment): ?>
        <a class="cancel-link" href="/logistic-company/views/shipment.php?id=<?= $shipment['id']; ?>">Cancel</a>
    <?php else: ?>
        <a class="cancel-link" href="/logistic-company/index.php">Cancel</a>
    <?php endif; ?>
</form>
</div>