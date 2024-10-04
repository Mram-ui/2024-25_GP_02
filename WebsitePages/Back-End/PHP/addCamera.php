<html lang="es" dir="ltr">

<head>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <meta charset="utf-8">
    <title>Add a Camera</title>
    <link rel="stylesheet" type="text/css" href="../../Front-End/CSS/boxes.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">

</head>

<body>
    <header class="header">
        <div class="logo">
            <a href="home.html"><img src="../../images/Logo2.png" alt="Company Logo"></a>
        </div>
    </header>

    <div class="main">
        <a id='arrow' href="login.html"><i  class="fa fa-chevron-left" style="color: #003f91; font-size: 30px; justify-self: end;"></i></a>
        <h2 class="title" >Add a Camera</h2>
        <form id='addcam' class="form" method="POST" action="">
            <label for="cameraName">Camera Name:</label> <input name="cameraName" class="form__input" type="text" placeholder="Camera 1" required>
            <label for="cameraIP">Camera IP Address:</label> <input name="cameraIP" class="form__input" type="text" placeholder="000.000.0.000" required>
            <label for="portNo">Port Number:</label> <input name="portNo" class="form__input" type="text" placeholder="0" required>
            <label for="stream">Streaming Channel:  </label><input name="stream" class="form__input" type="text" placeholder="stream1" required>
            <label for="cameraUsername">Camera Username:</label><input name="cameraUsername" class="form__input" type="text" placeholder="Username" required>
            <label for="cameraPassword">Camera Password:</label><input name="cameraPassword" class="form__input" type="text" placeholder="Password" required>

            <button class="form__button button submit">ADD CAMERA</button>
          </form>


    </div>
</body>

</html>
