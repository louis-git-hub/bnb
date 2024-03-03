<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Air Data</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js"></script>
    
    <style>
        /* Style général */
body, html {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    height: 100%;

}

/* En-tête */
header {
    background-color: #1a1a1a;
    color: white;
    padding: 20px; /* Rendre le header plus fin */
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1000;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

header h1 {
    margin: 0;
    font-size: 1.5em; /* Taille du titre */
    padding-left: 30px;
}

nav ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
}

.ul{
    padding-right: 80px;
    font-weight: bold;
}

nav ul li {
    margin: 0 15px;
}

nav ul li a {
    color: white;
    text-decoration: none;
    font-size: 16px; /* Augmenter la taille de la police des boutons */
    transition: color 0.3s;
}

nav ul li a:hover {
    color: #e7e430;
}

/* Contenu principal */
main {
    padding-top: 50px;
}



/* Pied de page */
footer {
    background-color: #1a1a1a;
    color: white;
    text-align: center;
    padding: 1em 0;
    margin-top: 2em;
}



.tableau-container {
    width: 100%;
    padding: 10px;
    padding-right: 0px;
    padding-left:0px;
}
.tableau-scroll, .tableau-fixe, .tableau-comparatif {
    width: 48%; /* Largeur de chaque tableau */
    display: inline-block;
    vertical-align: top;
}
.tableau-scroll {
    max-height: 500px;
    overflow-y: auto;
    padding: 25px;
    padding-top: 0px;
    padding-right: 0px;
}
.tableau-comparatif {
    margin-top: 20px; /* Espace entre le deuxième tableau et le tableau comparatif */
    margin-left: 25px
}
table {
    border-collapse: collapse;
    width: 100%;
}
th, td {
    border: 1px solid black;
    padding: 5px;
    text-align: left;
    font-size: 15px;
}

.grphh1{
    margin-left: 30px;
}





    </style>
</head>
<body>
<header>
        <h1>Bienvenue sur Air Data</h1>
        <nav>
            <ul class="ul">
                <li><a href="./airbnb-site.html">Accueil</a></li>
                <li><a href="#apropos">Graph</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </nav>
    </header>   

    <main>  
        <div class="home-container">
            <h1 class="grphh1">Graph</h1>
            <div class="content">
                <div class="tableau-container">
                    <?php
                    try {
                        try {
                            // Connexion à la base de données SQLite
                            $pdo = new PDO('sqlite:C:\Users\ordi2210239\Documents\projetRBNB\222.db');
                            
                            $statement = $pdo->query("SELECT * FROM numbers_dates ORDER BY execution_date DESC");
                            $resultats = $statement->fetchAll(PDO::FETCH_ASSOC);
    
                            echo "<div class='tableau-scroll'>";
                            echo "<table>";
                            echo "<tr><th>ID</th><th>Date d'exécution</th><th>Nombre</th><th>Date</th></tr>";
                            foreach ($resultats as $ligne) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($ligne['execution_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($ligne['execution_date']) . "</td>";
                                echo "<td>" . htmlspecialchars($ligne['number']) . "</td>";
                                echo "<td>" . htmlspecialchars($ligne['date']) . "</td>";
                                echo "</tr>";
                            }
                            echo "</table>";
                            echo "</div>";
                            // Récupération et affichage des données pour le premier tableau
                            
    
                            // Récupération et affichage des 7 dernières données pour le deuxième tableau
                            $statement2 = $pdo->query("SELECT * FROM numbers_dates ORDER BY execution_date DESC LIMIT 7");
                            $resultats2 = $statement2->fetchAll(PDO::FETCH_ASSOC);
    
                            echo "<div class='tableau-fixe'>";
                            echo "<table>";
                            echo "<tr><th>ID</th><th>Date d'exécution</th><th>Nombre</th><th>Date</th></tr>";
                            foreach ($resultats2 as $ligne) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($ligne['execution_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($ligne['execution_date']) . "</td>";
                                echo "<td>" . htmlspecialchars($ligne['number']) . "</td>";
                                echo "<td>" . htmlspecialchars($ligne['date']) . "</td>";
                                echo "</tr>";
                            }
                            echo "</table>";
                            echo "</div>";
    
                            // Récupération des 14 dernières données pour le tableau comparatif
                            $statement3 = $pdo->query("SELECT * FROM numbers_dates ORDER BY execution_date DESC, execution_id DESC LIMIT 14");
                            $donnees = $statement3->fetchAll(PDO::FETCH_ASSOC);
    
                            if (count($donnees) >= 14) {
                                $donneesDerniereID = array_slice($donnees, 0, 7);
                                $donneesAvantDerniereID = array_slice($donnees, 7, 7);
    
                                // Affichage du tableau de comparaison
                                echo "<div class='tableau-comparatif'>";
                                echo "<table>";
                                echo "<tr><th>ID</th><th>Nombre (Dernière ID)</th><th>Nombre (Avant Dernière ID)</th><th>Différence</th></tr>";
                                
                                for ($i = 0; $i < 7; $i++) {
                                    $nombreDerniereID = $donneesDerniereID[$i]['number'];
                                    $nombreAvantDerniereID = $donneesAvantDerniereID[$i]['number'];
                                    $difference = $nombreDerniereID - $nombreAvantDerniereID;
    
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($donneesDerniereID[$i]['execution_id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($nombreDerniereID) . "</td>";
                                    echo "<td>" . htmlspecialchars($nombreAvantDerniereID) . "</td>";
                                    echo "<td>" . htmlspecialchars($difference) . "</td>";
                                    echo "</tr>";
                                }
                                echo "</table>";
                                echo "</div>";
                            }
    
                        } catch (PDOException $e) {
                            echo "Erreur de connexion : " . $e->getMessage();
                        }
    
                        // PHP code for connecting to the database and fetching data ...
                        // Place your existing PHP and HTML code for displaying tables here ...

                        // PHP code to prepare data for the chart
                        $ids = [];
                        $averageNumbers = [];
                        foreach ($resultats as $ligne) {
                            if (!isset($ids[$ligne['execution_id']])) {
                                $ids[$ligne['execution_id']] = [];
                            }
                            $ids[$ligne['execution_id']][] = $ligne['number'];
                        }

                        foreach ($ids as $id => $numbers) {
                            $average = array_sum($numbers) / count($numbers);
                            $averageNumbers[$id] = $average; // Change structure for easier use in JavaScript
                        }

                        // Convert PHP arrays to JavaScript arrays
                        $labels = array_keys($averageNumbers);
                        $data = array_values($averageNumbers);
                    } catch (PDOException $e) {
                        echo "Erreur de connexion : " . $e->getMessage();
                    }
                    ?>
                </div>

                <!-- Chart container -->
                <div class="chart-container" style="position: relative; padding: 30px; height:40vh; width:80vw">
                    <canvas id="averageDataChart" width="2300" height="1200"></canvas>
                </div>
            </div>
        </div>
    </main>

    <script>
        var ctx = document.getElementById('averageDataChart').getContext('2d');
        var averageDataChart = new Chart(ctx, {
            type: 'bar', // Change to 'line', 'bar', 'pie', etc. as needed
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: 'Average Data',
                    data: <?= json_encode($data) ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    <footer>
        <p>&copy; 2024 Votre Nom ou Société</p>
    </footer>
</body>
</html>








