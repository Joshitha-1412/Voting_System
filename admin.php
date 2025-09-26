<?php
include 'functions.php';

// Handle Add Candidate
if(isset($_POST['add_candidate'])){
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $img = $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], 'assets/images/'.$img);
    addCandidate($name,$desc,$img);
}

// Handle Remove Candidate
if(isset($_GET['remove_id'])){
    removeCandidate($_GET['remove_id']);
}

// Handle Publish Result
if(isset($_POST['publish'])){
    setPublished(1);
}

$candidates = getCandidates();
$results = getResults();
$published = isPublished();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<h1>Admin Panel</h1>

<h2>Add Candidate</h2>
<form method="POST" enctype="multipart/form-data">
    <input type="text" name="name" placeholder="Name" required>
    <textarea name="description" placeholder="Description"></textarea>
    <input type="file" name="image" required>
    <button type="submit" name="add_candidate">Add Candidate</button>
</form>

<h2>All Candidates</h2>
<table border="1">
<tr><th>Name</th><th>Image</th><th>Description</th><th>Votes</th><th>Action</th></tr>
<?php foreach($candidates as $c): ?>
<tr>
    <td><?= $c['name'] ?></td>
    <td><img src="assets/images/<?= $c['image'] ?>" width="100"></td>
    <td><?= $c['description'] ?></td>
    <td><?= $c['votes'] ?></td>
    <td><a href="?remove_id=<?= $c['id'] ?>" onclick="return confirm('Remove?')">Remove</a></td>
</tr>
<?php endforeach; ?>
</table>

<h2>Publish Result</h2>
<form method="POST">
    <button type="submit" name="publish" <?= $published?'disabled':'' ?>><?= $published?'Published':'Publish Result' ?></button>
</form>

<?php if($published): ?>
<h2>Results</h2>
<p>Winning Candidate: <?= $results[0]['name'] ?> (Votes: <?= $results[0]['votes'] ?>, Lead: <?= $results[0]['diff'] ?>)</p>

<canvas id="voteChart" width="400" height="200"></canvas>
<script>
const ctx = document.getElementById('voteChart').getContext('2d');
new Chart(ctx,{
    type:'bar',
    data:{
        labels: <?= json_encode(array_column($candidates,'name')) ?>,
        datasets:[{
            label:'Votes',
            data: <?= json_encode(array_column($candidates,'votes')) ?>,
            backgroundColor:'rgba(75,192,192,0.6)'
        }]
    }
});
</script>
<?php endif; ?>
</body>
</html>
