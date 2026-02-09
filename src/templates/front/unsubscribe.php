<?php
if (!isset($GLOBAL_INCLUDE_CHECK)) die("Access Denied");
// Get email and token from URL
$email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
$sec_token = isset($_GET['sec_token']) ? htmlspecialchars($_GET['sec_token']) : '';
$key = isset($_GET['key']) ? htmlspecialchars($_GET['key']) : '';

// Validate the 'unsubscribe' key
if ($key !== 'unsubscribe') {
    header('Location: /');
    exit;
}
?>


<div class="container my-5">
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
            <h2 class="card-title text-center">Απεγγραφή από το Newsletter</h2>
            <p class="card-text text-center">Είστε σίγουροι ότι θέλετε να διαγραφείτε από το newsletter μας;</p>
            <form id="unsubscribeForm">
                <input type="hidden" name="email" value="<?php echo $email; ?>">
                <input type="hidden" name="sec_token" value="<?php echo $sec_token; ?>">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-danger">Ναι, επιθυμώ να διαγραφώ</button>
                </div>
            </form>
            <div id="unsubscribeMessage" class="mt-3 text-center"></div>
        </div>
    </div>
</div>


<script>
    document.getElementById('unsubscribeForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('action', 'unsubscribe'); 

        let csrf_token = document.getElementsByName('csrf_token')[0].getAttribute('content');
        formData.append('csrf_token', csrf_token);

        const unsubscribeButton = this.querySelector('button');
        unsubscribeButton.disabled = true;

        fetch('/includes/ajax.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                const messageDiv = document.getElementById('unsubscribeMessage');
                if (data.success) {
                    messageDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                } else {
                    const errorMessage = data.errors.join('<br>');
                    messageDiv.innerHTML = `<div class="alert alert-danger">${errorMessage}</div>`;
                    unsubscribeButton.disabled = false; 
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('unsubscribeMessage').innerHTML = `<div class="alert alert-danger">Σφάλμα κατά τη διαγραφή.</div>`;
                unsubscribeButton.disabled = false; 
            });
    });
</script>