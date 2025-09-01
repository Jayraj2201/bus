<?php
$conn = new mysqli("localhost", "root", "", "test");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f4f6f8;
            padding: 30px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        #addForm {
            display: none;
            background-color: white;
            padding: 20px;
            margin: 20px auto;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        form label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #333;
        }

        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-top: 30px;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #007BFF;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #e6f0ff;
        }

        .action-buttons button {
            margin: 0 3px;
        }
    </style>
</head>
<body>

    <h2>User Management</h2>
    <div style="text-align: center;">
        <button onclick="toggleAddForm()">Add User/Driver</button>
    </div>

    <div id="addForm">
        <h3 style="margin-bottom: 20px;">Add New User/Driver</h3>
        <form id="newUserForm">
            <label>First Name: <input type="text" name="first_name" required></label>
            <label>Last Name: <input type="text" name="last_name" required></label>
            <label>Email: <input type="email" name="email" required></label>
            <label>Password: <input type="password" name="password" required></label>
            <label>Role:
                <select name="role" required>
                    <option value="admin">Admin</option>
                    <option value="driver">Driver</option>
                    <option value="user">User</option>
                </select>
            </label>
            <div style="margin-top: 15px;">
                <button type="submit">Save</button>
                <button type="button" onclick="toggleAddForm()" style="background-color: #6c757d;">Cancel</button>
            </div>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th><th>First Name</th><th>Last Name</th><th>Email</th>
                <th>Role</th><th>Verified</th><th>Status</th><th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr id="row-<?= $row['id'] ?>">
                <td><?= $row['id'] ?></td>
                <td><span><?= $row['first_name'] ?></span><input type="text" value="<?= $row['first_name'] ?>" style="display:none"></td>
                <td><span><?= $row['last_name'] ?></span><input type="text" value="<?= $row['last_name'] ?>" style="display:none"></td>
                <td><span><?= $row['email'] ?></span><input type="text" value="<?= $row['email'] ?>" style="display:none"></td>
                <td>
                    <span><?= $row['role'] ?></span>
                    <select style="display:none">
                        <option value="admin" <?= $row['role']=='admin'?'selected':'' ?>>admin</option>
                        <option value="driver" <?= $row['role']=='driver'?'selected':'' ?>>driver</option>
                        <option value="user" <?= $row['role']=='user'?'selected':'' ?>>user</option>
                    </select>
                </td>
                <td>
                    <span><?= $row['is_verified'] ? 'Yes' : 'No' ?></span>
                    <select style="display:none">
                        <option value="1" <?= $row['is_verified'] ? 'selected' : '' ?>>Yes</option>
                        <option value="0" <?= !$row['is_verified'] ? 'selected' : '' ?>>No</option>
                    </select>
                </td>
                <td>
                    <span><?= $row['status'] ? 'Active' : 'Inactive' ?></span>
                    <select style="display:none">
                        <option value="1" <?= $row['status'] ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= !$row['status'] ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </td>
                <td class="action-buttons">
                    <button onclick="editRow(<?= $row['id'] ?>)">Edit</button>
                    <button onclick="saveRow(<?= $row['id'] ?>)" style="display:none; background-color: #28a745;">Save</button>
                    <button onclick="deleteUser(<?= $row['id'] ?>)" style="background-color: #dc3545;">Delete</button>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- JS loaded AFTER all HTML content -->
    <script>
        function toggleAddForm() {
            const form = document.getElementById("addForm");
            form.style.display = form.style.display === "none" ? "block" : "none";
        }

        function editRow(id) {
            const row = document.getElementById('row-' + id);
            row.querySelectorAll('span').forEach(span => span.style.display = 'none');
            row.querySelectorAll('input, select').forEach(el => el.style.display = 'block');
            const buttons = row.querySelectorAll('button');
            buttons[0].style.display = 'none'; // Edit
            buttons[1].style.display = 'inline'; // Save
        }

        function saveRow(id) {
            const row = document.getElementById('row-' + id);
            const inputs = row.querySelectorAll('input, select');
            const data = {
                id: id,
                first_name: inputs[0].value,
                last_name: inputs[1].value,
                email: inputs[2].value,
                role: inputs[3].value,
                is_verified: inputs[4].value,
                status: inputs[5].value
            };

            fetch('update_user.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(res => res.text())
            .then(response => {
                alert(response);
                location.reload();
            });
        }

        function deleteUser(id) {
            if (confirm("Are you sure you want to delete this user?")) {
                fetch('delete_user.php?id=' + id)
                    .then(res => res.text())
                    .then(response => {
                        alert(response);
                        location.reload();
                    });
            }
        }

        document.getElementById("newUserForm").addEventListener("submit", function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('add_user.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(response => {
                alert(response);
                location.reload();
            });
        });
    </script>

</body>
</html>
