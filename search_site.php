<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/images/spark_logo.png" type="image/x-icon">
    <title>SparkSearch</title>
    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            font-family: Arial, sans-serif;
        }
        table {
            display: flex;
            gap: 10px;
            flex-direction: column;
            align-items: flex-start;
        }
        table tr td {
            color: #ffefeb;
            font-size: 18px;
            background: #2b2a2a;
            border-radius: 5px;
            padding: 10px;
            font-family: sans-serif;
        }
        input, textarea, select {
            width: 90%;
            padding: 8px;
            border-radius: 5px;
            border: 2px solid #e71109;
            background: #00090a;
            color: #ffefeb;
        }
        input[type="submit"] {
            background-color: #e71109;
            color: white;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <form action="" method="post" enctype="multipart/form-data">
        <table>
            <tr>
                <td colspan="2"><strong>Insert New Website</strong></td>
            </tr>
            <tr>
                <td>Site Title:</td>
                <td><input type="text" name="site_title" aria-label="Site Title" /></td>
            </tr>
            <tr>
                <td>Site Link:</td>
                <td><input type="text" name="site_link" aria-label="Site Link" placeholder="e.g., example.onion" /></td>
            </tr>
            <tr>
                <td>Site Keywords:</td>
                <td><input type="text" name="site_keywords" aria-label="Site Keywords" /></td>
            </tr>
            <tr>
                <td>Site Description:</td>
                <td><textarea name="site_desc" aria-label="Site Description" rows="5"></textarea></td>
            </tr>
            <tr>
                <td>Site Image:</td>
                <td><input type="file" name="site_image" aria-label="Site Image" /></td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="submit" name="submit" value="Add Site" />
                </td>
            </tr>
        </table>
    </form>

    <?php
    // Establish database connection
    $connection = mysqli_connect("localhost", "root", "", "search");

    if (!$connection) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    if (isset($_POST['submit'])) {
        $site_title = mysqli_real_escape_string($connection, $_POST['site_title']);
        $site_link = mysqli_real_escape_string($connection, $_POST['site_link']);
        $site_keywords = mysqli_real_escape_string($connection, $_POST['site_keywords']);
        $site_desc = mysqli_real_escape_string($connection, $_POST['site_desc']);
        $site_image = $_FILES['site_image']['name'];
        $site_image_temp = $_FILES['site_image']['tmp_name'];

        if (empty($site_title) || empty($site_link) || empty($site_keywords) || empty($site_desc)) {
            echo "<script>alert('Please fill all fields.');</script>";
        } elseif (empty($site_image)) {
            echo "<script>alert('Please upload an image.');</script>";
        } elseif (!preg_match('/\.onion$/', $site_link)) {
            echo "<script>alert('Please enter a valid .onion URL.');</script>";
        } else {
            $image_info = getimagesize($site_image_temp);
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg', 'image/svg'];
            if (!$image_info || !in_array($image_info['mime'], $allowed_types)) {
                echo "<script>alert('Invalid image format.');</script>";
            } else {
                $upload_dir = "images/";
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                $unique_name = uniqid() . "_" . basename($site_image);
                if (move_uploaded_file($site_image_temp, $upload_dir . $unique_name)) {
                    $query = "INSERT INTO sites (site_title, site_link, site_keywords, site_desc, site_image) 
                              VALUES ('$site_title', '$site_link', '$site_keywords', '$site_desc', '$unique_name')";
                    if (mysqli_query($connection, $query)) {
                        echo "<script>alert('Site added successfully.');</script>";
                    } else {
                        echo "<script>alert('Error adding site: " . mysqli_error($connection) . "');</script>";
                    }
                } else {
                    echo "<script>alert('Error uploading image.');</script>";
                }
            }
        }
    }

    if (isset($_POST['search'])) {
        $search_keyword = mysqli_real_escape_string($connection, $_POST['search_keyword']);
        $search_query = "
            SELECT *, 
            (CASE
                WHEN site_title LIKE '%$search_keyword%' THEN 3
                WHEN site_keywords LIKE '%$search_keyword%' THEN 2
                WHEN site_desc LIKE '%$search_keyword%' THEN 1
                ELSE 0
            END) AS relevance
            FROM sites
            WHERE site_title LIKE '%$search_keyword%' 
               OR site_keywords LIKE '%$search_keyword%' 
               OR site_desc LIKE '%$search_keyword%'
            ORDER BY relevance DESC, site_title ASC
        ";
        $search_result = mysqli_query($connection, $search_query);
        if ($search_result && mysqli_num_rows($search_result) > 0) {
            while ($row = mysqli_fetch_assoc($search_result)) {
                echo "<div>";
                echo "<h3>" . htmlspecialchars($row['site_title']) . "</h3>";
                echo "<a href='" . htmlspecialchars($row['site_link']) . "'>" . htmlspecialchars($row['site_link']) . "</a>";
                echo "<p>" . htmlspecialchars($row['site_desc']) . "</p>";
                echo "<img src='images/" . htmlspecialchars($row['site_image']) . "' alt='" . htmlspecialchars($row['site_title']) . "' style='width:200px;'/>";
                echo "</div>";
            }
        } else {
            echo "<p>No results found.</p>";
        }
    }

    mysqli_close($connection);
    ?>
</body>
</html>
