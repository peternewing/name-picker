<?php
session_start(); // Start or resume a session
require_once 'db_connection.php'; // Include your DB connection script

// Error reporting (should be disabled or strictly controlled in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultimate Random Name Picker | Classroom Tools & Name Randomizer</title>
    <meta name="description" content="Discover the ultimate classroom tool with our random name picker! Perfect for selecting first and last names from lists. Explore our online name randomizer for an engaging experience.">
    <link rel="stylesheet" href="style.css">
  <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-TYLT0T4ZXV">
</script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-TYLT0T4ZXV');
</script>
</head>
<body>
    <nav>
        <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
            <a href="logout.php">Logout</a>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</span>
        <?php else: ?>
            <a href="login.php">Login</a> | <a href="signup.php">Signup</a>
        <?php endif; ?>
    </nav>

    <h1>Welcome to the Ultimate Random Name Picker.</h1>
    <p>Sign up to save lists</p>

    <!-- Example images with optimized alt tags -->
    <img src="images/random_name.jpg" alt="Random Name Picker Tool">

    <!-- Name addition inputs -->
    <div>
        <h2>Add Names</h2>
        <input type="text" id="nameInput" placeholder="Enter a name" />
        <button id="addName">Add Name Manually</button>
    </div>

    <div>
        <h2>Or Paste Names from Excel</h2>
        <textarea id="namesInput" placeholder="Paste names from Excel and click 'Add Names from Excel'"></textarea>
        <button id="addNamesFromExcel">Add Names from Excel</button>
    </div>

    <!-- Names list display -->
    <div>
        <h2>Names List</h2>
        <div id="nameList">
            <!-- Names will be dynamically added here -->
        </div>
    </div>

    <!-- Result display -->
    <div id="result">Selected name will appear here</div>

    <!-- Action buttons -->
    <button id="pickName">Pick a Random Name</button>
    <button id="selectAllNames">Select All Names</button>

    <!-- Save and load list features for logged-in users -->
    <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
    <div id="saveListForm">
        List Name: <input type="text" id="listName" placeholder="Enter list name">
        <button id="saveListBtn">Save List</button>
    </div>

    <select id="nameListsDropdown">
        <option value="">Select a List</option>
        <!-- Dynamically added options -->
    </select>
    <button id="loadListBtn">Load Selected List</button>
    <?php endif; ?>

    <script defer src="script.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
    $(document).ready(function() {
        var isLoggedIn = <?php echo isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true ? 'true' : 'false'; ?>;
        console.log("User Logged In Status:", isLoggedIn);

        if (isLoggedIn === true) {
            function collectNames() {
                var names = [];
                $('#nameList div').each(function() {
                    names.push($(this).text());
                });
                return names;
            }

            $('#saveListBtn').click(function() {
                var listName = $('#listName').val().trim();
                var names = collectNames();

                if (listName === "" || names.length === 0) {
                    alert("Please enter a list name and ensure there are names in the list.");
                    return;
                }

                console.log("Saving List:", listName, names);

                $.ajax({
                    url: 'save_name_list.php',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ list_name: listName, list_data: names }),
                    success: function(response) {
                        console.log("Save List Response:", response);
                        alert(response.message);
                        loadSavedLists(); // Reload lists to include the new addition
                    },
                    error: function(xhr) {
                        console.error("Save List Error:", xhr.responseText);
                        alert('Error: ' + xhr.responseText);
                    }
                });
            });

            function loadSavedLists() {
                $.ajax({
                    url: 'load-selected-lists.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#nameListsDropdown').empty().append($('<option>', { text: 'Select a List', value: '' }));
                        $.each(data, function(i, list) {
                            $('#nameListsDropdown').append($('<option>', {
                                value: list.list_id,
                                text: list.list_name
                            }));
                        });
                    },
                    error: function(xhr) {
                        console.error("Load Lists Error:", xhr.responseText);
                        alert('Error loading lists');
                    }
                });
            }

            loadSavedLists();

            $('#nameListsDropdown').change(function() {
                var listId = $(this).val();
                if (!listId) {
                    $('#nameList').empty();
                    return;
                }

                $.ajax({
                    url: 'get_list_names.php',
                    type: 'GET',
                    dataType: 'json',
                    data: { list_id: listId },
                    success: function(data) {
                        $('#nameList').empty();
                        names = [];
                        selectedIndices = new Set();
                        nameElements = [];
                        $.each(data, function(i, name) {
                            addName(name);
                        });
                    },
                    error: function(xhr) {
                        console.error("Load List Names Error:", xhr.responseText);
                        alert('Error: ' + xhr.responseText);
                    }
                });
            });

            $('#loadListBtn').click(function() {
                $('#nameListsDropdown').change();
            });
        }
    });
    </script>
</body>
</html>
