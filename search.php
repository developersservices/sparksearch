<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="search.css">
    <title>SparkSearch</title>
    <style>
        /* Body styling */
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }

        /* Search area container */
        .search_area {
            width: 100%;
            max-width: 600px; /* Max width for better layout */
            height: 500px;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
        }

        /* Logo styling */
        #spark_search_engine_logo {
            width: 500px;
            height: auto;
            object-fit: contain; /* Ensures the logo scales proportionally */
            background-position: center;
            margin-bottom: -100px; /* Adds space between logo and input fields */
            position: relative;
            top: -130px;
        }

        /* Text input styling */
        input[type="text"] {
            width: 100%;
            max-width: 400px;
            height: 60px;
            padding: 0 15px;
            border: 2px solid #cfcfcf;
            border-radius: 10px;
            font-size: 16px;
            outline: none;
            margin-bottom: 15px; /* Adds space between input and button */
            box-sizing: border-box;
            position: relative;
            top: -150px;
        }

        /* Submit button styling */
        input[type="submit"] {
            width: 100%;
            max-width: 100px;
            height: 60px;
            border: none;
            background: #cfcf0f;
            color: #000;
            font-size: 19px;
            font-weight: 600;
            cursor: pointer;
            outline: none;
            box-sizing: border-box;
            position: relative;
            top: -150px;
        }

        /* Submit button hover effect */
        input[type="submit"]:hover {
            border: 1px solid #cfcfcf;
        }

        /* Ensures responsive design */
        @media (max-width: 600px) {
            .search_area {
                height: auto;
                padding: 20px;
            }

            #spark_search_engine_logo {
                width: 150px;
                margin-bottom: 15px;
            }

            input[type="text"],
            input[type="submit"] {
                width: 100%;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <form action="searchdatabase.php" method="get" class="search_area">
        <img src="images/spark_logo.png" id="spark_search_engine_logo" alt="Spark Search Engine Logo">
        <input type="text" name="user_keyword" placeholder="Spark Search" />
        <input type="submit" name="search_sites" value="Search">
    </form>
</body>
</html>
