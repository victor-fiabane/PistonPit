<?php
    include_once("./db/config.php");
    include_once("./db/setDb.php");
    include_once("./crud.php");
    require_once 'auth_emplo.php';
    $crud = new Crud($db);
    $podeGerenciar = in_array($_SESSION['func_perfil'], array('ADMIN', 'FINANCEIRO'));
    $horaAbre     = "10:00";
    $horaFecha    = "22:00";
    $duracaoMin   = 30;
    $limiteMinimo = 2;
    $limiteMaximo = 12;
    $tiposCorrida = array(
        "CORRIDA"      => "Corrida",
        "TREINO"       => "Treino",
        "TOMADA_TEMPO" => "Tomada de tempo"
    );
    $hoje  = date("Y-m-d");
    $agora = time();
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
    if (!$podeGerenciar && (isset($_POST['publicaButton']) || isset($_POST['bloquearButton'])
        || isset($_POST['desbloquearButton']) || isset($_POST['cancelarButton']))) {
        $mensagem = "Seu perfil tem acesso apenas de consulta ao calendário.";
        $tipoMensagem = "erro";
    }
    if ($podeGerenciar && isset($_POST['publicaButton'])) {
        $dataSel = isset($_POST['dataText']) ? $_POST['dataText'] : "";
        $horaSel = isset($_POST['horaSelect']) ? $_POST['horaSelect'] : "";
        $tipoSel = isset($_POST['tipoSelect']) ? $_POST['tipoSelect'] : "";
        $limite  = isset($_POST['limiteText']) ? (int) $_POST['limiteText'] : 0;
        $desc    = isset($_POST['descText']) ? trim($_POST['descText']) : "";
        $diaAbrir = $dataSel;
        if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $dataSel)) {
            $mensagem = "Data inválida.";
        } else if ($dataSel < $hoje) {
            $mensagem = "Não é possível criar corrida em data passada.";
        } else if ($crud->existsBloqueio($dataSel)) {
            $mensagem = "Este dia está bloqueado. Desbloqueie antes de criar a corrida.";
        } else if (!in_array($horaSel, $horarios)) {
            $mensagem = "Escolha um horário da grade do dia.";
        } else if (!isset($tiposCorrida[$tipoSel])) {
            $mensagem = "Escolha o tipo do evento.";
        } else if (strtotime($dataSel . " " . $horaSel) <= $agora) {
            $mensagem = "Este horário já passou.";
        } else if ($limite < $limiteMinimo || $limite > $limiteMaximo) {
            $mensagem = "O limite de pilotos vai de " . $limiteMinimo . " a " . $limiteMaximo . ".";
        } else if ($crud->existsCorridaHorario($dataSel, $horaSel)) {
            $mensagem = "Este horário já está ocupado por outra corrida.";
        } else if ($crud->createCorridaPublica($_SESSION['id'], $dataSel, $horaSel, $tipoSel, $limite, $desc)) {
            header("Location: mainEmplo.php?mes=" . substr($dataSel, 0, 7) . "&dia=" . $dataSel . "&ok=publica");
            exit();
        } else {
            $mensagem = "Não foi possível criar a corrida.";
        }
        $tipoMensagem = "erro";
    }
    if ($podeGerenciar && isset($_POST['bloquearButton'])) {
        $dataSel = isset($_POST['dataText']) ? $_POST['dataText'] : "";
        $desc    = isset($_POST['bloqDescText']) ? trim($_POST['bloqDescText']) : "";
        $diaAbrir = $dataSel;
        $temCorrida = false;
        $doDia = $crud->listCorridaMes($dataSel, $dataSel);
        foreach ($doDia as $linha) {
            if ($linha['cor_status'] == 'AGENDADA') {
                $temCorrida = true;
            }
        }
        if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $dataSel)) {
            $mensagem = "Data inválida.";
        } else if ($dataSel < $hoje) {
            $mensagem = "Não é possível bloquear uma data passada.";
        } else if ($desc == "") {
            $mensagem = "Informe o motivo do bloqueio.";
        } else if ($crud->existsBloqueio($dataSel)) {
            $mensagem = "Este dia já está bloqueado.";
        } else if ($temCorrida) {
            $mensagem = "Existem corridas agendadas neste dia. Cancele as corridas antes de bloquear.";
        } else if ($crud->createBloqueio($dataSel, $desc, $_SESSION['id'])) {
            header("Location: mainEmplo.php?mes=" . substr($dataSel, 0, 7) . "&dia=" . $dataSel . "&ok=bloqueado");
            exit();
        } else {
            $mensagem = "Não foi possível bloquear o dia.";
        }
        $tipoMensagem = "erro";
    }
    if ($podeGerenciar && isset($_POST['desbloquearButton'])) {
        $dataSel = isset($_POST['dataText']) ? $_POST['dataText'] : "";
        $diaAbrir = $dataSel;
        if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $dataSel)) {
            $mensagem = "Data inválida.";
        } else if (!$crud->existsBloqueio($dataSel)) {
            $mensagem = "Este dia não está bloqueado.";
        } else if ($crud->deleteBloqueio($dataSel)) {
            header("Location: mainEmplo.php?mes=" . substr($dataSel, 0, 7) . "&dia=" . $dataSel . "&ok=liberado");
            exit();
        } else {
            $mensagem = "Não foi possível liberar o dia.";
        }
        $tipoMensagem = "erro";
    }
    if ($podeGerenciar && isset($_POST['cancelarButton'])) {
        $idCorrida = isset($_POST['id_corrida']) ? (int) $_POST['id_corrida'] : 0;
        $just = isset($_POST['justText']) ? trim($_POST['justText']) : "";
        $corrida = $crud->readCorridaId($idCorrida);
        $diaAbrir = $corrida ? $corrida['cor_data'] : "";
        if (!$corrida || $corrida['cor_status'] != 'AGENDADA') {
            $mensagem = "Corrida não encontrada ou já realizada.";
        } else if (strlen($just) < 5) {
            $mensagem = "Escreva o motivo do cancelamento.";
        } else if ($crud->cancelCorrida($idCorrida, $just, null, $_SESSION['id'])) {
            header("Location: mainEmplo.php?mes=" . substr($corrida['cor_data'], 0, 7) . "&dia=" . $corrida['cor_data'] . "&ok=cancelada");
            exit();
        } else {
            $mensagem = "Não foi possível cancelar a corrida.";
        }
        $tipoMensagem = "erro";
    }
    if ($mensagem == "" && isset($_GET['ok'])) {
        $avisos = array(
            "publica"   => "Corrida pública criada.",
            "bloqueado" => "Dia bloqueado.",
            "liberado"  => "Dia liberado.",
            "cancelada" => "Corrida cancelada e justificativa registrada."
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
    $pilotos   = $crud->listPartMes($primeiroDia, $ultimoDia);
    $diasBloqueados = array();
    foreach ($bloqueios as $bloqueio) {
        $diasBloqueados[$bloqueio['bloq_data']] = $bloqueio['bloq_desc'];
    }
    $pilotosCorrida = array();
    foreach ($pilotos as $piloto) {
        $pilotosCorrida[$piloto['fk_id_corrida']][] = $piloto['cli_nome'];
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
                $total = (int) $corrida['total_part'];
                $limite = (int) $corrida['cor_limite_pilot'];
                $itens[] = array(
                    "id"           => $id,
                    "hora"         => $hora,
                    "visibilidade" => $corrida['cor_visibilidade'],
                    "tipo"         => isset($tiposCorrida[$corrida['cor_tipo']]) ? $tiposCorrida[$corrida['cor_tipo']] : $corrida['cor_tipo'],
                    "desc"         => isset($corrida['cor_desc']) ? $corrida['cor_desc'] : "",
                    "dono"         => ($corrida['cor_visibilidade'] == 'PRIVADA') ? $corrida['cli_nome'] : $corrida['func_nome'],
                    "realizada"    => ($corrida['cor_status'] == 'REALIZADA'),
                    "agendada"     => ($corrida['cor_status'] == 'AGENDADA'),
                    "limite"       => $limite,
                    "total"        => $total,
                    "vagas"        => $limite - $total,
                    "pilotos"      => isset($pilotosCorrida[$id]) ? $pilotosCorrida[$id] : array()
                );
            }
        }
        $livres = array();
        foreach ($horarios as $hora) {
            if (in_array($hora, $ocupados)) { continue; }
            if ($data == $hoje && strtotime($data . " " . $hora) <= $agora) { continue; }
            $livres[] = $hora;
        }
        $podeCriar = ($podeGerenciar && !$bloqueado && $data >= $hoje && count($livres) > 0);
        $agendadasNoDia = 0;
        foreach ($itens as $item) {
            if ($item['agendada']) { $agendadasNoDia++; }
        }
        $calendario[$data] = array(
            "data"         => $data,
            "dia"          => $dia,
            "rotulo"       => $diasSemana[(int) date("w", $tsDia)] . ", " . date("d/m/Y", $tsDia),
            "bloqueado"    => $bloqueado,
            "bloqueioDesc" => $bloqueado ? $diasBloqueados[$data] : "",
            "hoje"         => ($data == $hoje),
            "passado"      => ($data < $hoje),
            "podeCriar"    => $podeCriar,
            "podeBloquear" => ($podeGerenciar && !$bloqueado && $data >= $hoje && $agendadasNoDia == 0),
            "temCorrida"   => ($agendadasNoDia > 0),
            "podeLiberar"  => ($podeGerenciar && $bloqueado),
            "livres"       => $livres,
            "corridas"     => $itens
        );
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style_MainMenu.css">
    <link rel="stylesheet" href="css/style_Calendario.css">
    <title>Menu Funcionário</title>
</head>
<body>
<header class="main-header">
        <nav>
            <a href="lista_client.php">Clientes</a>
            <?php if ($podeGerenciar) { ?>
                <a href="financeiro.php">Financeiro</a>
            <?php } ?>
            <?php if ($_SESSION['func_perfil'] == 'ADMIN') { ?>
                <a href="emplo_cadastro.php">Cadastro de Funcionários</a>
                <a href="lista_emplo.php">Listagem de Funcionários</a>
            <?php } ?>
            <a href="perfil_emplo.php">Perfil</a>
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
                        <a class="calNav" href="mainEmplo.php?mes=<?php echo $mesAnterior; ?>">&#8249;</a>
                        <h2 class="calMes"><?php echo $nomeMeses[(int) date("n", $tsMes)] . " de " . date("Y", $tsMes); ?></h2>
                        <a class="calNav" href="mainEmplo.php?mes=<?php echo $mesSeguinte; ?>">&#8250;</a>
                    </div>
                    <p class="calAjuda">
                        <?php if ($podeGerenciar) { ?>
                            Clique em um dia para ver as corridas, criar corrida pública ou bloquear o dia.
                        <?php } else { ?>
                            Clique em um dia para ver as corridas marcadas. Seu perfil tem acesso de consulta.
                        <?php } ?>
                    </p>
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
                                            $qtdPilotos = 0;
                                            foreach ($info['corridas'] as $item) {
                                                if ($item['tipo'] == 'PUBLICA') { $qtdPublica++; } else { $qtdPrivada++; }
                                                $qtdPilotos = $qtdPilotos + $item['total'];
                                            }
                                        ?>
                                        <?php if ($qtdPublica > 0) { ?>
                                            <span class="calTag publica"><?php echo $qtdPublica; ?> pública<?php echo ($qtdPublica > 1) ? "s" : ""; ?></span>
                                        <?php } ?>
                                        <?php if ($qtdPrivada > 0) { ?>
                                            <span class="calTag privada"><?php echo $qtdPrivada; ?> privada<?php echo ($qtdPrivada > 1) ? "s" : ""; ?></span>
                                        <?php } ?>
                                        <?php if ($qtdPilotos > 0) { ?>
                                            <span class="calTag minha"><?php echo $qtdPilotos; ?> piloto<?php echo ($qtdPilotos > 1) ? "s" : ""; ?></span>
                                        <?php } ?>
                                    <?php } ?>
                                </span>
                            </button>
                        <?php } ?>
                    </div>
                    <div class="calLegenda">
                        <span><i class="ponto publica"></i> corrida pública</span>
                        <span><i class="ponto privada"></i> corrida privada</span>
                        <span><i class="ponto minha"></i> pilotos inscritos</span>
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
    var GERENCIA = <?php echo $podeGerenciar ? "true" : "false"; ?>;
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
               (corrida.visibilidade === "PUBLICA" ? "Criada por " : "Marcada pelo cliente ") + esc(corrida.dono);
        if (corrida.desc) { html = html + " &middot; " + esc(corrida.desc); }
        if (corrida.realizada) { html = html + " &middot; já realizada"; }
        html = html + "</p>";
        if (corrida.pilotos.length > 0) {
            var lista = "";
            for (var p = 0; p < corrida.pilotos.length; p++) {
                lista = lista + "<li>" + esc(corrida.pilotos[p]) + "</li>";
            }
            html = html + '<ul class="listaPilotos">' + lista + "</ul>";
        } else {
            html = html + '<p class="corridaInfo">Nenhum piloto inscrito.</p>';
        }
        if (GERENCIA && corrida.agendada) {
            html = html + '<form method="post" class="calForm linha">' + campos(dia) +
                   '<input type="hidden" name="id_corrida" value="' + corrida.id + '">' +
                   '<input type="text" name="justText" class="inputCal" placeholder="Motivo do cancelamento" required>' +
                   '<button type="submit" name="cancelarButton" class="botaoCal secundario">Cancelar corrida</button></form>';
        }
        return html + "</div>";
    }
    function abrirDia(dia) {
        var info = DADOS[dia];
        if (!info) { return; }
        titulo.textContent = info.rotulo;
        var html = "";
        if (info.bloqueado) {
            html = html + '<p class="calAviso">Dia bloqueado' +
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
        if (info.podeCriar) {
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
            html = html + '<h4 class="calSub">Criar corrida pública</h4>' +
                   '<form method="post" class="calForm">' + campos(dia) +
                   '<label>Horário livre</label>' +
                   '<select name="horaSelect" class="inputCal" required>' + opcoes + "</select>" +
                   '<label>Tipo do evento</label>' +
                   '<select name="tipoSelect" class="inputCal" required>' + tipos + "</select>" +
                   '<label>Limite de pilotos</label>' +
                   '<input type="number" name="limiteText" class="inputCal" min="' + LIMITE_MIN + '" max="' + LIMITE_MAX + '" value="' + LIMITE_MAX + '" required>' +
                   '<label>Descrição (opcional)</label>' +
                   '<input type="text" name="descText" class="inputCal" maxlength="150" placeholder="Ex.: etapa do campeonato">' +
                   '<button type="submit" name="publicaButton" class="botaoCal">Criar corrida pública</button></form>';
        } else if (GERENCIA && !info.bloqueado && info.livres.length === 0) {
            html = html + '<p class="corridaInfo">Não há horário livre neste dia.</p>';
        }
        if (info.podeBloquear) {
            html = html + '<h4 class="calSub">Bloquear o dia</h4>' +
                   '<form method="post" class="calForm">' + campos(dia) +
                   '<label>Motivo</label>' +
                   '<input type="text" name="bloqDescText" class="inputCal" maxlength="150" placeholder="Ex.: manutenção da pista" required>' +
                   '<p class="corridaInfo">O dia bloqueado deixa de aceitar reserva e inscrição. Corridas já marcadas precisam ser canceladas antes.</p>' +
                   '<button type="submit" name="bloquearButton" class="botaoCal secundario">Bloquear dia</button></form>';
        }
        if (GERENCIA && !info.bloqueado && !info.passado && info.temCorrida) {
            html = html + '<h4 class="calSub">Bloquear o dia</h4>' +
                   '<p class="corridaInfo">Para bloquear este dia, cancele antes as corridas marcadas acima.</p>';
        }
        if (info.podeLiberar) {
            html = html + '<form method="post" class="calForm linha">' + campos(dia) +
                   '<button type="submit" name="desbloquearButton" class="botaoCal">Liberar o dia</button></form>';
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
