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
        <label for="statusShipment">Status:</label>
        <input name="status_shipment" id="status_shipment" placeholder="Enter shipment status" value="<?= htmlspecialchars($statusShipment, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div>
        <label for="shipWeight">Ship Weight:</label>
        <input type="number" step="0.01" name="ship_weight" id="ship_weight" placeholder="Weight of the shipment" value="<?= htmlspecialchars($shipWeight, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div>
        <label for="passengerAmount">Passenger Amount:</label>
        <input type="number" name="passenger_amount" id="passenger_amount" value="<?= htmlspecialchars($passengerAmount, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div>
        <label for="dateSent">Date sent:</label>
        <input type="datetime-local" name="date_sent" id="date_sent" value="<?= htmlspecialchars($dateSent, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div>
        <p>Date arrived: <?php if ($dateReceived) { echo htmlspecialchars($dateReceived, ENT_QUOTES, 'UTF-8'); } else { echo "Not received yet"; } ?></p>
    </div>
    <div>
        <label for="deliverFromUserId">Deliver From User ID:</label>
        <input type="number" name="deliver_from_user_id" id="deliver_from_user_id" value="<?= htmlspecialchars($deliverFromUserId, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div>
        <label for="deliverToUserId">Deliver To User ID:</label>
        <input type="number" name="deliver_to_user_id" id="deliver_to_user_id" value="<?= htmlspecialchars($deliverToUserId, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div>
        <label for="delivererUserId">Deliverer User ID:</label>
        <input type="number" name="deliverer_user_id" id="deliverer_user_id" value="<?= htmlspecialchars($delivererUserId, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div>
        <p>Registered by user ID: <?= htmlspecialchars($registeredByUserId, ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
    <div>
        <label for="fromAddressId">From Address ID:</label>
        <input type="number" name="from_address_id" id="from_address_id" value="<?= htmlspecialchars($fromAddressId, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div>
        <label for="toAddressId">To Address ID:</label>
        <input type="number" name="to_address_id" id="to_address_id" value="<?= htmlspecialchars($toAddressId, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div>
        <label for="deliveryContactInfo">Delivery Contact Info:</label>
        <input type="text" name="delivery_contact_info" id="delivery_contact_info" value="<?= htmlspecialchars($delivery_contact_info, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div>
        <p>Price: <?= htmlspecialchars($exactPrice, ENT_QUOTES, 'UTF-8'); ?> BGN</p>
    </div>
    <div>
        <label for="isPaid">Is Paid:</label>
        <input type="checkbox" name="is_paid" id="is_paid" <?= $isPaid ? 'checked' : ''; ?>>
    </div>
    <button type="submit">Submit</button>
    
</form>
    <?php if ($shipment): ?>
        <a class="cancel-link" href="/logistic-company/views/shipment.php?id=<?= $shipment['id']; ?>">Cancel</a>
    <?php else: ?>
        <a class="cancel-link" href="/logistic-company/index.php">Cancel</a>
    <?php endif; ?>
</div>