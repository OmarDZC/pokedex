<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>PokedexPHP</title>
</head>
<body>

    <div id="header">
        <img src="imgpok.jpg" alt="cabeceraPoke" class="headerImg">
    </div>
    
    <div id="containerPokd">
        <form action="" method="get">
            <label for="name">Introduce nombre del Pokemon deseado:</label>
            <input type="search" name="name" id="name" required>
            <input type="submit" value="buscar" id="buscar">
        </form>

        <div class="containerPokemon">
            <?php

                //confirma si se ha enviado solicitud o si esta vacio
                if (isset($_GET["name"]) && !empty($_GET["name"])) {
                    $pokemon_name = htmlspecialchars($_GET["name"]);

                    //API datos de pkm
                    $apiPkm = "https://pokeapi.co/api/v2/pokemon/" . strtolower($pokemon_name); //para que se construya con el nombre que quiera buscar
                    $resultPkm = file_get_contents($apiPkm);
                    $dataPkm = json_decode($resultPkm, true);

                    //verificar si la respuesta de la API contiene esos datos
                    if ($dataPkm && isset($dataPkm['name'])) {
                        //Datos basicos del pokemon
                        $name = $dataPkm['name'];
                        $img = $dataPkm['sprites']['front_default'];
                        $stats = $dataPkm['stats'];
                        $abilities = $dataPkm['abilities'];
                        $species_url = $dataPkm['species']['url']; //para sacar despues las evo

                        //nombre e imagen
                        echo "<h2>Nombre: $name</h2>";
                        echo "<img src='$img' alt='$name' class='imagenPkm'>";
                        
                        //stats
                        echo "<h2 class='stats'>Stats:</h2>";
                        echo "<ul>";
                        foreach($stats as $stat) {
                            $statName = $stat['stat']['name'];
                            $statValue = $stat['base_stat'];
                            echo "<li>$statName: $statValue</li>";
                        }
                        echo "</ul>";

                        /// API datos evoluciones (con species_url)
                        $resultEvo = file_get_contents($species_url);
                        $dataEvo = json_decode($resultEvo);

                        if ($dataEvo && isset($dataEvo->evolution_chain->url)) {
                            $evolution_chain_url = $dataEvo->evolution_chain->url;
                            $result_evolution = file_get_contents($evolution_chain_url);
                            $data_evolution = json_decode($result_evolution);

                            // Recorrer la cadena de evoluciones
                            echo "<h3>Evoluciones:</h3>";
                            echo "<ul class='evoluciones'>";

                            function getEvoluciones($evolution, &$evolutionList) {
                                $evolutionList[] = $evolution->species->name;
                                if (!empty($evolution->evolves_to)) {
                                    foreach($evolution->evolves_to as $next_evolution) {
                                        getEvoluciones($next_evolution, $evolutionList);
                                    }
                                }
                            }

                            $evolutionList = [];
                            getEvoluciones($data_evolution->chain, $evolutionList);

                            // Mostrar imágenes y nombres de cada evolución
                            foreach($evolutionList as $evolutionName) {
                                $evolutionData = file_get_contents("https://pokeapi.co/api/v2/pokemon/" . strtolower($evolutionName));
                                $evolutionData = json_decode($evolutionData);
                                $evolutionImage = $evolutionData->sprites->front_default;
                                echo "<li>
                                        <img src='$evolutionImage' alt='$evolutionName' class='imagenPkm'><br>$evolutionName
                                    </li>";
                            }

                            "</ul>";
                        }


                    }


                }

            ?>
        </div>

        <div class="botonAtras">
            <input type="button" value="Volver" onclick="window.location.href='index.php';"> 
        </div>

    </div>
    
</body>
</html>