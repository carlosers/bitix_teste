<?php
$arqJsonPlanos = file_get_contents('./planos.json');
$planos = json_decode($arqJsonPlanos,true);
$arqJsonPrecos = file_get_contents('./precos.json');
$precos = json_decode($arqJsonPrecos,true);
function calculaPlano($idades,$pprecos,$pD){

    $chave = 0;
    $_valor = [];
    $posId=0;
    for($i=0;$i<count($pprecos);$i++){
        $chave = ($pprecos[$i]['codigo']==$pD && $pprecos[$i]['minimo_vidas']<=count($idades))?$i:$chave;
    }
    
    foreach($idades as $_idade){
      if ($_idade >= 0 && $_idade <= 17)  $_valor[$posId]= $pprecos[$chave]['faixa1'];
      if ($_idade >= 18 && $_idade <= 40) $_valor[$posId]= $pprecos[$chave]['faixa2'];
      if ($_idade >= 41) $_valor[$posId]= $pprecos[$chave]['faixa3'];
      $posId++;
    }

    return $_valor;
}
function gravarBeneficiarios($precos){
    $quantidade = $_GET['totBenef'];
    $participantes = [];
    $_idades=[];
    for($i=1;$i<=$quantidade;$i++){
        $benef = 'idBenef'.$i;
        $idade = $_GET[$benef];
        $benef = 'nmBenef'.$i;
        $nome = $_GET[$benef]; 
        $participantes[]= ['idade'=>$idade,'nome'=>$nome]; 
        array_push($_idades,[$idade]);          
    }
    $planoEscolhido = $_GET['planoDesejado'];
    $paraJson=['numero_beneficiarios'=>$quantidade,'participantes'=>$participantes,'plano_escolhido'=>$planoEscolhido];
    $beneficiarios = json_encode($paraJson,JSON_FORCE_OBJECT);
    $arqBeneficiarios = file_put_contents('beneficiarios.json',array($beneficiarios));
    /////////// gerar proposta ///////////
    
    $valores = calculaPlano($_idades,$precos,$planoescolhido);
    $valorTotal = array_sum($valores);
    for($i=0;$i<$quantidade;$i++){
        $participantes[$i]['valor_individual'] = $valores[$i];
    }
    $paraJson =['numero_beneficiarios'=>$quantidade,'participantes'=>$participantes,'plano_escolhido'=>$planoEscolhido,'valor_total'=>$valorTotal];
    $proposta = json_encode($paraJson,JSON_FORCE_OBJECT);
    $arqProposta = file_put_contents('proposta.json',array($proposta));

}
$btnPouSSalvar = 'btn-secondary';
$onOffSalvar = 'disabled';
if(isset($_GET['totBenef'])){
    $qtBenef = $_GET['totBenef'];
    $planoDesejado = $_GET['planoDesejado'];
    $btnPouS = "btn-secondary";
    $onOff = 'disabled';
    if(isset($_GET['idBenef1'])){
        for($i=1;$i<=$qtBenef;$i++){
            $benef = 'idBenef'.$i;
            $$benef = $_GET[$benef];
            $benef = 'nmBenef'.$i;
            $$benef = $_GET[$benef];            
        }
    }
    if (isset($_GET['gravaBenef'])){
        if ($_GET['gravaBenef']=='sim') {
            $btnPouS = 'btn-primary';
            $onOff = 'active';
            $btnPouSSalvar = 'btn-secondary';
            $onOffSalvar = 'disabled';
            gravarBeneficiarios($precos);
        } else {
            $btnPouS = "btn-secondary";
            $onOff = 'disabled';
        }
    }
    if ($planoDesejado>0 && $_GET['gravaBenef'] == 'nao'){
        $btnPouSSalvar = 'btn-primary';
        $onOffSalvar = 'active';
        $_GET['gravaBenef'] = 'sim';
    
    }
    
} else { 
    $qtBenef = 0;
    $vtValor=[];
    $_GET['gravaBenef'] = 'nao';
    $btnPouS = "btn-secondary";
    $onOff = 'disabled';
    
}
$arqJsonPlanos = file_get_contents('./planos.json');
$planos = json_decode($arqJsonPlanos,true);
$arqJsonPrecos = file_get_contents('./precos.json');
$precos = json_decode($arqJsonPrecos,true);

if (!(isset($planoDesejado)) || $planoDesejado==0) $mostraPlano = 'NÃO ESCOLHIDO';
else {
    $mostraPlano = $planos[$planoDesejado-1]['nome'];
    $vtIdade=array();
    $vtNome=[]; 
    
    for($i=1;$i<=$qtBenef;$i++){
        $benef = 'idBenef'.$i;
        $$benef = $_GET[$benef];
        array_push($vtIdade,$$benef);
        $benef = 'nmBenef'.$i;
        $$benef = $_GET[$benef];
        array_push($vtNome,$$benef);
    }
    $vtValor = calculaPlano($vtIdade,$precos,$planoDesejado);

    $valorTotal = array_sum($vtValor);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="teste - um programa que gere um proposta de plano de saúde">
    <meta name="author" content="Carlos E R Santiago">
    <title>Cotação Plano de Saúde</title>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha
384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>


    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

</head>

<body class="bg-light">
    <div class="container">
        <div class="py-5 text-center">
            <img class="d-block mx-auto mb-4" src="img/bitix.webp" alt="Logo da empresa" width="auto" height="auto">
            <h2>Escolha seu Plano de Saúde</h2>
            <p class="lead">Preencha abaixo o formulário para a formulação da proposta</p>
            <div class="alert alert-info lead" role="alert">
                Planos com ***(n), onde n representa o número mínimo de beneficiários, apontam para novas condições no mesmo plano.
            </div>
            <div class="row">
                <div class="col">
                    <a class="btn btn-primary" href="./index.php" role="button">Nova Consulta</a>
                </div>
                <div class="col">
                <?php  
                    //echo "<a class=\"btn {$btnPouS} {$onOff}\" id=\"btGeraProp\" href=\"#\" role=\"button\">Gerar Proposta</a>"; 
                ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 order-md-2 mb-6">
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Proposta</span>
                    <span class="badge badge-secondary badge-pill">BitIX</span>
                </h4>
                <ul class="list-group mb-3">
                    <li class="list-group-item d-flex justify-content-between lh-condensed">
                        <div>
                            <h6 class="my-0" id="iPtotPlanoEsc">Preço total do Plano escolhido</h6>
                            <?php
                                echo "<small class=\"text-muted\" id=\"iPlano\">{$mostraPlano}</small>";
                            ?>
                        </div>
                        <?php 
                            if (!(isset($valorTotal))) $valorTotal = '0.00';
                            echo "<span class=\"text-muted\">R$ {$valorTotal}</span>";
                        ?>
                    </li>
                
                    <?php
                        $trocaCor = 'bg-light';
                        for($i=1;$i<=$qtBenef;$i++){
                            $bfIdade = 'idBenef'.$i;
                            $bfNome = 'nmBenef'.$i;
                            $bfValor = $vtValor[$i-1];
                            echo "<li class=\"list-group-item d-flex justify-content-between {$trocaCor}\">\n";
                            echo "<div class=\"text-success\">\n";
                                echo "<h6 class=\"my-0\">{$$bfNome}</h6>\n";
                            echo "<small id=\"iIdade\">{$$bfIdade} anos</small>\n";
                            echo "</div>\n";
                            echo "<span class=\"text-muted\">R$ {$bfValor}</span>\n";
                            echo "</li>\n";
                            $trocaCor=='bg-light'?$trocaCor='lh-condensed':$trocaCor='bg-light';
                        }
                    ?>
                </ul>
                
            </div>
            <!-- quadro esquerdo - formulário a ser preenchido -->
            <div class="col-md-6 order-md-1">
                <h4 class="mb-3">Participantes</h4>
                <form class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="totBenef">Quantos beneficiários irão participar do plano de saúde?</label>
                        <div class="input-group">
                            <?php
                            if ($qtBenef !=0) 
                            echo "<input type=\"text\" class=\"form-control\" id=\"totBenef\" name = \"totBenef\" value=\"{$qtBenef}\" onchange=\"this.form.submit()\" required>";
                            else
                            echo "<input type=\"text\" class=\"form-control\" id=\"totBenef\" name = \"totBenef\" placeholder=\"número de vidas asseguradas. ex: 5\" onchange=\"this.form.submit()\" required>";         
                            ?>
                        </div>
                    </div>
                    <?php
                    for($i=1;$i<=$qtBenef;$i++){
                        echo "<div class=\"row\">\n";
                        echo "<div class=\"col-md-5 mb-3\">\n";
                        echo "<label for=\"benef{$i}\">Idade do beneficiario n.{$i}?</label>\n";
                        $benef = 'idBenef'.$i;
                        if ($$benef!='')
                        echo "<input type=\"number\" class=\"form-control\" id=\"idBenef{$i}\" name = \"idBenef{$i}\" value=\"{$$benef}\" required>\n";
                        else
                        echo "<input type=\"number\" class=\"form-control\" id=\"idBenef{$i}\" name = \"idBenef{$i}\" placeholder=\"idade. ex: 18\"  required>\n";

                        echo "</div>\n";
                        echo "<div class=\"col-md-7 mb-3\">\n";
                        echo "<label for=\"benef{$i}\">Nome do beneficiario n.{$i}?</label>\n";
                        $benef = 'nmBenef'.$i;
                        if ($$benef!='')
                        echo "<input type=\"text\" class=\"form-control\" id=\"nmBenef{$i}\" name = \"nmBenef{$i}\" value=\"{$$benef}\"  required>\n";
                        else
                        echo "<input type=\"text\" class=\"form-control\" id=\"nmBenef{$i}\" name = \"nmBenef{$i}\" placeholder=\"Nome do participante. ex: Fulano de tal\"  required>\n";

                        echo "</div>\n";
                        echo "</div>\n";
                
                    }
                    ?>

                    <div class="mb-3">
                        <label for="escolhaPlano">Selecione o Plano desejado</label>
                        <select class="custom-select d-block w-100" id="planoDesejado" name="planoDesejado" onchange="this.form.submit()" required>
                            <option value="0">Escolha...</option>
                        <?php
                            if ($qtBenef != 0){
                                foreach ($planos as $reg) {

                                    $obs='';
                                   foreach ($precos as $rPrec){
                                        if ($qtBenef<$rPrec['minimo_vidas'] && $reg['codigo']==$rPrec['codigo']) {
                                            $obs = '***('.$rPrec['minimo_vidas'].')'; 
                                       }
                                   }

                                   if ($planoDesejado !=$reg['codigo'])
                                   echo "<option value=\"{$reg['codigo']}\">{$obs} {$reg['nome']}</option>";
                                   else echo "<option value=\"{$reg['codigo']}\" SELECTED>{$obs} {$reg['nome']}</option>";
                                    
                                   if ($obs=='***') $flagObs=1;
                                    $obs='';
                                }
                            }
                        ?>
                        
                        </select>
                        
                    </div>
                    <div class="row">
                            <div class="col text-center">
                            <?php 
                            if ($_GET['gravaBenef'] == 'sim') 
                            echo "<input type=\"hidden\" class=\"form-control\" id=\"gravaBenef\" name = \"gravaBenef\" value=\"sim\" >";

                            else
                            echo "<input type=\"hidden\" class=\"form-control\" id=\"gravaBenef\" name = \"gravaBenef\" value=\"nao\" >";

                            echo "<button type=\"submit\" class=\"btn {$btnPouSSalvar} {$onOffSalvar}\">Salvar e Gerar Proposta</button>";
                            ?>
                            </div>
                        </div>

                </form>   
                    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
                        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
                        crossorigin="anonymous"></script>
                    <script>
                        window.jQuery || document.write(
                            '<script src="../../assets/js/vendor/jquery-slim.min.js"><\/script>')
                    </script>
                    <script src="../../assets/js/vendor/popper.min.js"></script>
                    <script src="../../dist/js/bootstrap.min.js"></script>
                    <script src="../../assets/js/vendor/holder.min.js"></script>
                    <?php
                        if ($qtBenef>0) {
                            if ($planoDesejado>0){
                                echo "<script>";
                                echo "    $('#btSalvaBenef').focus()";
                                echo "</script>";
                            } else{
                                echo "<script>";
                                echo "    $('#idBenef1').focus()";
                                echo "</script>";
                            }
                        } else {
                            echo "<script>";
                            echo "    $('#totBenef').focus()";
                            echo "</script>";
                        }
                    ?>        
                    
</body>

</html>