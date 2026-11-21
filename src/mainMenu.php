<?php
    include_once(__DIR__ . "/../db/config.php");
    include_once(__DIR__ . "/crud.php");
    require_once(__DIR__ . "/auth.php");
    $crud = new Crud($db);
    $horaAbre      = "10:00";
    $horaFecha     = "22:00";
    $duracaoMin    = 30;
    $diasFrente    = 60;
    $limiteMinimo  = 2;
    $limiteMaximo  = 12;
    $horasCancela  = 24;
    $tiposCorrida = array(
        "CORRIDA"      => "Corrida",
        "TREINO"       => "Treino",
        "TOMADA_TEMPO" => "Tomada de tempo"
    );
    $hoje         = date("Y-m-d");
    $amanha       = date("Y-m-d", strtotime("+1 day"));
    $limiteFuturo = date("Y-m-d", strtotime("+" . $diasFrente . " days"));
    $agora        = time();
    $corte = $agora - ($duracaoMin * 60);
    $crud->updateCorridaRealizada(date("Y-m-d", $corte), date("H:i", $corte));
    $horarios = array();
    $marca = strtotime($hoje . " " . $horaAbre);
    $fecha = strtotime($hoje . " " . $horaFecha);
    while ($marca < $fecha) {
        $horarios[] = date("H:i", $marca);
        $marca = $marca + ($duracaoMin * 60);
    }
    $diasSemana = array("Domingo", "Segunda-feira", "Terça-feira", "Quarta-feira", "Quinta-feira", "Sexta-feira", "Sábado");
    $nomeMeses  = array(1 => "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
    $mensagem     = "";
    $tipoMensagem = "";
    $diaAbrir     = "";
    $mesParam = date("Y-m");
    if (isset($_POST['mesText'])) {
        $mesParam = $_POST['mesText'];
    } else if (isset($_GET['mes'])) {
        $mesParam = $_GET['mes'];
    }
    if (!preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])$/', $mesParam)) {
        $mesParam = date("Y-m");
    }
    if (isset($_POST['marcarButton'])) {
        $dataSel = isset($_POST['dataText']) ? $_POST['dataText'] : "";
        $horaSel = isset($_POST['horaSelect']) ? $_POST['horaSelect'] : "";
        $tipoSel = isset($_POST['tipoSelect']) ? $_POST['tipoSelect'] : "";
        $limite  = isset($_POST['limiteText']) ? (int) $_POST['limiteText'] : 0;
        $senha   = isset($_POST['senhaText']) ? trim($_POST['senhaText']) : "";
        $diaAbrir = $dataSel;
        if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $dataSel)) {
            $mensagem = "Data inválida.";
        } else if ($dataSel < $amanha || $dataSel > $limiteFuturo) {
            $mensagem = "A corrida pode ser marcada de amanhã até " . date("d/m/Y", strtotime($limiteFuturo)) . ".";
        } else if ($crud->existsBloqueio($dataSel)) {
            $mensagem = "Este dia está bloqueado pelo kartódromo.";
        } else if (!in_array($horaSel, $horarios)) {
            $mensagem = "Escolha um horário da grade do dia.";
        } else if (!isset($tiposCorrida[$tipoSel])) {
            $mensagem = "Escolha o tipo do evento.";
        } else if ($limite < $limiteMinimo || $limite > $limiteMaximo) {
            $mensagem = "O limite de pilotos vai de " . $limiteMinimo . " a " . $limiteMaximo . ".";
        } else if (strlen($senha) < 4 || strlen($senha) > 20) {
            $mensagem = "A senha da corrida precisa ter de 4 a 20 caracteres.";
        } else if ($crud->existsCorridaHorario($dataSel, $horaSel)) {
            $mensagem = "Este horário acabou de ser ocupado, escolha outro.";
        } else {
            $idNova = $crud->createCorridaPrivada($_SESSION['id'], $dataSel, $horaSel, $tipoSel, $limite, $senha);
            if ($idNova) {
                $crud->createPart($_SESSION['id'], $idNova);
                $_SESSION['senhaNova'] = array("id" => $idNova, "senha" => $senha);
                header("Location: mainMenu.php?mes=" . substr($dataSel, 0, 7) . "&dia=" . $dataSel . "&ok=marcada");
                exit();
            }
            $mensagem = "Não foi possível marcar a corrida, tente novamente.";
        }
        $tipoMensagem = "erro";
    }
    if (isset($_POST['inscreverButton'])) {
        $idCorrida = isset($_POST['id_corrida']) ? (int) $_POST['id_corrida'] : 0;
        $corrida = $crud->readCorridaId($idCorrida);
        $diaAbrir = $corrida ? $corrida['cor_data'] : "";
        if (!$corrida || $corrida['cor_status'] != 'AGENDADA' || $corrida['cor_visibilidade'] != 'PUBLICA') {
            $mensagem = "Corrida não encontrada.";
        } else if (strtotime($corrida['cor_data'] . " " . $corrida['cor_horario']) <= $agora) {
            $mensagem = "Esta corrida já aconteceu.";
        } else if ($crud->existsPart($_SESSION['id'], $idCorrida)) {
            $mensagem = "Você já está inscrito nesta corrida.";
        } else if ($crud->countPartCorrida($idCorrida) >= $corrida['cor_limite_pilot']) {
            $mensagem = "As vagas desta corrida acabaram.";
        } else if ($crud->createPart($_SESSION['id'], $idCorrida)) {
            header("Location: mainMenu.php?mes=" . substr($corrida['cor_data'], 0, 7) . "&dia=" . $corrida['cor_data'] . "&ok=inscrito");
            exit();
        } else {
            $mensagem = "Não foi possível concluir a inscrição.";
        }
        $tipoMensagem = "erro";
    }
    if (isset($_POST['presencaButton'])) {
        $idCorrida = isset($_POST['id_corrida']) ? (int) $_POST['id_corrida'] : 0;
        $senha = isset($_POST['senhaPresencaText']) ? trim($_POST['senhaPresencaText']) : "";
        $corrida = $crud->readCorridaId($idCorrida);
        $diaAbrir = $corrida ? $corrida['cor_data'] : "";
        if (!$corrida || $corrida['cor_status'] != 'AGENDADA' || $corrida['cor_visibilidade'] != 'PRIVADA') {
            $mensagem = "Corrida não encontrada.";
        } else if (strtotime($corrida['cor_data'] . " " . $corrida['cor_horario']) <= $agora) {
            $mensagem = "Esta corrida já aconteceu.";
        } else if ($senha == "" || !password_verify($senha, $corrida['cor_senha'])) {
            $mensagem = "Senha da corrida incorreta.";
        } else if ($crud->existsPart($_SESSION['id'], $idCorrida)) {
            $mensagem = "Sua presença nesta corrida já está confirmada.";
        } else if ($crud->countPartCorrida($idCorrida) >= $corrida['cor_limite_pilot']) {
            $mensagem = "As vagas desta corrida acabaram.";
        } else if ($crud->createPart($_SESSION['id'], $idCorrida)) {
            header("Location: mainMenu.php?mes=" . substr($corrida['cor_data'], 0, 7) . "&dia=" . $corrida['cor_data'] . "&ok=presenca");
            exit();
        } else {
            $mensagem = "Não foi possível confirmar a presença.";
        }
        $tipoMensagem = "erro";
    }
    if (isset($_POST['sairButton'])) {
        $idCorrida = isset($_POST['id_corrida']) ? (int) $_POST['id_corrida'] : 0;
        $corrida = $crud->readCorridaId($idCorrida);
        $diaAbrir = $corrida ? $corrida['cor_data'] : "";
        $horasFalta = $corrida ? (strtotime($corrida['cor_data'] . " " . $corrida['cor_horario']) - $agora) / 3600 : 0;
        if (!$corrida || !$crud->existsPart($_SESSION['id'], $idCorrida)) {
            $mensagem = "Você não está nesta corrida.";
        } else if ($corrida['fk_id_cli'] == $_SESSION['id']) {
            $mensagem = "Esta corrida é sua. Use o cancelamento da corrida.";
        } else if ($horasFalta < $horasCancela) {
            $mensagem = "A saída só é permitida até " . $horasCancela . " horas antes da corrida.";
        } else if ($crud->deletePart($_SESSION['id'], $idCorrida)) {
            header("Location: mainMenu.php?mes=" . substr($corrida['cor_data'], 0, 7) . "&dia=" . $corrida['cor_data'] . "&ok=saiu");
            exit();
        } else {
            $mensagem = "Não foi possível sair da corrida.";
        }
        $tipoMensagem = "erro";
    }
    if (isset($_POST['cancelarButton'])) {
        $idCorrida = isset($_POST['id_corrida']) ? (int) $_POST['id_corrida'] : 0;
        $just = isset($_POST['justText']) ? trim($_POST['justText']) : "";
        $corrida = $crud->readCorridaId($idCorrida);
        $diaAbrir = $corrida ? $corrida['cor_data'] : "";
        $horasFalta = $corrida ? (strtotime($corrida['cor_data'] . " " . $corrida['cor_horario']) - $agora) / 3600 : 0;
        if (!$corrida || $corrida['cor_status'] != 'AGENDADA' || $corrida['fk_id_cli'] != $_SESSION['id']) {
            $mensagem = "Corrida não encontrada.";
        } else if ($horasFalta < $horasCancela) {
            $mensagem = "O cancelamento só é permitido até " . $horasCancela . " horas antes da corrida.";
        } else if (strlen($just) < 5) {
            $mensagem = "Escreva o motivo do cancelamento.";
        } else if ($crud->cancelCorrida($idCorrida, $just, $_SESSION['id'], null)) {
            header("Location: mainMenu.php?mes=" . substr($corrida['cor_data'], 0, 7) . "&dia=" . $corrida['cor_data'] . "&ok=cancelada");
            exit();
        } else {
            $mensagem = "Não foi possível cancelar a corrida.";
        }
        $tipoMensagem = "erro";
    }
    if ($mensagem == "" && isset($_GET['ok'])) {
        $avisos = array(
            "marcada"   => "Corrida marcada. Anote a senha agora: ela aparece uma única vez.",
            "inscrito"  => "Inscrição confirmada.",
            "presenca"  => "Presença confirmada na corrida.",
            "saiu"      => "Você saiu da corrida e a vaga foi liberada.",
            "cancelada" => "Corrida cancelada."
        );
        if (isset($avisos[$_GET['ok']])) {
            $mensagem = $avisos[$_GET['ok']];
            $tipoMensagem = "sucesso";
        }
    }
    if ($diaAbrir == "" && isset($_GET['dia']) && preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $_GET['dia'])) {
        $diaAbrir = $_GET['dia'];
    }
    $tsMes       = strtotime($mesParam . "-01");
    $primeiroDia = date("Y-m-01", $tsMes);
    $ultimoDia   = date("Y-m-t", $tsMes);
    $mesAnterior = date("Y-m", strtotime("-1 month", $tsMes));
    $mesSeguinte = date("Y-m", strtotime("+1 month", $tsMes));
    $totalDias   = (int) date("t", $tsMes);
    $vazioInicio = (int) date("w", $tsMes);
    $corridas  = $crud->listCorridaMes($primeiroDia, $ultimoDia);
    $bloqueios = $crud->listBloqueioMes($primeiroDia, $ultimoDia);
    $minhas    = $crud->listPartCliente($_SESSION['id'], $primeiroDia, $ultimoDia);
    $diasBloqueados = array();
    foreach ($bloqueios as $bloqueio) {
        $diasBloqueados[$bloqueio['bloq_data']] = $bloqueio['bloq_desc'];
    }
    $participando = array();
    foreach ($minhas as $linha) {
        $participando[$linha['fk_id_corrida']] = true;
    }
    $corridasDia = array();
    foreach ($corridas as $corrida) {
        $corridasDia[$corrida['cor_data']][] = $corrida;
    }
    $calendario = array();
    for ($dia = 1; $dia <= $totalDias; $dia++) {
        $data = $mesParam . "-" . sprintf("%02d", $dia);
        $tsDia = strtotime($data);
        $bloqueado = isset($diasBloqueados[$data]);
        $ocupados = array();
        $itens = array();
        if (isset($corridasDia[$data])) {
            foreach ($corridasDia[$data] as $corrida) {
                $hora = substr($corrida['cor_horario'], 0, 5);
                $ocupados[] = $hora;
                $id = (int) $corrida['id_corrida'];
                $minha = ($corrida['fk_id_cli'] == $_SESSION['id']);
                $participo = isset($participando[$id]);
                $total = (int) $corrida['total_part'];
                $limite = (int) $corrida['cor_limite_pilot'];
                $agendada = ($corrida['cor_status'] == 'AGENDADA');
                $horasFalta = (strtotime($data . " " . $hora) - $agora) / 3600;
                $nomeDono = "Kartódromo";
                if ($corrida['cor_visibilidade'] == 'PRIVADA') {
                    $partes = explode(" ", trim($corrida['cli_nome']));
                    $nomeDono = $minha ? "Você" : $partes[0];
                }
                $senhaUmaVez = "";
                if ($minha && isset($_SESSION['senhaNova']) && $_SESSION['senhaNova']['id'] == $id) {
                    $senhaUmaVez = $_SESSION['senhaNova']['senha'];
                }
                $itens[] = array(
                    "id"           => $id,
                    "hora"         => $hora,
                    "visibilidade" => $corrida['cor_visibilidade'],
                    "tipo"         => isset($tiposCorrida[$corrida['cor_tipo']]) ? $tiposCorrida[$corrida['cor_tipo']] : $corrida['cor_tipo'],
                    "desc"         => isset($corrida['cor_desc']) ? $corrida['cor_desc'] : "",
                    "dono"         => $nomeDono,
                    "realizada"    => ($corrida['cor_status'] == 'REALIZADA'),
                    "minha"        => $minha,
                    "participo"    => $participo,
                    "limite"       => $limite,
                    "total"        => $total,
                    "vagas"        => $limite - $total,
                    "senhaUmaVez"  => $senhaUmaVez,
                    "podeEntrar"   => (!$participo && $agendada && ($limite - $total) > 0 && $horasFalta > 0),
                    "podeSair"     => ($participo && !$minha && $agendada && $horasFalta >= $horasCancela),
                    "podeCancelar" => ($minha && $agendada && $horasFalta >= $horasCancela)
                );
            }
        }
        $livres = array_values(array_diff($horarios, $ocupados));
        $podeMarcar = (!$bloqueado && $data >= $amanha && $data <= $limiteFuturo && count($livres) > 0);
        $calendario[$data] = array(
            "data"         => $data,
            "dia"          => $dia,
            "rotulo"       => $diasSemana[(int) date("w", $tsDia)] . ", " . date("d/m/Y", $tsDia),
            "bloqueado"    => $bloqueado,
            "bloqueioDesc" => $bloqueado ? $diasBloqueados[$data] : "",
            "hoje"         => ($data == $hoje),
            "passado"      => ($data < $hoje),
            "podeMarcar"   => $podeMarcar,
            "livres"       => $livres,
            "corridas"     => $itens,
            "minhaNoDia"   => false
        );
        foreach ($itens as $item) {
            if ($item['participo']) {
                $calendario[$data]['minhaNoDia'] = true;
            }
        }
    }
    unset($_SESSION['senhaNova']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style_MainMenu.css">
    <link rel="stylesheet" href="css/style_Calendario.css">
    <title>Menu</title>
</head>
<body>
<header class="main-header">
        <nav>
            <a href="conheca.php">Conheça o kartódromo</a>
            <a href="pistas.php">Nossas pistas</a>
            <a href="perfil_client.php">Perfil</a>
            <a href="logout.php">Sair</a>
        </nav>
</header>
    <div id="containerMain">
        <div id="mainPc">
            <div id="title">
                <h1 class="titleText">Olá, seja bem-vindo!</h1>
            </div>
            <div id="container">
                <div id="menu">
                <div id="calendario">
                    <?php if ($mensagem != "") { ?>
                        <p class="mensagem <?php echo $tipoMensagem; ?>"><?php echo htmlspecialchars($mensagem); ?></p>
                    <?php } ?>
                    <div class="calTopo">
                        <a class="calNav" href="mainMenu.php?mes=<?php echo $mesAnterior; ?>">&#8249;</a>
                        <h2 class="calMes"><?php echo $nomeMeses[(int) date("n", $tsMes)] . " de " . date("Y", $tsMes); ?></h2>
                        <a class="calNav" href="mainMenu.php?mes=<?php echo $mesSeguinte; ?>">&#8250;</a>
                    </div>
                    <p class="calAjuda">Clique em um dia para ver as corridas e os horários livres. Eventos de 30 minutos, das 10:00 às 21:30.</p>
                    <div class="calSemana">
                        <span>Dom</span><span>Seg</span><span>Ter</span><span>Qua</span><span>Qui</span><span>Sex</span><span>Sáb</span>
                    </div>
                    <div class="calGrade">
                        <?php for ($vazio = 0; $vazio < $vazioInicio; $vazio++) { ?>
                            <span class="calDia vazio"></span>
                        <?php } ?>
                        <?php foreach ($calendario as $data => $info) {
                            $classe = "calDia";
                            if ($info['bloqueado']) { $classe = $classe . " bloqueado"; }
                            if ($info['passado']) { $classe = $classe . " passado"; }
                            if ($info['hoje']) { $classe = $classe . " hoje"; }
                        ?>
                            <button type="button" class="<?php echo $classe; ?>" data-dia="<?php echo $data; ?>">
                                <span class="calNumero"><?php echo $info['dia']; ?></span>
                                <span class="calMarcas">
                                    <?php if ($info['bloqueado']) { ?>
                                        <span class="calTag fechado">Fechado</span>
                                    <?php } else { ?>
                                        <?php
                                            $qtdPublica = 0;
                                            $qtdPrivada = 0;
                                            foreach ($info['corridas'] as $item) {
                                                if ($item['visibilidade'] == 'PUBLICA') { $qtdPublica++; } else { $qtdPrivada++; }
                                            }
                                        ?>
                                        <?php if ($qtdPublica > 0) { ?>
                                            <span class="calTag publica"><?php echo $qtdPublica; ?> pública<?php echo ($qtdPublica > 1) ? "s" : ""; ?></span>
                                        <?php } ?>
                                        <?php if ($qtdPrivada > 0) { ?>
                                            <span class="calTag privada"><?php echo $qtdPrivada; ?> privada<?php echo ($qtdPrivada > 1) ? "s" : ""; ?></span>
                                        <?php } ?>
                                        <?php if ($info['minhaNoDia']) { ?>
                                            <span class="calTag minha">Você corre</span>
                                        <?php } ?>
                                    <?php } ?>
                                </span>
                            </button>
                        <?php } ?>
                    </div>
                    <div class="calLegenda">
                        <span><i class="ponto publica"></i> corrida pública</span>
                        <span><i class="ponto privada"></i> corrida privada</span>
                        <span><i class="ponto minha"></i> você está inscrito</span>
                        <span><i class="ponto fechado"></i> dia bloqueado</span>
                    </div>
                </div>
                <div id="modalCal" class="modalCal" aria-hidden="true">
                    <div class="modalCaixa" role="dialog" aria-modal="true" aria-labelledby="modalTitulo">
                        <div class="modalTopo">
                            <h3 id="modalTitulo"></h3>
                            <button type="button" id="modalFechar" class="modalFechar" aria-label="Fechar">&times;</button>
                        </div>
                        <div class="modalCorpo" id="modalCorpo"></div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
<script>
(function () {
    var DADOS = <?php echo json_encode($calendario, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    var TIPOS = <?php echo json_encode($tiposCorrida, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    var MES = <?php echo json_encode($mesParam); ?>;
    var LIMITE_MIN = <?php echo $limiteMinimo; ?>;
    var LIMITE_MAX = <?php echo $limiteMaximo; ?>;
    var HORAS_CANCELA = <?php echo $horasCancela; ?>;
    var DIA_ABRIR = <?php echo json_encode($diaAbrir); ?>;
    var modal = document.getElementById("modalCal");
    var titulo = document.getElementById("modalTitulo");
    var corpo = document.getElementById("modalCorpo");
    function esc(valor) {
        if (valor === null || valor === undefined) { return ""; }
        return String(valor).replace(/[&<>"']/g, function (c) {
            return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
        });
    }
    function campos(dia) {
        return '<input type="hidden" name="mesText" value="' + esc(MES) + '">' +
               '<input type="hidden" name="dataText" value="' + esc(dia) + '">';
    }
    function blocoCorrida(corrida, dia) {
        var html = '<div class="corridaItem">';
        html = html + '<div class="corridaCabeca">';
        html = html + '<span class="corridaHora">' + esc(corrida.hora) + '</span>';
        html = html + '<span class="calTag ' + (corrida.visibilidade === "PUBLICA" ? "publica" : "privada") + '">' +
                      (corrida.visibilidade === "PUBLICA" ? "Pública" : "Privada") + '</span>';
        html = html + '<span class="corridaVagas">' + corrida.total + " de " + corrida.limite + ' pilotos</span>';
        html = html + "</div>";
        html = html + '<p class="corridaInfo">' + esc(corrida.tipo) + " &middot; " +
               (corrida.visibilidade === "PUBLICA" ? "Organizada pelo kartódromo" : "Marcada por " + esc(corrida.dono));
        if (corrida.desc) { html = html + " &middot; " + esc(corrida.desc); }
        html = html + "</p>";
        if (corrida.realizada) {
            html = html + '<p class="corridaInfo">Corrida já realizada.</p>';
        }
        if (corrida.senhaUmaVez) {
            html = html + '<p class="corridaSenha">Senha de check-in: <strong>' + esc(corrida.senhaUmaVez) + "</strong>" +
                   "<br>Anote agora e passe aos convidados. Por segurança ela fica gravada criptografada e não será exibida de novo.</p>";
        }
        if (corrida.visibilidade === "PUBLICA" && corrida.podeEntrar) {
            html = html + '<form method="post" class="calForm linha">' + campos(dia) +
                   '<input type="hidden" name="id_corrida" value="' + corrida.id + '">' +
                   '<button type="submit" name="inscreverButton" class="botaoCal">Inscrever-se</button></form>';
        }
        if (corrida.visibilidade === "PRIVADA" && !corrida.minha && corrida.podeEntrar) {
            html = html + '<form method="post" class="calForm linha">' + campos(dia) +
                   '<input type="hidden" name="id_corrida" value="' + corrida.id + '">' +
                   '<input type="text" name="senhaPresencaText" class="inputCal" placeholder="Senha de check-in" required>' +
                   '<button type="submit" name="presencaButton" class="botaoCal">Confirmar presença</button></form>';
        }
        if (corrida.podeSair) {
            html = html + '<form method="post" class="calForm linha">' + campos(dia) +
                   '<input type="hidden" name="id_corrida" value="' + corrida.id + '">' +
                   '<button type="submit" name="sairButton" class="botaoCal secundario">Sair da corrida</button></form>';
        }
        if (corrida.podeCancelar) {
            html = html + '<form method="post" class="calForm linha">' + campos(dia) +
                   '<input type="hidden" name="id_corrida" value="' + corrida.id + '">' +
                   '<input type="text" name="justText" class="inputCal" placeholder="Motivo do cancelamento" required>' +
                   '<button type="submit" name="cancelarButton" class="botaoCal secundario">Cancelar corrida</button></form>';
        }
        if (corrida.participo) {
            html = html + '<p class="corridaOk">Você está nesta corrida.</p>';
        }
        if (!corrida.realizada && !corrida.podeEntrar && !corrida.participo && corrida.vagas <= 0) {
            html = html + '<p class="corridaInfo">Sem vagas.</p>';
        }
        if (!corrida.realizada && corrida.participo && !corrida.podeSair && !corrida.minha) {
            html = html + '<p class="corridaInfo">Faltam menos de ' + HORAS_CANCELA + " horas, a saída já não é permitida.</p>";
        }
        if (!corrida.realizada && corrida.minha && !corrida.podeCancelar) {
            html = html + '<p class="corridaInfo">Faltam menos de ' + HORAS_CANCELA + " horas, o cancelamento já não é permitido.</p>";
        }
        return html + "</div>";
    }
    function abrirDia(dia) {
        var info = DADOS[dia];
        if (!info) { return; }
        titulo.textContent = info.rotulo;
        var html = "";
        if (info.bloqueado) {
            html = html + '<p class="calAviso">Dia bloqueado pelo kartódromo' +
                   (info.bloqueioDesc ? ": " + esc(info.bloqueioDesc) : ".") + "</p>";
        }
        if (info.corridas.length === 0) {
            html = html + '<p class="corridaInfo">Nenhuma corrida marcada neste dia.</p>';
        } else {
            html = html + '<h4 class="calSub">Corridas do dia</h4>';
            for (var i = 0; i < info.corridas.length; i++) {
                html = html + blocoCorrida(info.corridas[i], dia);
            }
        }
        if (info.podeMarcar) {
            var opcoes = "";
            for (var h = 0; h < info.livres.length; h++) {
                opcoes = opcoes + '<option value="' + esc(info.livres[h]) + '">' + esc(info.livres[h]) + "</option>";
            }
            var tipos = "";
            for (var chave in TIPOS) {
                if (TIPOS.hasOwnProperty(chave)) {
                    tipos = tipos + '<option value="' + esc(chave) + '">' + esc(TIPOS[chave]) + "</option>";
                }
            }
            html = html + '<h4 class="calSub">Marcar corrida privada</h4>' +
                   '<form method="post" class="calForm">' + campos(dia) +
                   '<label>Horário livre</label>' +
                   '<select name="horaSelect" class="inputCal" required>' + opcoes + "</select>" +
                   '<label>Tipo do evento</label>' +
                   '<select name="tipoSelect" class="inputCal" required>' + tipos + "</select>" +
                   '<label>Limite de pilotos</label>' +
                   '<input type="number" name="limiteText" class="inputCal" min="' + LIMITE_MIN + '" max="' + LIMITE_MAX + '" value="' + LIMITE_MIN + '" required>' +
                   '<label>Senha de check-in</label>' +
                   '<input type="text" name="senhaText" class="inputCal" minlength="4" maxlength="20" placeholder="De 4 a 20 caracteres" required>' +
                   '<p class="corridaInfo">Você entra como piloto e os convidados confirmam presença com esta senha. Ela será exibida uma única vez depois de marcar.</p>' +
                   '<button type="submit" name="marcarButton" class="botaoCal">Marcar corrida</button></form>';
        } else if (!info.bloqueado && info.livres.length === 0) {
            html = html + '<p class="corridaInfo">Todos os horários deste dia estão ocupados.</p>';
        } else if (!info.bloqueado && !info.podeMarcar) {
            html = html + '<p class="corridaInfo">Este dia está fora do prazo de reserva: a marcação vai de amanhã até 60 dias à frente.</p>';
        }
        corpo.innerHTML = html;
        modal.classList.add("aberto");
        modal.setAttribute("aria-hidden", "false");
    }
    function fechar() {
        modal.classList.remove("aberto");
        modal.setAttribute("aria-hidden", "true");
    }
    var dias = document.querySelectorAll(".calDia[data-dia]");
    for (var d = 0; d < dias.length; d++) {
        dias[d].addEventListener("click", function () {
            abrirDia(this.getAttribute("data-dia"));
        });
    }
    document.getElementById("modalFechar").addEventListener("click", fechar);
    modal.addEventListener("click", function (evento) {
        if (evento.target === modal) { fechar(); }
    });
    document.addEventListener("keydown", function (evento) {
        if (evento.key === "Escape") { fechar(); }
    });
    if (DIA_ABRIR && DADOS[DIA_ABRIR]) {
        abrirDia(DIA_ABRIR);
    }
})();
</script>
</body>
</html>
