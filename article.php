<?php

session_start();

require 'includes/db.php';
require 'includes/article-funs.php';
require 'includes/authentication.php';

## Fetch connection to DB
$db_connection = getDB();

if (isset($_GET['id'])) {

    $article = getArticle($db_connection, $_GET['id']);

} else {

    $article = null;
    
}

?>
<?php require 'includes/header.php'; ?>
    <?php if ($article === null): ?>
        <p class="error-message">No articles found.</p>
    <?php else: ?>

        <article>
            <h3><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
            <p><?=  htmlspecialchars($article['body'], ENT_QUOTES, 'UTF-8'); ?></p>
            <br><br>
            <p>Created by: <strong> <?= htmlspecialchars($article['created_by'], ENT_QUOTES, 'UTF-8'); ?> </strong> </p>
            <p>Created at: <em> <?= htmlspecialchars($article['time_of'], ENT_QUOTES, 'UTF-8'); ?> </em> </p>
        </article>

        <?php if (checkAuthentication()): ?>

            <?php if ($_SESSION['username'] == "admin" || $_SESSION['username'] == $article['created_by']): ?>
                <a href="edit-article.php?id=<?= $article['id']; ?>" class="edit-link">Edit</a>
                <a href="remove-article.php?id=<?= $article['id']; ?>" class="delete-link">Delete</a>
            <?php else: ?>
                <br>
                <p><em>Can't edit or remove!</em></p>
            <?php endif; ?>

        <?php else: ?>
            <br>
            <p><em>Can't edit or remove!</em></p>
        <?php endif; ?>
        
    <?php endif; ?>
<?php require 'includes/footer.php'; ?>