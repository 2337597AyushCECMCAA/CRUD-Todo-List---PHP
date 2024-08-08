<?php 
$insert = false;
$update = false;
$delete = false;

$server = "localhost";
$username = "root";
$password = "";
$database = "crudphp";

$conn = mysqli_connect($server, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_POST['snoEdit'])) {
        // Update the record
        $sno = $_POST['snoEdit'];
        $title = $_POST['titleEdit'];
        $description = $_POST['descriptionEdit'];

        $sql = "UPDATE `notes` SET `title` = '$title', `description` = '$description' WHERE `sno` = $sno";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $update = true;
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "The record was not updated successfully due to this error: " . mysqli_error($conn);
        }
    } else {
        // Insert the record
        $title = $_POST['title'];
        $description = $_POST['description'];

        $sql = "INSERT INTO `notes` (`title`, `description`) VALUES ('$title', '$description')";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $insert = true;
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "The record was not inserted successfully due to this error: " . mysqli_error($conn);
        }
    }
}

if (isset($_GET['delete'])) {
    $sno = $_GET['delete'];
    $sql = "DELETE FROM `notes` WHERE `sno` = $sno";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $delete = true;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "The record was not deleted successfully due to this error: " . mysqli_error($conn);
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">

    <title>CRUD-ToDo</title>
    <style>
        body{
            padding-top: 30px;
        }



    </style>
</head>
<body>
<?php if ($insert): ?>
    <div class='alert alert-success alert-dismissible fade show' role='alert'>
        <strong>Success!</strong> Your Note has been inserted successfully.
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>
<?php elseif ($update): ?>
    <div class='alert alert-success alert-dismissible fade show' role='alert'>
        <strong>Success!</strong> Your Note has been updated successfully.
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>
<?php elseif ($delete): ?>
    <div class='alert alert-success alert-dismissible fade show' role='alert'>
        <strong>Success!</strong> Your Note has been deleted successfully.
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>
<?php endif; ?>

<div class="container">
    <h2 style="text-align: center; margin: bottom 30px;">Add A Note.</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <div class="mb-3">
            <label for="title" class="form-label">Note Title</label>
            <input type="text" class="form-control" id="title" name="title">
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Note Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add Note</button>
    </form>
</div>

<div class="container mt-3">
    <table class="table" id="myTable">
        <thead>
            <tr>
                <th scope="col">S.No</th>
                <th scope="col">Title</th>
                <th scope="col">Description</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $sql = "SELECT * FROM `notes`";
            $result = mysqli_query($conn, $sql);
            $sno = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                $sno++;
                echo "<tr>
                    <th scope='row'>$sno</th>
                    <td>".$row['title']."</td>
                    <td>".$row['description']."</td>
                    <td>
                        <button class='edit btn btn-sm btn-primary' id=".$row['sno'].">Edit</button>
                        <button class='delete btn btn-sm btn-danger' id=d".$row['sno'].">Delete</button>
                    </td>
                </tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit this Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="hidden" name="snoEdit" id="snoEdit">
                    <div class="mb-3">
                        <label for="titleEdit" class="form-label">Note Title</label>
                        <input type="text" class="form-control" id="titleEdit" name="titleEdit">
                    </div>
                    <div class="mb-3">
                        <label for="descriptionEdit" class="form-label">Note Description</label>
                        <textarea class="form-control" id="descriptionEdit" name="descriptionEdit" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="//cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>

<script>
$(document).ready(function() {
    let table = new DataTable('#myTable');

    // Delegate event binding for edit buttons
    $('#myTable tbody').on('click', '.edit', function() {
        let tr = $(this).closest('tr');
        let title = tr.find('td:eq(1)').text();
        let description = tr.find('td:eq(2)').text();
        $('#titleEdit').val(title);
        $('#descriptionEdit').val(description);
        $('#snoEdit').val($(this).attr('id'));
        $('#editModal').modal('show');
    });

    // Delegate event binding for delete buttons
    $('#myTable tbody').on('click', '.delete', function() {
        let sno = $(this).attr('id').substr(1);

        if (confirm("Are you sure you want to delete this note?")) {
            window.location = "<?php echo $_SERVER['PHP_SELF']; ?>?delete=" + sno;
        }
    });
});

</script>

</body>
</html>
