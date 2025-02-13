<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords"
        content="online notepad, digital notepad, free online notepad, secure note taking, encrypted notes, private notes, collaborative note taking, note organization, productivity tools, idea organization, best online notepad, how to take notes online">
    <meta name="author" content="iNote.pw">
    <meta name="robots" content="index, follow">
    <meta property="og:url" content="https://inote.pw/Terms.php">
    <meta property="og:image" content="https://inote.pw/favicon.svg">
    <meta name="description" content="iNote.pw is a simple and secure online notepad designed to help you capture your thoughts, ideas, and to-do lists quickly and easily. We believe in the power of clear, uncluttered thinking, and our platform provides a clean, minimalist space for your notes.">
    <meta property="og:image" content="<?php echo $base_url; ?>/favicon.svg">
    <meta name="twitter:card" content="summary_large_image">
  	<meta name="twitter:title" content="About Us - Inote PW">
	<meta name="twitter:description" content="Explore iNote PW's Privacy Policy to understand how we handle your data, protect your privacy, and ensure secure usage of our services.">
	<meta name="twitter:image" content="https://inote.pw/favicon.svg">
    <link rel="icon" href="<?php echo $base_url; ?>/favicon.ico" sizes="any">
    <link rel="icon" href="<?php echo $base_url; ?>/favicon.svg" type="image/svg+xml">
    <title>About Us - Inote PW</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: monospace;
            background-color: #f0f0f0;
            color: #333;
            display: flex;
            flex-direction: column;
            justify-content: center;
            /* Center vertically */
            align-items: center;
            /* Center horizontally */
            min-height: 100vh;
        }

        .container {
            width: 90%;
            max-width: 800px;
            background-color: #2b2b2b;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
            z-index: 1;
        }

        .legal-links {
            text-align: center;
            margin-top: 20px;
            width: 100%;
        }

		.links a {
    		color: #555;
    		text-decoration: none;
    		margin: 0 10px; /* Menyesuaikan margin kanan-kiri */
    		font-size: 15px;
    		transition: color 0.3s ease;
    		display: inline-block; /* Memastikan tautan tetap dalam satu baris */
    		padding: 8px 12px; /* Menambahkan padding untuk area tap yang lebih baik */
		}
      .links {
        text-align: center;
        margin-top: 20px;
      }

        .links a:hover {
            color: #555;
        }

        body.dark-mode .links a {
            color: #fff;
        }

        body.dark-mode .links a:hover {
            color: #ccc;
        }

        h1,
        h2 {
            text-align: center;
            color: #fff;
        }

        p {
            text-align: left;
            color: #ccc;
        }

        body.dark-mode p {
            color: #fff;
        }

        .about-us {
            text-align: center;
            margin-top: 20px;
            padding: 20px;
            background-color: #2b2b2b;
            color: #fff;
            border-radius: 8px;
        }

        body.dark-mode .about-us {
            background-color: #333;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .container {
                width: 95%;
            }

            .links a {
                font-size: 14px;
            }
        }

        /* Terms styles */
        .terms-content {
            margin-top: 20px;
            color: #ccc;
        }

        .terms-content h2 {
            color: #fff;
            margin-bottom: 10px;
        }

        .terms-content p {
            margin-bottom: 15px;
        }

    </style>
</head>
<body>
    <div class="container">
        <h1>About Us</h1>
        <p>Inote PW is an online platform that allows you to easily create, store, and share notes. We focus on simplicity and efficiency, ensuring that you can access your notes anytime and anywhere. With advanced security features, your notes will always be safe.</p>
    </div>
    <div class="legal-links">
        <div class="links">
            <a href="<?php echo $base_url; ?>/index.php">Home</a>
            <a href="<?php echo $base_url; ?>/Privacy.php">Privacy</a>
            <a href="<?php echo $base_url; ?>/Terms.php">Terms</a>
            <a href="<?php echo $base_url; ?>/ContactUs.php">Contact Us</a>
            <a href="<?php echo $base_url; ?>/AboutUs.php">About Us</a>
        </div>
    </div>
</body>
</html>
