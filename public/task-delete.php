<?php
require_once '../app.php';
require_once join_paths([MODELS_ROOT, 'Task.php']);
$session = session('tasks');
if (!$session->verifyCsrfToken()) {
    $session->set('errors', ['セッションが切れました。ページをリロードしてください！']);
    redirect('./index.php');
}
$id = request_get('id');
Task::delete($id);
redirect('./index.php');
