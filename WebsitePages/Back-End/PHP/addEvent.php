<html lang="es" dir="ltr">

<head>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <meta charset="utf-8">
    <title>Add Event</title>
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
        <a id='arrow' href="../../Back-End/PHP/userHome.php"><i  class="fa fa-chevron-left" style="color: #003f91; font-size: 30px; justify-self: end;"></i></a>
        <h2 class="title" >Add Event</h2>
        <form id='addEvent' class="form" method="POST" action="">
            <label for="eventName">Event Name:</label> <input name="eventName" class="form__input" type="text" placeholder="Name" required> <br>
            <label for="eventLocation">Event Location:</label> <input name="eventLocation" class="form__input" type="text" placeholder="Exhibition center, Riyadh" required> <br>

            <div id="times">
                <div class="timeBlocks"><label for="startDate">Start Date:</label>  <input name="startDate" class="form__input time" type="date"  required> </div>
                <div class="timeBlocks"> <label for="startTime">Start Time:  </label> <input name="startTime" class="form__input time" type="time"  required> </div>
                 <br>
                 <div class="timeBlocks"><label  for="endDate">End Date:  </label> <input name="endDate" class="form__input time" type="date"  required> </div>
                 <div class="timeBlocks"> <label for="endTime">End Time:  </label>  <input name="endTime" class="form__input time" type="time"  required> </div>
            </div>

            <div class="AllHalls">
                <div id="hall" class="hall">
                    <label for="hallName">Hall Name:</label><br>
                    <input name="hallName" class="form__input" type="text" placeholder="main hall" required><br>
                    <label for="hallCamera">Hall Camera:</label><br>
                    <select name="hallCamera">
                        <option>Camera 1</option>
                        <option>Camera 2</option>
                    </select> <br>
                    <label for="hallThreshold">Hall Threshold:</label><br>
                    <input name="hallThreshold" class="form__input" type="text" placeholder="##" required>
                </div>
                <div class="card">
                    <a href="#"><img class="Plus" src="images/plus.png" alt="Plus"></a>
                </div>
            </div>
            <br>
            <button class="form__button button submit">ADD EVENT</button>
          </form>


    </div>
</body>

</html>
