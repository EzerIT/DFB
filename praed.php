<?php
require_once('head.inc.php');

makeheadstart('Prædikentekster');
makeheadend();
makemenus(6);
?>


<div class="container">
    <div class="row justify-content-center">

    <div class="col-lg-10 col-xl-8">
      <div class="card mt-4">
        <div class="card-body">
          <img class="img-fluid float-right d-none d-lg-block" style="width: 300px; margin-top: 0px; margin-left: 10px" src="img/61.jpg" alt="">
          <h1>Kirkeårets prædikentekster</h1>

          <p>På denne side vil der gradvis opstå links til Den Frie Bibels oversættelse af
              kirkeårets prædikentekster i den danske folkekirke.</p>

          <table class="table table-striped">
              <tr>
                  <th>Dato<br>2021</th><th>Dag</th><th>1. læsning</th><th>2. læsning</th><th>3. læsning</th>
              </tr>
              <tr>
                  <td>14.2</td><td>Fastelavns søndag</td><td><a target="_blank" href="show.php?bog=sl&kap=2">Sl 2</a></td>
                  <td>&nbsp;</td><td>&nbsp;</td>
              </tr>
          </table>

        </div>
      </div>
    </div>
 
  </div><!--End of row-->
</div><!--End of container-->

<?php
endbody();
?>
