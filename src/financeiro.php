<?php
include_once("./db/config.php");
include_once("./db/setDb.php");
include_once("./crud.php");
require_once "auth_emplo.php";
if ($_SESSION['func_perfil'] != 'ADMIN' && $_SESSION['func_perfil'] != 'FINANCEIRO') {
    header("Location: mainEmplo.php");
    exit();
}
$crud = new Crud($db);
$mensagem = "";
$tipoMensagem = "";
$hoje = date("Y-m-d");
$nomeMeses = array(1 => "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
$mesParam = date("Y-m");
if (isset($_POST['mesText'])) {
    $mesParam = $_POST['mesText'];
} else if (isset($_GET['mes'])) {
    $mesParam = $_GET['mes'];
}
if (!preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])$/', $mesParam)) {
    $mesParam = date("Y-m");
}
if (isset($_POST['receitaButton']) || isset($_POST['despesaButton'])) {
    $ehReceita = isset($_POST['receitaButton']);
    $desc    = isset($_POST['descText']) ? trim($_POST['descText']) : "";
    $dataSel = isset($_POST['dataText']) ? $_POST['dataText'] : "";
    $valor   = isset($_POST['valorText']) ? trim($_POST['valorText']) : "";
    $valor = str_replace(array("R$", " "), "", $valor);
    if (strpos($valor, ",") !== false) {
        $valor = str_replace(".", "", $valor);
        $valor = str_replace(",", ".", $valor);
    }
    if ($desc == "") {
        $mensagem = "Informe a descrição do lançamento.";
    } else if (strlen($desc) > 150) {
        $mensagem = "A descrição pode ter no máximo 150 caracteres.";
    } else if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $dataSel)) {
        $mensagem = "Informe a data do lançamento.";
    } else if (!is_numeric($valor) || $valor <= 0) {
        $mensagem = "Informe um valor maior que zero.";
    } else if ($valor > 99999999.99) {
        $mensagem = "O valor informado passa do limite do cadastro.";
    } else if ($ehReceita && $crud->createReceita($desc, $valor, $dataSel, $_SESSION['id'])) {
        header("Location: financeiro.php?mes=" . substr($dataSel, 0, 7) . "&ok=receita");
        exit();
    } else if (!$ehReceita && $crud->createDespesa($desc, $valor, $dataSel, $_SESSION['id'])) {
        header("Location: financeiro.php?mes=" . substr($dataSel, 0, 7) . "&ok=despesa");
        exit();
    } else {
        $mensagem = "Não foi possível gravar o lançamento.";
    }
    $tipoMensagem = "erro";
}
if ($mensagem == "" && isset($_GET['ok'])) {
    $avisos = array(
        "receita" => "Receita lançada.",
        "despesa" => "Despesa lançada."
    );
    if (isset($avisos[$_GET['ok']])) {
        $mensagem = $avisos[$_GET['ok']];
        $tipoMensagem = "sucesso";
    }
}
$tsMes       = strtotime($mesParam . "-01");
$primeiroDia = date("Y-m-01", $tsMes);
$ultimoDia   = date("Y-m-t", $tsMes);
$mesAnterior = date("Y-m", strtotime("-1 month", $tsMes));
$mesSeguinte = date("Y-m", strtotime("+1 month", $tsMes));
$totalReceita = $crud->somaReceitaMes($primeiroDia, $ultimoDia);
$totalDespesa = $crud->somaDespesaMes($primeiroDia, $ultimoDia);
$saldo = $totalReceita - $totalDespesa;
$receitas = $crud->listReceitaMes($primeiroDia, $ultimoDia);
$despesas = $crud->listDespesaMes($primeiroDia, $ultimoDia);
$lancamentos = array();
foreach ($receitas as $linha) {
    $lancamentos[] = array(
        "data"  => $linha['rece_data'],
        "tipo"  => "Receita",
        "desc"  => $linha['rece_desc'],
        "valor" => (float) $linha['rece_valor'],
        "func"  => $linha['func_nome']
    );
}
foreach ($despesas as $linha) {
    $lancamentos[] = array(
        "data"  => $linha['desp_data'],
        "tipo"  => "Despesa",
        "desc"  => $linha['desp_desc'],
        "valor" => (float) $linha['desp_valor'],
        "func"  => $linha['func_nome']
    );
}
$ordem = array();
foreach ($lancamentos as $linha) {
    $ordem[] = $linha['data'];
}
if (count($lancamentos) > 0) {
    array_multisort($ordem, SORT_DESC, $lancamentos);
}
$dataPadrao = ($mesParam == date("Y-m")) ? $hoje : $primeiroDia;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style_Perfil.css">
    <link rel="stylesheet" href="css/style_Financeiro.css">
    <title>Lançamento Financeiro</title>
</head>
<body>
    <div id="containerPc">
        <header class="main-header">
            <nav>
                <a href="mainEmplo.php">Menu</a>
                <a href="logout.php">Sair</a>
            </nav>
        </header>
        <div id="content">
            <div id="container-perfil">
                <div id="container-titulo">
                    <h1>Relatório financeiro</h1>
                    <p>Receitas e despesas do mês, com o saldo do período</p>
                </div>
                <?php if ($mensagem != "") { ?>
                    <p class="mensagem <?php echo $tipoMensagem; ?>"><?php echo htmlspecialchars($mensagem); ?></p>
                <?php } ?>
                <div class="finTopo">
                    <a class="finNav" href="financeiro.php?mes=<?php echo $mesAnterior; ?>">&#8249;</a>
                    <h2 class="finMes"><?php echo $nomeMeses[(int) date("n", $tsMes)] . " de " . date("Y", $tsMes); ?></h2>
                    <a class="finNav" href="financeiro.php?mes=<?php echo $mesSeguinte; ?>">&#8250;</a>
                </div>
                <div id="container-grafico">
                    <h2>Movimento do mês</h2>
                    <div id="graficoBarras"></div>
                    <?php if ($totalReceita == 0 && $totalDespesa == 0) { ?>
                        <p class="finAviso">Nenhum lançamento neste mês. Use o formulário abaixo para lançar a primeira receita ou despesa.</p>
                    <?php } ?>
                    <div class="finResumo">
                        <span class="finItem">Receitas: <strong class="finReceita">R$ <?php echo number_format($totalReceita, 2, ",", "."); ?></strong></span>
                        <span class="finItem">Despesas: <strong class="finDespesa">R$ <?php echo number_format($totalDespesa, 2, ",", "."); ?></strong></span>
                        <span class="finItem">Saldo: <strong class="<?php echo ($saldo < 0) ? "finDespesa" : "finReceita"; ?>">R$ <?php echo number_format($saldo, 2, ",", "."); ?></strong></span>
                    </div>
                </div>
                <form method="post" id="container-form">
                    <h2>Novo lançamento</h2>
                    <input type="hidden" name="mesText" value="<?php echo htmlspecialchars($mesParam); ?>">
                    <p>Descrição</p>
                    <input type="text" name="descText" class="inputText" maxlength="150" placeholder="Ex.: reserva de corrida, manutenção dos karts" required autofocus>
                    <p>Valor</p>
                    <input type="text" name="valorText" class="inputText" placeholder="Ex.: 150,00" required>
                    <p>Data</p>
                    <input type="date" name="dataText" class="inputText" value="<?php echo htmlspecialchars($dataPadrao); ?>" required>
                    <div id="acoes-form">
                        <input type="submit" name="receitaButton" class="botoes" value="Lançar receita">
                        <input type="submit" name="despesaButton" class="botoes botaoDespesa" value="Lançar despesa">
                    </div>
                </form>
                <div id="container-historico">
                    <h2>Lançamentos do mês</h2>
                    <?php if (count($lancamentos) == 0) { ?>
                        <p>Nenhuma receita ou despesa lançada neste mês.</p>
                    <?php } else { ?>
                        <p><?php echo count($lancamentos); ?> lançamento(s)</p>
                        <div id="container-tabela">
                            <table class="tabela">
                                <tr>
                                    <th>Data</th>
                                    <th>Tipo</th>
                                    <th>Descrição</th>
                                    <th>Valor</th>
                                    <th>Lançado por</th>
                                </tr>
                                <?php foreach ($lancamentos as $lancamento) { ?>
                                    <tr>
                                        <td><?php echo date("d/m/Y", strtotime($lancamento['data'])); ?></td>
                                        <td><span class="finTag <?php echo ($lancamento['tipo'] == "Receita") ? "receita" : "despesa"; ?>"><?php echo $lancamento['tipo']; ?></span></td>
                                        <td><?php echo htmlspecialchars($lancamento['desc']); ?></td>
                                        <td>R$ <?php echo number_format($lancamento['valor'], 2, ",", "."); ?></td>
                                        <td><?php echo htmlspecialchars($lancamento['func'] ? $lancamento['func'] : "-"); ?></td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<script>
(function () {
    var VALORES = <?php echo json_encode(array("receita" => $totalReceita, "despesa" => $totalDespesa)); ?>;
    var alvo = document.getElementById("graficoBarras");
    function moeda(valor) {
        return "R$ " + valor.toLocaleString("pt-BR", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    var barras = [
        { nome: "Receitas", valor: VALORES.receita, classe: "receita" },
        { nome: "Despesas", valor: VALORES.despesa, classe: "despesa" }
    ];
    var maior = Math.max(barras[0].valor, barras[1].valor);
    var html = "";
    for (var i = 0; i < barras.length; i++) {
        var altura = (maior > 0) ? Math.round((barras[i].valor / maior) * 100) : 0;
        if (barras[i].valor > 0 && altura < 4) { altura = 4; }
        html = html + '<div class="finColuna">' +
               '<span class="finValor">' + moeda(barras[i].valor) + "</span>" +
               '<div class="finTrilha"><div class="finBarra ' + barras[i].classe + '" style="height: ' + altura + '%"></div></div>' +
               '<span class="finRotulo">' + barras[i].nome + "</span>" +
               "</div>";
    }
    alvo.innerHTML = html;
})();
</script>
</body>
</html>
