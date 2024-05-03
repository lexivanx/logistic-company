<?php if (!empty($errors)): ?>
    <ul>
        <?php foreach ($errors as $error) { ?>
            <li class="error-message"><?= $error; ?></li>
        <?php } ?>
    </ul>
<?php endif; ?>

<form method="post">

    <div>
        <label for="title">Title</label>
        <input name="title" id="title" placeholder="Place a name for article" value="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>">
    </div>

    <div>
        <label for="body">Body</label>
        <textarea name="body" rows="10" cols="25" id="body" placeholder="Body of your article"><?= htmlspecialchars($body, ENT_QUOTES, 'UTF-8'); ?></textarea>
    </div>

    <div>
        <label for="time_of">Article date</label>
        <input type="datetime-local" name="time_of" id="time_of" value="<?= htmlspecialchars($time_of, ENT_QUOTES, 'UTF-8'); ?>">
    </div>

    <button>Submit</button>

</form>