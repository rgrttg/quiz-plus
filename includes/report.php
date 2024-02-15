<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// include dirname(__DIR__) . "/utils/helper.php";
include "data-collector.php";
// prettyPrint($_SESSION);
// exit();
$totalPoints = 0;
foreach ($_SESSION as $key => $data) {
    if (isset($data['single-choice']) && $data['single-choice'] === "1") {
        $totalPoints += 1;
    } else {
        $counter = 0;
        for ($i = 1; $i <= 5; $i++) {

            if (isset($data["answer"]) && $data["answer"] === '1') {
                $counter += 1;
            } else if (isset($data["answer"]) && $data["answer"] === '0') {
                $counter -= 1;
            } else {
                $counter += 0;
            }
        }
        $totalPoints += $counter;
    }
}


$procent = round(($totalPoints / (count($_SESSION))) * 100, 2);
if ($procent > 100) {
    $procent = 100;
} else if ($procent < 0) {
    $procent = $totalPoints = 0;
}
// TODO: DATABASE FOR MOST DIFFICULT QUESTIONS AND MOST DIFFICULT TOPICS + JS PIE VISUALIZATION

// add data in new table for visualization
$topic = $_SESSION['quiz']['topic'];
addStatistic($topic, $procent, $dbConnection);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>report</title>
    <link rel="stylesheet" href="../styles.css">
    <script src="https://d3js.org/d3-scale-chromatic.v1.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">


    <script src="https://d3js.org/d3.v6.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
</head>

<body>
    <?php include "header.php"; ?>


    <section id="form-quiz">
        <section id="form-container">
            <h1 id="report"><?php echo "You answered $procent percent of the questions correctly with total points: $totalPoints"; ?></h1>
        </section>

    <!-- Roger's Chart From W3schools animiert [x] und ohne flackern [x] ------ -->
        <canvas id="myChart" style="width:100%;max-width:600px"></canvas>
        <script>
        const yRichtig = <?php echo $procent ?>;
        const yFalsch = 100 - <?= $procent ?>;
        const xValues = ["Richtig", "Falsch"];
        const yValues = [yRichtig, yFalsch];
        const barColors = [
          "#6b806f",
          "#000532"
        ];
        
        new Chart("myChart", {
          type: "pie",
          data: {
            labels: xValues,
            datasets: [{
              backgroundColor: barColors,
              data: yValues
            }]
          },
          options: {
            title: {
              display: true,
              text: "Percent"
            }
          }
        });
        </script>
    <!-- End of Roger's Chart -->

        <button onclick="update(data1)">Data 1</button>
        <button onclick="update(data2)">Data 2</button>
        <!-- <div id="my_dataviz"></div> -->


        <h2>Zeitschrift Abbonieren</h2>
        <button id="myBtn">Abbonieren</button>
        <div id="myModal" class="modal">
            <div class="modal-content">

                <span class="close">&times;</span>
                <form class="start-quiz" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <label style="color: black;" for="name">name</label>
                    <input type="text" name="name" id="name">
                    <label style="color: black;" for="name">e-mail</label>
                    <input type="email" name="email">
                    <span class="error"> <?php if (!empty($emailErr)) {
                                                echo $emailErr;
                                            } else {
                                                echo "";
                                            }; ?></span>
                    <br><br>
                    <input type="submit" name="submit" value="Abbonieren">
                </form>
            </div>

        </div>

    </section>

    <?php include "footer.php" ?>
    <script src="../script.js"></script>
    <script src="../pie.js"></script>
</body>

</html>