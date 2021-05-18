<?php 
              //Funções para labs.php

              function addgrupo($id_grupo,$descricao_grupo, $descricao_turma) {
                  echo "\t\t\t\t\t<option value=\"" . $id_grupo . "\">" . $descricao_grupo . " - " . $descricao_turma . "</option>\n";
              }

              function addhorario($id_horario,$descricao_horario){
                  echo "\t\t\t\t\t<option value=\"" . $id_horario . "\">" . $descricao_horario . "</option>\n";
              }
              function showReserva($grupo,$turma,$horario,$data){
                
              echo "\n<div class=\"row\">\n";
              echo "\t\t\t\t  <div class=\"column\" style=\"background-color:#aaa;\">\n";
              echo "\t\t\t\t <p> ". date('d/m/Y',strtotime($data)) ." </p>\n";
              echo "\t\t\t\t  </div>\n";
              echo "\t\t\t\t  <div class=\"column\" style=\"background-color:#bbb;\">\n";
              echo "\t\t\t\t <p> " . $grupo . " - " . $turma . ". </p>\n";
              echo "\t\t\t\t  </div>\n";
              echo "\t\t\t\t  <div class=\"column\" style=\"background-color:#ccc;\">\n";
              echo "\t\t\t\t <p> " . $horario . "</p>\n";
              echo "\t\t\t\t  </div>\n";

              echo " </div>\n";

              }

              function getDay(){
                return date("Y-m-d");
              }

              function getLastDayOfM(){
                return date("Y-m-t");
              }

              function pegouvalor(){
                echo "<br><p> " . $_POST["grupo"] . "</p>";
              }

              function alerta($msg,$title) {
                //echo "<script type='text/javascript'> alert('$msg');</script>";
                echo "<div id=\"dialog-confirm\" title=\"" . $title . "\">
                  <p class=textdiag><span class=\"ui-icon ui-icon-alert\" style=\"float:left; margin:12px 12px 20px 0;\"></span> " . $msg . "</p>
                </div>";
              }

              function confirmar($msg,$title) {
                //echo "<script type='text/javascript'> alert('$msg');</script>";
                echo "<div id=\"dialog-confirm\" title=\"" . $title . "\">
                  <p class=textdiag><span class=\"ui-icon ui-icon-check\" style=\"float:left; margin:12px 12px 20px 0;\"></span> " . $msg . "</p>
                </div>";
              }

              function bdConnect(){


                    // PROD :)
                    
                    /*
                    $servername = ;
                    $username = ;
                    $dbname = ;
                    $count = 0;
                    /*
                    

                    // DESENV
                    /* 
                    $servername = ;
                    $username = ;
                    $password = ;
                    $dbname = ;
                    $count = 0;
                    */


                    // Create connection
                    $conn = new mysqli($servername, $username, $password, $dbname);

                    // Check connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }
                    return $conn;
              }

              function buildForm(){
                    $conn = bdConnect();

                    $sql = "SELECT g.id as ID, g.descricao AS GRUPO, t.descricao AS TURMA FROM grupo g INNER JOIN turma t ON g.id_turma = t.id";


                    $result = $conn->query($sql);
                    

                    if ($result->num_rows > 0) {
                      // output data of each row
                      
                      echo "\t\t\t<p id=\"l_grupo\"> Grupo:</p>\n"; 
                      echo "\t\t\t\t <select id=\"group\" name=\"grupo\" required=\"required\" oninvalid=\"this.setCustomValidity('Informe o Grupo')\" onchange=\"try{setCustomValidity('')}catch(e){}\" >\r\n";
                      echo "\t\t\t\t <option value=\"\" disabled selected>Escolha o grupo</option>\n";
                      while($row = $result->fetch_assoc()) {  
                        
                      addgrupo($row["ID"],$row["GRUPO"],$row["TURMA"]);

                      } 
                      echo "\t\t\t\t</select>\r\n";
                      
                    } else {
                      echo "0 results";
                    }

                    $sql = "SELECT * FROM horarios";

                    $result = $conn->query($sql);
                    

                    if ($result->num_rows > 0) {
                      // output data of each row
                      
                      echo "\t\t\t <p id=\"l_horario\">Horário:</p>\n"; 
                      echo "\t\t\t\t <select id=\"hour\" name=\"horarios\" required=\"required\" oninvalid=\"this.setCustomValidity('Informe o Horário')\" onchange=\"try{setCustomValidity('')}catch(e){}\" >\r\n";
                      echo "\t\t\t\t <option value=\"\" disabled selected>Escolha o horário</option>\n";
                      while($row = $result->fetch_assoc()) {  
                        
                      addhorario($row["id"],$row["descricao"]);

                      } 
                      echo "\t\t\t</select>\r\n";
                      
                    } else {
                      echo "0 results";
                    }

                    $conn->close();
                    echo "\t\t\t<p id=\"l_data\">Data:</p>\n"; 
                    $diaatual = getDay();
                    $ultimodia = date('Y-m-d', strtotime('+30 days', strtotime($diaatual)));
                    echo "\t\t\t\t <input id=\"data\" type=\"date\" name=\"data\" required=\"required\" oninvalid=\"this.setCustomValidity('Informe o Data')\" onchange=\"try{setCustomValidity('')}catch(e){}\" min=\"".$diaatual."\" max=\"".$ultimodia."\"><br>\n";
                    echo "\t\t\t<br><input type=\"submit\" value=\"Confirmar\" onclick=\"validator('group','hour','data')\">\n";
                    echo "\t\t\t<input type=\"reset\" value=\"Cancelar\">\n";
              }

              function insertReserva($grupo,$horarios,$data){
                //insert into database
		//ALTERACAO 16-19-2019 - Adequacao ao semestre - Aulas 34,56M e 34,56T

                if((date('l',strtotime($data)) === 'Friday') && ($horarios === '1' || $horarios === '2' || $horarios === '4')){
                  $msg = "Na sexta-feira, apenas o horário T12 pode ser reservado. Por favor, escolher outro horário ou dia!";
                  $title = "Não foi possível cadastrar horário!";
                  return alerta($msg,$title);
                } elseif(((date('l',strtotime($data)) === 'Monday') || (date('l',strtotime($data)) === 'Wednesday')) && ($horarios === '2') ){
                  $msg = "Nas segundas e quartas, o horários M56 é reservado para aula. Por favor, escolher outro horário ou dia!";
                  $title = "Não foi possível cadastrar horário!";
                  return alerta($msg,$title);
                }

                $conn = bdConnect();
                $sql = "SELECT b.descricao as BANCADA, h.descricao as HORARIO, g.id_bancada as ID_BANCADA, r.id_horarios as ID_HORARIO, r.data as DATA FROM reserva r inner join horarios h ON (h.id = r.id_horarios) inner join grupo g ON (g.id =r.id_grupo) inner join bancada b ON (b.id = g.id_bancada) WHERE r.ativo = 1";

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                      // output data of each row

                  while($row = $result->fetch_assoc()) {  

                    $sql2 = "SELECT id_bancada as ID_BANCADA from grupo where id = $grupo";
                    $result2 = $conn->query($sql2);
                    $bancada = $result2->fetch_assoc();
                        
                    if($row["ID_HORARIO"] === $horarios && $row["DATA"] === $data && $bancada["ID_BANCADA"] === $row["ID_BANCADA"]){
                      $conn->close();
                      $msg = "Para a data " . date('d/m/Y',strtotime($data)) . " escolhida, o horário " . $row["HORARIO"] . " na " . $row["BANCADA"] . " já está reservado! Por favor, escolher outro horário ou dia!";
                      $title = "Não foi possível cadastrar horário!";
                      return alerta($msg,$title);
                    }

                  } 
                      
                } 
                else {
                      echo "0 results";
                }



                $sql = "INSERT INTO reserva (id_grupo,id_horarios,ativo,data) VALUES (" . $grupo . ", " . $horarios . ", 1, '" . $data . "')";

                if ($conn->query($sql) === TRUE) {
                   // echo "<br>New record created successfully<br>";
                  $conn->close();
                  $title = "Horário reservado com sucesso!";
                  $msg = "O horário foi reservado para a data escolhida! ";
                  confirmar($msg,$title);
                } else {
                   // echo "<br>Error: " . $sql . "<br>" . $conn->error;
                    $conn->close();
                }

               // echo "<meta HTTP-EQUIV='refresh' CONTENT='5;URL=labs.php'>";
              }

              function getReservas(){

                $conn = bdConnect();

                $sql = "SELECT g.descricao AS GRUPO, t.descricao as TURMA, h.descricao as HORARIO, r.data as DATA from reserva r inner join grupo g on (r.id_grupo = g.id) inner join horarios h on (r.id_horarios = h.id) inner join turma t on (g.id_turma = t.id) WHERE r.ativo = 1 order by data asc, HORARIO asc";

                $result = $conn->query($sql);
                    

                if ($result->num_rows > 0) {
                      // output data of each row

                  while($row = $result->fetch_assoc()) {  
                        
                    showReserva($row["GRUPO"],$row["TURMA"],$row["HORARIO"],$row["DATA"]);

                  } 
                      
                } 
                else {
                      echo "\t\t\t<div class=\"text\"><br>\n";
                      echo "\t\t\t\t<h4> Nenhum horário marcado </h4> <br>\n";
                      echo "\t\t\t</div>\n";
                }

              }

              function showReservas(){
                echo "<div class=ex1>\n";
                echo "\t\t\t<div class=\"row\">\n";
                echo "\t\t\t\t  <div class=\"column\" style=\"background-color:#aaa;\">\n";
                echo "\t\t\t\t <p style=\"font-weight: bold\";> DATA </p>\n";
                echo "\t\t\t\t  </div>\n";
                echo "\t\t\t\t  <div class=\"column\" style=\"background-color:#bbb;\">\n";
                echo "\t\t\t\t <p style=\"font-weight: bold\"> GRUPO </p>\n";
                echo "\t\t\t\t  </div>\n";
                echo "\t\t\t\t  <div class=\"column\" style=\"background-color:#ccc;\">\n";
                echo "\t\t\t\t <p style=\"font-weight: bold\"> HORÁRIO </p>\n";
                echo "\t\t\t\t  </div>\n";

                echo "\t\t\t</div>\n";
                getReservas();
                echo "</div>\n";
              }
              //Códigos Turmas

              


?>
