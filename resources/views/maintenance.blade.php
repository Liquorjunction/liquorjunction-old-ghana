<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Under Maintenance</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f3f4f6;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }

        .container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 90%;
            max-width: 600px;
        }

        .image-center {
            margin-bottom: 20px;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        h1 {
            font-size: 36px;
            color: #FBB516;
            margin: 20px 0;
        }

        p {
            font-size: 18px;
            color: #555;
            margin: 10px 0 20px;
        }

        .button-container {
            margin-top: 30px;
        }

        .btn {
            background-color: #FBB516;
            color: black;
            border: none;
            padding: 12px 25px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #AF7E0F;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="image-center">
            <img width="380px" src="{{ asset('assets/frontend/images/construction.png') }}" alt="Maintenance">
        </div>
        <h1>Sorry for Inconvenience</h1>
        <p>Our website is presently undergoing maintenance. We are diligently working  to restore all functionalities at the earliest opportunity. We will be back shortly.</p>
        <div class="button-container">
            <a href="tel:+233593993670" class="btn">For more assistance, please call: <span style="font-weight: 600;">+233 593993670</span></a>
        </div>
    </div>

</body>
</html>
