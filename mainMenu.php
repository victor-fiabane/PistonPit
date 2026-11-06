<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style_MainMenu.css">
    <title>Menu</title>
</head>

<body>

    <?php
    include_once("./db/config.php");
    include_once("./db/setDb.php");
    include_once("./crud.php");
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    ?>

<header class="main-header">
        <nav>
            <a href="conheca.html">Conheça o kartódromo</a>
            <a href="pistas.html">Nossas pistas</a>
            <a href="perfil_client.php">Perfil</a>
            <a href="logout.php">Sair</a>
        </nav>
</header>



    <!--              Desktop HTML                 -->
    <div id="containerMain">
        <div id="mainPc">
            <!-- title div -->
            <div id="title">

            </div>
            <!--input div -->
            <div id="container">
                <div id="menu">

                <!-- CALENDARIO -->
                <div class="calendario-container" style="background-color: #333; padding: 20px; border-radius: 10px; color: white;">
                        <h2>Calendário de Eventos</h2>
                        
                        <div class="calendario-grid" style="margin-bottom: 30px;">
                            <?php if(count($calendario['corridas']) > 0 || count($calendario['bloqueios']) > 0): ?>
                                <ul style='list-style: none; padding: 0;'>
                                    
                                    <!-- Exibe Bloqueios -->
                                    <?php foreach($calendario['bloqueios'] as $bloqueio): ?>
                                        <li style='color: #ff4d4d; padding: 10px; border-bottom: 1px solid #555;'>
                                            ⛔ <strong><?= date('d/m/Y', strtotime($bloqueio['bloq_data'])) ?></strong> - Bloqueado pela Administração
                                        </li>
                                    <?php endforeach; ?>

                                    <!-- Exibe Corridas -->
                                    <?php foreach($calendario['corridas'] as $corrida): 
                                        $isDono = ($corrida['fk_id_cli'] == $idClienteLogado);
                                        $tipoStr = str_replace('_', ' ', $corrida['cor_tipo']);
                                    ?>
                                        <li style='padding: 10px; border-bottom: 1px solid #555; display: flex; justify-content: space-between; align-items: center;'>
                                            <span>🏁 <strong><?= date('d/m/Y', strtotime($corrida['cor_data'])) ?> às <?= $corrida['cor_horario'] ?></strong> - <?= $tipoStr ?> (<?= $corrida['cor_visibilidade'] ?>) | Pilotos: <?= $corrida['cor_limite_pilot'] ?></span>
                                            
                                            <!-- Botão de cancelar apenas se for o dono da corrida -->
                                            <?php if($isDono): ?>
                                                <form action='processa_cancelamento.php' method='POST' style='display:inline;' onsubmit='return confirm("Tem certeza que deseja cancelar?");'>
                                                    <input type='hidden' name='id_corrida' value='<?= $corrida['id_corrida'] ?>'>
                                                    <input type='text' name='justificativa' placeholder='Motivo' required style='margin-right: 10px; padding: 5px; width: 150px;'>
                                                    <button type='submit' name='btnCancelar' style='background-color: #ff4d4d; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;'>Cancelar</button>
                                                </form>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p>Nenhum evento agendado para este mês.</p>
                            <?php endif; ?>
                        </div>

                        <hr style="border-color: #555;">

                        <div class="agendamento-form" style="margin-top: 20px;">
                            <h3>Agendar Nova Corrida Privada</h3>
                            <form action="processa_agendamento.php" method="POST" style="display: flex; flex-direction: column; gap: 15px; max-width: 400px;">
                                <div>
                                    <label for="cor_data">Data:</label><br>
                                    <input type="date" name="cor_data" required style="width: 100%; padding: 8px;">
                                </div>
                                <div>
                                    <label for="cor_horario">Horário:</label><br>
                                    <input type="time" name="cor_horario" required style="width: 100%; padding: 8px;">
                                </div>
                                <div>
                                    <label for="cor_tipo">Tipo de Evento:</label><br>
                                    <select name="cor_tipo" required style="width: 100%; padding: 8px;">
                                        <option value="TREINO">Treino Livre</option>
                                        <option value="CORRIDA">Corrida</option>
                                        <option value="TOMADA_TEMPO">Tomada de Tempo</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="cor_limite_pilot">Limite de Pilotos:</label><br>
                                    <input type="number" name="cor_limite_pilot" min="1" max="30" required style="width: 100%; padding: 8px;">
                                </div>
                                <div>
                                    <label for="cor_senha">Senha (Exigida no Check-in):</label><br>
                                    <input type="password" name="cor_senha" placeholder="Crie uma senha" required style="width: 100%; padding: 8px;">
                                </div>
                                <button type="submit" name="btnAgendar" style="padding: 10px; background-color: #f39c12; border: none; font-weight: bold; cursor: pointer;">Confirmar Agendamento</button>
                            </form>
                        </div>
                    </div>

                </div>

            </div>

            <?php # php openning



            ?> <!-- php closing -->


        </div>
    </div>

    <!---              Mobile HTML-                -->


    <div id="containerMobile">

        <!-- title div -->
        <div id="title">
        
        </div>
        <!--input div -->
        <div id="container">
            <div id="menu">

            <!-- CALENDARIO -->

            </div>

        </div>



</body>

</html>