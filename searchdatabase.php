<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
        }
        form {
            display: flex;
            align-items: center;
            width: 100%;
            height: 70px;
            background: #ddd;
            position: fixed;
            top: 0;
            z-index: 99999;
        }
        #spark_search_engine_logo {
            height: 100px;
            margin-right: 10px;
        }
        input[type="text"] {
            width: 300px;
            height: 40px;
            outline: none;
            border: 1px solid #ddd;
            font-size: 16px;
            border-top-left-radius: 20px;
            border-bottom-left-radius: 20px;
            padding: 0px 10px;
            background: #f9f9f9;
        }
        input[type="submit"] {
            height: 40px;
            outline: none;
            border: 1px solid #ddd;
            border-left: none;
            border-top-right-radius: 20px;
            border-bottom-right-radius: 20px;
            font-size: 16px;
            cursor: pointer;
            background: #007bff;
            color: white;
            padding: 0 15px;
        }
        .results {
            margin: 20px;
            padding-top: 60px;
        }
        .site_card {
            width: 60%;
            padding: 10px;
            border-radius: 8px;
            background: #ddda;
            display: flex; 
            align-items: center; 
            gap: 10px;
            margin-bottom: 15px; 
            border: 1px solid #cfcfcf; 
            border-radius: 8px;
        }
        .image_content img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
        }
        .site_info {
            width: 70%;
        }
        .site_info h3 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #222;
        }
        .site_info p {
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
        }
        .notfound{
            padding-top: 70px;
            padding-left: 10px;
        }
        .site_info a {
            text-decoration: none;
            color: #007bff;
            font-size: 14px;
        }
        .site_info a:hover {
            text-decoration: underline;
        }
@media (max-width: 660px) {
    #spark_search_engine_logo {
        display: none;
    }
    input[type="text"] {
        width: 70%;
        border-top-left-radius: 20px;
        border-bottom-left-radius: 20px;
        margin-left: 10px;
    }
    .results {
        margin: 0;
        padding-top: 100px;
        }
    .site_card {
        width: 100%;
        flex-direction: column;
        align-items: flex-start;
    }
    .image_content img {
        width: 50px;
        height: 50px;
        margin-right: 1px;
    }
    .site_info {
        width: 100%;
        margin-left: 5px;
    }
    .site_info h3 {
        font-size: 1.1em;
    }
    .site_info p a {
        font-size: 13px;
        width: 100%;
        display: block;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
}
    </style>
</head>
<body>
<form action="searchdatabase.php" method="get">
<a href="search.php"><img src="images/spark_logo.png" id="spark_search_engine_logo" alt="Spark Search Engine Logo"></a>
    <input type="text" name="user_keyword" placeholder="Search Now" />
    <input type="submit" name="search_sites" value="Search">
</form>

<?php
$connection = mysqli_connect("localhost", "root", "", "search");

// Check database connection
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

if (isset($_GET['search_sites'])) {
    $user_keyword = $_GET['user_keyword'];

    // Check if the search keyword is empty
    if (empty($user_keyword)) {
        echo "<script>alert('Please enter a search term.')</script>";
    } else {
        // Prevent SQL injection
        $user_keyword = mysqli_real_escape_string($connection, $user_keyword);

        // Query to fetch search results
        $query = "SELECT DISTINCT site_title, site_keywords, site_image, site_link, site_desc 
                  FROM sites 
                  WHERE site_title LIKE '%$user_keyword%' 
                     OR site_keywords LIKE '%$user_keyword%' 
                     OR site_desc LIKE '%$user_keyword%'
                  ORDER BY 
                      CASE 
                          WHEN site_title LIKE '%$user_keyword%' THEN 1
                          WHEN site_keywords LIKE '%$user_keyword%' THEN 2
                          ELSE 3
                      END";

        $result = mysqli_query($connection, $query);

        // Check if query executed successfully
        if ($result && mysqli_num_rows($result) > 0) {
            $result_count = mysqli_num_rows($result);
            echo "<p class='result_count'>Found $result_count result(s) for: <strong>" . htmlspecialchars($user_keyword) . "</strong></p>";
            echo "<div class='results'>";

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='site_card'>";

                // Display site image if available
                if (!empty($row['site_image'])) {
                    echo "<div class='image_content'>";
                    echo "<img src='images/" . htmlspecialchars($row['site_image']) . "' alt='Site Image' style='width: 60px; height: 60px; object-fit: cover; border-radius: 50%;'>";
                    echo "</div>";
                }

                echo "<div class='site_info' style='flex: 1;'>";
                echo "<h3 style='font-size: 1.2em; font-weight: bold;'>" . htmlspecialchars($row['site_title']) . "</h3>";

                // Extract domain using parse_url()
                $site_url = $row['site_link'];
                $parsed_url = parse_url($site_url);
                $domain = isset($parsed_url['host']) ? $parsed_url['host'] : htmlspecialchars($site_url);

                // Display the domain in the anchor text, but use the full URL as the link
                echo "<p><a href='" . htmlspecialchars($row['site_link']) . "' style='color: #007bff; text-decoration: none;' target='_blank'>" . htmlspecialchars($domain) . "</a></p>";

                // Display the site description
                echo "<p style='font-size: 14px; color: #555;'>";
                if (empty($row['site_desc'])) {
                    echo 'No description available.';
                } else {
                    $description = $row['site_desc'];
                    echo (mb_strlen($description) > 150) ? htmlspecialchars(mb_substr($description, 0, 150)) . ' ...' : htmlspecialchars($description);
                }
                echo "</p>";

                echo "</div>";
                echo "</div>";
            }

            echo "</div>";
        } else {
            echo "<p class='notfound' style='color: #d9534f; font-size: 16px;'>No results found for: <strong>" . htmlspecialchars($user_keyword) . "</strong></p>";
        }
    }
}

mysqli_close($connection);
?>

</body>
</html>
